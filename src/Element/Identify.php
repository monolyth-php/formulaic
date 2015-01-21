<?php

namespace Formulaic\Element;

trait Identify
{
    public function prefix($prefix)
    {
        array_unshift($this->prefix, $prefix);
    }

    public function prefixId($prefix)
    {
        $this->prefixId = $prefix;
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
        if ($this->prefixId) {
            $id = $this->prefixId.'-'.$id;
        }
        $id = preg_replace('/[\W]+/', '-', $id);
        return trim(preg_replace('/[-]+/', '-', $id), '-');
    }
}

