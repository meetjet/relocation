<?php

namespace App\Models;

use App\Enums\FaqStatus;
use App\Enums\ListingItemStatus;
use App\Observers\FaqObserver;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;
use Stancl\VirtualColumn\VirtualColumn;

class Faq extends Model
{
    use HasFactory;
    use SoftDeletes;
    use VirtualColumn;
    use HasUUID;
    use HasTags;
    use HasSlug;

    protected $fillable = ['slug', 'user_id', 'original', 'country', 'title', 'question', 'answer', 'status', 'visibility'];

    public static function boot(): void
    {
        parent::boot();

        self::observe(FaqObserver::class);
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
            'original',
            'country',
            'title',
            'question',
            'answer',
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
     * @param Builder $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', FaqStatus::PUBLISHED)
            ->where('visibility', true);
    }
}
