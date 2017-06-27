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
        yield assert('fizzbuz' == $form['foo']['bar']['baz']->getValue());
        yield assert("$form" == <<<EOT
<form action="" id="test" method="post">
<input id="test-foo-bar-baz" name="foo[bar][baz]" type="text" value="fizzbuz">
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
        yield assert($form->valid() != true);
        $_POST['test'] = [1];
        $form = new class extends Formulaic\Post {};
        $form[] = (new Formulaic\Checkbox\Group(
            'test',
            [1 => 'foo', 2 => 'bar']
        ))->isRequired();
        yield assert($form->valid());
        yield assert("$form" == <<<EOT
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
        yield assert($form->valid() != true);
        $_POST['test'] = 1;
        $form = new class extends Formulaic\Post {};
        $form[] = (new Formulaic\Radio\Group(
            'test',
            [1 => 'foo', 2 => 'bar']
        ))->isRequired();
        yield assert($form->valid());
        yield assert("$form" == <<<EOT
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
        yield assert($bit['Batman']->getElement()->checked());
        yield assert($bit[1]->getElement()->checked());
        yield assert($bit[2]->getElement()->checked());
        yield assert(!$bit[3]->getElement()->checked());
        yield assert(!$bit[4]->getElement()->checked());
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
        yield assert($bit->getValue()->hulk);
        yield assert(!isset($bit->getValue()->superman));
    };
};

