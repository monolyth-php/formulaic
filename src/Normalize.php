<?php

namespace Monolyth\Formulaic;

trait Normalize
{
    /**
     * "Normalize" a name string, i.e. only the first part without stuff in
     * square brackets (since that's invalid on models).
     *
     * @param string $name
     * @return string
     */
    protected static function normalize(string $name) : string
    {
        return explode('[', $name)[0];
    }

}

