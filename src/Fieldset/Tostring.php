<?php

namespace Monolyth\Formulaic\Fieldset;

trait Tostring
{
    public function __toString()
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

