<?php

namespace Monolyth\Formulaic;

class Textarea extends Element
{
    public function __construct($name = null)
    {
        parent::__construct($name);
        unset($this->attributes['value']);
    }

    public function __toString()
    {
        return $this->htmlBefore
            .'<textarea'.$this->attributes().'>'
            .htmlentities($this->value, ENT_COMPAT, 'UTF-8')
            .'</textarea>'
            .$this->htmlAfter;
    }

    /**
     * The maximum length of the field.
     *
     * @param integer $length Max characters
     * @return self
     */
    public function maxLength($length)
    {
        $this->attributes['maxlength'] = (int)$length;
        return $this->addTest('maxlength', function($value) use ($length) {
            return mb_strlen(trim($value), 'UTF-8') <= (int)$length;
        });
    }
}

