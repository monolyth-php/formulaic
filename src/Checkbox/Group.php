<?php

namespace Monolyth\Formulaic\Checkbox;

use Monolyth\Formulaic\Radio;
use ArrayObject;

class Group extends Radio\Group
{
    private $value;

    /**
     * @param mixed $value New value or array of key/value pairs.
     * @return self
     */
    public function setValue(mixed $value) : self
    {
        if (is_scalar($value)) {
            $value = [$value];
        }
        if (is_object($value)) {
            $value = $value->getArrayCopy();
        }
        foreach ((array)$this as $element) {
            if (in_array($element->getElement()->getValue(), $value)) {
                $element->getElement()->check();
            } else {
                $element->getElement()->check(false);
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
        return $this->addTest('required', function ($value) use ($min, $max) {
            $checked = 0;
            foreach ($value as $option) {
                if ($option->getElement() instanceof Radio
                    && $option->getElement()->checked()
                ) {
                    $checked++;
                }
            }
            return $checked >= $min && (is_null($max) or $checked <= $max);
        });
    }
}

