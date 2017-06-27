<?php

namespace Monolyth\Formulaic\Label;

use Monolyth\Formulaic\Radio;

trait Tostring
{
    /**
     * Returns the label and its associated element, as a string.
     *
     * @return string
     */
    public function __toString() : string
    {
        if ($id = $this->element->id()) {
            $this->attributes['for'] = $id;
        }
        $out = '<label'.$this->attributes().'>';
        if ($this->element instanceof Radio) {
            $out .= "{$this->element} {$this->label}";
            $out .= '</label>';
        } else {
            $out .= "{$this->label}</label>\n";
            $out .= $this->element;
        }
        return $out;
    }
}

