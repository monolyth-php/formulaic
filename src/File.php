<?php

namespace Monolyth\Formulaic;

class File extends Element
{
    protected array $attributes = ['type' => 'file'];

    /**
     * A rendered version of this element.
     *
     * @return string
     */
    public function __toString() : string
    {
        $work = clone $this;
        $work->value = null;
        return $work->parentToString();
    }

    public function setValue(mixed $fileData = null) : self
    {
        return parent::setValue($fileData['tmp_name'] ?? null);
    }

    private function parentToString() : string
    {
        return parent::__toString();
    }
}

