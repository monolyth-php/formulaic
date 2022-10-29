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
        $old = $this->value;
        $this->value = null;
        $out = parent::__toString();
        $this->value = $old;
        return $out;
    }
}

