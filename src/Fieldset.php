<?php

namespace Monolyth\Formulaic;

class Fieldset extends Element\Group
{
    use Attributes;
    use Fieldset\Tostring;
    use Element\Identify;

    private string $legend;

    /**
     * Constructor. Pass null for $legend to omit the `<legend>` tag.
     * `$callback` is called with the newly created fieldset as its argument
     * for further processing.
     *
     * @param string $legend Optional legend to display.
     * @param callable $callback
     * @return void
     */
    public function __construct(string $legend = null, callable $callback)
    {
        $this->legend = $legend;
        $callback($this);
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
}

