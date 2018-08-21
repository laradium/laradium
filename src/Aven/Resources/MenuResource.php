<?php

namespace Netcore\Aven\Aven\Resources;

use Netcore\Aven\Models\Menu;
use Netcore\Aven\Aven\AbstractAvenResource;
use Netcore\Aven\Aven\FieldSet;
use Netcore\Aven\Aven\Resource;
use Netcore\Aven\Aven\ColumnSet;
use Netcore\Aven\Aven\Table;

Class MenuResource extends AbstractAvenResource
{

    /**
     * @var string
     */
    protected $resource = Menu::class;

    /**
     * @return \Netcore\Aven\Aven\Resource
     */
    public function resource()
    {
        $this->registerEvent('afterSave', function () {
            cache()->forget(Menu::$cacheKey);
        });

        return (new Resource)->make(function (FieldSet $set) {
            $set->boolean('is_active');
            $set->text('key')->rules('required');
            $set->text('name')->rules('required')->translatable();
            $set->tab('Items')->fields(function (FieldSet $set) {
                $set->hasMany('items')->fields(function (FieldSet $set) {
                    $set->boolean('is_active');
                    $set->select('target')->options([
                        '_self'   => 'Self',
                        '_target' => 'Target',
                    ])->rules('required');
                    $set->text('name')->rules('required')->translatable();
                    $set->text('url')->rules('required')->translatable();
                })->sortable('sequence_no');
            });


            // TODO needs possibility to add attributes
        });
    }

    /**
     * @return Table
     */
    public function table()
    {
        return (new Table)->make(function (ColumnSet $column) {
            $column->add('key');
            $column->add('is_active')->modify(function ($item) {
                return $item->is_active ? 'Yes' : 'No';
            });
            $column->add('name')->translatable();
        })->relations(['translations']);
    }
}