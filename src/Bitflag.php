<?php

namespace Monolyth\Formulaic;

use StdClass;
use JsonSerializable;
use DomainException;

class Bitflag extends Checkbox\Group
{
    protected $value = null;
    protected $class = null;

    /**
     * Constructor.
     *
     * @param string $label Label for the bitflag.
     * @param array $options Hash of key/value options.
     * @param mixed $class Optional name of class or object to store bitflag
     *  state on.
     */
    public function __construct(string $label, array $options, $class = null)
    {
        parent::__construct($label, $options);
        $default = new Hidden("{$label}[]");
        $default->setValue(0);
        $this[] = $default;
        if (isset($class)) {
            if (!is_object($class)) {
                throw new DomainException("Third parameter to Bitflag::__construct must be a class.");
            }
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
     */
    public function setValue($value)
    {
        if (is_object($value)) {
            $this->class = $value;
            if (isset($this->value)) {
                $old = clone $this->value;
                $work = clone $value;
                if ($work instanceof JsonSerializable) {
                    $work = $work->jsonSerialize();
                }
                foreach ($work as $prop => $status) {
                    $value->$prop = isset($old->$prop) ? $old->$prop : false;
                }
            }
            $this->value = $value;
        }
        if (!isset($this->value)) {
            $this->value = clone $this->class;
        }
        if (is_array($value)) {
            $work = clone $this->value;
            if ($work instanceof JsonSerializable) {
                $work = $work->jsonSerialize();
            }
            foreach ($work as $prop => $status) {
                $this->value->$prop = false;
            }
            foreach ($value as $prop) {
                if ($this->hasBit($prop) && isset($this->value->$prop)) {
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
     * @return object
     */
    public function & getValue()
    {
        return $this->value;
    }

    /**
     * Check if the bit identified by %name is on.
     *
     * @param string $name
     * @return bool True if on, else false.
     */
    public function hasBit(string $name) : bool
    {
        foreach ((array)$this as $element) {
            if ($element->getElement()->getValue() == $name) {
                return true;
            }
        }
        return false;
    }
}

