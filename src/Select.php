<?php

namespace Monolyth\Formulaic;

use ArrayObject;
use DomainException;
use Stringable;

class Select extends ArrayObject implements Labelable, Testable, Bindable
{
    use Attributes;
    use Element\Identify;
    use Element\Wrap;
    use Validate\Test;
    use Validate\Required;
    use Validate\Element;
    use Select\Tostring;
    use Transform;
    use Normalize;

    private $userInput = false;
    protected $value;
    protected $name;
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
     * @return self
     */
    public function setIdPrefix($prefix) : self
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
     * @param string|Stringable|array|ArrayObject $value
     * @return self
     * @throws DomainException if passed an array without the "multiple"
     *  attribute being set to true
     */
    public function setValue(string|Stringable|array|ArrayObject $value = null) : self
    {
        if ((!isset($this->attributes['multiple']) || !$this->attributes['multiple'])
            && (is_array($value) || $value instanceof ArrayObject)
        ) {
            throw new DomainException("Select::setValue only accepts array when the multiple attribute is true");
        }
        $this->value = $this->transform($value);
        foreach ((array)$this as $option) {
            if ($option->getValue() == $value) {
                $option->selected();
            } else {
                $option->unselected();
            }
        }
        return $this;
    }

    /**
     * This is here to avoid the need to check instanceof Label.
     *
     * @return self
     */
    public function getElement() : self
    {
        return $this;
    }

    /**
     * Gets or sets the origin of the current value (user input or bound).
     * Normally, you won't need to call this directly since Formulaic handles
     * data binding transparently.
     *
     * @param bool|null $status null (empty) to get, true or false to set
     * @return bool The current status (true for user input, false for
     *  undefined or bound from a model object)
     */
    public function valueSuppliedByUser(bool $status = null) : bool
    {
        return $this->userInput = $status ?? $this->userInput;
    }

    public function bind(object $model) : self
    {
        $name = self::normalize($this->name());
        if ($this->valueSuppliedByUser()) {
            try {
                $model->$name = $this->transform($this->getValue());
            } catch (TypeError $e) {
                throw new TransformerRequiredException($model, $name, $value);
            }
        } else {
            $this->setValue($this->transform($model->$name));
        }
        return $this;
    }
}

