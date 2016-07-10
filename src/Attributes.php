<?php

namespace Monolyth\Formulaic;

trait Attributes
{
    public function attributes()
    {
        $return = [];
        ksort($this->attributes);
        foreach ($this->attributes as $name => $value) {
            if (is_null($value)) {
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

    public function attribute($name, $value = null)
    {
        if ($value === false) {
            unset($this->attributes[$name]);
        } else {
            $this->attributes[$name] = $value;
        }
        return $this;
    }
}

