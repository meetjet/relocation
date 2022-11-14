<?php

namespace App\Models;

use App\Observers\PictureObserver;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Stancl\VirtualColumn\VirtualColumn;

class Picture extends Model
{
    use HasFactory;
    use VirtualColumn;
    use HasUUID;

    protected $fillable = ['caption', 'tmp_image'];

    protected $casts = [
        'content' => AsCollection::class,
    ];

    protected $attributes = [
        'content' => "{}",
    ];

    protected $appends = ['raw', 'large', 'medium', 'thumbnail', 'thumbnail_square'];

    public static function boot(): void
    {
        parent::boot();

        self::observe(PictureObserver::class);
    }

    /**
     * @return string[]
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'uuid',
            'model_type',
            'model_id',
            'content',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * @return MorphTo
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return string|null
     */
    public function getRawAttribute(): ?string
    {
        if ($this->content->has('raw')) {
            return $this->content->get('raw');
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getLargeAttribute(): ?string
    {
        if ($this->content->has('large')) {
            return $this->content->get('large');
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getMediumAttribute(): ?string
    {
        if ($this->content->has('medium')) {
            return $this->content->get('medium');
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getThumbnailAttribute(): ?string
    {
        if ($this->content->has('thumbnail')) {
            return $this->content->get('thumbnail');
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getThumbnailSquareAttribute(): ?string
    {
        if ($this->content->has('thumbnail-square')) {
            return $this->content->get('thumbnail-square');
        }

        return null;
    }
}
