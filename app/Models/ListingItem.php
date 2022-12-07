<?php

namespace App\Models;

use App\Enums\ListingItemStatus;
use App\Observers\ListingItemObserver;
use App\Scopes\CountryScope;
use App\Scopes\HasUserScope;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\Tags\HasTags;
use Stancl\VirtualColumn\VirtualColumn;

class ListingItem extends Model
{
    use HasFactory;
    use SoftDeletes;
    use VirtualColumn;
    use HasUUID;
    use HasTags;
    use HasSEO;

    protected $fillable = [
        'user_id',
        'category_id',
        'country',
        'location',
        'title',
        'description',
        'price',
        'currency',
        'status',
        'visibility',
        'custom_nickname',
        'published_at',
        'email',
        'phone',
        'original',
    ];

    protected $appends = ['contact'];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public static function boot(): void
    {
        parent::boot();

        self::observe(ListingItemObserver::class);
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
            'user_id',
            'category_id',
            'country',
            'location',
            'title',
            'description',
            'price',
            'currency',
            'status',
            'visibility',
            'created_at',
            'updated_at',
            'deleted_at',
            'published_at',
            'original',
        ];
    }

    /**
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * @return SEOData
     */
    public function getDynamicSEOData(): SEOData
    {
        $picture = $this->firstPicture()->first();

        return new SEOData(
            title: $this->seo->title ?: $this->title,
            image: $picture->thumbnail_square ?? null
        );
    }

    /**
     * Get item price.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn($value) => (int)$value,
        );
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ListingCategory::class, 'category_id');
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
        $query->where('status', ListingItemStatus::PUBLISHED)
            ->where('visibility', true);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
