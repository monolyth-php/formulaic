<?php

namespace Monolyth\Formulaic;

trait Attributes
{
    protected array $attributes = [];

    /**
     * Formats set attributes as a string ready for insertion into HTML.
     *
     * @return string
     */
    public function attributes() : string
    {
        $return = [];
        ksort($this->attributes);
        foreach ($this->attributes as $name => $value) {
            if ($value === true) {
                $return[] = $name;
            } else {
                if ($value === null || $value === false) {
                    continue;
                }
                if ($name == 'name') {
                    $value = preg_replace("@^\[(.*?)\]@", '$1', $value);
                    $value = preg_replace("@\[(.*?)\[\]\]$@", '[$1][]', $value);
                }
                $return[] = sprintf(
                    '%s="%s"',
                    $name,
                    htmlentities($value, ENT_COMPAT, 'UTF-8')
                );
            }
        }
        return $return ? ' '.implode(' ', $return) : '';
    }

    /**
     * Set an attribute with optional value.
     *
     * @param string $name
     * @param string|null $value
     * @return self
     */
    public function attribute(string $name, ?string $value = null) : self
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * Unset an attribute.
     *
     * @param string $name
     * @return self
     */
    public function unsetAttribute(string $name) : self
    {
        unset($this->attributes[$name]);
        return $this;
    }
}

