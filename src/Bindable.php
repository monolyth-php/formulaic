<?php

namespace Monolyth\Formulaic;

use DomainException;
use ArrayObject;
use TypeError;
use ReflectionFunction;

/**
 * Trait to make something bindable.
 */
trait Bindable
{
    /** @var object */
    private $model;

    /** @var callable[][] */
    private $transformers = [];

    /**
     * Binds the element to a model.
     *
     * @param object $model The model to bind to.
     * @return object Self
     * @throws Monolyth\Formulaic\TransformerRequiredException if the property
     *  on the model is type hinted (PHP7.4+) and its value is incompatible.
     */
    public function bind(object $model) : object
    {
        $this->model = $model;
        $name = self::normalize($this->name());
        try {
            $model->$name = $this->transform($this->getValue());
        } catch (TypeError $e) {
            $value = $this->getValue();
            throw new TransformerRequiredException($model, $name, $value);
        }
        return $this;
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
                if (isset($model->$name)) {
                    $value = $model->$name;
                } elseif ($model instanceof ArrayObject && isset($model[$name])) {
                    $value = $model[$name];
                } else {
                    $value = null;
                }
                $curr = $element instanceof Radio ? $element->checked() : $element->getValue();
                $userSupplied = $element->valueSuppliedByUser();
                if ($element instanceof Radio) {
                    $element->check((bool)$value);
                } elseif (!is_null($value)) {
                    $element->setValue($value);
                }
                if ($userSupplied) {
                    if ($element instanceof Radio) {
                        $element->check((bool)$curr);
            //            $model->$name = $element->checked();
                    } else {
                        $element->setValue($curr);
              //          $model->$name = $element->transform($element->getValue());
                    }
                }
                $element->bind($model);
            }
        }
        return $this;
    }

    public function withTransformer(callable $transformer) : self
    {
        $reflection = new ReflectionFunction($transformer);
        $parameter = $reflection->getParameters()[0];
        $type = '*';
        if ($parameter->hasType()) {
            $type = $parameter->getType();
            if ((float)phpversion() >= 7.4) {
                $type = $type->getName();
            } else {
                $type = "$type";
            }
        }
        $this->transformers[$type] = $transformer;
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

    protected function transform($value)
    {
        if (is_object($value)) {
        } else {
            $type = gettype($value);
        }
        if (isset($this->transformers[$type])) {
            $value = $this->transformers[$type]($value);
        } elseif (isset($this->transformers['*'])) {
            $value = $this->transformers['*']($value);
        }
        return $value;
    }
}

