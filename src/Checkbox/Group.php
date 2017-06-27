<?php

namespace Monolyth\Formulaic\Checkbox;

use Monolyth\Formulaic\Radio;

class Group extends Radio\Group
{
    use Group\Tostring;

    private $value;

    /**
     * @param mixed $value New value or array of key/value pairs.
     */
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
    
    /**
     * Gets all checked values as an array.
     *
     * @return array
     */
    public function & getValue() : array
    {
        $this->value = [];
        foreach ((array)$this as $element) {
            if ($element->getElement() instanceof Radio
                && $element->getElement()->checked()
            ) {
                $this->value[] = $element->getElement()->getValue();
            }
        }
        return $this->value;
    }

    /**
     * @param int $min Minimum number of checked items.
     * @param int $max Optional maximum number of checked items.
     * @return self
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

