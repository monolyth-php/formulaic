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
        if (property_exists($model, $name)) {
            $value = $this->getValue();
            try {
                $model->$name = $this->transform($value);
            } catch (TypeError $e) {
                throw new TransformerRequiredException($model, $name, $value);
            }
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

    public function withTransformers(callable ...$transformers) : object
    {
        array_walk($transformers, function (callable $transformer) : void {
            $this->withTransformer($transformer);
        });
        return $this;
    }

    public function withTransformer(callable $transformer) : object
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

    protected function transform($value, string $requested = null)
    {
        if (is_object($value)) {
            if (isset($requested) && ($value instanceof $requested)) {
                return $value;
            }
            $types = [get_class($value)];
            $types = array_merge($types, array_values(class_parents($value)));
            $types = array_merge($types, array_values(class_implements($value)));
        } else {
            $types = [gettype($value)];
        }
        $types[] = '*';
        foreach ($types as $type) {
            if (isset($this->transformers[$type])) {
                $value = $this->transformers[$type]($value);
                if (isset($requested) && (is_object($value) ? ($value instanceof $requested) : gettype($value) != $requested)) {
                    return $this->transform($value, $requested);
                } else {
                    return $value;
                }
            }
        }
        return $value;
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

