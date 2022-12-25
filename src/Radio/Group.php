<?php

namespace Monolyth\Formulaic\Radio;

use Monolyth\Formulaic\{ Attributes, Validate, Radio, Checkbox, Element, Label, Labelable, Testable, Transform, Normalize };
use ArrayObject;
use Stringable;

class Group extends Element\Group implements Labelable, Testable, Stringable
{
    use Attributes;
    use Validate\Group;
    use Validate\Test;
    use Transform;
    use Element\Identify;
    use Normalize;
    
    private mixed $value = null;
    
    /**
     * Constructor.
     *
     * @param string $name
     * @param callable|array $options Either a callback (called with new group
     *  as first parameter) or an array of value/label pairs.
     */
    public function __construct(protected string $name, callable|array $options)
    {
        if (is_callable($options)) {
            $options($this);
            foreach ((array)$this as $option) {
                foreach ($this->prefix as $prefix) {
                    $option->prefix($prefix);
                }
                $option->prefix($name);
            }
        } else {
            foreach ($options as $value => $txt) {
                if ($this instanceof Checkbox\Group) {
                    $option = new Checkbox("{$name}[]");
                } else {
                    $option = new Radio($name);
                }
                $option->inGroup(true);
                $option->setValue($value);
                foreach ($this->prefix as $prefix) {
                    $option->prefix($prefix);
                }
                $this[] = new Label($txt, $option);
            }
        }
        $this->prefix[] = $name;
    }

    /**
     * Returns the base name of the group.
     *
     * @return string
     */
    public function name() : string
    {
        return $this->name;
    }
    
    /**
     * Returns the ID (base name) of the group.
     *
     * @return string
     */
    public function id() : string
    {
        return $this->prefix[0];
    }
    
    /**
     * Sets the element where the value matches to `checked`.
     *
     * @param mixed $value
     * @return self
     */
    public function setValue(mixed $value) : self
    {
        foreach ($this as $element) {
            if ((string)$element->getValue() !== "$value") {
                $element->check(false);
            } else {
                $element->check();
                // Radio groups can only ever have one option selected
                break;
            }
        }
        return $this;
    }

    /**
     * Gets the checked value in the group.
     *
     * @return array Array with a single entry (for compatibility with
     *  Element\Group, but obviously radio groups can only ever have one entry
     *  checked at a time).
     */
    public function getValue() : array
    {
        foreach ((array)$this as $element) {
            if ($element->getElement() instanceof Radio
                && $element->getElement()->checked()
            ) {
                return [$element->getElement()->getValue()];
            }
        }
        return [0];
    }
    
    /**
     * Marks the group as required.
     *
     * @return self
     */
    public function isRequired() : self
    {
        foreach ($this as $option) {
            $option->isRequired();
        }
        return $this->addTest('required', function ($value) {
            foreach ($this as $option) {
                if ($option->getElement() instanceof Radio && $option->getElement()->checked()) {
                    return true;
                }
            }
            return false;
        });
    }

    /**
     * Bind to a model.
     */
    public function bind(object $model) : self
    {
        $name = self::normalize($this->name());
        if ($this->valueSuppliedByUser()) {
            $model->$name = $this->transform($this->getValue());
        } else {
            $this->setValue($this->transform($model->$name ?? null));
        }
        return $this;
    }

    public function valid() : bool
    {
        $errors = $this->runTests();
        return $errors ? false : true;
    }

    public function errors() : array
    {
        return $this->runTests();
    }
}

