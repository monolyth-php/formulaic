<?php

namespace Monolyth\Formulaic\Radio\Group;

trait Tostring
{
    /**
     * Returns a rendered version of this radio group.
     *
     * @return string
     */
    public function __toString() : string
    {
        $out = '<div>';
        if ($this->htmlGroup & self::WRAP_GROUP) {
            $out .= $this->htmlBefore;
        }
        foreach ((array)$this as $field) {
            if ($this->htmlGroup & self::WRAP_LABEL) {
                $field->wrap($this->htmlBefore, $this->htmlAfter);
            }
            if ($this->htmlGroup & self::WRAP_ELEMENT) {
                $field->getElement()->wrap($this->htmlBefore, $this->htmlAfter);
            }
        }
        if (count((array)$this)) {
            $out .= "\n";
            foreach ((array)$this as $element) {
                $out .= "$element\n";
            }
        }
        if ($this->htmlGroup & self::WRAP_GROUP) {
            $out .= $this->htmlAfter;
        }
        $out .= '</div>';
        return $out;
    }
}

