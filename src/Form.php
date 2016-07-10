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

    /**
     * Hash of key/value pairs for HTML attributes.
     */
    protected $attributes = [];

    /**
     * Return the form name if set, or null.
     *
     * @return string|null The name, or null.
     */
    public function name()
    {
        return isset($this->attributes['name']) ?
            $this->attributes['name'] :
            null;
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
     * Returns a `json_encode`able hash.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $copy = [];
        foreach ((array)$this as $key => $value) {
            $element = $value->getElement();
            if (is_object($element)
                and method_exists($element, 'name')
                and $name = $element->name()
            ) {
                $copy[$name] = $value;
            }
            $copy[$key] = $value;
        }
        return $copy;
    }

    /**
     * Binds a $model object to this form by proxying Bindable::bindGroup.
     *
     * @param object The model to bind.
     * @see Monolyth\Formulaic\Bindable::bindGroup
     * @return static $this
     */
    public function bind($model)
    {
        return $this->bindGroup($model);
    }
}

