<?php

namespace Laradium\Laradium\Models;

use Illuminate\Database\Eloquent\Model;

class SettingTranslation extends Model implements \Czim\Paperclip\Contracts\AttachableInterface
{
    use \Czim\Paperclip\Model\PaperclipTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'file',
        'value',
        'locale'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * Language constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->hasAttachedFile('file', [
            'path' => '/settings/translatable/:attachment/:id/:variant/:hash/:filename'
        ]);

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