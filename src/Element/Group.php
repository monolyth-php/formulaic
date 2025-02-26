<?php

namespace Monolyth\Formulaic\Element;

use Monolyth\Formulaic\{ Validate, QueryHelper, Bindable, JsonSerialize, Label, Transform, ContainsFile };
use ArrayObject;
use JsonSerializable;
use Stringable;

class Group extends ArrayObject implements JsonSerializable, Bindable, Stringable
{
    use Validate\Group;
    use QueryHelper;
    use JsonSerialize;
    use Transform;
    use ContainsFile;

    const WRAP_GROUP = 1;
    const WRAP_LABEL = 2;
    const WRAP_ELEMENT = 4;

    private array $prefix = [];

    private mixed $value;

    protected string $htmlBefore;

    protected string $htmlAfter;

    protected int $htmlGroup = 4;

    /**
     * Constructor.
     *
     * @param string|null $name Name of the group. Note that form elements are
     *  also prefixed with this name (if you don't want that, don't group
     *  them!). The exception is for fieldsets.
     * @param callable $callback Will be called with the new group as argument,
     *  so you can populate it.
     */
    public function __construct(private ?string $name, callable $callback)
    {
        $callback($this);
        if (isset($name)) {
            $this->prefix($name);
        }
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
    public function setIdPrefix(?string $prefix = null)
    {
        foreach ($this as $element) {
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
     * Set a group of values on elements in this group. Call with a hash of
     * object key/value pairs, where the keys must match element names.
     *
     * @param array $value Type hinted as mixed, but must really be an array
     * @return Monolyth\Formulaic\Element\Group
     */
    public function setValue(mixed $value) : self
    {
        if (!is_array($value)) {
            return $this;
        }
        foreach ($this as $element) {
            if (isset($value[$element->name()])) {
                $element->setValue($value[$element->name()]);
            }
        }
        return $this;
    }

    /**
     * Returns a hash of key/value pairs for all elements in this group,
     * or something else if a transformer was set.
     *
     * @return array
     */
    public function getValue() : array
    {
        $value = [];
        foreach ((array)$this as $field) {
            $value[$field->name()] = $field->getElement()->getValue();
        }
        return $value;
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
                $field->wrap($this->htmlBefore, $this->htmlAfter);
            }
            if (isset($this->htmlBefore, $this->htmlAfter) && $this->htmlGroup & self::WRAP_ELEMENT) {
                $field->getElement()->wrap($this->htmlBefore, $this->htmlAfter);
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
    public function wrap(string $before, string $after, int $group = null) : self
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
        $name = $this->name();
        $value = $this->getValue();
        $transformed = $this->transform($value);
        if ($value !== $transformed && $this->valueSuppliedByUser()) {
            $model->$name = $transformed;
        } else {
            foreach ($this as $element) {
                if ($element instanceof Bindable && isset($model->$name) && is_object($model->$name)) {
                    $element->bind($model->$name);
                }
            }
        }
        return $this;
    }
}

