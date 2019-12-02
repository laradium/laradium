<?php

namespace Laradium\Laradium\Models;

use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Czim\Paperclip\Model\PaperclipTrait;
use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Traits\PaperclipAndTranslatable;

class Setting extends Model implements \Czim\Paperclip\Contracts\AttachableInterface, TranslatableContract
{
    use PaperclipTrait, PaperclipAndTranslatable;

    use Translatable {
        PaperclipAndTranslatable::getAttribute insteadof Translatable;
        PaperclipAndTranslatable::setAttribute insteadof Translatable;
    }

    use PaperclipTrait {
        PaperclipAndTranslatable::getAttribute insteadof PaperclipTrait;
        PaperclipAndTranslatable::setAttribute insteadof PaperclipTrait;
    }

    /**
     * @var array
     */
    protected $fillable = [
        'key',
        'group',
        'name',
        'type',
        'meta',
        'non_translatable_value',
        'is_translatable',
        'file'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'meta' => 'array'
    ];

    /**
     * @var string
     */
    public $translationModel = SettingTranslation::class;

    /**
     * @var array
     */
    public $translatedAttributes = [
        'value'
    ];

    /**
     * @var array
     */
    protected $with = ['translations'];

    /**
     * Setting constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->hasAttachedFile('file', []);

        parent::__construct($attributes);
    }

    /**
     * @return array
     */
    public function getAttributesData()
    {
        $attributes = array_get($this->meta, 'attributes', []);
        if (!is_array($attributes)) {
            $attributes = [];
        }

        if (!isset($attributes['class'])) {
            $attributes['class'] = $this->getClass();
        }

        return implode(' ', array_map(
            function ($v, $k) {
                return sprintf('%s="%s"', $k, $v);
            },
            $attributes,
            array_keys($attributes)
        ));
    }

    /**
     * @return array
     */
    public function getOptionsData()
    {
        $options = array_get($this->meta, 'options', []);
        if (is_array($options)) {
            return $options;
        }

        if (function_exists($options)) {
            $options = $options();
        }

        if (!is_array($options)) {
            return [];
        }

        return $options;
    }

    /**
     * @param $type
     * @return bool
     */
    public function is($type)
    {
        return $this->type === $type;
    }

    /**
     * @return string
     */
    public function getValueAttribute()
    {
        if ($this->is('checkbox')) {
            return $this->value === '1';
        }

        if ($this->is_translatable) {
            return $this->value ?? null;
        } else {
            return $this->non_translatable_value;
        }
    }

    /**
     * @return string
     */
    private function getClass()
    {
        $classes = [
            'text'     => 'form-control',
            'textarea' => 'form-control',
            'checkbox' => '',
            'file'     => 'form-control',
            'select'   => 'form-control'
        ];

        return array_get($classes, $this->type, 'form-control');
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return parent::getAttributes();
    }
}