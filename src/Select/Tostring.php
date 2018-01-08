<?php

namespace Monolyth\Formulaic\Select;

trait Tostring
{
    public function __toString()
    {
        if (isset($this->attributes['name'])
            && !is_bool($this->attributes['name'])
        ) {
            $old = $this->attributes['name'];
            $parts = $this->prefix;
            $parts[] = $old;
            $start = array_shift($parts);
            $this->attributes['name'] = $start;
            foreach ($parts as $part) {
                if (!is_bool($part)) {
                    $this->attributes['name'] .= "[$part]";
                }
            }
        }
        if (isset($this->attributes['name'])
            && is_bool($this->attributes['name'])
        ) {
            $old = $this->attributes['name'];
            unset($this->attributes['name']);
        }
        if ($id = $this->id()) {
            if (isset($this->idPrefix)) {
                $id = "{$this->idPrefix}-$id";
            }
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
        if (isset($old)) {
            $this->attributes['name'] = $old;
        }
        return $out;
    }
}

