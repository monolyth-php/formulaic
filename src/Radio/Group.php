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

class Group extends Element\Group implements Labelable
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
    
    public function __construct($name, $options)
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
                $option->setValue($value);
                foreach ($this->prefix as $prefix) {
                    $option->prefix($prefix);
                }
                $this[] = new Label($txt, $option);
            }
        }
        $this->prefix[] = $name;
    }

    public function name()
    {
        return $this->id();
    }
    
    public function id()
    {
        return $this->prefix[0];
    }
    
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

    public function & getValue()
    {
        foreach ((array)$this as $element) {
            if ($element->getElement() instanceof Radio
                && $element->getElement()->checked()
            ) {
                return $element->getElement()->getValue();
            }
        }
        return $this->value;
    }
    
    public function isRequired()
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

