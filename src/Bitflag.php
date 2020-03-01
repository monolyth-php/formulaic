<?php

namespace Monolyth\Formulaic;

use StdClass;
use JsonSerializable;
use DomainException;
use ArrayObject;

class Bitflag extends Checkbox\Group
{
    /** @var object */
    protected $value = null;

    /** @var object */
    protected $class = null;

    /**
     * Constructor.
     *
     * @param string $label Label for the bitflag.
     * @param array $options Hash of key/value options.
     * @param object|null $class Optional object to store bitflag
     *  state on.
     * @return void
     */
    public function __construct(string $label, array $options, object $class = null)
    {
        parent::__construct($label, $options);
        $default = new Hidden("{$label}[]");
        $default->setValue(0);
        $this[] = $default;
        if (isset($class)) {
            $this->class = $class;
        } else {
            $this->class = new StdClass;
            foreach ($options as $key => $value) {
                $this->class->$key = false;
            }
        }
    }

    /**
     * Set the current value.
     *
     * @param mixed $value Object or array containing new state.
     * @return void
     */
    public function setValue($value) : void
    {
        if (is_object($value) && !($value instanceof ArrayObject)) {
            $this->class = $value;
            if (isset($this->value)) {
                $old = clone $this->value;
                $work = clone $value;
                if ($work instanceof JsonSerializable) {
                    $work = $work->jsonSerialize();
                }
                foreach ($work as $prop => $status) {
                    $value->$prop = (bool)($old->$prop ?? false);
                }
            }
            $this->value = $value;
        }
        if (!isset($this->value)) {
            $this->value = clone $this->class;
        }
        if (is_array($value) || $value instanceof ArrayObject) {
            $work = clone $this->value;
            if ($work instanceof JsonSerializable) {
                $work = $work->jsonSerialize();
            }
            foreach ($work as $prop => $status) {
                $this->value->$prop = false;
            }
            foreach ($value as $prop) {
                if (is_string($prop) && $this->hasBit($prop) && isset($this->value->$prop)) {
                    $this->value->$prop = true;
                }
            }
        }
        $found = [];
        foreach ((array)$this as $element) {
            if ($element->getElement() instanceof Hidden) {
                continue;
            }
            $check = $element->getElement()->getValue();
            if (isset($this->value->$check) && $this->value->$check) {
                $element->getElement()->check();
            } else {
                $element->getElement()->check(false);
            }
            $found[] = $check;
        }
    }

    /**
     * Get the current value.
     *
     * @return ArrayObject
     */
    public function getValue() : ArrayObject
    {
        $array = [];
        foreach ($this as $value) {
            if ($value instanceof Hidden) {
                continue;
            }
            if ($value->getElement()->checked()) {
                $array[] = $value->getElement()->getValue();
            }
        }
        return new class($array) extends ArrayObject {
            public function __toString() : string {
                $bit = 0;
                foreach ($this as $value) {
                    $bit |= $value;
                }
                return "$bit";
            }
        };
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
    public function offsetGet($index)
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
        parent::bindGroup($model);
        $this->setValue($model);
        return $this;
    }
}

