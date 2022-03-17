<?php

namespace Monolyth\Formulaic;

trait QueryHelper
{
    /**
     * Get the element at the specified `$index`.
     *
     * @param mixed $index
     * @return mixed The found index, or null
     */
    public function offsetGet($index) : mixed
    {
        foreach ((array)$this as $i => $element) {
            $i = (string)$i;
            if (is_string($element)) {
                continue;
            }
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
     * @param mixed $index Optional index. Normal use is to either assign
     *  elements to `$this[]` or `$this['someElementName']`.
     * @param mixed $newvalue
     * @return void
     */
    public function offsetSet($index, $newvalue) : void
    {
        if (!isset($index)) {
            $index = count((array)$this);
        }
        if (is_object($newvalue)
            && method_exists($newvalue, 'setIdPrefix')
            && isset($this->attributes['id'])
        ) {
            $newvalue->setIdPrefix($this->attributes['id']);
        }
        parent::offsetSet($index, $newvalue);
    }
    
    /**
     * Append any item to the form.
     *
     * @param mixed $newvalue
     * @return void
     */
    public function append($newvalue) : void
    {
        $index = count((array)$this);
        $this->offsetSet($index, $newvalue);
    }
}

