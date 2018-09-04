<?php

namespace Laradium\Laradium\Models;

use Illuminate\Database\Eloquent\Model;

class SettingTranslation extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'value',
        'locale'
    ];
    /**
     * @var bool
     */
    public $timestamps = false;
}