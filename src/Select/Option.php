<?php

namespace Monolyth\Formulaic\Select;

use Monolyth\Formulaic\Element;

class Option extends Element
{
    private $label;

    public function __construct($value, $label)
    {
        $this->value = $value;
        $this->label = $label;
        parent::__construct();
    }

    public function getName()
    {
        return $this->label;
    }

    public function selected()
    {
        $this->attributes['selected'] = null;
    }

    public function unselected()
    {
        unset($this->attributes['selected']);
    }

    public function __toString()
    {
        return '<option'.$this->attributes().'>'.$this->label.'</option>';
    }
}

