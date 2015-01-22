<?php

namespace Formulaic;

trait QueryHelper
{
    public function offsetGet($index)
    {
        foreach ((array)$this as $element) {
            if ($element->name() == $index) {
                return $element;
            }
            if ($element instanceof Label
                && $element->getElement()->name() == $index
            ) { 
                return $element;
            }
            if ($element instanceof Element\Group && isset($element[$index])) {
                return $element[$index];
            }
        }
    }
}
