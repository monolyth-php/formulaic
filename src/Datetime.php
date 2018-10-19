<?php

namespace Monolyth\Formulaic;

class Datetime extends Text
{
    /**
     * @var array
     *
     * Hash of attributes.
     */
    protected $attributes = ['type' => 'datetime'];

    /**
     * @var string
     *
     * Default format.
     */
    protected $format = 'Y-m-d H:i:s';

    /**
     * @param string $name Name of the element.
     * @return void
     */
    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->addTest('valid', function ($value) {
            return strtotime($value);
        });
    }

    /**
     * @param string $timestamp Timestamp for the new datetime value. This can
     *  be any string parsable by PHP's `strtotime`.
     * @return Monolyth\Formulaic\Element Self
     */
    public function setValue(string $timestamp = null) : Element
    {
        if ($time = strtotime($timestamp)) {
            $timestamp = date($this->format, $time);
        }
        return parent::setValue($timestamp);
    }

    /**
     * Requires the datetime to be in the past.
     *
     * @return Monolyth\Formulaic\Element Self
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
     * @return Monolyth\Formulaic\Element Self
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
     * @param string $min Minimum timestamp. This can be any string parsable by
     *  PHP's `strtotime`.
     * @return Monolyth\Formulaic\Element Self
     */
    public function setMin(string $min) : Element
    {
        $min = date($this->format, strtotime($min));
        $this->attributes['min'] = $min;
        return $this->addTest('min', function ($value) use ($min) {
            return $value >= $min;
        });
    }

    /**
     * Set the maximum datetime.
     *
     * @param string $max Maximum timestamp. This can be any string parsable by
     *  PHP's `strtotime`.
     * @return Monolyth\Formulaic\Element Self
     */
    public function setMax(string $max) : Element
    {
        $max = date($this->format, strtotime($max));
        $this->attributes['max'] = $max;
        return $this->addTest('max', function ($value) use ($max) {
            return $value <= $max;
        });
    }
}

