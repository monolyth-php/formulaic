<?php

namespace Monolyth\Formulaic;

class File extends Element
{
    protected $attributes = ['type' => 'file'];

    public function __toString()
    {
        $old = $this->value;
        $this->value = null;
        $out = parent::__toString();
        $this->value = $old;
        return $out;
    }
}

