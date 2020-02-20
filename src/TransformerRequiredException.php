<?php

namespace Monolyth\Formulaic;

use TypeError;

class TransformerRequiredException extends TypeError
{
    public function __construct(object $model, string $property, $value)
    {
        $type = is_object($value) ? get_class($value) : gettype($value);
        $class = get_class($model);
        return parent::__construct("Model $class does not accept $type for property $property.");
    }
}

