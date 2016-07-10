<?php

namespace Monolyth\Formulaic\Select;

trait Tostring
{
    public function __toString()
    {
        if ($id = $this->id()) {
            $this->attributes['id'] = $id;
        }
        $out = $this->htmlBefore;
        $out .= '<select'.$this->attributes().'>';
        if (count((array)$this)) {
            $out .= "\n";
            foreach ((array)$this as $option) {
                $out .= "$option\n";
            }
        }
        $out .= '</select>';
        $out .= $this->htmlAfter;
        return $out;
    }
}

