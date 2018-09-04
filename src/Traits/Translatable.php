<?php

namespace Laradium\Laradium\Traits;

trait Translatable
{

    protected $translatable = false;
    protected $locale;

    public function translatable()
    {
        $this->translatable = true;

        return $this;
    }

    public function isTranslatable()
    {
        return $this->translatable;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    public function getLocale()
    {
        return $this->locale;
    }
}