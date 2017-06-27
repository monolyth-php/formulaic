<?php

namespace Monolyth\Formulaic;

class Tel extends Text
{
    protected $attributes = ['type' => 'tel'];

    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->addTest('numeric', function ($value) {
            return preg_replace('/[^\d]/', '', $value) == $value;
        });
    }

    /**
     * Set the value of the element.
     *
     * @param string $value Optional value (null for "undefined").
     */
    public function setValue(string $value = null)
    {
        if (!is_null($value)) {
            $tmp = preg_replace('/[^\d]/', '', $value);
            if (strlen($tmp)) {
                $value = $tmp;
                if ($value{0} != '0') {
                    $value = "0$value";
                }
            }
        }
        return parent::setValue($value);
    }
}

