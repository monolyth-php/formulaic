<?php

namespace Monolyth\Formulaic\Validate;

trait Group
{
    /**
     * Check whether the group is valid.
     *
     * @return bool
     */
    public function valid() : bool
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

    /**
     * Returns all errors for this group.
     *
     * @return array
     */
    public function errors() : array
    {
        $errors = $this->privateErrors();
        foreach ((array)$this as $element) {
            $name = $element->getElement()->name();
            if ($error = $element->getElement()->errors()) {
                $errors = array_merge($errors, [$name => $error]);
            }
        }
        return $errors;
    }

    /**
     * Helper function for internal use.
     *
     * @return array
     */
    private function privateErrors() : array
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

