<?php

namespace Laradium\Laradium\Traits;

trait Translatable
{

    private $translatable = false;
    private $localeColumn;

    public function translatable($localeColumn = 'locale')
    {
        $this->translatable = true;
        $this->localeColumn = $localeColumn;

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

    public function getLocaleColumn()
    {
        return $this->localeColumn;
    }

    public function getTranslations()
    {
        $translations = [];
        $model = $this->getModel();

        foreach (translate()->languages() as $language) {
            $isoCode = $language->iso_code;
            $this->model(
                $model->translateOrNew($isoCode)
            );
            $this->build(['translations', $isoCode]);
            $translations[] = [
                'iso_code' => $isoCode,
                'value'    => $this->getValue(),
                'name'     => $this->getNameAttribute(),
            ];
        }

        return $translations;
    }
}