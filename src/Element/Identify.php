<?php

namespace Monolyth\Formulaic\Element;

trait Identify
{
    protected array $prefix = [];

    /**
     * Add a prefix to this element.
     *
     * @param string $prefix
     */
    public function prefix(string $prefix)
    {
        array_unshift($this->prefix, $prefix);
    }
    
    /**
     * Returns the name of the current element.
     *
     * @return string
     */
    public function name() : string
    {
        return $this->attributes['name'] ?? '';
    }
    
    /**
     * Returns the ID generated for the element.
     *
     * @return string
     */
    public function id() : string
    {
        $id = $this->name();
        if ($this->prefix) {
            $id = implode('-', $this->prefix)."-$id";
        }
        $id = preg_replace('/[\W]+/', '-', $id);
        return trim(preg_replace('/[-]+/', '-', $id), '-');
    }
}

