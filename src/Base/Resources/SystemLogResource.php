<?php

namespace Laradium\Laradium\Base\Resources;

use Laradium\Laradium\Base\AbstractResource;
use Laradium\Laradium\Base\ColumnSet;
use Laradium\Laradium\Base\FieldSet;
use Laradium\Laradium\Models\Setting;
use Laradium\Laradium\Models\SystemLog;

class SystemLogResource extends AbstractResource
{
    /**
     * @var string
     */
    protected $resource = SystemLog::class;

    /**
     * @return \Laradium\Laradium\Base\Resource
     */
    protected function resource()
    {
        return laradium()->resource(function () {
        });
    }

    public function index()
    {
        return response()->json(['wot' => 'meit']);
    }
}
