<?php

namespace Netcore\Aven\Models;

use Illuminate\Database\Eloquent\Model;

class MenuTranslation extends Model
{

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'locale',
    ];
}
