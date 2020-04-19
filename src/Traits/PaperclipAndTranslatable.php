<?php

namespace Laradium\Laradium\Traits;

use Czim\FileHandling\Contracts\Storage\StorableFileFactoryInterface;
use Czim\Paperclip\Attachment\Attachment;

trait PaperclipAndTranslatable
{

    /**
     * @param $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        [$attribute, $locale] = $this->getAttributeAndLocale($key);

        if ($this->isTranslationAttribute($attribute)) {
            if ($this->getTranslation($locale) === null) {
                return $this->getAttributeValue($attribute);
            }
            // If the given $attribute has a mutator, we push it to $attributes and then call getAttributeValue
            // on it. This way, we can use Eloquent's checking for Mutation, type casting, and
            // Date fields.
            if ($this->hasGetMutator($attribute)) {
                $this->attributes[$attribute] = $this->getAttributeOrFallback($locale, $attribute);
                return $this->getAttributeValue($attribute);
            }
            return $this->getAttributeOrFallback($locale, $attribute);
        }

        if (array_key_exists($key, $this->attachedFiles)) {
            return $this->attachedFiles[$key];
        }

        return parent::getAttribute($key);
    }

    /**
     * @param $key
     * @param $value
     * @return $this|PaperclipAndTranslatable|void
     */
    public function setAttribute($key, $value)
    {
        if (array_key_exists($key, $this->attachedFiles)) {

            if ($value) {
                $attachedFile = $this->attachedFiles[$key];

                if ($value === Attachment::NULL_ATTACHMENT) {
                    $attachedFile->setToBeDeleted();

                    return;
                }

                /** @var StorableFileFactoryInterface $factory */
                $factory = app(StorableFileFactoryInterface::class);

                $attachedFile->setUploadedFile(
                    $factory->makeFromAny($value)
                );
            }

            $this->attachedUpdated = true;
            return;
        }

        [$attribute, $locale] = $this->getAttributeAndLocale($key);
        if ($this->isTranslationAttribute($attribute)) {
            $this->getTranslationOrNew($locale)->$attribute = $value;

            return $this;
        }

        return parent::setAttribute($key, $value);
    }
}