<?php

namespace Laradium\Laradium\Base\Resources;

use Laradium\Laradium\Base\AbstractResource;
use Laradium\Laradium\Base\ColumnSet;
use Laradium\Laradium\Base\FieldSet;
use Laradium\Laradium\Models\Language;

Class LanguageResource extends AbstractResource
{
    /**
     * @var string
     */
    protected $resource = Language::class;

    /**
     * @return \Laradium\Laradium\Base\Resource
     */
    public function resource()
    {
        $this->event(['afterSave', 'afterDelete'], function () {
            cache()->forget('laradium::languages');
        });

        return laradium()->resource(function (FieldSet $set) {
            $set->text('iso_code')->rules('required|min:2|max:2');
            $set->text('title')->rules('required|min:2|max:255');
            $set->text('title_localized')->rules('required|min:2|max:255');
            $set->boolean('is_fallback');
            $set->boolean('is_visible');
            $set->file('icon')->rules('image|max:' . config('laradium.file_size'));
        });
    }

    /**
     * @return \Laradium\Laradium\Base\Table
     */
    public function table()
    {
        return laradium()->table(function (ColumnSet $column) {
            $column->add('iso_code');
            $column->add('title');
            $column->add('title_localized');
            $column->add('is_visible');
            $column->add('is_fallback');
        });
    }
}