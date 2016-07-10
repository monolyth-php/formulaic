<?php

namespace Monolyth\Formulaic;

class Url extends Text
{
    protected $attributes = ['type' => 'url'];

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->attributes['placeholder'] = 'http://';
        $this->addTest('url', function($value) {
            return filter_var($value, FILTER_VALIDATE_URL);
        });
    }

    public function setValue($value)
    {
        if ($value && !preg_match("@^(https?|ftp)://@", $value)) {
            $value = "http://$value";
        }
        return parent::setValue($value);
    }
}

