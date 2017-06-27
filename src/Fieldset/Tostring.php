<?php

namespace Monolyth\Formulaic\Fieldset;

trait Tostring
{
    /**
     * Returns a rendered version of the fieldset.
     *
     * @return string
     */
    public function __toString() : string
    {
        $out = '<fieldset'.$this->attributes().'>';
        if (isset($this->legend)) {
            $out .= "\n<legend>{$this->legend}</legend>";
        }
        $fields = (array)$this;
        if ($fields) {
            $out .= "\n";
            foreach ($fields as $field) {
                if (isset($this->prefix)) {
                    $field->prefix(implode('-', $this->prefix));
                }
                $out .= "<div>$field</div>\n";
            }
        }
        $out .= '</fieldset>';
        return $out;
    }
}

