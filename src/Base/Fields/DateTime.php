<?php

namespace Laradium\Laradium\Base\Fields;

use Carbon\Carbon;
use Laradium\Laradium\Base\Field;

class DateTime extends Field
{
    /**
     * @param $value
     * @return Carbon
     */
    public function override($value)
    {
        try {
            $value = Carbon::parse($value);
        } catch (\Exception $e) {
        }

        return $value;
    }

    /**
     * @param $closure
     * @return $this
     */
    public function modify()
    {
        return $this->model()->starts_at ? $this->model()->starts_at->format('Y-m-d H:i') : '';
    }
}