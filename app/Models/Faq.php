<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\VirtualColumn\VirtualColumn;

class Faq extends Model
{
    use HasFactory;
    use SoftDeletes;
    use VirtualColumn;
    use HasUUID;

    protected $fillable = ['original', 'question', 'answer', 'status', 'visibility'];

    /**
     * @return string[]
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'uuid',
            'original',
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
