<?php

namespace Monolyth\Formulaic\Radio;

use Monolyth\Formulaic\{ Attributes, Validate, Radio, Checkbox, Element, Label, Labelable, Testable, Transform, Normalize };
use ArrayObject;

class Group extends Element\Group implements Labelable, Testable
{
    use Attributes;
    use Validate\Group;
    use Validate\Test;
    use Group\Tostring;
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
    public function __construct(string $name, callable|array $options)
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
        return $this->id();
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
     * @return Monolyth\Formulaic\Radio\Group
     */
    public function setValue($value) : self
    {
        foreach ((array)$this as $element) {
            if ($value == $element->getElement()->getValue()) {
                $element->getElement()->check();
            } else {
                $element->getElement()->check(false);
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
    public function getValue() : object
    {
        foreach ((array)$this as $element) {
            if ($element->getElement() instanceof Radio
                && $element->getElement()->checked()
            ) {
                return new class([$element->getElement()->getValue()]) extends ArrayObject {
                    public function __toString() : string
                    {
                        return "{$this[0]}";
                    }
                };
            }
        }
        return new class([$this->value]) extends ArrayObject {
            public function __toString() : string
            {
                return "0";
            }
        };
    }
    
    /**
     * Marks the group as required.
     *
     * @return self
     */
    public function isRequired() : Group
    {
        foreach ((array)$this as $el) {
            if (!is_object($el)) {
                continue;
            }
            $el->getElement()->attribute('required', 1);
        }
        return $this->addTest('required', function ($value) {
            foreach ($value as $option) {
                if ($option->getElement() instanceof Radio
                    && $option->getElement()->checked()
                ) {
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
}

