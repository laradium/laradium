<?php

namespace Laradium\Laradium\Base;

use Illuminate\Database\Eloquent\Model;

class Resource
{

    /**
     * @var Model
     */
    private $model;

    /**
     * @var \Closure
     */
    private $closure;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var
     */
    private $where;

    /**
     * @var array
     */
    private $defaultViews = [
        'index'  => 'laradium::admin.resource.index',
        'create' => 'laradium::admin.resource.create',
        'edit'   => 'laradium::admin.resource.edit'
    ];

    /**
     * @var array
     */
    private $action = [];

    /**
     * @var bool
     */
    private $isShared;

    /**
     * @var array
     */
    private $views = [];

    /**
     * @var array
     */
    private $customRoutes = [];

    /**
     * @var bool
     */
    protected $usesPermissions;

    /**
     * Resource constructor.
     */
    public function __construct()
    {
        $this->fieldSet = new FieldSet();
    }

    public function make(\Closure $closure)
    {
        $this->closure = $closure;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        if (!$this->slug && $this->name) {
            $this->slug = strtolower(str_replace(' ', '-', $this->name));
        }

        if (!$this->slug && !$this->name) {
            $this->slug = str_replace('_', '-', $this->model->getTable());
        }

        if ($this->prefix) {
            $this->slug = $this->prefix . '/' . $this->slug;
        }

        return $this->slug;
    }

    /**
     * @return string
     */
    public function getName()
    {
        if (!$this->name && !$this->slug) {
            return ucfirst(str_replace('_', ' ', $this->model->getTable()));
        }

        if (!$this->name && $this->slug) {
            return ucfirst(str_replace('-', ' ', $this->slug));
        }

        return ucfirst($this->name);
    }

    public function build($slug, $name, $prefix, bool $isShared, array $actions, array $views, bool $usesPermissions)
    {
        $this->slug = $slug;
        $this->name = $name;
        $this->prefix = $prefix;
        $this->actions = $actions;
        $this->views = $views;
        $this->usesPermissions = $usesPermissions;

        return $this;
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return FieldSet
     */
    public function fieldSet()
    {
        return $this->fieldSet;
    }

    public function getForm(Model $model)
    {
        return (new FormNew)
            ->model($model)
            ->fld($this->closure)
            ->build();
    }

    /**
     * @return mixed
     */
    public function closure()
    {
        return $this->closure;
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
     * @param string $action
     * @return string
     */
    public function getRouteName($action = 'index')
    {
        return 'admin.' . $this->getSlug() . '.' . $action;
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
    }/**
 * @param $action
 * @return array|mixed
 */
    public function getBreadcrumbs($action)
    {
        $name = $this->getName();
        $defaultBreadcrumbs = [];
        if (in_array('laradium', request()->route()->controllerMiddleware())) {
            $defaultBreadcrumbs[] = [
                'name' => 'Admin',
                'url'  => url('admin')
            ];
        }

        if($this->getPrefix()) {
            $defaultBreadcrumbs[] = [
                'name' => ucfirst($prefix),
                'url'  => url($prefix)
            ];
        }

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

        if(isset($breadcrumbs[$action])) {
            return array_merge($defaultBreadcrumbs, $breadcrumbs[$action]);
        }

        return [];
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
    }/**
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
     * @param string $action
     * @return string
     */
    public function getAction($action = 'index'): string
    {
        if ($action === 'create') {
            return $this->getUrl('create');
        } else {
            if ($action === 'edit') {
                return $this->getUrl($this->getModel()->id . '/edit');
            } else {
                if ($action === 'store') {
                    return $this->getUrl();
                } else {
                    if ($action === 'update') {
                        return $this->getUrl($this->model->id);
                    }
                }
            }
        }

        return $this->getUrl();
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
            return url('/' . $this->getSlug() . '/' . $value);
        }

        return url('/admin/' . $this->getSlug() . '/' . $value);
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function where(\Closure $closure)
    {
        $this->where = $closure;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getWhere()
    {
        return $this->where;
    }

    /**
     * @param array $assets
     * @return $this
     */
    public function css($assets = []): self
    {
        $this->css = $assets;

        return $this;
    }

    /**
     * @param array $assets
     * @return $this
     */
    public function js($assets = []): self
    {
        $this->js = $assets;

        return $this;
    }

    /**
     * @param array $assets
     * @return $this
     */
    public function jsBeforeSource($assets = []): self
    {
        $this->jsBeforeSource = $assets;

        return $this;
    }

    /**
     * @return array
     */
    public function getCss(): array
    {
        return $this->css;
    }

    /**
     * @return array
     */
    public function getJs(): array
    {
        return $this->js;
    }

    /**
     * @return array
     */
    public function getJsBeforeSource(): array
    {
        return $this->jsBeforeSource;
    }
}