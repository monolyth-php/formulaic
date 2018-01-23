<?php

namespace Monolyth\Formulaic;

use ArrayObject;
use DomainException;
use JsonSerializable;

/**
 * The base Form class.
 */
abstract class Form extends ArrayObject implements JsonSerializable
{
    use Attributes;
    use Form\Tostring;
    use Validate\Group;
    use QueryHelper;
    use Bindable;
    use JsonSerialize;

    /**
     * Hash of key/value pairs for HTML attributes.
     */
    protected $attributes = [];

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
    public function getArrayCopy()
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
     * Binds a $model object to this form by proxying Bindable::bindGroup.
     *
     * @param object The model to bind.
     * @see Monolyth\Formulaic\Bindable::bindGroup
     * @return self
     */
    public function bind($model) : Form
    {
        return $this->bindGroup($model);
    }
}

