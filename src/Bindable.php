<?php

namespace Monolyth\Formulaic;

use DomainException;
use ArrayObject;
use TypeError;
use ReflectionFunction;
use ReflectionProperty;
use BackedEnum;

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
        $value = $this instanceof Radio ? $this->checked() : $this->getValue();
        try {
            $model->$name = $this->transform($value, $this->getType($model, $name));
        } catch (TypeError $e) {
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
                $name = self::normalize($field->getElement()->name());
                if (property_exists($model, $name)) {
                    if (isset($model->$name) && is_object($model->$name)) {
                        $field->bindGroup($model->$name);
                    } else {
                        $field->bind($model);
                    }
                }
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
                    $element->setValue($value instanceof BackedEnum ? $value->value : $value);
                }
                if ($userSupplied) {
                    if ($element instanceof Radio) {
                        $element->check((bool)$curr);
                    } else {
                        $element->setValue($curr);
                    }
                }
                $element->bind($model);
            }
        }
        return $this;
    }

    /**
     * Specify one or more transformers used to transform in- and output to
     * values compatible with your model.
     *
     * @param callable ...$transformers
     * @return object Self
     */
    public function withTransformers(callable ...$transformers) : object
    {
        array_walk($transformers, function (callable $transformer) : void {
            $this->withTransformer($transformer);
        });
        return $this;
    }

    /**
     * Specify a transformer used to transform in- or output to values
     * compatible with your model.
     *
     * @param callable ...$transformers
     * @return object Self
     */
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
        $type = $this->normalizedType($type);
        $this->transformers[$type] = $transformer;
        return $this;
    }

    /**
     * Internal method performing the actual transformation.
     *
     * @param mixed $value Element's current value.
     * @return mixed Transformed value, or original if no suitable
     *  transformation was found.
     */
    protected function transform(mixed $value) : mixed
    {
        if (is_object($value)) {
            if (isset($requested) && ($value instanceof $requested)) {
                return $value;
            }
            $types = [get_class($value)];
            $types = array_merge($types, array_values(class_parents($value)));
            $types = array_merge($types, array_values(class_implements($value)));
        } else {
            $types = [$this->normalizedType(gettype($value))];
        }
        $types[] = '*';
        foreach ($types as $type) {
            if (isset($this->transformers[$type])) {
                return $this->transformers[$type]($value);
            }
        }
        return $value;
    }

    /**
     * Internal helper to properly get the request value type.
     *
     * @param object $model
     * @param string $name
     * @return string|null The requested class, or null if not relevant/defined.
     */
    protected function getType(object $model, string $name) :? string
    {
        $check = $model->$name ?? null;
        if (is_object($check)) {
            return get_class($check);
        }
        return $this->normalizedType(gettype($check));
    }

    protected function normalizedType(string $type) : string
    {
        switch ($type) {
            case 'integer': return 'int';
        }
        return $type;
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

