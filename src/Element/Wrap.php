<?php

namespace Monolyth\Formulaic\Element;

use Monolyth\Formulaic\Testable;

trait Wrap
{
    protected $htmlBefore = null;
    protected $htmlAfter = null;

    /**
     * Specify HTML to wrap this element in. Sometimes this is needed for
     * fine-grained output control, e.g. when styling checkboxes.
     *
     * @param string $before HTML to prepend.
     * @param string $after HTML to append.
     * @return self
     */
    public function wrap(string $before, string $after) : Testable
    {
        $this->htmlBefore = $before;
        $this->htmlAfter = $after;
        return $this;
    }
}

