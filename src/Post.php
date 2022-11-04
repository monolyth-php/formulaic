<?php

namespace Monolyth\Formulaic;

/**
 * Implements a POST-form. The method and source default to that. You could
 * override the method, but that would not make sense. Elements are
 * automatically populated from `$_POST` and/or `$_FILES`.
 */
abstract class Post extends Form
{
    use ContainsFile;

    /**
     * Returns the default string representation of this form.
     *
     * @return string The form as '<form>...</form>'.
     * @see Monolyth\Formulaic\Form::__toString
     */
    public function __toString() : string
    {
        if ($this->containsFile()) {
            $this->attributes['enctype'] = 'multipart/form-data';
        }
        $this->attributes['method'] = 'post';
        return parent::__toString();
    }

    public function wasSubmitted() : bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
    }

    protected function getSource() : array
    {
        return $_FILES + $_POST;
    }
}

