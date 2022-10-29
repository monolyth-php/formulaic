<?php

namespace Monolyth\Formulaic;

class Label implements Bindable
{
    use Label\Tostring;
    use Element\Identify {
        prefix as originalPrefix;
        name as private;
        id as private;
    }
    use Attributes;

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
     * @return mixed Either the label if the method returned the underlying
     *  Labelable (to continue proxying) or whatever (scalar?) value.
     */
    public function __call(string $fn, array $args) : mixed
    {
        $return = $this->element->$fn(...$args);
        return $return instanceof Labelable ? $this : $return;
    }

    /**
     * Return the underlying element.
     *
     * @return Labelable
     */
    public function getElement() : Labelable
    {
        return $this->element;
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

    /**
     * Proxy to the underlying model (to satisfy interface).
     */
    public function bind(object $model) : self
    {
        $this->element->bind($model);
        return $this;
    }
}

