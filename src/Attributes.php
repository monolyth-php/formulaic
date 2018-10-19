<?php

namespace Monolyth\Formulaic;

trait Attributes
{
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
            if (is_null($value) || $value === true) {
                if ($name == 'value') {
                    continue;
                }
                $return[] = $name;
            } else {
                if ($value === false) {
                    continue;
                }
                if ($name == 'name') {
                    $value = preg_replace("@^\[(.*?)\]@", '$1', $value);
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
     * @return object Self.
     */
    public function attribute(string $name, string $value = null) : object
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * Unset an attribute.
     *
     * @param string $name
     * @return object Self.
     */
    public function unsetAttribute(string $name) : object
    {
        unset($this->attributes[$name]);
        return $this;
    }
}

