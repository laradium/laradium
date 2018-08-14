<?php

namespace Netcore\Aven\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{

    /**
     * @var array
     */
    protected $fillable = [
        'locale',
        'group',
        'key',
        'value',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
