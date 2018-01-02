<?php

namespace Monolyth\Formulaic\Validate;

use Monolyth\Formulaic\Testable;

trait Required
{
    /**
     * This is a required field.
     *
     * @return self
     */
    public function isRequired() : Testable
    {
        $this->attributes['required'] = true;
        return $this->addTest('required', function ($value) {
            if (is_array($value)) {
                return $value;
            }
            return strlen(trim($value));
        });
    }
}

