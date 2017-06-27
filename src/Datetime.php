<?php

namespace Monolyth\Formulaic;

class Datetime extends Text
{
    protected $attributes = ['type' => 'datetime'];
    protected $format = 'Y-m-d H:i:s';

    /**
     * @param string $name Optional name of the element.
     */
    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->addTest('valid', function ($value) {
            return strtotime($value);
        });
    }

    /**
     * @param int $timestamp Timestamp for the new datetime value.
     * @return self
     */
    public function setValue(int $timestamp) : Element
    {
        return parent::setValue($this->format($timestamp));
    }

    /**
     * Requires the datetime to be in the past.
     *
     * @return self
     */
    public function isInPast() : Element
    {
        return $this->addTest('inpast', function ($value) {
            return strtotime($value) < time();
        });
    }

    /**
     * Requires the datetime to be in the future.
     *
     * @return self
     */
    public function isInFuture() : Element
    {
        return $this->addTest('infuture', function ($value) {
            return strtotime($value) > time();
        });
    }

    /**
     * Set the minimum datetime.
     *
     * @param int $min Minimum timestamp.
     * @return self
     */
    public function setMin(int $min) : Element
    {
        $min = date($this->format, $min);
        $this->attributes['min'] = $min;
        return $this->addTest('min', function ($value) use ($min) {
            return $value >= $min;
        });
    }

    /**
     * Set the maximum datetime.
     *
     * @param int $max Maximum timestamp.
     * @return self
     */
    public function setMax(int $max) : Element
    {
        $max = date($this->format, $max);
        $this->attributes['max'] = $max;
        return $this->addTest('max', function ($value) use ($max) {
            return $value <= $max;
        });
    }
}

