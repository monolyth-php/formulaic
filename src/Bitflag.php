<?php

namespace Monolyth\Formulaic;

use ArrayObject;

class Bitflag extends Checkbox\Group
{
    /**
     * Set the current value.
     *
     * @param mixed $value Integer or array containing new state.
     * @return self
     */
    public function setValue($value) : self
    {
        $value = $this->transform($value, ArrayObject::class);
        if (is_object($value)) {
            if (method_exists($value, 'getArrayCopy')) {
                $value = $value->getArrayCopy();
            } else {
                $value = (array)$value;
            }
        }
        parent::setValue($value);
        foreach ((array)$this as $element) {
            if ($element->getElement() instanceof Hidden) {
                continue;
            }
            $check = $element->getElement()->getValue();
            if (in_array($check, $value)) {
                $element->getElement()->check();
            } else {
                $element->getElement()->check(false);
            }
        }
        return $this;
    }

    /**
     * Get the current value.
     *
     * @return ArrayObject
     */
    public function getValue() : object
    {
        $values = new ArrayObject;
        foreach ($this as $value) {
            if ($value->getElement()->checked()) {
                $values[] = $value->getElement()->getValue();
            }
        }
        return new ArrayObject($values);
    }

    /**
     * Get the internal object containing the current status.
     *
     * @return object
     */
    public function getInternalStatus() : object
    {
        return $this->value;
    }

    /**
     * Check if the bit identified by `$name` is on.
     *
     * @param string $name
     * @return bool True if on, else false.
    ` */
    public function hasBit(string $name) : bool
    {
        foreach ((array)$this as $element) {
            if ($element->getElement()->getValue() == $name) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return the value of the element as a byte.
     *
     * @return int
     */
    public function getByteValue() : int
    {
        $bits = (array)$this->getValue();
        return array_reduce($bits, function ($carry, $item) {
            return $carry | (int)$item;
        }, 0);

    }

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
            if ($element->getElement()->getValue() == $index) {
                return $element;
            }
        }
        return parent::offsetGet($index);
    }

    /**
     * Binds a model to the bitflag (it's actually a group).
     *
     * @param object $model
     * @return object Self
     */
    public function bindGroup(object $model) : object
    {
        foreach ($this as $element) {
            $name = $element->getElement()->getValue();
            $model->$name = $element->getElement()->checked();
        }
        return $this;
    }
}

