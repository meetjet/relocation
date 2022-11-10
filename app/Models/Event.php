<?php

namespace App\Models;

use App\Enums\EventStatus;
use App\Scopes\CountryScope;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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

    protected $fillable = ['slug', 'user_id', 'country', 'city', 'title', 'description', 'status', 'visibility'];

    protected $appends = ['formatted_price'];

//    public static function boot(): void
//    {
//        parent::boot();
//
//        self::observe(EventObserver::class);
//    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CountryScope());
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
            'city',
            'title',
            'description',
            'status',
            'visibility',
            'created_at',
            'updated_at',
            'deleted_at',
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
     * @return MorphMany
     */
    public function pictures(): MorphMany
    {
        return $this->morphMany(Picture::class, 'model');
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
     * TODO: make price formatting based on currency
     * @return string
     */
    public function getFormattedPriceAttribute(): string
    {
        return (string)$this->price;
    }
}
