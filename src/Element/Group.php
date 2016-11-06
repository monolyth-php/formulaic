<?php

namespace Monolyth\Formulaic\Element;

use Monolyth\Formulaic\Validate;
use ArrayObject;
use Monolyth\Formulaic\QueryHelper;
use Monolyth\Formulaic\Bindable;

class Group extends ArrayObject
{
    use Validate\Group;
    use QueryHelper;
    use Bindable;

    const WRAP_GROUP = 1;
    const WRAP_LABEL = 2;
    const WRAP_ELEMENT = 4;

    private $prefix = [];
    private $name;
    private $value = [];
    protected $htmlBefore = null;
    protected $htmlAfter = null;
    protected $htmlGroup = 4;

    public function __construct($name = null, callable $callback)
    {
        if ($name) {
            $this->name = $name;
        }
        $callback($this);
        $this->prefix($name);
    }

    public function prefix($prefix)
    {
        $this->prefix[] = $prefix;
        foreach ((array)$this as $element) {
            $element->prefix($prefix);
        }
        return $this;
    }

    public function setIdPrefix($prefix = null)
    {
        foreach ((array)$this as $element) {
            $element->setIdPrefix($prefix);
        }
    }

    public function name()
    {
        return isset($this->name) ? $this->name : null;
    }

    public function setValue($value)
    {
        if (is_scalar($value) or is_null($value)) {
            return;
        }
        foreach ($value as $name => $val) {
            if ($field = $this[$name]) {
                $field->getElement()->setValue($val);
            }
        }
        return $this;
    }

    public function setDefaultValue($value)
    {
        if (!$this->valueSuppliedByUser()) {
            $this->setValue($value);
        }
        return $this;
    }

    public function & getValue()
    {
        $this->value = [];
        foreach ((array)$this as $field) {
            $this->value[$field->getName()] = $field->getElement()->getValue();
        }
        return $this->value;
    }

    /**
     * Convenience method to keep our interface consistent.
     */
    public function getElement()
    {
        return $this;
    }

    public function __toString()
    {
        $out = '';
        if ($this->htmlGroup & self::WRAP_GROUP) {
            $out .= $this->htmlBefore;
        }
        foreach ((array)$this as $field) {
            if ($this->htmlGroup & self::WRAP_LABEL) {
                $field->wrap($this->htmlBefore, $this->htmlAfter);
            }
            if ($this->htmlGroup & self::WRAP_ELEMENT) {
                $field->getElement()->wrap($this->htmlBefore, $this->htmlAfter);
            }
        }
        $out .= trim(implode("\n", (array)$this));
        if ($this->htmlGroup & self::WRAP_GROUP) {
            $out .= $this->htmlAfter;
        }
        return $out;
    }

    public function valueSuppliedByUser($status = null)
    {
        $is = false;
        foreach ((array)$this as $field) {
            if (isset($status)) {
                $field->getElement()->valueSuppliedByUser($status);
            }
            if ($field->getElement()->valueSuppliedByUser()) {
                $is = true;
            }
        }
        return $is;
    }

    /**
     * Specify HTML to wrap this element in. Sometimes this is needed for
     * fine-grained output control, e.g. when styling checkboxes.
     *
     * @param string $before HTML to prepend.
     * @param string $after HTML to append.
     * @param int $group Bitflag stating what to wrap. Use any of the
     *                       Element\Group::WRAP_* constants. Defaults to
     *                       WRAP_ELEMENT.
     * @return Element $this
     * @see Element::wrap
     */
    public function wrap($before, $after, $group = null)
    {
        if (!isset($group)) {
            $group = self::WRAP_ELEMENT;
        }
        $this->htmlBefore = $before;
        $this->htmlAfter = $after;
        $this->htmlGroup = $group;
        return $this;
    }
}

