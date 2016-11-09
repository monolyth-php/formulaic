<?php

namespace Monolyth\Formulaic;

use ArrayObject;

class Select extends ArrayObject implements Labelable
{
    use Attributes;
    use Element\Identify;
    use Element\Wrap;
    use Validate\Test;
    use Validate\Required;
    use Validate\Element;
    use Select\Tostring;
    use Bindable;

    private $userInput = false;
    protected $attributes = [];
    protected $value;
    protected $name;
    protected $prefix = [];
    protected $idPrefix;

    public function __construct($name, $options)
    {
        if (isset($name)) {
            $this->attributes['name'] = $name;
        }
        if (is_callable($options)) {
            $options($this);
        } else {
            foreach ($options as $value => $txt) {
                $option = new Select\Option($value, $txt);
                $this[] = $option;
            }
        }
        $this->addTest('valid', function ($value) {
            foreach ((array)$this as $option) {
                if ($option->getValue() == $value) {
                    return true;
                }
            }
            return false;
        });
    }

    public function setIdPrefix($prefix)
    {
        $this->idPrefix = $prefix;
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
        foreach ((array)$this as $option) {
            if ($option->getValue() == $value) {
                $option->selected();
            } else {
                $option->unselected();
            }
        }
    }

    /**
     * This is here to avoid the need to check instanceof Label.
     *
     * @return Element $this
     */
    public function getElement()
    {
        return $this;
    }

    /**
     * Gets or sets the origin of the current value (user input or bound).
     * Normally, you won't need to call this directly since Formulaic handles
     * data binding transparently.
     *
     * @param mixed $status null to get, true or false to set.
     * @return boolean The current status (true for user input, false for
     *                 undefined or bound from a model object).
     */
    public function valueSuppliedByUser($status = null)
    {
        if (isset($status)) {
            $this->userInput = (bool)$status;
        }
        return $this->userInput;
    }
}

