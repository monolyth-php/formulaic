<?php

namespace Monolyth\Formulaic\Select;

use Monolyth\Formulaic\Element;

class Option extends Element
{
    private $label;

    /**
     * Constrcutor.
     *
     * @param string $value
     * @param string $label
     */
    public function __construct(string $value, string $label)
    {
        $this->value = $value;
        $this->label = $label;
        parent::__construct($value);
    }

    /**
     * Returns the label of the element.
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->label;
    }

    /**
     * Mark the option as selected.
     */
    public function selected()
    {
        $this->attributes['selected'] = null;
    }

    /**
     * Mark the option as unselected.
     */
    public function unselected()
    {
        unset($this->attributes['selected']);
    }

    /**
     * Returns the option rendered as string.
     *
     * @return string
     */
    public function __toString() : string
    {
        unset($this->attributes['name']);
        return '<option'.$this->attributes().'>'.$this->label.'</option>';
    }
}

