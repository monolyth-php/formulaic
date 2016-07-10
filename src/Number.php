<?php

namespace Monolyth\Formulaic;

class Number extends Text
{
    protected $attributes = ['type' => 'number', 'step' => 1];

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->addTest('numeric', 'is_numeric');
        $this->setStep(1);
    }

    public function setMin($min)
    {
        $this->attributes['min'] = $min;
        return $this->addTest('min', function ($value) use ($min) {
            return $value >= $min;
        });
    }

    public function setMax($max)
    {
        $this->attributes['max'] = $max;
        return $this->addTest('max', function ($value) use ($max) {
            return $value <= $max;
        });
    }

    public function setStep($step)
    {
        $this->attributes['step'] = $step;
        $offset = isset($this->attributes['min']) ?
            $this->attributes['min'] :
            0;
        return $this->addTest('step', function ($value) use ($step, $offset) {
            return !fmod($value - $offset, $step);
        });
    }

    /** The field must contain an integer. */
    public function isInteger()
    {
        return $this->addTest('integer', 'is_int');
    }
    
    /** The field must contain a number greater than zero. */
    public function isGreaterThanZero()
    {
        return $this->addTest('positive', function ($value) {
            return (float)$value > 0;
        });
    }
}

