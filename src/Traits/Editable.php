<?php

namespace Laradium\Laradium\Traits;

use Illuminate\Http\Request;
use Laradium\Laradium\Base\Form;
use Yajra\DataTables\Facades\DataTables;

trait Editable
{

    /**
     * @param Request $request
     * @return array
     */
    public function editable(Request $request, $locale = null)
    {
        $model = $this->model;
        $resource = $this->resource();
        $form = (new Form(
            $this
                ->getResource($model)
                ->make($resource->closure())
                ->build())
        )->build();

        $this->fireEvent('beforeSave', $request);

        $validationRules = $form->getValidationRules();
        $validationRules = array_only($validationRules, $request->get('name'));
        $validationRules['value'] = $validationRules[$request->get('name')] ?? '';
        $request->validate($validationRules);

        $model = $model->where('id', $request->get('pk'))->first();

        if (!$model) {
            return [
                'state' => 'error'
            ];
        }

        if ($locale && in_array($locale, translate()->languages()->pluck('iso_code')->toArray())) {
            $translation = $model->translations()->where('locale', $locale)->firstOrCreate([
                'locale' => $locale
            ]);

            if ($translation) {
                $translation->update([$request->get('name') => $request->get('value')]);
            }
        }

        if (!$locale) {
            $model->update([$request->get('name') => $request->get('value')]);
        }

        $this->fireEvent('afterSave', $request);

        return [
            'state' => 'success'
        ];
    }
}