<?php

namespace App\Models;

use App\Scopes\HasUserScope;
use Bavix\Wallet\Interfaces\Customer;
use Bavix\Wallet\Traits\CanPay;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use JoelButcher\Socialstream\HasConnectedAccounts;
use JoelButcher\Socialstream\SetsProfilePhotoFromUrl;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Comments\Models\Concerns\InteractsWithComments;
use Spatie\Comments\Models\Concerns\Interfaces\CanComment;

class User extends Authenticatable implements FilamentUser, Customer, CanComment
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto {
        getProfilePhotoUrlAttribute as getPhotoUrl;
    }
    use SoftDeletes;
    use HasTeams;
    use HasConnectedAccounts;
    use Notifiable;
    use SetsProfilePhotoFromUrl;
    use TwoFactorAuthenticatable;
    use CanPay;
    use InteractsWithComments;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
        'contact',
        'is_admin',
    ];

    /**
     * @return void
     */
    protected static function booted(): void
    {
        static::forceDeleted(static function (User $user) {
            // Delete all user announcement. TODO: try to implement in job.
            $user->listingItems()
                ->withoutGlobalScope(HasUserScope::class)
                ->withTrashed()
                ->get()
                ->each(function ($_listingItem) {
                    $_listingItem->forceDelete();
                });
        });
    }

    /**
     * Get the URL to the user's profile photo.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute()
    {
        if (filter_var($this->profile_photo_path, FILTER_VALIDATE_URL)) {
            return $this->profile_photo_path;
        }

        return $this->getPhotoUrl();
    }

    /**
     * @return bool
     */
    public function canAccessFilament(): bool
    {
        // TODO: temporary way to identify administrator users.
        return str_ends_with(Str::lower($this->email), '@relocation.digital');
    }

    /**
     * @return bool
     */
    public function getIsAdminAttribute(): bool
    {
        return $this->canAccessFilament();
    }

    /**
     * @return ConnectedAccount|null
     */
    public function getContactAttribute(): ?ConnectedAccount
    {
        return $this->currentConnectedAccount()->first();
    }

    /**
     * @return HasMany
     */
    public function listingItems(): HasMany
    {
        return $this->hasMany(ListingItem::class, 'user_id');
    }
}
