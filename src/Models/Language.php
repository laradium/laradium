<?php

namespace Laradium\Laradium\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model implements \Czim\Paperclip\Contracts\AttachableInterface
{
    use \Czim\Paperclip\Model\PaperclipTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'iso_code',
        'title',
        'title_localized',
        'is_fallback',
        'is_visible',
        'icon',
    ];

    /**
     * Language constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->hasAttachedFile('icon', []);

        parent::__construct($attributes);
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return parent::getAttributes();
    }
}
