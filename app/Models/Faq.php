<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Tags\HasTags;
use Stancl\VirtualColumn\VirtualColumn;

class Faq extends Model
{
    use HasFactory;
    use SoftDeletes;
    use VirtualColumn;
    use HasUUID;
    use HasTags;

    protected $fillable = ['original', 'title', 'question', 'answer', 'status', 'visibility'];

    /**
     * @return string[]
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'uuid',
            'original',
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
}
