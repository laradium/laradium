<?php

namespace Laradium\Laradium\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemLog extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /** Relationships */

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    /** Accessors */

    /**
     * @param $value
     * @return object|array
     */
    public function getDataAttribute($value)
    {
        return json_decode($value);
    }
}
