<?php

namespace Monolyth\Formulaic;

use JsonSerializable;

trait JsonSerialize
{
    /**
     * Returns a `json_encode`able hash.
     *
     * @return mixed
     */
    public function jsonSerialize() : mixed
    {
        $copy = [];
        foreach ((array)$this as $key => $value) {
            if (!is_string($value)) {
                $element = $value->getElement();
                if (is_object($element)
                    and method_exists($element, 'name')
                    and $name = $element->name()
                ) {
                    $copy[$name] = $value instanceof JsonSerializable ? $value->jsonSerialize() : $value;
                }
                $copy[$key] = $value instanceof JsonSerializable ? $value->jsonSerialize() : $value;
            } else {
                $copy[$key] = $value;
            }
        }
        return $copy;
    }
}

