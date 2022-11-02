<?php

namespace Monolyth\Formulaic\Checkbox;

use Monolyth\Formulaic\Radio;
use ArrayObject;

class Group extends Radio\Group
{
    private $value;

    /**
     * @param array $value New hash of key/value pairs.
     * @return self
     */
    public function setValue(mixed $value) : self
    {
        if (!is_array($value)) {
            return $this;
        }
        foreach ($this as $element) {
            if (!in_array($element->getValue(), $value)) {
                $element->check(false);
            } else {
                $element->check();
            }
        }
        return $this;
    }
    
    /**
     * Gets all checked values as an `ArrayObject`.
     *
     * @return ArrayObject
     */
    public function getValue() : object
    {
        $this->value = [];
        foreach ((array)$this as $element) {
            if ($element->getElement() instanceof Radio
                && $element->getElement()->checked()
            ) {
                $this->value[] = $element->getElement()->getValue();
            }
        }
        return new ArrayObject($this->value);
    }

    /**
     * @param int $min Minimum number of checked items.
     * @param int $max Optional maximum number of checked items.
     * @return Monolyth\Formulaic\Radio\Group Self.
     */
    public function isRequired(int $min = 1, int $max = null) : Radio\Group
    {
        return $this->addTest('required', function () use ($min, $max) {
            $checked = 0;
            foreach ($this as $option) {
                if ($option->getElement() instanceof Radio && $option->getElement()->checked()) {
                    $checked++;
                }
            }
            return $checked >= $min && (is_null($max) or $checked <= $max);
        });
    }
}

