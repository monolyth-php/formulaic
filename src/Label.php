<?php

namespace Monolyth\Formulaic;

class Label
{
    use Label\Tostring;
    use Element\Identify {
        prefix as originalPrefix;
    }

    /**
     * Constructor.
     *
     * @param string $label The text of the label.
     * @param Monolyth\Formulaic\Labelable $element Any labelable element.
     * @return void
     */
    public function __construct(protected string $label, protected Labelable $element) {}

    /**
     * For convenience, forward all non-existing calls to the underlying
     * Labelable element.
     *
     * @param string $fn
     * @param array $args
     * @return mixed
     */
    public function __call(string $fn, array $args) : mixed
    {
        return $this->element->$fn(...$args);
    }

    /**
     * Set the associated element's value. This is not forwarded since we want
     * to return the label, not the element.
     *
     * @param mixed $value
     * @return self
     */
    public function setValue(mixed $value = null) : self
    {
        $this->element->setValue($value);
        return $this;
    }

    /**
     * Prefix label _and_ element with $prefix.
     *
     * @param string $prefix
     * @return void
     */
    public function prefix(string $prefix) : void
    {
        $this->originalPrefix($prefix);
        $this->element->prefix($prefix);
    }
}

