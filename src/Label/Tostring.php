<?php

namespace Monolyth\Formulaic\Label;

use Monolyth\Formulaic\Radio;

trait Tostring
{
    public function __toString()
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

