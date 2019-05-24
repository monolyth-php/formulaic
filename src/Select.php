<?php

namespace Monolyth\Formulaic;

use ArrayObject;
use DomainException;

class Select extends ArrayObject implements Labelable, Testable
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

    /**
     * Constructor.
     *
     * @param string $name Name of the element.
     * @param array|callable $options Hash of options (value/label), or a
     *  callable which receives the new Select element as its parameter.
     * @return void
     */
    public function __construct(string $name, $options)
    {
        if (isset($name)) {
            $this->attributes['name'] = $name;
        }
        if (is_callable($options)) {
            $options($this);
        } elseif (is_array($options)) {
            foreach ($options as $value => $txt) {
                $option = new Select\Option($value, $txt);
                $this[] = $option;
            }
        } else {
            throw new DomainException('$options must be either a hash or a callable.');
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

    /**
     * Set the prefix for this element.
     *
     * @param string $prefix
     * @return Monolyth\Formulaic\Select
     */
    public function setIdPrefix($prefix) : Select
    {
        $this->idPrefix = $prefix;
        return $this;
    }

    /**
     * Return the current value of the element.
     *
     * @return string
     */
    public function getValue() : string
    {
        return "{$this->value}";
    }

    /**
     * Set the value of the element.
     *
     * @param string|null $value
     * @return void
     */
    public function setValue(string $value = null) : void
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
     * @return Monolyth\Formulaic\Select $this
     */
    public function getElement() : Select
    {
        return $this;
    }

    /**
     * Gets or sets the origin of the current value (user input or bound).
     * Normally, you won't need to call this directly since Formulaic handles
     * data binding transparently.
     *
     * @param bool|null $status null to get, true or false to set.
     * @return bool The current status (true for user input, false for
     *  undefined or bound from a model object).
     */
    public function valueSuppliedByUser(bool $status = null) : bool
    {
        if (isset($status)) {
            $this->userInput = (bool)$status;
        }
        return $this->userInput;
    }
}

