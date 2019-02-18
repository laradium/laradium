<?php

namespace Laradium\Laradium\Base\Resources;

use Laradium\Laradium\Base\AbstractResource;
use Laradium\Laradium\Base\ColumnSet;
use Laradium\Laradium\Base\FieldSet;
use Laradium\Laradium\Base\Laradium;
use Laradium\Laradium\Models\Menu;

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

        return laradium()->resource(function (FieldSet $set) {
            $set->tab('Items')->fields(function (FieldSet $set) {
                $set->tree('items')->fields(function (FieldSet $set) {
                    $set->select2('icon')->options(getFontAwesomeIcons());
                    $set->text('name')->rules('required|max:255')->translatable()->col(6);
                    $set->select('target')->options([
                        '_self'  => 'Self',
                        '_blank' => 'Blank',
                    ])->rules('required')->col(6);
                    $set->select('type')->options(Menu::$types);
                    $set->text('url')->rules('required_if:items.*.type,url|max:255')->translatable()->col(4);
                    $set->select('resource')->options($this->getResourceOptions())->rules('required_if:items.*.type,resource')->col(4);
                    $set->select('route')->options($this->getRouteOptions())->rules('required_if:items.*.type,route')->col(4);
                })->sortable();
            });

            $set->tab('Main')->fields(function (FieldSet $set) {
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

    /**
     * @return array
     */
    public function getResourceOptions(): array
    {
        $resources = collect((new Laradium)->resources())->mapWithKeys(function ($r) {
            return [$r => (new $r)->getBaseResource()->getName()];
        })->toArray();

        return array_merge(['' => '- Select -'], $resources);
    }

    /**
     * @return mixed
     */
    public function getRouteOptions()
    {
        return (new Menu)->getRoutes((request()->route()->menu && request()->route()->menu === '1') ? 'admin' : 'public');
    }
}