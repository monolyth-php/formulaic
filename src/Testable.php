<?php

namespace Monolyth\Formulaic;

interface Testable
{
    public function addTest(string $name, callable $test) : Testable;
}

