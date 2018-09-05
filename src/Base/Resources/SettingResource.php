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
        $this->registerEvent('afterSave', function () {
            setting()->clear_cache();
        });

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
        $table = laradium()->table(function (ColumnSet $column) {

            $column->add('name')->modify(function($row){
                return ( $row->is_translatable ? $this->translatableIcon() : '' ) . $row->name;
            });

            $column->add('value')->modify(function ($item) {
                return $this->modifyValueColumn($item);
            })->editable();

        })->dataTable(false)
            ->actions(['edit'])
            ->relations(['translations']);

        $table->tabs([
            'group' => Setting::select('group')->groupBy('group')->pluck('group', 'group')
        ]);

        return $table;

    }


    /**
     * @param $item
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string|void
     */
    public function modifyValueColumn($item)
    {
        //we do not want to display textarea content in table
        if( $item->type == 'textarea' ){
            return;
        }

        if( $item->is_translatable ) {
            return view('laradium::admin.resource._partials.translation', compact('item'));
        }

        return $item->non_translatable_value ? e($item->non_translatable_value) : '<span style="font-size:80%">- empty -</span>';
    }

    /**
     * @return string
     */
    public function translatableIcon()
    {
        return '<span data-toggle="tooltip" data-placement="top" title="" data-original-title="Value is translatable"><i class="fa fa-language"></i></span> ';
    }

}