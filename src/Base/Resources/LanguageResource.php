<?php
namespace Laradium\Laradium\Base\Resources;
use Laradium\Laradium\Models\Language;
use Laradium\Laradium\Base\AbstractResource;
use Laradium\Laradium\Base\FieldSet;
use Laradium\Laradium\Base\ColumnSet;
Class LanguageResource extends AbstractResource
{
    /**
     * @var string
     */
    protected $resource = Language::class;
    /**
     * @return \Laradium\Laradium\Base\Resource
     */
    public function resource()
    {
        $this->registerEvent('afterSave', function () {
            cache()->forget('languages');
        });
        return laradium()->resource(function (FieldSet $set) {
            $set->text('iso_code')->rules('required');
            $set->text('title')->rules('required');
            $set->text('title_localized')->rules('required');
            $set->boolean('is_fallback');
            $set->boolean('is_visible');
            $set->file('icon');
        });
    }
    /**
     * @return \Laradium\Laradium\Base\Table
     */
    public function table()
    {
        return laradium()->table(function (ColumnSet $column) {
            $column->add('iso_code');
            $column->add('title');
            $column->add('title_localized');
            $column->add('is_visible');
            $column->add('is_fallback');
        });
    }
}