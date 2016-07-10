<?php

namespace Monolyth\Formulaic\Checkbox;

use Monolyth\Formulaic\Radio;

class Group extends Radio\Group
{
    use Group\Tostring;

    private $value;

    public function setValue($value)
    {
        if (is_scalar($value)) {
            $value = [$value];
        }
        foreach ((array)$this as $element) {
            if (in_array($element->getElement()->getValue(), $value)) {
                $element->getElement()->check();
            } else {
                $element->getElement()->check(false);
            }
        }
    }
    
    public function & getValue()
    {
        $this->value = [];
        foreach ((array)$this as $element) {
            if ($element->getElement()->checked()) {
                $this->value[] = $element->getElement()->getValue();
            }
        }
        return $this->value;
    }

    public function isRequired($min = 1, $max = null)
    {
        return $this->addTest('required', function ($value) use ($min, $max) {
            $checked = 0;
            foreach ($value as $option) {
                if ($option->getElement()->checked()) {
                    $checked++;
                }
            }
            return $checked >= $min && (is_null($max) or $checked <= $max);
        });
    }
}

