<?php

namespace Formulaic;

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
     * @see Formulaic\Form::__toString
     */
    public function __toString()
    {
        $this->attributes['method'] = 'get';
        return parent::__toString();
    }

    /**
     * Adds an item to the form, checking to see if its $_GET-variant exists
     * and if so, uses that as the value.
     *
     * @param integer|string|null $index The index to set the new item at.
     * @param mixed $item An element or a label containing one.
     * @return void
     */
    public function offsetSet($index, $item)
    {
        $name = $item->getElement()->name();
        if (array_key_exists($name, $_GET)) {
            $item->getElement()->setValue($_GET[$name]);
            $item->getElement()->valueSuppliedByUser(true);
        }
        parent::offsetSet($index, $item);
    }
}

