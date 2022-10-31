<?php

namespace Monolyth\Formulaic\Element;

use Monolyth\Formulaic\Validate;
use ArrayObject;
use Monolyth\Formulaic\QueryHelper;
use Monolyth\Formulaic\Bindable;
use Monolyth\Formulaic\JsonSerialize;
use Monolyth\Formulaic\Label;
use JsonSerializable;

class Group extends ArrayObject implements JsonSerializable, Bindable
{
    use Validate\Group;
    use QueryHelper;
    use JsonSerialize;

    const WRAP_GROUP = 1;
    const WRAP_LABEL = 2;
    const WRAP_ELEMENT = 4;

    private array $prefix = [];

    private string $name;

    private array $value = [];

    protected string $htmlBefore;

    protected string $htmlAfter;

    protected int $htmlGroup = 4;

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
            $out .= $this->htmlBefore ?? '';
        }
        foreach ($this as $field) {
            if (is_string($field)) {
                echo $field;
                continue;
            }
            if (isset($this->htmlBefore, $this->htmlAfter) && $this->htmlGroup & self::WRAP_LABEL && $field instanceof Label) {
                $field->wrap($this->htmlBefore ?? '', $this->htmlAfter ?? '');
            }
            if (isset($this->htmlBefore, $this->htmlAfter) && $this->htmlGroup & self::WRAP_ELEMENT) {
                $field->getElement()->wrap($this->htmlBefore ?? '', $this->htmlAfter ?? '');
            }
        }
        $out .= trim(implode('', (array)$this))."\n";
        if ($this->htmlGroup & self::WRAP_GROUP) {
            $out .= $this->htmlAfter ?? '';
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
            if ($field->getElement()->valueSuppliedByUser($status)) {
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

    /**
     * Bind a model to this group.
     *
     * @param object $model
     * @return self
     */
    public function bind(object $model) : self
    {
        foreach ($this as $element) {
            if ($element instanceof Bindable) {
                $element->bind($model);
            }
        }
        return $this;
    }
}

