<?php

namespace Laradium\Laradium\Traits;

use Illuminate\Http\Request;
use Laradium\Laradium\Base\Form;
use Yajra\DataTables\Facades\DataTables;

trait Datatable
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
                ->getBaseResource($model)
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

    /**
     * @return mixed
     */
    public function dataTable()
    {
        $resource = $this;
        $table = $this->table();
        $slug = $this->getBaseResource()->getSlug();

        if (count($table->getRelations())) {
            $model = $this->model->with($table->getRelations())->select('*');
        } else {
            $model = $this->model->select('*');
        }

        if ($table->getTabs()) {
            foreach ($table->getTabs() as $key => $tabs) {
                if (request()->has($key) && request()->get($key) !== 'all') {
                    $value = request()->get($key) === 'null' ? null : request()->get($key);
                    $model = $model->where($key, $value);
                }
            }
        }

        if ($where = $this->resource()->getWhere()) {
            $model = $model->where($where);
        }

        if ($table->getWhere()) {
            $model = $model->where($table->getWhere());
        }

        $dataTable = DataTables::of($model);

        $columns = $table->columns();
        $rawColumns = ['action'];

        // Editable columns
        $editableColumnNames = [];
        $editableColumns = $columns->where('editable', true)->where('translatable', false);
        foreach ($editableColumns as $column) {
            $dataTable->editColumn($column['column_parsed'], function ($item) use ($column, $slug) {
                return view('laradium::admin.resource._partials.editable', compact('item', 'column', 'slug'))->render();
            });

            $editableColumnNames[] = $column['column_parsed'];
        }

        // Translatable columns
        foreach ($columns->where('translatable', true)->where('editable', false) as $column) {
            $dataTable->addColumn($column['column_parsed'], function ($item) use ($column) {
                return view('laradium::admin.resource._partials.translation', compact('item', 'column'))->render();
            });

            $rawColumns = array_merge($rawColumns, [$column['column_parsed']]);
        }

        // Editable & translatable columns
        foreach ($columns->where('translatable', true)->where('editable', true) as $column) {
            $dataTable->addColumn($column['column_parsed'], function ($item) use ($column, $slug) {
                return view('laradium::admin.resource._partials.translation_editable',
                    compact('item', 'column', 'slug'))->render();
            });

            $editableColumnNames[] = $column['column_parsed'];

            $rawColumns = array_merge($rawColumns, [$column['column_parsed']]);
        }

        // Modified columns
        foreach ($columns->where('modify', '!=', null) as $column) {
            $dataTable->editColumn($column['column_parsed'], $column['modify']);

            // If column is modified AND has editable flag, we need to re-apply it
            if (in_array($column['column_parsed'], $editableColumnNames)) {
                $dataTable->editColumn($column['column_parsed'], function ($item) use ($column, $slug) {
                    $value = $column['modify']($item);
                    if (is_array($value)) {
                        $type = $value['type'];
                        $translatable = $value['translatable'] ?? false;

                        if (isset($value['column'])) {
                            $column['column_parsed'] = $value['column'];
                        }

                        $value = $value['value'];
                    }

                    if (isset($type) && $type !== 'text' || !isset($type)) {
                        return $value;
                    }

                    if (isset($translatable) && $translatable) {
                        return view('laradium::admin.resource._partials.translation_editable',
                            compact('item', 'column', 'slug'))->render();
                    }

                    return view('laradium::admin.resource._partials.editable',
                        compact('item', 'column', 'slug'))->render();
                });
            }

            $rawColumns = array_merge($rawColumns, [$column['column_parsed']]);
        }

        $dataTable->addColumn('action', function ($item) use ($resource, $slug) {
            return view('laradium::admin.resource._partials.action', compact('item', 'resource', 'slug'))->render();
        });

        if ($editableColumns->count()) {
            $rawColumns = array_merge($rawColumns, $editableColumns->pluck('column')->toArray());
        }

        $dataTable->rawColumns($rawColumns);

        if ($table->getSearch()) {
            $dataTable->filter($table->getSearch());
        } else {
            $dataTable->filter(function ($query) use ($columns, $table) {
                if (request()->has('search') && isset(request()->input('search')['value']) && !empty(request()->input('search')['value'])) {
                    $searchTerm = request()->input('search')['value'];

                    if ($table->getTabs()) {
                        $tab = array_keys($table->getTabs())[0]; // Only will work for one level tabs

                        if (request()->input($tab) !== 'all') {
                            $query->where($tab, request()->input($tab));
                        }

                        $query->where(function ($query) use ($columns, $searchTerm) {
                            foreach ($columns as $i => $column) {
                                if ($column['not_searchable']) {
                                    continue;
                                }

                                if ($column['translatable']) {
                                    if ($i === 0) {
                                        $query->whereTranslationLike($column['column'], '%' . $searchTerm . '%');
                                    } else {
                                        $query->orWhereTranslationLike($column['column'], '%' . $searchTerm . '%');
                                    }
                                } else {
                                    if ($i === 0) {
                                        $query->where($column['column'], 'LIKE', '%' . $searchTerm . '%');
                                    } else {
                                        $query->orWhere($column['column'], 'LIKE', '%' . $searchTerm . '%');
                                    }
                                }
                            }
                        });
                    } else {
                        foreach ($columns as $i => $column) {
                            if ($column['not_searchable']) {
                                continue;
                            }

                            if ($column['translatable']) {
                                if ($i === 0) {
                                    $query->whereTranslationLike($column['column'], '%' . $searchTerm . '%');
                                } else {
                                    $query->orWhereTranslationLike($column['column'], '%' . $searchTerm . '%');
                                }
                            } else {
                                if ($i === 0) {
                                    $query->where($column['column'], 'LIKE', '%' . $searchTerm . '%');
                                } else {
                                    $query->orWhere($column['column'], 'LIKE', '%' . $searchTerm . '%');
                                }
                            }
                        }
                    }
                }
            });
        }

        if (!request()->get('order')) {
            $dataTable->order(function ($query) use ($table) {
                $orderBy = $table->getOrderBy();

                $query->orderBy($orderBy['column'], $orderBy['direction']);
            });
        }

        return $dataTable->make(true);
    }
}