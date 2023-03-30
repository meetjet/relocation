<?php

namespace App\Models;

use App\Enums\AdvertisementStatus;
use App\Observers\AdvertisementObserver;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\VirtualColumn\VirtualColumn;

class Advertisement extends Model
{
    use HasFactory;
    use SoftDeletes;
    use VirtualColumn;
    use HasUUID;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'country',
        'status',
        'visibility',
    ];

    public static function boot(): void
    {
        parent::boot();

        self::observe(AdvertisementObserver::class);
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
            'title',
            'description',
            'country',
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
        $query->where('status', AdvertisementStatus::PUBLISHED)
            ->where('visibility', true);
    }
}
