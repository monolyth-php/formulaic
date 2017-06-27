<?php

namespace Monolyth\Formulaic\Radio;

use Monolyth\Formulaic\Attributes;
use Monolyth\Formulaic\Validate;
use Monolyth\Formulaic\Radio;
use Monolyth\Formulaic\Checkbox;
use Monolyth\Formulaic\Element;
use Monolyth\Formulaic\Label;
use Monolyth\Formulaic\Labelable;
use Monolyth\Formulaic\Bindable;
use Monolyth\Formulaic\Testable;

class Group extends Element\Group implements Labelable, Testable
{
    use Attributes;
    use Validate\Group;
    use Validate\Test;
    use Group\Tostring;
    use Bindable;
    
    protected $attributes = [];
    protected $tests = [];
    protected $source = [];
    private $prefix = [];
    private $value = null;
    
    /**
     * Constructor.
     *
     * @param string $name
     * @param callable|array $options Either a callback (called with new group
     *  as first parameter) or an array of value/label pairs.
     */
    public function __construct(string $name, $options)
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
     */
    public function setValue($value)
    {
        foreach ((array)$this as $element) {
            if ($value == $element->getElement()->getValue()) {
                $element->getElement()->check();
            } else {
                $element->getElement()->check(false);
            }
        }
    }

    /**
     * Gets the checked value in the group.
     *
     * @return array Array with a single entry (for compatibility with
     *  Element\Group, but obviously radio groups can only ever have one entry
     *  checked at a time).
     */
    public function & getValue() : array
    {
        foreach ((array)$this as $element) {
            if ($element->getElement() instanceof Radio
                && $element->getElement()->checked()
            ) {
                return [$element->getElement()->getValue()];
            }
        }
        return [$this->value];
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
}

