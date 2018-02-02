<?php

use Monolyth\Formulaic;

/** Test element groups */
return function () : Generator {
    /** Groups can contain groups */
    yield function () {
        $_POST['foo'] = ['bar' => ['baz' => 'fizzbuz']];
        $form = new class extends Formulaic\Post {};
        $form->attribute('id', 'test');
        $form[] = new Formulaic\Element\Group('foo', function ($group) {
            $group[] = new Formulaic\Element\Group('bar', function ($group) {
                $group[] = new Formulaic\Text('baz');
            });
        });
        assert('fizzbuz' == $form['foo']['bar']['baz']->getValue());
        assert("$form" == <<<EOT
<form action="" id="test" method="post">
<input id="test-foo-bar-baz" name="foo[bar][baz]" type="text" value="fizzbuz">
</form>
EOT
        );
    };

    /** Labels in groups cascade the group name to their elements */
    yield function () {
        $form = new class extends Formulaic\Post {};
        $form[] = new Formulaic\Element\Group('test', function ($group) {
            $group[] = new Formulaic\Label('dummy', (new Formulaic\Text('foo'))->isRequired());
        });
        assert("$form" == <<<EOT
<form action="" method="post">
<label for="test-foo">dummy</label>
<input id="test-foo" name="test[foo]" required type="text">
</form>
EOT
        );
    };

    /** Groups of checkboxes */
    yield function () {
        $form = new class extends Formulaic\Post {};
        $form[] = (new Formulaic\Checkbox\Group(
            'test',
            [1 => 'foo', 2 => 'bar']
        ))->isRequired();
        assert($form->valid() != true);
        $_POST['test'] = [1];
        $form = new class extends Formulaic\Post {};
        $form[] = (new Formulaic\Checkbox\Group(
            'test',
            [1 => 'foo', 2 => 'bar']
        ))->isRequired();
        assert($form->valid());
        assert("$form" == <<<EOT
<form action="" method="post">
<div>
<label for="test-1"><input checked id="test-1" name="test[]" type="checkbox" value="1"> foo</label>
<label for="test-2"><input id="test-2" name="test[]" type="checkbox" value="2"> bar</label>
</div>
</form>
EOT
        );
    };

    /** Groups of radio buttons */
    yield function () {
        $form = new class extends Formulaic\Post {};
        $form[] = (new Formulaic\Radio\Group(
            'test',
            [1 => 'foo', 2 => 'bar']
        ))->isRequired();
        assert($form->valid() != true);
        $_POST['test'] = 1;
        $form = new class extends Formulaic\Post {};
        $form[] = (new Formulaic\Radio\Group(
            'test',
            [1 => 'foo', 2 => 'bar']
        ))->isRequired();
        assert($form->valid());
        assert($form['test']->getValue()->__toString() === "1");
        assert("$form" == <<<EOT
<form action="" method="post">
<div>
<label for="test-1"><input checked id="test-1" name="test" required="1" type="radio" value="1"> foo</label>
<label for="test-2"><input id="test-2" name="test" required="1" type="radio" value="2"> bar</label>
</div>
</form>
EOT
        );
    };

    /** Bitflags */
    yield function () {
        $bit = new Formulaic\Bitflag('superhero', [
            1 => 'Batman',
            2 => 'Superman',
            4 => 'Spiderman',
            8 => 'The Hulk',
            16 => 'Daredevil',
        ]);
        $bit->setValue([1, 2, 4]);
        assert($bit['Batman']->getElement()->checked());
        assert($bit[1]->getElement()->checked());
        assert($bit[2]->getElement()->checked());
        assert(!$bit[3]->getElement()->checked());
        assert(!$bit[4]->getElement()->checked());
    };

    /** Non-supplied bitflags are left alone */
    yield function () {
        $bit = new Formulaic\Bitflag('superhero', [
            'spidey' => 'Spiderman',
            'hulk' => 'The Hulk',
            'daredevil' => 'Daredevil',
        ]);
        $bit->setDefaultValue(['superman']);
        $bit->setValue(['hulk']);
        assert(in_array('hulk', (array)$bit->getValue()));
        assert(!in_array('superman', (array)$bit->getValue()));
    };
};

