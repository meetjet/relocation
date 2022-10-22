<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;
use Stancl\VirtualColumn\VirtualColumn;

class ListingItem extends Model
{
    use HasFactory;
    use SoftDeletes;
    use VirtualColumn;
    use HasUUID;
    use HasTags;
    use HasSlug;

    protected $fillable = ['slug', 'user_id', 'category_id', 'country', 'title', 'description', 'status', 'visibility'];

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
            'category_id',
            'country',
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
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ListingCategory::class, 'category_id');
    }
}
