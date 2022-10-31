<?php

namespace Monolyth\Formulaic;

use Stringable;

/**
 * Generic button (`type="button"`).
 */
class Button extends Element implements Stringable
{
    /**
     * @var string
     *
     * The text to show in the button.
     */
    protected string $text;

    protected array $attributes = ['type' => 'button'];

    /**
     * Constructor.
     *
     * @param string $text Text for in the button.
     * @param string $name Optional name for the button.
     * @return void
     */
    public function __construct(string $text, string $name = null)
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
    public function __toString() : string
    {
        return '<button'.$this->attributes().'>'.$this->text."</button>\n";
    }
}

