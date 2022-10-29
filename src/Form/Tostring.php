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
                $out .= "$field\n";
            }
        }
        $out .= '</form>';
        return $out;
    }
}

