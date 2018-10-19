<?php

namespace Monolyth\Formulaic;

use DomainException;
use ArrayObject;
use Throwable;

/**
 * Trait to make something bindable.
 */
trait Bindable
{
    /** @var object */.
    private $model;

    /**
     * Binds the element to a model.
     *
     * @param object $model The model to bind to.
     * @return void
     */
    public function bind(object $model) : void
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
     * @return object Self.
     */
    public function bindGroup(object $model) : object
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
Bindable::bindGroup must be called on an object extending ArrayObject.
EOT
            );
        }
        foreach ($this as $field) {
            if (is_string($field)) {
                continue;
            }
            if ($field instanceof Fieldset) {
                $field->bind($model);
                continue;
            }
            if ($field instanceof Element\Group) {
                $field->bindGroup($model->{self::normalize($field->getElement()->name())});
                continue;
            }
            if ($field instanceof Button) {
                continue;
            }
            $name = self::normalize($field->getElement()->name());
            if ($element = $field->getElement()) {
                try {
                    $value = $model->$name;
                } catch (Throwable $e) {
                    continue;
                }
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

    /**
     * "Normalize" a name string, i.e. only the first part without stuff in
     * square brackets (since that's invalid on models).
     *
     * @param string $name
     * @return string
     */
    protected static function normalize(string $name) : string
    {
        return explode('[', $name)[0];
    }
}

