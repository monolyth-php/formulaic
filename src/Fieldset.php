<?php

namespace Monolyth\Formulaic;

class Fieldset extends Element\Group
{
    use Attributes;
    use Element\Identify;

    /**
     * Constructor. Pass null for $legend to omit the `<legend>` tag.
     * `$callback` is called with the newly created fieldset as its argument
     * for further processing.
     *
     * @param string|null $legend Optional legend to display.
     * @param callable $callback
     * @return void
     */
    public function __construct(private ?string $legend, callable $callback)
    {
        parent::__construct(null, $callback);
    }

    /**
     * Returns the value of the legend, or an empty string if not set.
     *
     * @return string
     */
    public function name() : string
    {
        return isset($this->legend) ? $this->legend : '';
    }

    /**
     * @param object $model
     * @return self
     */
    public function bind(object $model) : self
    {
        foreach ($this as $element) {
            $element->bind($model);
        }
        return $this;
    }

    /**
     * Set the values of this fieldset. This is an override since fieldset,
     * although being element groups, do not add an additional prefix; they are
     * just a grouping for better user interface.
     *
     * @param array $value Type hinted as mixed, but must really be an array
     * @return self
     */
    public function setValue(mixed $value) : self
    {
        if (!is_array($value)) {
            return $this;
        }
        foreach ($this as $element) {
            $element->setValue($value[$element->name()] ?? null);
        }
        return $this;
    }

    /**
     * Returns a rendered version of the fieldset.
     *
     * @return string
     */
    public function __toString() : string
    {
        $out = "<fieldset".$this->attributes().">\n";
        if (isset($this->legend)) {
            $out .= "<legend>{$this->legend}</legend>\n";
        }
        $fields = (array)$this;
        if ($fields) {
            foreach ($fields as $field) {
                if (isset($this->prefix)) {
                    $field->prefix(implode('-', $this->prefix));
                }
                $out .= "$field";
            }
        }
        $out .= "</fieldset>\n";
        return $out;
    }
}

