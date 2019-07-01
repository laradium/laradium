<?php

namespace Laradium\Laradium\Base\Resources;

use Laradium\Laradium\Base\AbstractResource;
use Laradium\Laradium\Base\ColumnSet;
use Laradium\Laradium\Base\FieldSet;
use Laradium\Laradium\Models\Setting;

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
        $this->event('afterSave', function () {
            setting()->clearCache();
        });

        return laradium()->resource(function (FieldSet $set) {
            $fieldType = $set->getModel()->type;

            if ($set->getModel()->is_translatable) {
                $set->$fieldType($fieldType === 'file' ? 'file' : 'value')
                    ->translatable()
                    ->label($set->getModel()->name);
            } else if ($fieldType === 'file') {
                $set->$fieldType('file')->label($set->getModel()->name);
            } else {
                if ($fieldType) {
                    $set->$fieldType('non_translatable_value')->label($set->getModel()->name);
                }
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
                return ($row->is_translatable ? $this->translatableIcon() . ' ' : '') . $row->name;
            })->raw();

            $column->add('value')->modify(function ($item) {
                return $this->modifyValueColumn($item);
            })->notSortable()->new()->raw();

        })
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
     * @return string
     * @throws \Throwable
     */
    public function modifyValueColumn($item)
    {
        //we do not want to display textarea content in table
        if ($item->type === 'textarea') {
            return '- too long to show -';
        }

        if ($item->type === 'file') {

            if ($item->is_translatable) {
                $html = '';
                foreach ($item->translations as $translation) {
                    $html .= view('laradium::admin.table._partials.file', [
                        'item' => $translation,
                        'locale' => $translation->locale
                    ])->render();
                }

                return $html;
            }

            return view('laradium::admin.table._partials.file', ['item' => $item])->render();
        }

        if ($item->is_translatable) {
            $html = '';
            foreach ($item->translations as $translation) {
                $html .= '<li><b>' . strtoupper($translation->locale) . ': </b>' . $translation->value . '</li>';
            }

            return view('laradium::admin.resource._partials.translation_editable',
                [
                    'item' => $item,
                    'column' => ['column_parsed' => 'value'],
                    'slug' => 'settings',
                ])
                ->render();
        }

        return view('laradium::admin.table._partials.editable', [
            'item' => $item,
            'column' => ['column_parsed' => 'non_translatable_value'],
            'slug' => '/admin/settings',
        ])->render();
    }

    /**
     * @return string
     */
    public function translatableIcon(): string
    {
        return '<span data-toggle="tooltip" data-placement="top" title="" data-original-title="Value is translatable"><i class="fa fa-language"></i></span>';
    }

}
