<?php

namespace App\Models;

use App\Enums\PlaceStatus;
use App\Scopes\CountryScope;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;
use Stancl\VirtualColumn\VirtualColumn;

class Place extends Model
{
    use HasFactory;
    use SoftDeletes;
    use VirtualColumn;
    use HasUUID;
    use HasTags;
    use HasSlug;
    use HasTags;

    protected $fillable = [
        'slug',
        'country',
        'location',
        'type',
        'title',
        'description',
        'address_ru',
        'address_en',
        'latitude',
        'longitude',
        'status',
        'visibility',
    ];

    /**
     * @return string[]
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'uuid',
            'slug',
            'country',
            'location',
            'type',
            'title',
            'description',
            'address_ru',
            'address_en',
            'latitude',
            'longitude',
            'status',
            'visibility',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CountryScope());
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
     * @return HasMany
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'place_slug', 'slug');
    }


    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PlaceCategory::class, 'category_id');
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
        $query->where('status', PlaceStatus::ACTIVE)
            ->where('visibility', true);
    }

    /**
     * @param Builder $query
     * @param string $slug
     *
     * @return Builder
     */
    public function scopeBySlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }

    /**
     * @return bool
     */
    public function getIsCurrentAttribute(): bool
    {
        $currentRoute = Route::current();

        return $currentRoute && $currentRoute->parameter('point') === $this->slug;
    }
}
