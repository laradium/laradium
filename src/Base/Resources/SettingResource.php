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
     * @var array
     */
    protected $actions = ['edit'];

    /**
     * @return \Laradium\Laradium\Base\Resource
     */
    public function resource()
    {
        $this->registerEvent('afterSave', function () {
            setting()->clearCache();
        });

        return laradium()->resource(function (FieldSet $set) {
            $fieldType = $set->model()->type;

            if ($set->model()->is_translatable) {
                $set->$fieldType('value')->translatable();
            } else if ($fieldType === 'file') {
                $set->$fieldType('file');
            } else {
                $set->$fieldType('non_translatable_value')->label('Value');
            }
        });
    }

    /**
     * @return \Laradium\Laradium\Base\Table
     */
    public function table()
    {
        return laradium()->table(function (ColumnSet $column) {

            $column->add('name')->modify(function ($row) {
                return ($row->is_translatable ? $this->translatableIcon() : '') . $row->name;
            });

            $column->add('value')->modify(function ($item) {
                return $this->modifyValueColumn($item);
            })->editable();

        })->dataTable(false)
            ->relations(['translations'])
            ->tabs([
                'group' => Setting::select('group')->groupBy('group')->get()->mapWithKeys(function ($setting) {
                    return [
                        $setting->group => ucfirst(str_replace('-', ' ', $setting->group))
                    ];
                })->all()
            ])
            ->search(function ($query) {
                if (request()->has('search') && isset(request()->input('search')['value']) && !empty(request()->input('search')['value'])) {
                    $searchTerm = request()->input('search')['value'];

                    $query->where('group', request()->input('group'))
                        ->where(function ($query) use ($searchTerm) {
                            $query->whereTranslationLike('value', '%' . $searchTerm . '%')
                                ->orWhere('non_translatable_value', 'LIKE', '%' . $searchTerm . '%')
                                ->orWhere('name', 'LIKE', '%' . $searchTerm . '%');
                        });
                }
            });
    }

    /**
     * @param $item
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function modifyValueColumn($item)
    {
        //we do not want to display textarea content in table
        if ($item->type === 'textarea') {
            return '<span style="font-size:80%">- too long to show -</span>';
        }

        if ($item->type === 'file') {
            if ($item->file->exists()) {
                return $item->file->url();
            } else {
                return '<span style="font-size:80%">- empty -</span>';
            }
        }

        if ($item->is_translatable) {
            $column = [
                'column_parsed' => 'value'
            ];

            return view('laradium::admin.resource._partials.translation', compact('item', 'column'));
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