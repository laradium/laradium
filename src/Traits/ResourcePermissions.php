<?php

namespace Laradium\Laradium\Traits;

use Illuminate\Support\Str;

trait ResourcePermissions
{
    /**
     * ResourcePermissions constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->authorizePermissionsFor($this->resource);
    }

    /**
     * Authorize a resource action based on the incoming request.
     *
     * @param  string $model
     * @param  string|null $parameter
     * @param  array $options
     * @return void
     */
    public function authorizePermissionsFor($model, $parameter = null, array $options = []): void
    {
        $parameter = $parameter ?: $this->getParameter($model);

        $middleware = [];

        foreach ($this->permissionsMap() as $method => $permissions) {
            if (is_array($permissions)) {
                foreach ($permissions as $permission) {
                    $middleware["permission:{$permission} {$parameter}"][] = $method;
                }
            } else {
                $middleware["permission:{$permissions} {$parameter}"][] = $method;
            }
        }

        foreach ($middleware as $middlewareName => $methods) {
            $this->middleware($middlewareName, $options)->only($methods);
        }
    }

    /**
     * Get the map of resource methods for permissions
     *
     * @return array
     */
    protected function permissionsMap(): array
    {
        return [
            'index'     => 'view',
            'show'      => 'show',
            'create'    => 'create',
            'store'     => 'create',
            'edit'      => 'update',
            'update'    => 'update',
            'destroy'   => 'delete',
            'getForm'   => ['create', 'update'],
            'dataTable' => 'view',
            'editable'  => 'update',
            'toggle'    => 'update'
        ];
    }

    /**
     * @param $model
     * @return string
     */
    protected function getParameter($model)
    {
        $model = class_basename($model);
        $model = trim(preg_replace('/([A-Z])/', ' $1', $model));
        $model = strtolower(Str::plural($model));

        return $model;
    }
}