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
use Illuminate\Support\Carbon;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\Tags\HasTags;
use Stancl\VirtualColumn\VirtualColumn;

class Event extends Model
{
    use HasFactory;
    use SoftDeletes;
    use VirtualColumn;
    use HasUUID;
    use HasTags;
    use HasSEO;

    protected $fillable = [
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
        'place_slug',
        'address',
        'category_id',
        'published_at',
        'start_date',
        'start_time',
        'finish_date',
        'finish_time',
    ];

    protected $appends = [
        'formatted_price',
        'frontend_price',
        'contact',
        'frontend_start_datetime',
        'frontend_finish_datetime',
        'frontend_address',
        'frontend_url',
    ];

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
            'place_slug',
            'address',
            'category_id',
            'start_date',
            'start_time',
            'finish_date',
            'finish_time',
        ];
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
    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'place_slug', 'slug');
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
     * @param Builder $query
     */
    public function scopeStartOfCurrentWeek(Builder $query): void
    {
        $query->where('start_date', '>=', Carbon::now()->startOfWeek());
    }

    /**
     * @param Builder $query
     */
    public function scopeOrderByStartDate(Builder $query): void
    {
        $query->orderBy('start_date')
            ->orderBy('start_time');
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
        if ($this->payment_type) {
            return $this->payment_type === EventPaymentType::PAID
                ? $this->formatted_price
                : EventPaymentType::getDescription($this->payment_type);
        }

        return null;
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

    /**
     * @return string|null
     */
    public function getFrontendStartDatetimeAttribute(): ?string
    {
        if ($this->start_date) {
            $result = $this->start_date->translatedFormat("j F Y");

            if ($this->start_time) {
                $result .= ', ' . $this->start_time->translatedFormat("H:i");
            }

            return $result;
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getFrontendFinishDatetimeAttribute(): ?string
    {
        if ($this->finish_date) {
            $result = $this->finish_date->translatedFormat("j F Y");

            if ($this->finish_time) {
                $result .= ', ' . $this->finish_time->translatedFormat("H:i");
            }

            return $result;
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getFrontendAddressAttribute(): ?string
    {
        if ($this->address) {
            return $this->address;
        }

        if ($this->place && $this->place->address_ru) {
            return $this->place->address_ru;
        }

        if ($this->place && $this->place->address_en) {
            return $this->place->address_en;
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getFrontendUrlAttribute(): ?string
    {
        if (
            !$this->deleted_at
            && $this->status === EventStatus::PUBLISHED
            && $this->visibility
            && $this->category
            && $this->uuid
            && $this->country
        ) {
            return addSubdomainToUrl(route('events.show', [$this->category->slug, $this->uuid]), $this->country);
        }

        return null;
    }
}
