<?php

namespace Monolyth\Formulaic\Validate;

trait Element
{
    public function valid()
    {
        return $this->errors() ? false : true;
    }

    public function errors()
    {
        $errors = [];
        foreach ($this->tests as $error => $test) {
            if (!$test($this->getValue())) {
                $errors[] = $error;
            }
        }
        return $errors ? $errors : null;
    }
}

