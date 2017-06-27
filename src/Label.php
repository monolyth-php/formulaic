<?php

namespace Monolyth\Formulaic;

class Label extends Element
{
    use Label\Tostring;

    protected $label;
    protected $element;

    /**
     * Constructor.
     *
     * @param string $label The text of the label.
     * @param Monolyth\Formulaic\Labelable $element Any labelable element.
     */
    public function __construct($label, Labelable $element)
    {
        $this->label = $label;
        $this->element = $element;
    }

    /**
     * Get the text of the label.
     *
     * @return string
     */
    public function name() : string
    {
        return $this->label;
    }

    /**
     * Get the associated element.
     *
     * @return Monolyth\Formulaic\Labelable
     */
    public function getElement() : Labelable
    {
        return $this->element;
    }

    /**
     * Get the associated element's value.
     *
     * @return mixed
     */
    public function & getValue()
    {
        return $this->element->getValue();
    }

    /*
    public function raw()
    {
        return $this->txt;
    }
    */

    /**
     * Prefix label _and_ element with $prefix.
     *
     * @param string $prefix
     */
    public function prefix(string $prefix)
    {
        parent::prefix($prefix);
        $this->element->prefix($prefix);
    }
}

