<?php

namespace Laradium\Laradium\Base;

use File;
use Illuminate\Http\Request;
use Laradium\Laradium\Content\Base\Resources\PageResource;
use Laradium\Laradium\PassThroughs\Resource\Import;
use Laradium\Laradium\Services\Asset\AssetManager;
use Laradium\Laradium\Services\Layout;
use Laradium\Laradium\Traits\Crud;
use Laradium\Laradium\Traits\CrudEvent;
use Laradium\Laradium\Traits\Editable;

abstract class AbstractResource
{

    use Crud, CrudEvent, Editable;

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
     * @var bool
     */
    protected $isShared = false;

    /**
     * @var array
     */
    protected $actions = [
        'create',
        'edit',
        'delete'
    ];

    /**
     * @var array
     */
    protected $defaultViews = [
        'index'  => 'laradium::admin.resource.index',
        'create' => 'laradium::admin.resource.create',
        'edit'   => 'laradium::admin.resource.edit'
    ];

    /**
     * @var array
     */
    protected $views = [];

    /**
     * @var array
     */
    protected $customRoutes = [];

    /**
     * @var
     */
    private $baseResource;

    /**
     * @var array
     */
    private $middleware = [];

    /**
     * @var Layout
     */
    protected $layout;

    /**
     * AbstractResource constructor.
     */
    public function __construct()
    {
        if (class_exists($this->resource)) {
            $this->model(new $this->resource);
        }
        $this->layout = new Layout;

        $this->events = collect([]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view($this->layout->getView('index'), [
            'table'    => $this->table()->resource($this)->model($this->getModel()),
            'resource' => $this,
            'layout'   => $this->layout
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view($this->getView('create'), [
            'form'           => $this->getForm(),
            'resource'       => $this,
            'js'             => $this->resource()->getJs(),
            'css'            => $this->resource()->getCss(),
            'jsBeforeSource' => $this->resource()->getJsBeforeSource(),
            'layout'         => $this->layout
        ]);
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

        return view($this->getView('edit'), [
            'form'           => $this->getForm(),
            'resource'       => $this,
            'js'             => $this->resource()->getJs(),
            'css'            => $this->resource()->getCss(),
            'jsBeforeSource' => $this->resource()->getJsBeforeSource(),
            'layout'         => $this->layout
        ]);
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
     * @return array
     */
    public function dataTable()
    {
        return $this->table()->model($this->getModel())->resource($this)->data();
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
        )->abstractResource($this)->build();

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
     * @param $name
     * @return string
     */
    public function getView($name): string
    {
        if (!isset($this->views[$name])) {
            return $this->defaultViews[$name];
        }

        return $this->views[$name];
    }

    /**
     * @return array
     */
    public function getCustomRoutes()
    {
        return $this->customRoutes;
    }

    /**
     * @return string
     */
    public function resourceName()
    {
        return $this->resource;
    }

    /**
     * @return bool
     */
    public function isShared()
    {
        return $this->isShared;
    }

    /**
     * @return array
     */
    public function getResourceMiddleware()
    {
        if ($this->isShared()) {
            return array_merge(['web'], $this->middleware);
        }

        return array_merge(['web', 'laradium'], $this->middleware);
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