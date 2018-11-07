<?php

namespace Laradium\Laradium\Traits;

trait Translatable
{

    /**
     * @var bool
     */
    private $translatable = false;

    /**
     * @return $this
     */
    public function translatable(): self
    {
        $this->translatable = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function isTranslatable(): bool
    {
        return $this->translatable;
    }

    /**
     * @return array
     */
    public function getTranslations(): array
    {
        $translations = [];

        if ($this->isTranslatable()) {
            $model = $this->getModel();
            $attributes = $this->getAttributes();
            unset($attributes[count($attributes) - 1]);
            foreach (translate()->languages() as $language) {
                $isoCode = $language->iso_code;
                $this->model(
                    $model->translateOrNew($isoCode)
                );
                $this->build(array_merge($attributes, ['translations', $isoCode]));
                $translations[] = [
                    'iso_code' => $isoCode,
                    'value'    => $this->getValue(),
                    'name'     => $this->getNameAttribute(),
                ];
            }

            $this->model($model);
        }

        return $translations;
    }
}