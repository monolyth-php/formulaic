<?php

use Monolyth\Formulaic;
use Gentry\Gentry\Wrapper;

/** Test bitflags and complex bindings */
return function () : Generator {
    /** Assert POSTed values are properly persisted and bound */
    yield function () {
        $_POST['superhero'] = ['superman'];
        $form = new Wrapper(new class() extends Formulaic\Post {
            public function __construct()
            {
                $this[] = new Formulaic\Bitflag('superhero', [
                    'batman' => 'Batman',
                    'superman' => 'Superman',
                    'spiderman' => 'Spiderman',
                    'hulk' => 'The Hulk',
                    'daredevil' => 'Daredevil',
                ]);
            }
        });
        $binder = (object)['superhero' => (object)['batman' => true]];
        $form->bind($binder);
        assert($binder->superhero->batman === false);
        assert($binder->superhero->superman === true);
        assert($binder->superhero->spiderman === false);
    };
};

