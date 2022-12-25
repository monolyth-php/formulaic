<?php

namespace Monolyth\Formulaic;

/**
 * Implements a GET-form. The method and source default to that. You could
 * override the method, but that would not make sense. Elements are
 * automatically populated from `$_GET`.
 */
abstract class Get extends Form
{
    /**
     * Returns the default string representation of this form.
     *
     * @return string The form as '<form>...</form>'.
     * @see Monolyth\Formulaic\Form::__toString
     */
    public function __toString() : string
    {
        $this->attributes['method'] = 'get';
        return parent::__toString();
    }

    public function wasSubmitted() : bool
    {
        // The assumption is this must contain _something_.
        // There's really no other way to check this.
        return (bool)count($_GET);
    }

    protected function getSource() : array
    {
        return $_GET;
    }
}

