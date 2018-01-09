<?php

namespace Monolyth\Formulaic;

/**
 * Implements a POST-form. The method and source default to that. You could
 * override the method, but that would not make sense. Elements are
 * automatically populated from `$_POST` and/or `$_FILES`.
 */
abstract class Post extends Form
{
    /**
     * Returns the default string representation of this form.
     *
     * @return string The form as '<form>...</form>'.
     * @see Monolyth\Formulaic\Form::__toString
     */
    public function __toString() : string
    {
        foreach ((array)$this as $field) {
            if (is_string($field)) {
                continue;
            }
            if ($field->getElement() instanceof File) {
                $this->attributes['enctype'] = 'multipart/form-data';
            }
        }
        $this->attributes['method'] = 'post';
        return parent::__toString();
    }

    /**
     * Adds an item to the form, checking to see if its $_POST or $_FILES
     * variant exists and if so, uses that as the value.
     *
     * @param integer|string|null $index The index to set the new item at.
     * @param mixed $item An element or a label containing one.
     * @return void
     */
    public function offsetSet($index, $item)
    {
        if ($item instanceof Fieldset) {
            foreach ($item as $subitem) {
                $this->setValue($subitem);
            }
        } else {
            $this->setValue($item);
        }
        parent::offsetSet($index, $item);
    }

    /**
     * Internal helper method to set the value of whatever we are dealing with.
     *
     * @param mixed $item
     */
    private function setValue($item)
    {
        if (is_string($item)) {
            return;
        }
        $element = $item->getElement();
        $name = $element->name();
        if ($element instanceof File) {
            if (array_key_exists($name, $_FILES)) {
                $element->setValue($_FILES[$name]);
                $element->valueSuppliedByUser(true);
            }
        } elseif (array_key_exists($name, $_POST)) {
            if ($element instanceof Radio) {
                if ($_POST[$name] == $element->getValue()
                    || (is_array($_POST[$name])
                        && in_array($element->getValue(), $_POST[$name])
                    )
                ) {
                    $element->check();
                } else {
                    $element->check(false);
                }
            } else {
                $element->setValue($_POST[$name]);
            }
            $element->valueSuppliedByUser(true);
        } elseif ($element instanceof Radio) {
            $element->check(false);
        }
    }
}

