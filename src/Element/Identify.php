<?php

namespace Monolyth\Formulaic\Element;

trait Identify
{
    public function prefix($prefix)
    {
        array_unshift($this->prefix, $prefix);
    }
    
    public function name()
    {
        return isset($this->attributes['name']) ?
            $this->attributes['name'] :
            null;
    }
    
    public function id()
    {
        if (!($name = $this->name())) {
            return null;
        }
        $id = $name;
        if ($this->prefix) {
            $id = implode('-', $this->prefix)."-$id";
        }
        $id = preg_replace('/[\W]+/', '-', $id);
        return trim(preg_replace('/[-]+/', '-', $id), '-');
    }
}

