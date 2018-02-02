<?php

namespace Monolyth\Formulaic;

class Textarea extends Element
{
    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name);
        unset($this->attributes['value']);
    }

    /**
     * Returns string representation of textarea.
     *
     * @return string
     */
    public function __toString() : string
    {
        $old = $this->prepareToString();
        $out = $this->htmlBefore
            .'<textarea'.$this->attributes().'>'
            .htmlentities($this->value, ENT_COMPAT, 'UTF-8')
            .'</textarea>'
            .$this->htmlAfter;
        if (isset($old)) {
            $this->attributes['name'] = $old;
        }
        return $out;
    }

    /**
     * The maximum length of the field.
     *
     * @param integer $length Max characters
     * @return self
     */
    public function maxLength(int $length) : Element
    {
        $this->attributes['maxlength'] = (int)$length;
        return $this->addTest('maxlength', function($value) use ($length) {
            return mb_strlen(trim($value), 'UTF-8') <= (int)$length;
        });
    }
}

