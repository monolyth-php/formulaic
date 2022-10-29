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
        $out = "<fieldset".$this->attributes().">\n";
        if (isset($this->legend)) {
            $out .= "<legend>{$this->legend}</legend>\n";
        }
        $fields = (array)$this;
        if ($fields) {
            foreach ($fields as $field) {
                if (isset($this->prefix)) {
                    $field->prefix(implode('-', $this->prefix));
                }
                $out .= "$field";
            }
        }
        $out .= "</fieldset>\n";
        return $out;
    }
}

