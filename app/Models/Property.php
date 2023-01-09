<?php

namespace App\Models;

use App\Enums\PropertyStatus;
use App\Observers\PropertyObserver;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Tags\HasTags;
use Stancl\VirtualColumn\VirtualColumn;

class Property extends Model
{
    use HasFactory;
    use SoftDeletes;
    use VirtualColumn;
    use HasUUID;
    use HasTags;

    protected $fillable = [
        'user_id',
        'description',
        'type',
        'rooms_number',
        'country',
        'location',
        'address_original',
        'address_ru',
        'address_en',
        'custom_nickname',
        'email',
        'phone',
        'status',
        'visibility',
    ];

    protected $appends = [
        'contact',
    ];

    public static function boot(): void
    {
        parent::boot();

        self::observe(PropertyObserver::class);
    }

    /**
     * @return string[]
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'uuid',
            'user_id',
            'description',
            'type',
            'rooms_number',
            'country',
            'location',
            'address_original',
            'address_ru',
            'address_en',
            'status',
            'visibility',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return MorphMany
     */
    public function pictures(): MorphMany
    {
        return $this->morphMany(Picture::class, 'model');
    }

    /**
     * @return MorphOne
     */
    public function firstPicture(): MorphOne
    {
        return $this->morphOne(Picture::class, 'model')->oldestOfMany();
    }

    /**
     * @param Builder $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', PropertyStatus::PUBLISHED)
            ->where('visibility', true);
    }

    /**
     * @return ConnectedAccount|null
     */
    public function getContactAttribute(): ?ConnectedAccount
    {
        if ($this->user) {
            return $this->user->contact;
        }

        return null;
    }
}
