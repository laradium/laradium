<?php
namespace Laradium\Laradium\Base\Resources;
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
        return laradium()->resource(function (FieldSet $set) {
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
     * @return \Laradium\Laradium\Base\Table
     */
    public function table()
    {
        return laradium()->table(function (ColumnSet $column) {
            $column->add('key');
            $column->add('is_active')->modify(function ($item) {
                return $item->is_active ? 'Yes' : 'No';
            });
            $column->add('name')->translatable();
        })->relations(['translations']);
    }
}