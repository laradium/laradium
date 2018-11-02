<?php

namespace Laradium\Laradium\PassThroughs\Resource;

use Laradium\Laradium\Base\AbstractResource;
use Laradium\Laradium\PassThroughs\PassThrough;

class Import extends PassThrough
{
    /**
     * AbstractResource instance.
     *
     * @var AbstractResource
     */
    private $resource;

    /**
     * Import constructor.
     *
     * @param AbstractResource $resource
     */
    public function __construct(AbstractResource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @return bool
     */
    public function inProgress(): bool
    {
        return is_file(storage_path('app/import/' . $this->resource->getModel()->getTable() . '-import.lock'));
    }

    /**
     * @return bool|string
     */
    public function status()
    {
        return file_get_contents(storage_path('app/import/' . $this->resource->getModel()->getTable() . '-import.lock'));
    }

}