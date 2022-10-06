<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait HasUUID
{
    public static function bootHasUUID(): void
    {
        static::creating(static function ($model) {
            $uuidFieldName = $model->getUUIDFieldName();

            if (empty($model->$uuidFieldName)) {
                $model->$uuidFieldName = static::generateUUID();
            }
        });
    }

    /**
     * @return string
     */
    public function getUUIDFieldName(): string
    {
        if (!empty($this->uuidFieldName)) {
            return $this->uuidFieldName;
        }

        return 'uuid';
    }

    /**
     * @return string
     */
    public static function generateUUID(): string
    {
        return Str::uuid()->toString();
    }

    /**
     * @param Builder $query
     * @param string $uuid
     *
     * @return Builder
     */
    public function scopeByUUID(Builder $query, string $uuid): Builder
    {
        return $query->where($this->getUUIDFieldName(), $uuid);
    }

    /**
     * @param string $uuid
     *
     * @return mixed
     */
    public static function findByUuid(string $uuid)
    {
        return static::byUUID($uuid)->first();
    }
}
