<?php

namespace Formulaic;

/**
 * Generic button (`type="button"`).
 */
class Button extends Element
{
    /**
     * The text to show in the button.
     */
    protected $text;
    /**
     * Array of attributes.
     */
    protected $attributes = ['type' => 'button'];

    /**
     * Constructor.
     *
     * @param string $text Text for in the button.
     * @param string $name Optional name for the button.
     * @return void
     */
    public function __construct($text = null, $name = null)
    {
        if (isset($name)) {
            $this->attributes['name'] = $name;
        }
        $this->text = $text;
    }

    /**
     * Return toString representation of the button.
     *
     * @return string Printable string of HTML.
     */
    public function __toString()
    {
        return '<button'.$this->attributes().'>'.$this->text.'</button>';
    }
}

