<?php

namespace Monolyth\Formulaic;

class Text extends Element
{
    protected $attributes = ['type' => 'text'];

    /**
     * Set the size of the text input.
     *
     * @param int $size The size
     * @return self
     */
    public function size($size)
    {
        $this->attributes['size'] = $size;
        return $this;
    }

    /**
     * The field must match the pattern supplied.
     *
     * @param string $pattern Regex the field must match
     * @return self
     */
    public function matchPattern($pattern)
    {
        $this->attributes['pattern'] = $pattern;
        return $this->addTest('pattern', function ($value) use ($pattern) {
            return preg_match("@^$pattern$@", trim($value));
        });
    }
    
    /**
     * The maximum length of the field.
     *
     * @param int $lenger Max characters
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

