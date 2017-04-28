<?php

namespace Monolyth\Formulaic;

use StdClass;
use JsonSerializable;
use DomainException;

class Bitflag extends Checkbox\Group
{
    protected $value = null;
    protected $class = null;

    public function __construct($label, $options, $class = null)
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

    public function & getValue()
    {
        return $this->value;
    }

    public function hasBit($name)
    {
        foreach ((array)$this as $element) {
            if ($element->getElement()->getValue() == $name) {
                return true;
            }
        }
        return false;
    }
}

