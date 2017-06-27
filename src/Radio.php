<?php

namespace Monolyth\Formulaic;

class Radio extends Element
{
    protected $attributes = ['type' => 'radio', 'name' => true];
    protected $value = 1;
    protected $inGroup = false;

    /**
     * Returns the ID of the element.
     *
     * @return string
     */
    public function id() : string
    {
        $name = $this->name();
        $name = preg_replace("@\[\]$@", '', $name);
        if ($name) {
            return $name.'-'.$this->value;
        }
        return $name;
    }

    /**
     * Check (or uncheck) the radio button.
     *
     * @param bool $value True or false. Defaults to true.
     */
    public function check(bool $value = true)
    {
        if ($value == false) {
            unset($this->attributes['checked']);
        } else {
            $this->attributes['checked'] = $value;
        }
    }

    /**
     * Check if the element is currently checked.
     *
     * @return bool
     */
    public function checked() : bool
    {
        return array_key_exists('checked', $this->attributes);
    }

    /**
     * Mark the field as required.
     *
     * @return self
     */
    public function isRequired() : Element
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

