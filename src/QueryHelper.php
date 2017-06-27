<?php

namespace Monolyth\Formulaic;

trait QueryHelper
{
    /**
     * Get the element at the specified `$index`.
     *
     * @param string $index
     */
    public function offsetGet(string $index)
    {
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

    /**
     * Set an element or group or whatever at the specific `$index`.
     *
     * @param string $index Optional index. Normal use is to either assign
     *  elements to `$this[]` or `$this['someElementName']`.
     */
    public function offsetSet(string $index = null, $newvalue)
    {
        if (!isset($index)) {
            $index = count((array)$this);
        }
        if (isset($this->attributes['id'])) {
            $newvalue->setIdPrefix($this->attributes['id']);
        }
        parent::offsetSet($index, $newvalue);
    }
    
    /**
     * Append any item to the form.
     *
     * @param mixed $newvalue
     */
    public function append($newvalue)
    {
        $index = count((array)$this);
        return $this->offsetSet($index, $newvalue);
    }
}

