<?php

namespace Monolyth\Formulaic;

class Label extends Element
{
    use Label\Tostring;

    protected $label;
    protected $element;

    public function __construct($label, Labelable $element)
    {
        $this->label = $label;
        $this->element = $element;
    }

    public function name()
    {
        return $this->label;
    }

    public function getElement()
    {
        return $this->element;
    }

    public function & getValue()
    {
        return $this->element->getValue();
    }

    public function raw()
    {
        return $this->txt;
    }

    public function prefix(string $prefix)
    {
        parent::prefix($prefix);
        return $this->element->prefix($prefix);
    }
}

