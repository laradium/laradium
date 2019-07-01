<?php

namespace Laradium\Laradium\Base\Resources;

use DB;
use Illuminate\Http\Request;
use Laradium\Laradium\Base\AbstractResource;
use Laradium\Laradium\Base\ColumnSet;
use Laradium\Laradium\Base\FieldSet;
use Laradium\Laradium\Base\Resource;
use Laradium\Laradium\Base\Table;
use Laradium\Laradium\Exports\TranslationExport;
use Laradium\Laradium\Imports\TranslationImport;
use Laradium\Laradium\Models\Language;
use Laradium\Laradium\Models\Translation;
use Maatwebsite\Excel\Facades\Excel;

Class TranslationResource extends AbstractResource
{

    /**
     * @var string
     */
    protected $resource = Translation::class;

    /**
     * @return Resource
     */
    public function resource()
    {
        $this->event(['afterSave', 'afterDelete'], function () {
            cache()->forget('translations');
        });

        return laradium()->resource(function (FieldSet $set) {
            $set->select('locale')->options($this->localeList());
            $set->text('group')->rules('required');
            $set->text('key')->rules('required');
            $set->text('value')->rules('required');
        });
    }

    /**
     * @return Table
     */
    public function table()
    {
        return laradium()->table(function (ColumnSet $column) {
            $column->add('locale');
            $column->add('group');
            $column->add('key');
            $column->add('value')->editable();
        });
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function import(Request $request)
    {
        $request->validate(['import' => 'required']);

        try {
            (new TranslationImport)->import($request->file('import'), null, \Maatwebsite\Excel\Excel::XLSX);

            cache()->forget('translations');
        } catch (\Exception $e) {
            logger()->error($e);

            return back()->withError('Something went wrong, please try again!');
        }

        return back()->withSuccess('Translations successfully imported!');
    }

    /**
     * @return mixed
     */
    public function export()
    {
        try {
            return Excel::download(new TranslationExport, 'translations.xlsx');
        } catch (\Exception $e) {
            logger()->error($e);

            return back()->withError('Something went wrong, please try again!');
        }
    }

    /**
     * @return mixed
     */
    protected function localeList()
    {
        return Language::pluck('iso_code', 'iso_code')->toArray();
    }
}