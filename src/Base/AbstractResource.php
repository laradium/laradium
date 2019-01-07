<?php

namespace Laradium\Laradium\Base;

use App\Models\User;
use File;
use Illuminate\Http\Request;
use Laradium\Laradium\Content\Base\Resources\PageResource;
use Laradium\Laradium\PassThroughs\Resource\Import;
use Laradium\Laradium\Traits\Crud;
use Laradium\Laradium\Traits\CrudEvent;
use Laradium\Laradium\Traits\Datatable;

abstract class AbstractResource
{

    use Crud, CrudEvent, Datatable;

    /**
     * @var
     */
    protected $model;

    /**
     * @var string
     */
    protected $resource;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var array
     */
    protected $actions = [
        'create',
        'edit',
        'delete'
    ];

    /**
     * @var
     */
    private $baseResource;

    /**
     * AbstractResource constructor.
     */
    public function __construct()
    {
        if (class_exists($this->resource)) {
            $this->model(new $this->resource);
        }

        $this->events = collect([]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $table = $this->table()->setModel($this->getModel());
        $resource = $this;

        return view('laradium::admin.resource.index', compact('table', 'resource'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $form = $this->getForm();
        $resource = $this;

        return view('laradium::admin.resource.create', compact('form', 'resource'));
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \ReflectionException
     */
    public function store(Request $request)
    {
        $form = $this->getForm();
        $validationRequest = $this->prepareRequest($request);

        $this->fireEvent('beforeSave', $request);

        $validationRules = $form->getValidationRules();
        $validationRequest->validate($validationRules);

        $model = $this->saveData($request->all(), $this->getModel());

        $form->model($model);
        $this->model($model);

        $this->fireEvent(['afterSave', 'afterCreate'], $request);

        if ($request->ajax()) {
            return [
                'success'  => 'Resource successfully created',
                'redirect' => $form->getAction('edit')
            ];
        }

        return back()->withSuccess('Resource successfully created!');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $model = $this->model;
        if ($where = $this->resource()->getWhere()) {
            $model = $model->where($where);
        }

        $this->model($model->findOrFail($id));
        $form = $this->getForm();
        $resource = $this;

        return view('laradium::admin.resource.edit', compact('form', 'resource'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \ReflectionException
     */
    public function update(Request $request, $id)
    {
        $model = $this->model;
        if ($where = $this->resource()->getWhere()) {
            $model = $model->where($where);
        }

        $this->model($model->findOrFail($id));

        $form = $this->getForm();
        $validationRequest = $this->prepareRequest($request);

        $this->fireEvent('beforeSave', $request);

        $validationRules = $form->getValidationRules();
        $validationRequest->validate($validationRules);

        $this->saveData($request->all(), $this->getModel());

        $this->fireEvent('afterSave', $request);

        if ($request->ajax()) {
            return [
                'success'  => 'Resource successfully updated!',
                'redirect' => $form->getAction('edit')
            ];
        }

        return back()->withSuccess('Resource successfully updated!');
    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     */
    public function destroy(Request $request, $id)
    {
        $model = $this->model;
        if ($where = $this->resource()->getWhere()) {
            $model = $model->where($where);
        }

        $model = $model->findOrFail($id);
        $model->delete();

        $this->fireEvent('afterDelete', $request);

        if ($request->ajax()) {
            return [
                'state' => 'success'
            ];
        }

        return back()->withSuccess('Resource successfully deleted!');
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Request $request, $id)
    {
        $column = $request->get('column', null);

        abort_unless($column, 400);

        $model = $this->getModel();
        if ($where = $this->resource()->getWhere()) {
            $model = $model->where($where);
        }

        $model = $model->findOrFail($id);

        $model->$column = !$model->$column;
        $model->save();

        return response()->json([
            'state' => 'success'
        ]);
    }

    /**
     * @param null $model
     * @return Resource
     */
    public function getBaseResource($model = null)
    {
        $model = $model ?? $this->getModel();

        return (new Resource)->model($model)
            ->name($this->name)
            ->slug($this->slug);
    }

    /**
     * @return Form
     */
    private function getForm()
    {
        $form = (new Form(
            $this
                ->getBaseResource($this->getModel())
                ->make($this->resource()->closure())
                ->build())
        )->build();

        return $form;
    }

    /**
     * @param $value
     * @return $this
     */
    public function model($value)
    {
        $this->model = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return Import
     */
    public function importHelper()
    {
        return new Import($this);
    }

    /**
     * @param $value
     * @return bool
     */
    public function hasAction($value)
    {
        return in_array($value, $this->actions);
    }

    /**
     * @return array
     */
    public function getActions()
    {
        $actions = collect($this->actions)->push('index'); // Index is allowed by default

        if ($this instanceof PageResource) {
            $actions->push('create');
        }

        $allActions = collect([
            'index'  => 'index',
            'create' => [
                'create',
                'store'
            ],
            'edit'   => [
                'edit',
                'update'
            ],
            'show'   => 'show',
            'delete' => 'destroy'
        ]);

        $availableActions = $actions->diffAssoc($allActions);

        return $allActions->only($availableActions)->flatten()->all();
    }

    /**
     * @param $action
     * @return array|mixed
     */
    public function getBreadcrumbs($action)
    {
        $form = $this->getForm();

        $breadcrumbs = [
            'index'  => [
                [
                    'name' => $this->getBaseResource()->getName(),
                    'url'  => $form->getAction('index')
                ]
            ],
            'create' => [
                [
                    'name' => $this->getBaseResource()->getName(),
                    'url'  => $form->getAction('index')
                ],
                [
                    'name' => 'Create',
                    'url'  => $form->getAction('create')
                ]
            ],
            'edit'   => [
                [
                    'name' => $this->getBaseResource()->getName(),
                    'url'  => $form->getAction('index')
                ],
                [
                    'name' => 'Edit',
                    'url'  => $form->getAction('edit')
                ]
            ],
        ];

        return $breadcrumbs[$action] ?? [];
    }

    /**
     * @return string
     */
    public function resourceName()
    {
        return $this->resource;
    }

    /**
     * @return \Laradium\Laradium\Base\Resource
     */
    abstract protected function resource();

    /**
     * @return Table
     */
//    abstract protected function table();
}