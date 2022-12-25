<?php

namespace Monolyth\Formulaic;

trait ContainsFile
{
    public function containsFile() : bool
    {
        foreach ($this as $element) {
            if ($element->getElement() instanceof File || ($element->getElement() instanceof Element\Group && $element->containsFile())) {
                return true;
            }
        }
        return false;
    }
}

