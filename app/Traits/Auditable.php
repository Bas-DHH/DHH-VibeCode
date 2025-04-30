<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    protected static function bootAuditable()
    {
        static::created(function (Model $model) {
            AuditLog::log('created', $model, null, $model->getAttributes());
        });

        static::updated(function (Model $model) {
            $oldValues = collect($model->getOriginal())
                ->only($model->getDirty())
                ->toArray();

            $newValues = collect($model->getDirty())
                ->mapWithKeys(function ($value, $key) use ($model) {
                    return [$key => $model->getAttribute($key)];
                })
                ->toArray();

            AuditLog::log('updated', $model, $oldValues, $newValues);
        });

        static::deleted(function (Model $model) {
            AuditLog::log('deleted', $model, $model->getOriginal(), null);
        });
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
} 