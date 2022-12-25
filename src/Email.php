<?php

namespace Monolyth\Formulaic;

class Email extends Text
{
    protected array $attributes = ['type' => 'email'];

    /**
     * Constructor.
     *
     * @param string $name Name of the element.
     */
    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->addTest('valid', function ($value) {
            return filter_var($value, FILTER_VALIDATE_EMAIL)
                && preg_match("/.*@.*\..*/", $value);
        });
    }
}

