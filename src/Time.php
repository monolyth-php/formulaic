<?php

namespace Monolyth\Formulaic;

class Time extends Datetime
{
    protected array $attributes = ['type' => 'time'];
    protected $format = 'H:i:s';
}

