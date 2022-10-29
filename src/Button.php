<?php

namespace Monolyth\Formulaic;

/**
 * Generic button (`type="button"`).
 */
class Button extends Element
{
    /**
     * @var string
     *
     * The text to show in the button.
     */
    protected string $text;

    /**
     * Constructor.
     *
     * @param string $text Text for in the button.
     * @param string $name Optional name for the button.
     * @return void
     */
    public function __construct(string $text = null, string $name = null)
    {
        if (isset($name)) {
            $this->attributes['name'] = $name;
        }
        $this->attributes['type'] = 'button';
        $this->text = $text;
    }

    /**
     * Return toString representation of the button.
     *
     * @return string Printable string of HTML.
     */
    public function __toString() : string
    {
        return '<button'.$this->attributes().'>'.$this->text.'</button>';
    }
}

