<?php

namespace Monolyth\Formulaic\Validate;

trait Group
{
    public function valid()
    {
        if ($this->privateErrors()) {
            return false;
        }
        foreach ((array)$this as $element) {
            if (!$element->getElement()->valid()) {
                return false;
            }
        }
        return true;
    }
    
    public function errors()
    {
        $errors = $this->privateErrors();
        foreach ((array)$this as $element) {
            if ($error = $element->getElement()->errors()) {
                $errors = array_merge(
                    $errors,
                    [$element->getElement()->name() => $error]
                );
            }
        }
        return $errors;
    }

    private function privateErrors()
    {
        $errors = [];
        if (isset($this->tests)) {
            foreach ($this->tests as $error => $test) {
                if (!$test((array)$this)) {
                    $errors[] = $error;
                }
            }
        }
        return $errors;
    }
}

