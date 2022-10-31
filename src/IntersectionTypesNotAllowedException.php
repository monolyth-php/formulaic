<?php

namespace Monolyth\Formulaic;

use DomainException;

class IntersectionTypesNotAllowedException extends DomainException
{
    public function __construct()
    {
        parent::__construct("Intersection types are not allowed in the argument for a transformer.");
    }
}

