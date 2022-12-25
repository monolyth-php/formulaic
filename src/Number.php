<?php

namespace Monolyth\Formulaic;

class Number extends Text
{
    protected array $attributes = ['type' => 'number', 'step' => 1];

    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->addTest('numeric', 'is_numeric');
        $this->setStep(1);
    }

    /**
     * Set the minimum value.
     *
     * @param float $min
     * @return self
     */
    public function setMin(float $min) : Number
    {
        $this->attributes['min'] = $min;
        return $this->addTest('min', function ($value) use ($min) {
            return $value >= $min;
        });
    }

    /**
     * Set the maximum value.
     *
     * @param float $max
     * @return self
     */
    public function setMax(float $max) : Number
    {
        $this->attributes['max'] = $max;
        return $this->addTest('max', function ($value) use ($max) {
            return $value <= $max;
        });
    }

    /**
     * Set the allowed step interval.
     *
     * @param float $step
     * @return self
     */
    public function setStep(float $step) : Number
    {
        $this->attributes['step'] = $step;
        $offset = isset($this->attributes['min']) ?
            $this->attributes['min'] :
            0;
        return $this->addTest('step', function ($value) use ($step, $offset) {
            if (!is_numeric($value)) {
                return false;
            }
            return !fmod($value - $offset, $step);
        });
    }

    /**
     * The field must contain an integer.
     *
     * @return self
     */
    public function isInteger() : Number
    {
        return $this->addTest('integer', 'is_int');
    }
    
    /**
     * The field must contain a number greater than zero.
     *
     * @return self
     */
    public function isGreaterThanZero() : Number
    {
        return $this->addTest('positive', function ($value) {
            return (float)$value > 0;
        });
    }
}

