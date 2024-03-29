<?php

namespace Monolyth\Formulaic;

class Date extends Datetime
{
    /**
     * @var array
     *
     * Hash of attributes.
     */
    protected array $attributes = ['type' => 'date'];

    /**
     * @var string
     *
     * The default format.
     */
    protected $format = 'Y-m-d';
}

