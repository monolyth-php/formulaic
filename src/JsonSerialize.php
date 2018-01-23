<?php

namespace Monolyth\Formulaic;

trait JsonSerialize
{
    /**
     * Returns a `json_encode`able hash.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $copy = [];
        foreach ((array)$this as $key => $value) {
            if (is_string($value)) {
                continue;
            }
            $element = $value->getElement();
            if (is_object($element)
                and method_exists($element, 'name')
                and $name = $element->name()
            ) {
                $copy[$name] = $value instanceof JsonSerializable ? $value->jsonSerialize() : $value;
            }
            $copy[$key] = $value instanceof JsonSerializable ? $value->jsonSerialize() : $value;
        }
        return $copy;
    }
}

