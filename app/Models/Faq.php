<?php

namespace App\Models;

use App\Enums\FaqStatus;
use App\Observers\FaqObserver;
use App\Scopes\CountryScope;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
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
    use HasSEO;

    protected $fillable = ['slug', 'user_id', 'original', 'country', 'title', 'question', 'answer', 'status', 'visibility'];

    protected $appends = ['frontend_url'];

    public static function boot(): void
    {
        parent::boot();

        self::observe(FaqObserver::class);
    }

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
     * @return SEOData
     */
    public function getDynamicSEOData(): SEOData
    {
        return new SEOData(
            title: $this->seo->title ?: $this->title
        );
    }

    /**
     * @param Builder $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', FaqStatus::PUBLISHED)
            ->where('visibility', true);
    }

    /**
     * @return string|null
     */
    public function getFrontendUrlAttribute(): ?string
    {
        if (
            !$this->deleted_at
            && $this->status === FaqStatus::PUBLISHED
            && $this->visibility
            && $this->slug
        ) {
            return addSubdomainToUrl(route('faqs.show', $this->slug), $this->country);
        }

        return null;
    }
}
