<?php

namespace Netcore\Aven\Aven\Resources;

use Netcore\Aven\Models\Language;
use Netcore\Aven\Aven\AbstractAvenResource;
use Netcore\Aven\Aven\FieldSet;
use Netcore\Aven\Aven\Resource;
use Netcore\Aven\Aven\ColumnSet;
use Netcore\Aven\Aven\Table;

Class LanguageResource extends AbstractAvenResource
{

    /**
     * @var string
     */
    protected $resource = Language::class;

    /**
     * @return \Netcore\Aven\Aven\Resource
     */
    public function resource()
    {
        $this->registerEvent('afterSave', function () {
            cache()->forget('languages');
        });

        return (new Resource)->make(function (FieldSet $set) {
            $set->text('iso_code')->rules('required');
            $set->text('title')->rules('required');
            $set->text('title_localized')->rules('required');
            $set->boolean('is_fallback');
            $set->boolean('is_visible');
            $set->file('icon');
        });
    }

    /**
     * @return Table
     */
    public function table()
    {
        return (new Table)->make(function (ColumnSet $column) {
            $column->add('iso_code');
            $column->add('title');
            $column->add('title_localized');
            $column->add('is_visible');
            $column->add('is_fallback');
        });
    }
}