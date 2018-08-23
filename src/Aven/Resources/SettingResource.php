<?php

namespace Netcore\Aven\Aven\Resources;

use Netcore\Aven\Models\Setting;
use Netcore\Aven\Aven\AbstractAvenResource;
use Netcore\Aven\Aven\FieldSet;
use Netcore\Aven\Aven\Resource;
use Netcore\Aven\Aven\ColumnSet;
use Netcore\Aven\Aven\Table;

Class SettingResource extends AbstractAvenResource
{

    /**
     * @var string
     */
    protected $resource = Setting::class;

    /**
     * @return \Netcore\Aven\Aven\Resource
     */
    public function resource()
    {
        return (new Resource)->make(function (FieldSet $set) {
            $set->text('value')->translatable();
        });
    }

     /**
     * @return Table
     */
    public function table()
    {
        return (new Table)->make(function (ColumnSet $column) {
            $column->add('group');
            $column->add('name');
            $column->add('is_translatable');
            $column->add('value')->translatable();
        })->actions(['edit'])->relations(['translations']);
    }
}