<?php

namespace Laradium\Laradium\Base\Resources;

use Laradium\Laradium\Base\Laradium;
use Laradium\Laradium\Models\Menu;
use Laradium\Laradium\Base\AbstractResource;
use Laradium\Laradium\Base\FieldSet;
use Laradium\Laradium\Base\ColumnSet;

Class MenuResource extends AbstractResource
{

    /**
     * @var string
     */
    protected $resource = Menu::class;

    /**
     * @return \Laradium\Laradium\Base\Resource
     */
    public function resource()
    {
        $this->event('afterSave', function () {
            cache()->forget(Menu::$cacheKey);
        });

        $resources = collect((new Laradium)->resources())->mapWithKeys(function ($r) {
            return [$r => (new $r)->getBaseResource()->getName()];
        })->toArray();
        $resources = array_merge(['' => '- Select -'], $resources);

        return laradium()->resource(function (FieldSet $set) use ($resources) {
            $set->tab('Items')->fields(function (FieldSet $set) use ($resources) {
                $set->hasMany('items')->fields(function (FieldSet $set) use ($resources) {
                    $set->select('target')->options([
                        '_self'  => 'Self',
                        '_blank' => 'Blank',
                    ])->rules('required');
                    $set->text('name')->rules('required|max:255')->translatable();
                    $set->text('url')->rules('max:255')->translatable();
                    $set->select('resource')->options($resources);
                })->sortable('sequence_no')->label('Items')->nestable();
            });

            $set->tab('Main')->fields(function (FieldSet $set) use ($resources) {
                $set->boolean('is_active');
                $set->text('key')->rules('required|max:255');
                $set->text('name')->rules('required|max:255')->translatable();
            });
            // TODO needs possibility to add attributes
        });
    }

    /**
     * @return \Laradium\Laradium\Base\Table
     */
    public function table()
    {
        return laradium()->table(function (ColumnSet $column) {
            $column->add('key');
            $column->add('is_active')->switchable();
            $column->add('name')->translatable();
        })->relations(['translations']);
    }
}