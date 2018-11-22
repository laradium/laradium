<?php

namespace Laradium\Laradium\Models;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\PassThroughs\Translation\Import;

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

    /**
     * @return Import
     */
    public function import()
    {
        return new Import();
    }
}
