<?php

namespace Monolyth\Formulaic;

class Password extends Text
{
    protected array $attributes = ['type' => 'password'];

    /**
     * Returns string representation of the element.
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

