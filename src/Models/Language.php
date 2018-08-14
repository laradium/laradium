<?php

namespace Netcore\Aven\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{

    /**
     * @var array
     */
    protected $fillable = [
        'iso_code',
        'title',
        'title_localized',
        'is_fallback',
        'is_visible',
    ];
}
