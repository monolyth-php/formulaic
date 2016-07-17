<?php

namespace Monolyth\Formulaic\Test;

use Monolyth\Formulaic;

/**
 * Data persistance
 */
class PersistanceTest
{
    /**
     * After setting a value {?} and binding a model {?} the model gets updated
     * if the element changes {?}.
     */
    public function testDataPersists()
    {
        $_POST['name'] = 'Linus';
        $user = new class {
            public $name = 'Marijn';
        };
        yield assert('Marijn' == $user->name);
        $form = new class extends Formulaic\Post {
            public function __construct() {
                $this[] = new Formulaic\Text('name');
            }
        };
        $form->bind($user);
        yield assert('Linus' == $user->name);
        $form['name']->getElement()->setValue('Chuck Norris');
        yield assert('Chuck Norris' == $user->name);
    }
}

