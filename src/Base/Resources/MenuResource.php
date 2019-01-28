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
                $set->tree('items')->fields(function (FieldSet $set) use ($resources) {
                    $set->select2('icon')->options(getFontAwesomeIcons());
                    $set->text('name')->rules('max:255')->translatable()->col(6);
                    $set->text('url')->rules('max:255')->translatable()->col(6);
                    $set->select('target')->options([
                        '_self'  => 'Self',
                        '_blank' => 'Blank',
                    ])->rules('required')->col(6);
                    $set->select('resource')->options($resources)->col(6);
                })->sortable();
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