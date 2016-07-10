<?php

namespace Monolyth\Formulaic\Validate;

trait Test
{
    public function addTest($name, callable $fn)
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

