<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Stancl\VirtualColumn\VirtualColumn;

class Picture extends Model
{
    use HasFactory;
    use VirtualColumn;
    use HasUUID;

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
            'url',
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
}
