<?php

namespace Laradium\Laradium\PassThroughs;

abstract class PassThrough
{
    /**
     * Magic getter.
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        $camelized = $this->camelize($name);

        return $this->$camelized();
    }

    /**
     * Camelize string.
     *
     * @param        $input
     * @param string $separator
     * @return mixed
     */
    private function camelize($input, $separator = '_')
    {
        return str_replace($separator, '', lcfirst(ucwords($input, $separator)));
    }
}
