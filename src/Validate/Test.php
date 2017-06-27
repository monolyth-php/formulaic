<?php

namespace Monolyth\Formulaic\Validate;

use Monolyth\Formulaic\Testable;

trait Test
{
    /**
     * Generic test adder.
     *
     * @param string $name Unique name of the test.
     * @param callable $fn Test function, called with the element's current
     *  value as argument. Should return true if the test passes, else false.
     * @return self
     */
    public function addTest(string $name, callable $fn) : Testable
    {
        $this->tests[$name] = function ($value) use ($name, $fn) {
            if (is_string($value) && !strlen(trim($value))) {
                $value = null;
            }
            if ($value || $name == 'required') {
                return $fn($value);
            }
            return true;
        };
        return $this;
    }
}

