<?php

namespace Monolyth\Formulaic\Validate;

trait Element
{
    /**
     * Check whether the element is valid.
     *
     * @return bool
     */
    public function valid() : bool
    {
        return $this->errors() ? false : true;
    }

    /**
     * Get array of errors for element.
     *
     * @return array
     */
    public function errors() : array
    {
        $errors = [];
        foreach ($this->tests as $error => $test) {
            if (!$test($this->getValue())) {
                $errors[] = $error;
            }
        }
        return $errors;
    }
}

