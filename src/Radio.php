<?php

namespace Monolyth\Formulaic;

class Radio extends Element
{
    protected $attributes = ['type' => 'radio', 'name' => true];
    protected $value = 1;
    protected $inGroup = false;

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

    /**
     * Get or set whether this element is part of a group.
     *
     * @param bool $status
     * @return bool
     */
    public function inGroup(bool $status = null) : bool
    {
        if (!is_null($status)) {
            $this->inGroup = $status;
        }
        return $this->inGroup;
    }
}

