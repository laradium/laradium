<?php

namespace Laradium\Laradium\Base;

use File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Laradium\Laradium\Content\Base\Resources\PageResource;
use Laradium\Laradium\Interfaces\ResourceFilterInterface;
use Laradium\Laradium\PassThroughs\Resource\Import;
use Laradium\Laradium\Services\Layout;
use Laradium\Laradium\Traits\Crud;
use Laradium\Laradium\Traits\CrudEvent;
use Laradium\Laradium\Traits\Editable;

abstract class AbstractResource extends Controller
{

    use Crud, CrudEvent, Editable, AuthorizesRequests, DispatchesJobs, ValidatesRequests;

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
     * AbstractResource constructor.
     */
    public function __construct()
    {
        $this->baseResource = $this
            ->resource()
            ->model($this->getModel())
            ->build(
                $this->slug,
                $this->name,
                $this->prefix,
                $this->isShared,
                $this->actions,
                $this->views,
                $this->usesPermissions
            );
        

        $this->layout = new Layout;
        if ($this->getResource()->isShared() && $template = config('laradium.shared_resources_template')) {
            $this->layout->set($template);
        }

        $this->middleware($this->getResource()->isShared() ? ['web'] : ['web', 'laradium']);
    }
    
    public function getResource()
    {
        return $this->baseResource;
    }

    public function form($model = null)
    {
        $model = $model ?? new $this->resource;
        $resource = $this->getResource();

        return $resource->getForm($model)->returnUrl(route($this->getResource()->getRouteName('index')));
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
        $form = $this->form($this->getModel($id))
            ->url(route($this->getResource()->getRouteName('update'), $id));

        $resource = $this->getResource();

        return view($this->getResource()->getView('edit'), [
            'form'        => $form,
            'resource'    => $resource,
            'breadcrumbs' => $this->getResource()->getBreadcrumbs('edit'),
//            'js'             => $this->resource()->getJs(),
//            'css'            => $this->resource()->getCss(),
//            'jsBeforeSource' => $this->resource()->getJsBeforeSource(),
            'layout'      => $this->layout,
            'title'       => 'Edit ' . $resource->getName()
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     */
    public function update(Request $request, $id)
    {
        return $this
            ->form($this->getModel($id))
            ->update($request);
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
     * @return Model
     */
    public function getModel($id = null): Model
    {
        $model = $this->getModelInstance($id);

        if ($this instanceof ResourceFilterInterface) {
            $model = $this->filter($model)->getModel();
        }

        return $model;
    }

    public function getModelInstance($id)
    {
        if ($id) {
            return $this->resource::findOrFail($id);
        }

        return new $this->resource;
    }

    /**
     * @return Import
     */
    public function importHelper()
    {
        return new Import($this);
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