<?php

namespace Monolyth\Formulaic;

class Url extends Text
{
    protected $attributes = ['type' => 'url'];

    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->attributes['placeholder'] = 'http://';
        $this->addTest('url', function($value) {
            return filter_var($value, FILTER_VALIDATE_URL);
        });
    }

    /**
     * Set the value.
     *
     * @param string $value Optional URL, pass `null` for "undefined".
     */
    public function setValue(string $value = null) : Element
    {
        if ($value && !preg_match("@^(https?|ftp)://@", $value)) {
            $value = "http://$value";
        }
        return parent::setValue($value);
    }
}

