<?php

namespace Laradium\Laradium\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

trait ModelBoot
{

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        $belongsTo = laradium()->belongsTo();
        if ($belongsTo && Schema::hasColumn(self::getModel()->getTable(), $belongsTo->getForeignKey()) && !app()->runningInConsole()) {
            static::addGlobalScope($belongsTo->getRelation(), function (Builder $builder) use ($belongsTo) {
                $builder->where($belongsTo->getForeignKey(), '=', $belongsTo->getCurrent());
            });

            static::creating(function ($model) use ($belongsTo) {
                $model->{$belongsTo->getForeignKey()} = $belongsTo->getCurrent();
            });
        }
    }

}
