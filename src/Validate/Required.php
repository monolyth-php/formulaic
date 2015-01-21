<?php

namespace Formulaic\Validate;

trait Required
{
    /** This is a required field. */
    public function isRequired()
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

