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
        $this->registerEvent('afterSave', function () {
            cache()->forget(Menu::$cacheKey);
        });

        $resources = collect((new Laradium)->resources())->mapWithKeys(function ($r) {
            return [$r => (new $r)->getName()];
        })->toArray();
        $resources = array_merge(['' => '- Select -'], $resources);

        return laradium()->resource(function (FieldSet $set) use ($resources) {

            if (laradium()->belongsTo()->isEnabled()) {
                laradium()->belongsTo()->getSelect($set, $languages = true);
            }

            $set->boolean('is_active');
            $set->text('key')->rules('required|max:255');
            $set->text('name')->rules('required|max:255')->translatable();
            $set->tab('Items')->fields(function (FieldSet $set) use ($resources) {
                $set->hasMany('items')->fields(function (FieldSet $set) use ($resources) {
                    $set->select('target')->options([
                        '_self'  => 'Self',
                        '_blank' => 'Blank',
                    ])->rules('required');
                    $set->text('name')->rules('required|max:255')->translatable();
                    $set->text('url')->rules('max:255')->translatable();
                    $set->select('resource')->options($resources);
                })->sortable('sequence_no');
            });

            // TODO needs possibility to add attributes
        });
    }

    /**
     * @return \Laradium\Laradium\Base\Table
     */
    public function table()
    {
        $belongsToForeignKey = laradium()->belongsTo()->getForeignKey();
        $belongsToId = auth()->user()->{$belongsToForeignKey};

        $table = laradium()->table(function (ColumnSet $column) {
            $column->add('key');
            $column->add('is_active')->modify(function ($item) {
                return $item->is_active ? 'Yes' : 'No';
            });
            $column->add('name')->translatable();
        })->relations(['translations']);

        if ($belongsToId) {
            $table->where(function ($q) use ($belongsToId) {
                $q->where($belongsToForeignKey, $belongsToId);
            });
        } else {
            $table->tabs([
                $belongsToForeignKey => laradium()->belongsTo()->getOptions($global = true)
            ]);
        }

        return $table;
    }
}