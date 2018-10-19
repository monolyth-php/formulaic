<?php

namespace Monolyth\Formulaic\Button;

use Monolyth\Formulaic\Button;

/**
 * A reset button (`type="reset"`). Note that Formulaic doesn't (can't)
 * do anything when this is clicked; it's a client side thing.
 */
class Reset extends Button
{
    /**
     * @var array
     *
     * Hash of attributes.
     */
    protected $attributes = ['type' => 'reset'];
}

