<?php

namespace Netcore\Aven\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use Translatable;

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
        'has_manager',
        'is_translatable'
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
     * @return array
     */
    public function getAttributesData()
    {
        $attributes = array_get($this->meta, 'attributes', []);
        if (! is_array($attributes)) {
            $attributes = [];
        }
        if (! isset($attributes['class'])) {
            $attributes['class'] = $this->getClass();
        }
        return implode(' ', array_map(
            function ($v, $k) { return sprintf('%s="%s"', $k, $v); },
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
        if (! is_array($options)) {
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
    public function getValue()
    {
        if ($this->is('file')) {
            return asset(config('aven-setting.upload_path') . '/' . $this->value);
        }
        if ($this->is('checkbox')) {
            return $this->value === '1';
        }

        if($this->is_translatable) {
            return $this->value;
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
}