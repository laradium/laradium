<?php

namespace Laradium\Laradium\Base\Fields;

use Carbon\Carbon;
use Laradium\Laradium\Base\Field;

class DateTime extends Field
{
    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        $data = parent::formattedResponse();

        $data['value'] = $this->getValue() ? Carbon::parse($this->getValue())->format('Y-m-d H:i') : '';

        return $data;
    }
}