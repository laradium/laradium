<?php

namespace Netcore\Aven\Aven\Resources;

use Netcore\Aven\Models\Language;
use Netcore\Aven\Models\Translation;
use Netcore\Aven\Aven\AbstractAvenResource;
use Netcore\Aven\Aven\ColumnSet;
use Netcore\Aven\Aven\FieldSet;
use Netcore\Aven\Aven\Resource;
use Netcore\Aven\Aven\Table;

Class TranslationResource extends AbstractAvenResource
{

    /**
     * @var string
     */
    protected $resource = Translation::class;

    /**
     * @return \Netcore\Aven\Aven\Resource
     */
    public function resource()
    {
        $this->registerEvent('afterSave', function () {
            cache()->forget('translations');
        });

        return (new Resource)->make(function (FieldSet $set) {
            $set->select('locale')->options($this->localeList());
            $set->text('group')->rules('required');
            $set->text('key')->rules('required');
            $set->text('value')->rules('required');
        });
    }

    /**
     * @return Table
     */
    public function table()
    {
        return (new Table)->make(function (ColumnSet $column) {
            $column->add('locale');
            $column->add('group');
            $column->add('key');
            $column->add('value')->editable();
        });
    }

    /**
     * @return mixed
     */
    protected function localeList() {
        return Language::pluck('iso_code', 'iso_code')->toArray();
    }
}