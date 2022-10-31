<?php

namespace Monolyth\Formulaic;

use ArrayObject;
use DomainException;
use JsonSerializable;

/**
 * The base Form class.
 */
abstract class Form extends ArrayObject implements JsonSerializable, Bindable
{
    use Attributes;
    use Validate\Group;
    use QueryHelper;
    use JsonSerialize;

    /**
     * Returns name of the form.
     *
     * @return string
     */
    public function name() : string
    {
        return isset($this->attributes['name'])
            ? $this->attributes['name']
            : '';
    }

    /**
     * Returns the current form as an array of key/value pairs with data.
     *
     * @return array
     */
    public function getArrayCopy() : array
    {
        $copy = [];
        foreach ((array)$this as $key => $value) {
            if (is_string($value)) {
                continue;
            }
            $element = $value->getElement();
            if (is_object($element)
                and method_exists($element, 'name')
                and $name = $element->name()
                and !($element instanceof Button)
            ) {
                $copy[$name] = $element->getValue();
            }
        }
        return $copy;
    }

    /**
     * Binds a $model object to this form. Note that any properties not matching
     * fields in the form will be ignored, so it's safe to pass a full model to
     * a partial form.
     *
     * @param object The model to bind.
     * @return Monolyth\Formulaic\Form self
     */
    public function bind(object $model) : self
    {
        foreach ($this as $element) {
            if ($element instanceof Bindable) {
                $element->bind($model);
            }
        }
        return $this;
    }

    /**
     * Returns a rendered version of the form.
     *
     * @return string
     */
    public function __toString() : string
    {
        if ($name = $this->name()) {
            $this->attributes['id'] = $name;
        }
        if (!isset($this->attributes['action'])) {
            $this->attributes['action'] = '';
        }
        ksort($this->attributes);
        $out = "<form".$this->attributes().">\n";
        if (count($this)) {
            foreach ($this as $field) {
                $out .= $field;
            }
        }
        $out .= '</form>';
        return $out;
    }
}

