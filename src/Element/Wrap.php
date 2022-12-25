<?php

namespace Monolyth\Formulaic\Element;

trait Wrap
{
    protected string $htmlBefore;

    protected string $htmlAfter;

    /**
     * Specify HTML to wrap this element in. Sometimes this is needed for
     * fine-grained output control, e.g. when styling checkboxes.
     *
     * @param string $before HTML to prepend
     * @param string $after HTML to append
     * @return self
     */
    public function wrap(string $before, string $after) : self
    {
        $this->htmlBefore = $before;
        $this->htmlAfter = $after;
        return $this;
    }
}

