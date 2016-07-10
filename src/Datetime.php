<?php

namespace Monolyth\Formulaic;

class Datetime extends Text
{
    protected $attributes = ['type' => 'datetime'];
    protected $format = 'Y-m-d H:i:s';

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->addTest('valid', function ($value) {
            return strtotime($value);
        });
    }

    public function setValue($value)
    {
        if (is_int($value)) {
            $value = date($this->format, $value);
        } elseif ($time = strtotime($value)) {
            $value = date($this->format, $time);
        }
        return parent::setValue($value);
    }

    public function isInPast()
    {
        return $this->addTest('inpast', function ($value) {
            return strtotime($value) < time();
        });
    }

    public function isInFuture()
    {
        return $this->addTest('infuture', function ($value) {
            return strtotime($value) > time();
        });
    }

    public function setMin($min)
    {
        if (is_int($min)) {
            $min = date($this->format, $min);
        } elseif ($time = strtotime($min)) {
            $min = date($this->format, $time);
        }
        $this->attributes['min'] = $min;
        return $this->addTest('min', function ($value) use ($min) {
            return $value >= $min;
        });
    }

    public function setMax($max)
    {
        if (is_int($max)) {
            $max = date($this->format, $max);
        } elseif ($time = strtotime($max)) {
            $max = date($this->format, $time);
        }
        $this->attributes['max'] = $max;
        return $this->addTest('max', function ($value) use ($max) {
            return $value <= $max;
        });
    }
}

