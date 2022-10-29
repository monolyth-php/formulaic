<?php

namespace Monolyth\Formulaic\Button;

use Monolyth\Formulaic\Button;

/**
 * A submit button (`type="submit"`).
 */
class Submit extends Button
{
    /**
     * @var array
     *
     * Hash of attributes.
     */
    protected array $attributes = ['type' => 'submit'];
}

