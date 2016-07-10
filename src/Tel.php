<?php

namespace Monolyth\Formulaic;

class Tel extends Text
{
    protected $attributes = ['type' => 'tel'];

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->addTest('numeric', function ($value) {
            return preg_replace('/[^\d]/', '', $value) == $value;
        });
    }

    public function setValue($value)
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

