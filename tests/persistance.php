<?php

use Monolyth\Formulaic;
use Gentry\Gentry\Wrapper;

/** Data persistance */
return function () : Generator {
    /** After setting a value and binding a model the model gets updated if the element was posted */
    yield function () {
        $_POST['name'] = 'Linus';
        $user = new class {
            public $name = 'Marijn';
        };
        assert('Marijn' == $user->name);
        $form = new Wrapper(new class extends Formulaic\Post {
            public function __construct() {
                $this[] = new Formulaic\Text('name');
            }
        });
        $form->bind($user);
        assert('Linus' == $user->name);
        $form['name']->getElement()->setValue('Chuck Norris');
        assert('Chuck Norris' == $user->name);
    };
};

