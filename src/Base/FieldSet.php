<?php

namespace Laradium\Laradium\Base;

use Laradium\Laradium\Base\Fields\BelongsTo;
use Laradium\Laradium\Base\Fields\BelongsToMany;
use Laradium\Laradium\Base\Fields\Block;
use Laradium\Laradium\Base\Fields\Boolean;
use Laradium\Laradium\Base\Fields\Breadcrumbs;
use Laradium\Laradium\Base\Fields\Button;
use Laradium\Laradium\Base\Fields\Code;
use Laradium\Laradium\Base\Fields\Col;
use Laradium\Laradium\Base\Fields\Color;
use Laradium\Laradium\Base\Fields\Crud;
use Laradium\Laradium\Base\Fields\CustomContent;
use Laradium\Laradium\Base\Fields\Date;
use Laradium\Laradium\Base\Fields\DateTime;
use Laradium\Laradium\Base\Fields\Email;
use Laradium\Laradium\Base\Fields\File;
use Laradium\Laradium\Base\Fields\FormSubmit;
use Laradium\Laradium\Base\Fields\HasMany;
use Laradium\Laradium\Base\Fields\HasOne;
use Laradium\Laradium\Base\Fields\Hidden;
use Laradium\Laradium\Base\Fields\LanguageSelect;
use Laradium\Laradium\Base\Fields\Link;
use Laradium\Laradium\Base\Fields\MapPosition;
use Laradium\Laradium\Base\Fields\Modal;
use Laradium\Laradium\Base\Fields\ModalButton;
use Laradium\Laradium\Base\Fields\MorphTo;
use Laradium\Laradium\Base\Fields\Password;
use Laradium\Laradium\Base\Fields\Radio;
use Laradium\Laradium\Base\Fields\Row;
use Laradium\Laradium\Base\Fields\SaveButtons;
use Laradium\Laradium\Base\Fields\Select;
use Laradium\Laradium\Base\Fields\Select2;
use Laradium\Laradium\Base\Fields\SvgIcon;
use Laradium\Laradium\Base\Fields\Tab;
use Laradium\Laradium\Base\Fields\Text;
use Laradium\Laradium\Base\Fields\Table;
use Laradium\Laradium\Base\Fields\Textarea;
use Laradium\Laradium\Base\Fields\Time;
use Laradium\Laradium\Base\Fields\Tree;
use Laradium\Laradium\Base\Fields\Wysiwyg;
use Laradium\Laradium\Registries\FieldRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Basic fields
 * @method Text text(string $name)
 * @method Textarea textarea(string $name)
 * @method Wysiwyg wysiwyg(string $name)
 * @method Select select(string $name)
 * @method Select2 select2(string $name)
 * @method Color color(string $name)
 * @method Boolean boolean(string $name)
 * @method Email email(string $name)
 * @method File file(string $name)
 * @method Code code(string $name)
 * @method Time time(string $name)
 * @method Date date(string $name)
 * @method DateTime dateTime(string $name)
 * @method Hidden hidden(string $name)
 * @method Password password(string $name)
 * @method Radio radio(string $name)
 * @method SvgIcon svgIcon(string $name)
 * @method Tab tab(string $name)
 *
 * Relations
 * @method BelongsTo belongsTo(string $name)
 * @method BelongsToMany belongsToMany(string $name)
 * @method HasOne hasOne(string $name)
 * @method HasMany hasMany(string $name)
 * @method MorphTo morphTo(string $name)
 *
 * Elements
 * @method Block block($col)
 * @method Button button(string $name)
 * @method Col col($col)
 * @method Link link(string $name, string $link)
 * @method Row row($fields)
 *
 * Addition fields
 * @method SaveButtons saveButtons()
 * @method Breadcrumbs breadcrumbs(array $breadcrumbs)
 * @method Crud crud(FormNew $form)
 * @method Table table(\Laradium\Laradium\Base\Table $table)
 * @method CustomContent customContent($content)
 * @method FormSubmit formSubmit(string $formName)
 * @method LanguageSelect languageSelect()
 * @method MapPosition mapPosition(string $name)
 * @method Modal modal(string $name)
 * @method ModalButton modalButton(string $name)
 * @method Tree tree(string $name)
 */
class FieldSet
{

    /**
     * @var \Illuminate\Foundation\Application|mixed
     */
    protected $fieldRegistry;

    /**
     * @var Collection
     */
    public $fields;

    /**
     * @var
     */
    protected $model;

    /**
     * FieldSet constructor.
     */
    public function __construct()
    {
        $this->fieldRegistry = app(FieldRegistry::class);
        $this->fields = new Collection;
    }

    /**
     * @param Model $model
     * @return $this
     */
    public function model($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return Collection
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * @param $method
     * @param $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        $class = $this->fieldRegistry->getClassByName($method);
        $model = $this->getModel();
        if (!$model) {
            $model = null;
        }

        if (class_exists($class)) {
            $field = new $class($parameters, $model);
            $this->fields->push($field);

            return $field;
        }

        return $this;
    }
}