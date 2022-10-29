<?php

namespace Monolyth\Formulaic\Element;

trait Tostring
{
    /**
     * Returns a rendered string representation of the element.
     *
     * @return string
     */
    public function __toString() : string
    {
        $old = $this->prepareToString();
        $out = $this->htmlBefore;
        $out .= '<input'.$this->attributes().">\n";
        $out .= $this->htmlAfter;
        if (isset($old)) {
            $this->attributes['name'] = $old;
        }
        return $out;
    }

    protected function prepareToString() :? string
    {
        $old = null;
        if ($id = $this->id()) {
            if (isset($this->idPrefix)) {
                $id = "{$this->idPrefix}-$id";
            }
            $this->attributes['id'] = $id;
        }
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
        return $old;
    }
}

