<?php

namespace Monolyth\Formulaic;

trait QueryHelper
{
    public function offsetGet($index)
    {
        $index = (string)$index;
        foreach ((array)$this as $i => $element) {
            $i = (string)$i;
            if ($element instanceof Label
                && ($element->getElement()->name() == $index
                    || $i == $index
                )
            ) { 
                return $element;
            }
            if ($element->name() == $index || $i == $index) {
                return $element;
            }
            if ($element instanceof Element\Group
                and $found = $element[$index]
            ) {
                return $found;
            }
        }
        return null;
    }

    public function offsetSet($index, $newvalue)
    {
        if (!isset($index)) {
            $index = count((array)$this);
        }
        if (isset($this->attributes['id'])) {
            $newvalue->setIdPrefix($this->attributes['id']);
        }
        parent::offsetSet($index, $newvalue);
    }
    
    public function append($newvalue)
    {
        $index = count((array)$this);
        return $this->offsetSet($index, $newvalue);
    }
}

