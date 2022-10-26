<?php

namespace Monolyth\Formulaic\Element;

use Monolyth\Formulaic\Validate;
use ArrayObject;
use Monolyth\Formulaic\QueryHelper;
use Monolyth\Formulaic\Bindable;
use Monolyth\Formulaic\JsonSerialize;
use JsonSerializable;

class Group extends ArrayObject implements JsonSerializable
{
    use Validate\Group;
    use QueryHelper;
    use Bindable;
    use JsonSerialize;

    const WRAP_GROUP = 1;
    const WRAP_LABEL = 2;
    const WRAP_ELEMENT = 4;

    private $prefix = [];
    private $name;
    private $value = [];
    /** @var string */
    protected $htmlBefore = '';
    /** @var string */
    protected $htmlAfter = '';
    /** @var int */
    protected $htmlGroup = 4;

    /**
     * Constructor.
     *
     * @param string $name Name of the group. Note that form elements are also
     *  prefixed with this name (if you don't want that, don't group them!).
     * @param callable $callback Will be called with the new group as argument,
     *  so you can populate it.
     */
    public function __construct(string $name, callable $callback)
    {
        $this->name = $name;
        $callback($this);
        $this->prefix($name);
    }

    /**
     * Sets the prefix for this group (a.k.a. the name). Useful if your group
     * requires multiple prefixes.
     *
     * @param string $prefix
     */
    public function prefix(string $prefix)
    {
        $this->prefix[] = $prefix;
        foreach ((array)$this as $element) {
            if (is_object($element)) {
                $element->prefix($prefix);
            }
        }
        return $this;
    }

    /**
     * Sets the ID prefix for this group. The ID prefix is optionally prefixed
     * to all generated IDs to resolve ambiguity.
     *
     * @param string $prefix The prefix. Set to `null` to remove.
     */
    public function setIdPrefix(string $prefix = null)
    {
        foreach ((array)$this as $element) {
            if (is_object($element)) {
                $element->setIdPrefix($prefix);
            }
        }
    }

    /**
     * Returns the name of the group. For groups with multiple prefixes, all
     * additional prefixes are ignored.
     *
     * @return string
     */
    public function name() : string
    {
        return $this->name;
    }

    /**
     * Set a group of values on elements in this group. Call with a hash or
     * object key/value pairs, where the keys must match element names.
     *
     * @param mixed $value
     * @return Monolyth\Formulaic\Element\Group
     */
    public function setValue($value) : self
    {
        if (is_scalar($value) or is_null($value)) {
            return $this;
        }
        foreach ($value as $name => $val) {
            if ($field = $this[$name]) {
                $field->getElement()->setValue($val);
            }
        }
        return $this;
    }

    /**
     * Set a group of values on elements in this group as defaults. Call with a
     * hash or object key/value pairs, where the keys must match element names.
     * If any element is supplied by the user, it is ignored.
     *
     * @param iterable $value
     * @return self
     */
    public function setDefaultValue($value)
    {
        if (!$this->valueSuppliedByUser()) {
            $this->setValue($value);
        }
        return $this;
    }

    /**
     * Returns a hash of key/value pairs for all elements in this group.
     *
     * @return array
     */
    public function getValue() : object
    {
        $this->value = [];
        foreach ((array)$this as $field) {
            $this->value[$field->name()] = $field->getElement()->getValue();
        }
        return new ArrayObject($this->value);
    }

    /**
     * Convenience method to keep our interface consistent.
     *
     * @return self
     */
    public function getElement() : Group
    {
        return $this;
    }

    /**
     * `__toString` this group.
     *
     * @return string
     */
    public function __toString() : string
    {
        $out = '';
        if ($this->htmlGroup & self::WRAP_GROUP) {
            $out .= $this->htmlBefore;
        }
        foreach ((array)$this as $field) {
            if (is_string($field)) {
                continue;
            }
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

    /**
     * Mark the entire group as set by the user.
     *
     * @param bool $status Optional setter. Omit if you just want to query the
     *  current status.
     * @return bool
     */
    public function valueSuppliedByUser(bool $status = null) : bool
    {
        $is = false;
        foreach ((array)$this as $field) {
            if (is_string($field)) {
                continue;
            }
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
     * @return self
     * @see Element::wrap
     */
    public function wrap(string $before, string $after, int $group = null) : Group
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

