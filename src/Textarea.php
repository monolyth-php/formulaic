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
        $work = clone $this;
        $work->generateId();
        $work->generatePrintableName();
        $out = ($work->htmlBefore ?? '')
            .'<textarea'.$work->attributes().">\n"
            .htmlentities($work->value ?? '', ENT_COMPAT, 'UTF-8')."\n"
            ."</textarea>\n"
            .($work->htmlAfter ?? '');
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

