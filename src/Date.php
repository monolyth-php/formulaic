<?php

namespace Formulaic;

class Date extends Datetime
{
    protected $attributes = ['type' => 'date'];
    protected $format = 'Y-m-d';
}

