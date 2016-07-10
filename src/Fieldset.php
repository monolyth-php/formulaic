<?php

namespace Monolyth\Formulaic;

class Fieldset extends Element\Group
{
    use Attributes;
    use Fieldset\Tostring;
    use Element\Identify;
    use Bindable;

    protected $attributes = [];
    private $legend;
    private $prefix = [];

    public function __construct($legend = null, callable $callback)
    {
        $this->legend = $legend;
        $callback($this);
    }

    public function name()
    {
        return isset($this->legend) ? $this->legend : null;
    }

    public function bind($model)
    {
        return $this->bindGroup($model);
    }
}

