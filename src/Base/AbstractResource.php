<?php

namespace Laradium\Laradium\Base;

use App\Models\User;
use File;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laradium\Laradium\Content\Base\Resources\PageResource;
use Laradium\Laradium\Interfaces\ResourceFilterInterface;
use Laradium\Laradium\PassThroughs\Resource\Import;
use Laradium\Laradium\Services\Layout;
use Laradium\Laradium\Traits\Crud;
use Laradium\Laradium\Traits\CrudEvent;
use Laradium\Laradium\Traits\Editable;
use ReflectionException;

abstract class AbstractResource extends Controller
{

    use Crud, CrudEvent, Editable, AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var
     */
    protected $model;

    /**
     * @var bool
     */
    protected $withoutCard = false;

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
     * @var Layout
     */
    protected $layout;

    /**
     * @var bool
     */
    protected $usesPermissions = false;

    /**
     * @var InterfaceBuilder
     */
    protected $builder;

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
        if ($this->isShared() && $template = config('laradium.shared_resources_template')) {
            $this->layout->set($template);
        }

        $this->middleware($this->isShared() ? ['web'] : ['web', 'laradium']);

        $this->builder = new InterfaceBuilder;
    }

    /**
     * @return FormNew
     */
    protected function getForm(): FormNew
    {
        $model = $this->getModel();

        return (new FormNew('crud-form'))
            ->model($model)
            ->returnUrl($this->getAction())
            ->fields($this->resource()->closure());
    }

    /**
     * @param $url
     * @param string $method
     * @return InterfaceBuilder
     */
    protected function formBuilder(string $url, string $method = 'post'): InterfaceBuilder
    {
        return $this->builder->components(function (FieldSet $set) use ($url, $method) {
            $set->col(12)->fields(function (FieldSet $set) {
                $set->breadcrumbs($this->getBreadcrumbs('edit'));
            });

            $set->crud($this->getForm()->url($url)->method($method))
                ->withoutCard($this->withoutCard);
        });
    }

    /**
     * @return View
     */
    public function index()
    {
        $this->builder->components(function (FieldSet $set) {
            $set->col(12)->fields(function (FieldSet $set) {
                $set->breadcrumbs($this->getBreadcrumbs('edit'));

                $set->customContent(function () {
                    return view('laradium::admin._partials.import', [
                        'resource' => $this,
                    ])->render();
                });
            });

            $set->block(12)->fields(function (FieldSet $set) {
                $set->table($this->table()
                    ->url($this->getAction('data-table'))
                    ->toggleUrl($this->getAction('toggle'))
                    ->make(function (ColumnSet $column) {
                        $column->add('action')->modify(function ($item) {
                            return view('laradium::admin.table._partials.action', [
                                'resource' => $this,
                                'item'     => $item
                            ])->render();
                        })
                            ->notSortable()
                            ->notSearchable();
                    })
                );
            });
        });

        return view($this->layout->getView('index'), [
            'resource' => $this,
            'builder'  => $this->builder,
            'layout'   => $this->layout
        ]);
    }

    /**
     * @return Factory|View
     */
    public function create()
    {
        return view($this->getView('create'), [
            'resource' => $this,
            'layout'   => $this->layout,
            'builder'  => $this->formBuilder($this->getAction('store'))
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function store(Request $request): JsonResponse
    {
        return $this->getForm()->events($this->getEvents())->redirectTo(function($model) {
            return $this->getAction('edit', $model->id);
        })->store($request);
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

        if ($this instanceof ResourceFilterInterface) {
            $model = $this->filter($model);
        }

        $this->model($model = $model->findOrFail($id));

        return view($this->getView('edit'), [
            'resource' => $this,
            'layout'   => $this->layout,
            'builder'  => $this->formBuilder($this->getAction('update'), 'put')
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function update(Request $request, $id): JsonResponse
    {
        $model = $this->getModel();

        if ($where = $this->resource()->getWhere()) {
            $model = $model->where($where);
        }

        if ($this instanceof ResourceFilterInterface) {
            $model = $this->filter($model);
        }

        $this->model($model->findOrFail($id));

        return $this->getForm()->events($this->getEvents())->redirectTo(function($model) {
            return $this->getAction('edit', $model->id);
        })->update($request);
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

        if ($this instanceof ResourceFilterInterface) {
            $model = $this->filter($model);
        }

        $this->model($model = $model->findOrFail($id));

        $this->fireEvent('beforeDelete', $request);

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
     * @param string $action
     * @return string
     */
    public function getAction($action = 'index', $id = null): string
    {
        $id = $id ?? ($this->getModel() ? $this->getModel()->id : null);

        if ($action === 'create') {
            return $this->getUrl('create');
        } elseif ($action === 'edit') {
            return $this->getUrl($id . '/edit');
        } elseif ($action === 'store') {
            return $this->getUrl();
        } elseif ($action === 'update') {
            return $this->getUrl($id);
        } elseif ($action === 'data-table') {
            return $this->getUrl('data-table');
        } elseif ($action === 'toggle') {
            return $this->getUrl('toggle/' . $id);
        }


        return $this->getUrl();
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

        if ($this instanceof ResourceFilterInterface) {
            $model = $this->filter($model);
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
            'index'  => [
                'index',
                'data-table',
                'export',
                'toggle'
            ],
            'create' => [
                'create',
                'store',
                'import',
                'form'
            ],
            'edit'   => [
                'edit',
                'update',
                'editable',
                'form'
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
        $defaultBreadcrumbs = [];

        if (in_array('laradium', request()->route()->computedMiddleware)) {
            $defaultBreadcrumbs[] = [
                'name' => 'Admin',
                'url'  => url('/admin')
            ];
        }

        if ($this->getPrefix()) {
            $defaultBreadcrumbs[] = [
                'name' => ucfirst($this->getPrefix()),
                'url'  => url($this->getPrefix())
            ];
        }

        $breadcrumbs = [
            'index'  => [
                [
                    'name' => $name,
                    'url'  => $this->getAction()
                ]
            ],
            'create' => [
                [
                    'name' => $name,
                    'url'  => $this->getAction()
                ],
                [
                    'name' => 'Create',
                    'url'  => $this->getAction('create')
                ]
            ],
            'edit'   => [
                [
                    'name' => $name,
                    'url'  => $this->getAction()
                ],
                [
                    'name' => 'Edit',
                    'url'  => $this->getAction('edit')
                ]
            ],
        ];

        return array_merge($defaultBreadcrumbs, $breadcrumbs[$action] ?? []);
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
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $action
     * @return string
     */
    public function getPermission($action = 'view')
    {
        $model = class_basename($this->resource);
        $model = trim(preg_replace('/([A-Z])/', ' $1', $model));
        $model = strtolower(Str::plural($model));

        return $action . ' ' . $model;
    }

    /**
     * @return string
     */
    public function getGuard()
    {
        return $this->isShared() ? 'web' : 'admin';
    }

    /**
     * @param $action
     * @param null $user
     * @return bool
     */
    public function hasPermission($action, $user = null)
    {
        $guard = $this->getGuard();
        $user = $user ?? auth($guard)->user();

        if (!$user) {
            return false;
        }

        if (!method_exists($user, 'hasPermissionTo')) {
            return true;
        }

        if (!$this->usesPermissions) {
            return true;
        }

        return $user->hasPermissionTo($this->getPermission($action), $guard);
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