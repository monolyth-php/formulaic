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

    /**
     * Add a generated ID to the element if none was manually set.
     *
     * @return void
     */
    protected function generateId() : void
    {
        if (isset($this->attributes['id'])) {
            return;
        }
        if ($id = $this->id()) {
            if (isset($this->idPrefix)) {
                $id = "{$this->idPrefix}-$id";
            }
            $this->attributes['id'] = $id;
        }
    }

    /**
     * Add a "printable name" to the element. For elements in groups, these will
     * automatically contain group name(s), so your element foo[bar][baz] can
     * simply be named 'baz' in code.
     *
     * @return void
     */
    protected function generatePrintableName() : void
    {
        if (isset($this->attributes['name']) && is_string($this->attributes['name'])) {
            $parts = $this->prefix;
            $parts[] = $this->attributes['name'];
            $start = array_shift($parts);
            $this->attributes['name'] = $start;
            foreach ($parts as $part) {
                if (is_string($part)) {
                    $this->attributes['name'] .= "[$part]";
                }
            }
        }
    }

}

