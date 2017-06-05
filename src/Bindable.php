<?php

namespace Monolyth\Formulaic;

use DomainException;
use ArrayObject;

/**
 * Trait to make something bindable.
 */
trait Bindable
{
    private $model;

    /**
     * Binds the element to a model.
     *
     * @param object $model The model to bind to.
     */
    public function bind($model)
    {
        $this->model = $model;
    }

    /**
     * Binds a $model object to a group of elements.
     *
     * $model can be any object. All its public properties are looped over, and
     * the values are bound to those of the form if they exist on the form.
     * For form elements that have not been initialized from user input, the
     * value is set to the current model's value too. This allows you to provide
     * defaults a user can edit (e.g. update the property "name" on a User
     * model).
     *
     * @param object The model to bind.
     * @return static $this
     */
    public function bindGroup($model)
    {
        if (!is_object($model)) {
            throw new DomainException(
                <<<EOT
Bindable::bindGroup must be called with an object containing publicly accessible
key/value pairs of data.
EOT
            );
        }
        if (!($this instanceof ArrayObject)) {
            throw new DomainException(
                <<<EOT
Bindable::bindGroup must be called on object extending ArrayObject.
EOT
            );
        }
        foreach ($this as $field) {
            if ($field instanceof Element\Group) {
                $field->bindGroup($model->{$field->name()});
                continue;
            }
            $name = $field->getElement()->name();
            if ($element = $field->getElement() and property_exists($model, $name)) {
                $value = $model->$name;
                $curr = $element instanceof Radio ? $element->checked() : $element->getValue();
                $userSupplied = $element->valueSuppliedByUser();
                if ($element instanceof Radio) {
                    $element->check((bool)$value);
                } else {
                    $element->setValue($value);
                }
                if ($userSupplied) {
                    if ($element instanceof Radio) {
                        $element->check((bool)$curr);
                        $model->$name = $element->checked();
                    } else {
                        $element->setValue($curr);
                        $model->$name = $element->getValue();
                    }
                }
                $element->bind($model);
            }
        }
        return $this;
    }
}

