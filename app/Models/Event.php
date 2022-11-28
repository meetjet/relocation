<?php

namespace App\Models;

use App\Enums\EventPaymentType;
use App\Enums\EventStatus;
use App\Observers\EventObserver;
use App\Scopes\CountryScope;
use App\Scopes\HasUserScope;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;
use Stancl\VirtualColumn\VirtualColumn;

class Event extends Model
{
    use HasFactory;
    use SoftDeletes;
    use VirtualColumn;
    use HasUUID;
    use HasTags;
    use HasSlug;

    protected $fillable = [
        'slug',
        'user_id',
        'country',
        'location',
        'title',
        'description',
        'status',
        'visibility',
        'custom_nickname',
        'email',
        'phone',
        'price',
        'currency',
        'payment_type',
        'point_slug',
        'address',
        'category_id',
        'published_at',
        'start_date',
        'start_time',
        'finish_date',
        'finish_time',
    ];

    protected $appends = ['formatted_price', 'frontend_price', 'contact'];

    protected $casts = [
        'price' => 'integer',
        'published_at' => 'datetime',
        'start_date' => 'datetime',
        'start_time' => 'datetime',
        'finish_date' => 'datetime',
        'finish_time' => 'datetime',
    ];

    public static function boot(): void
    {
        parent::boot();

        self::observe(EventObserver::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CountryScope());
        static::addGlobalScope(new HasUserScope());
    }

    /**
     * @return string[]
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'uuid',
            'slug',
            'user_id',
            'country',
            'location',
            'title',
            'description',
            'status',
            'visibility',
            'created_at',
            'updated_at',
            'deleted_at',
            'published_at',
            'price',
            'currency',
            'payment_type',
            'point_slug',
            'address',
            'category_id',
            'start_date',
            'start_time',
            'finish_date',
            'finish_time',
        ];
    }

    /**
     * @return SlugOptions
     */
    public function getSlugOptions(): SlugOptions
    {
        $slugOptions = SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->skipGenerateWhen(fn() => is_null($this->title));

        if (!is_null($this->slug)) {
            $slugOptions->doNotGenerateSlugsOnUpdate();
        }

        return $slugOptions;
    }

    /**
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }

    /**
     * @return BelongsTo
     */
    public function point(): BelongsTo
    {
        return $this->belongsTo(EventPoint::class, 'point_slug', 'slug');
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
        $query->where('status', EventStatus::PUBLISHED)
            ->where('visibility', true);
    }

    /**
     * @return string|null
     */
    public function getFormattedPriceAttribute(): ?string
    {
        return $this->price
            ? $this->price . ' ' . currencies()->getSign($this->currency)
            : null;
    }

    /**
     * @return string|null
     */
    public function getFrontendPriceAttribute(): ?string
    {
        return $this->payment_type === EventPaymentType::FREE
            ? EventPaymentType::getDescription($this->payment_type)
            : $this->formatted_price;
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
