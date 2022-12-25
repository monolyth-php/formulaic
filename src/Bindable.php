<?php

namespace Monolyth\Formulaic;

/**
 * Interface indicating an element is bindable.
 */
interface Bindable
{
    /**
     * Binds the element to a model.
     *
     * @param object $model The model to bind to.
     * @return self
     */
    public function bind(object $model) : self;
}

