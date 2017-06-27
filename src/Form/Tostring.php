<?php

namespace Monolyth\Formulaic\Form;

use ArrayObject;
use Monolyth\Formulaic\Select;

trait Tostring
{
    /**
     * Returns a rendered version of the form.
     *
     * @return string
     */
    public function __toString() : string
    {
        if ($name = $this->name()) {
            $this->attributes['id'] = $name;
        }
        if (!isset($this->attributes['action'])) {
            $this->attributes['action'] = '';
        }
        ksort($this->attributes);
        $out = '<form'.$this->attributes().'>';
        $fields = (array)$this;
        if ($fields) {
            $out .= "\n";
            foreach ($fields as $field) {
                if ($field instanceof ArrayObject
                    && !($field instanceof Select)
                ) {
                    $out .= "$field\n";
                } else {
                    $out .= "<div>$field</div>\n";
                }
            }
        }
        $out .= '</form>';
        return $out;
    }
}

