<?php

namespace Monolyth\Formulaic;

use Stringable;

class Label implements Bindable, Stringable
{
    use Element\Identify {
        prefix as originalPrefix;
        name as private;
        id as private;
    }
    use Attributes;
    use Element\Wrap;

    private string $idPrefix;

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
     *
     * @param object $model
     * @return self
     */
    public function bind(object $model) : self
    {
        $this->element->bind($model);
        return $this;
    }

    /**
     * Set the ID prefix. Needs so the for attribute stays aligned with the
     * underlying element.
     *
     * @param string $id
     * @return void
     */
    public function setIdPrefix(string $id) : void
    {
        $this->idPrefix = $id;
        $this->element->setIdPrefix($id);
    }

    /**
     * Returns the label and its associated element, as a string.
     *
     * @return string
     */
    public function __toString() : string
    {
        if ($id = $this->element->id()) {
            $this->attributes['for'] = $id;
        }
        if (isset($this->idPrefix)) {
            $this->attributes['for'] = "{$this->idPrefix}-{$this->attributes['for']}";
        }
        $out = $this->htmlBefore ?? '';
        $out .= '<label'.$this->attributes().'>';
        if ($this->element instanceof Radio) {
            $element = trim("{$this->element}");
            $out .= "$element {$this->label}";
            $out .= "</label>\n";
        } else {
            $out .= "{$this->label}</label>\n";
            $out .= $this->element;
        }
        $out .= $this->htmlAfter ?? '';
        return $out;
    }
}

