<?php

namespace Laradium\Laradium\Base\Fields;

use Laradium\Laradium\Base\Field;

class SvgIcon extends Field
{

    /**
     * @var string
     */
    private $path;

    /**
     * @var
     */
    private $filter;

    /**
     * @param string $value
     * @return $this
     */
    public function path($value): self
    {
        $this->path = $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function filter($value)
    {
        $this->filter = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        $data = parent::formattedResponse();
        $data['options'] = $this->getOptions();

        return $data;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        $icons = [];
        $i = 1;
        foreach (\File::allFiles($this->path) as $path) {
            $fileName = pathinfo($path->getPathname(), PATHINFO_FILENAME);
            if ($this->filter && !str_contains($fileName, $this->filter)) {
                continue;
            }
            $name = str_replace('-', ' ', $fileName);
            $name = str_replace('_', ' ', $name);
            $name = ucfirst($name);
            $icons[] = [
                'id'       => $path->getPathname(),
                'text'     => '<span class="svg-wrapper">' . \File::get($path->getPathname()) . '</span> ' . $name,
                'selected' => $fileName === $this->getValue()
            ];
            $i++;
        }

        return $icons;
    }
}