<?php

namespace Laradium\Laradium\Base;

use File;
use Illuminate\Http\Request;
use Laradium\Laradium\Content\Base\Resources\PageResource;
use Laradium\Laradium\Interfaces\ResourceFilterInterface;
use Laradium\Laradium\PassThroughs\Resource\Import;
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
     * @var string
     */
    protected $prefix;

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

        $this->events = collect([]);
        $this->layout = new Layout;
        if ($template = config('laradium.shared_resources_template')) {
            $this->layout->set($template);
        }
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
        $model = $this->getModel();

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
        $model = $this->getModel();

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
        $model = $this->getModel();

        if ($where = $this->resource()->getWhere()) {
            $model = $model->where($where);
        }

        $this->model($model->findOrFail($id));
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
            ->slug($this->slug)
            ->prefix($this->prefix);
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
        $model = $this->model;

        if ($this instanceof ResourceFilterInterface) {
            $model = $this->filter($model)->getModel();
        }

        return $model;
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
        $baseResource = $this->getBaseResource();
        $name = $baseResource->getName();

        $breadcrumbs = [
            'index'  => [
                [
                    'name' => $name,
                    'url'  => $this->getAction('index')
                ]
            ],
            'create' => [
                [
                    'name' => $name,
                    'url'  => $this->getAction('index')
                ],
                [
                    'name' => 'Create',
                    'url'  => $this->getAction('create')
                ]
            ],
            'edit'   => [
                [
                    'name' => $name,
                    'url'  => $this->getAction('index')
                ],
                [
                    'name' => 'Edit',
                    'url'  => $this->getAction('edit')
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
     * @param string $action
     * @return string
     */
    public function getAction($action = 'index'): string
    {
        if ($action === 'create') {
            return $this->getUrl('create');
        } else if ($action === 'edit') {
            return $this->getUrl($this->getModel()->id . '/edit');
        } else if ($action === 'store') {
            return $this->getUrl();
        } else if ($action === 'update') {
            return $this->getUrl($this->model->id);
        }

        return $this->getUrl();
    }

    /**
     * @param string $value
     * @return string
     */
    private function getUrl($value = '')
    {
        if ($this->isShared()) {
            return url('/' . $this->getBaseResource()->getSlug() . '/' . $value);
        }

        return url('/admin/' . $this->getBaseResource()->getSlug() . '/' . $value);
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