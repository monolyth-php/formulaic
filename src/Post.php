<?php

namespace Formulaic;

abstract class Post extends Form
{
    protected $attributes = ['method' => 'post'];

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->addSource($_POST);
    }

    public function cancelled()
    {
        return isset($_POST['act_cancel']);
    }
}
