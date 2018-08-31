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
            $fieldType = $set->model()->type;

            if($set->model()->is_translatable) {
                $set->$fieldType('value')->translatable();
            } else {
                $set->$fieldType('non_translatable_value');
            }
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
            $column->add('value')->modify(function ($item) {
                if($item->is_translatable) {
                    return view('aven::admin.resource._partials.translation', compact('item'));
                } else {
                    return $item->non_translatable_value ? e($item->non_translatable_value) : 'N/A';
                }
            });
        })->actions(['edit'])->relations(['translations']);
    }
}