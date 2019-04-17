<?php

namespace Laradium\Laradium\Models;

use Czim\Paperclip\Model\PaperclipTrait;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model implements \Czim\Paperclip\Contracts\AttachableInterface
{

    use PaperclipTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'file'
    ];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->hasAttachedFile('file', [
            'path' => '/attachment/:hash/:filename',
        ]);

        parent::__construct($attributes);
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return parent::getAttributes();
    }
}
