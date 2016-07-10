<?php

namespace Monolyth\Formulaic;

class Email extends Text
{
    protected $attributes = ['type' => 'email'];

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->addTest('valid', function ($value) {
            return filter_var($value, FILTER_VALIDATE_EMAIL)
                && preg_match("/.*@.*\..*/", $value);
        });
    }
}

