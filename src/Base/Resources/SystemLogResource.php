<?php

namespace Laradium\Laradium\Base\Resources;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Laradium\Laradium\Base\AbstractResource;
use Laradium\Laradium\Base\ColumnSet;
use Laradium\Laradium\Base\Resource;
use Laradium\Laradium\Models\SystemLog;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SystemLogResource extends AbstractResource
{
    /**
     * @var string
     */
    protected $resource = SystemLog::class;

    /**
     * @var string
     */
    protected $name = 'System';

    /**
     * @var array
     */
    protected $customRoutes = [
        'phpInfo'  => [
            'method' => 'GET',
        ],
        'download' => [
            'method' => 'GET',
            'params' => '{logFile}'
        ],
        'data'     => [
            'method' => 'GET',
            'params' => '{systemLog}'
        ]
    ];

    /**
     * @var array
     */
    protected $actions = [
        'delete'
    ];

    /**
     * @return Resource
     */
    protected function resource()
    {
        return laradium()->resource(function () {
        });
    }

    /**
     * @return mixed
     */
    protected function table()
    {
        return laradium()->table(function (ColumnSet $column) {
            $column->add('id', 'ID');

            if (config('laradium-system.columns.type')) {
                $column->add('type', 'Type')->modify(function (SystemLog $item) {
                    return view('laradium::admin.system-logs.tds.type', [
                        'item' => $item
                    ])->render();
                });
            }

            $column->add('user_id', 'User')->modify(function (SystemLog $item) {
                if (!$item->user) {
                    return '-';
                }

                if (isset($item->user->full_name)) {
                    return $item->user->full_name;
                }

                if (isset($item->user->first_name)) {
                    return $item->user->first_name . ' ' . $item->user->last_name;
                }

                return $item->user->name;
            })->notSearchable()->notSortable();

            $column->add('method', 'Method');
            $column->add('message', 'Message');
            $column->add('url', 'URL');

            if (config('laradium-system.columns.ip')) {
                $column->add('ip', 'IP');
            }

            if (config('laradium-system.columns.browser')) {
                $column->add('browser', 'Browser');
            }

            if (config('laradium-system.columns.platform')) {
                $column->add('platform', 'Platform');
            }

            foreach (config('laradium-system.custom_columns') as $key => $title) {
                $column->add($key, $title);
            }

            $column->add('action', 'Actions')->modify(function (SystemLog $item) {
                return view('laradium::admin.system-logs.tds.actions', [
                    'item'     => $item,
                    'resource' => $this
                ])->render();
            });
        })->js([
            asset('laradium/admin/assets/plugins/json-viewer/jquery.json-viewer.js')
        ])->css([
            asset('laradium/admin/assets/plugins/json-viewer/jquery.json-viewer.css')
        ]);
    }

    /**
     * @return Factory|View
     */
    public function index()
    {
        $logFiles = array_diff(scandir(storage_path('logs'), SCANDIR_SORT_DESCENDING), [
            '..', '.', '.gitignore', '.gitkeep'
        ]);

        return view('laradium::admin.system-logs.index', [
            'table'      => $this->table()->resource($this)->model($this->getModel()),
            'resource'   => $this,
            'layout'     => $this->layout,
            'systemInfo' => laradium()->system()->info(),
            'logFiles'   => $logFiles
        ]);
    }

    public function phpInfo(): void
    {
        if (config('laradium-system.php-info')) {
            phpinfo();
        }
    }

    /**
     * @param $logFile
     * @return BinaryFileResponse
     */
    public function download($logFile): BinaryFileResponse
    {
        return response()->download(storage_path('logs/' . $logFile));
    }

    /**
     * @param SystemLog $systemLog
     * @return JsonResponse
     */
    public function data(SystemLog $systemLog): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $systemLog->data
        ]);
    }
}
