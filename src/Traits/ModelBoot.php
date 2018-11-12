<?php

namespace Laradium\Laradium\Traits;

use Illuminate\Database\Eloquent\Builder;

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

        static::addGlobalScope($belongsTo->getRelation(), function (Builder $builder) use ($belongsTo) {
            $builder->where($belongsTo->getForeignKey(), '=', $belongsTo->getCurrent());
        });

        static::creating(function ($model) use ($belongsTo) {
            $model->{$belongsTo->getForeignKey()} = $belongsTo->getCurrent();
        });
    }

}
