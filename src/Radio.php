<?php

namespace Monolyth\Formulaic;

class Radio extends Element
{
    protected $attributes = ['type' => 'radio', 'name' => true];
    protected $value = 1;

    public function id()
    {
        $name = $this->name();
        if (is_bool($name)) {
            return null;
        }
        $name = preg_replace("@\[\]$@", '', $name);
        if ($name) {
            return $name.'-'.$this->value;
        }
        return $name;
    }

    public function check($value = null)
    {
        if ($value === false) {
            unset($this->attributes['checked']);
        } else {
            $this->attributes['checked'] = $value;
        }
    }

    public function checked()
    {
        return array_key_exists('checked', $this->attributes);
    }

    /** This is a required field. */
    public function isRequired()
    {
        $this->attributes['required'] = true;
        return $this->addTest('required', function($value) {
            return $this->checked();
        });
    }
}

