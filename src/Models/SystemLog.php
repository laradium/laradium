<?php

namespace Laradium\Laradium\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'method',
        'url',
        'message',
        'type',
        'ip',
        'browser',
        'platform',
        'data'
    ];
}
