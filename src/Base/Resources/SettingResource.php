<?php
namespace Laradium\Laradium\Base\Resources;
use Laradium\Laradium\Models\Setting;
use Laradium\Laradium\Base\AbstractResource;
use Laradium\Laradium\Base\FieldSet;
use Laradium\Laradium\Base\ColumnSet;
Class SettingResource extends AbstractResource
{
    /**
     * @var string
     */
    protected $resource = Setting::class;
    /**
     * @return \Laradium\Laradium\Base\Resource
     */
    public function resource()
    {
        return laradium()->resource(function (FieldSet $set) {
            $fieldType = $set->model()->type;
            if($set->model()->is_translatable) {
                $set->$fieldType('value')->translatable();
            } else {
                $set->$fieldType('non_translatable_value');
            }
        });
    }
    /**
     * @return \Laradium\Laradium\Base\Table
     */
    public function table()
    {
        return laradium()->table(function (ColumnSet $column) {
            $column->add('group');
            $column->add('name');
            $column->add('is_translatable');
            $column->add('value')->modify(function ($item) {
                if($item->is_translatable) {
                    return view('laradium::admin.resource._partials.translation', compact('item'));
                } else {
                    return $item->non_translatable_value ? e($item->non_translatable_value) : 'N/A';
                }
            });
        })->actions(['edit'])->relations(['translations']);
    }
}