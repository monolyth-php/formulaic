<?php

use Monolyth\Formulaic;
use Gentry\Gentry\Wrapper;

$_SERVER['REQUEST_METHOD'] = 'POST';

/** Test element groups */
return function () : Generator {
    /** Groups can contain groups */
    yield function () {
        $_POST = ['foo' => ['bar' => ['baz' => 'fizzbuz']]];
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
        $form[] = new Wrapper(new Formulaic\Element\Group('test', function ($group) {
            $group[] = new Formulaic\Label('dummy', (new Formulaic\Text('foo'))->isRequired());
        }));
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
        $_POST = [];
        $form = new class extends Formulaic\Post {};
        $form[] = new Wrapper((new Formulaic\Checkbox\Group(
            'test',
            [1 => 'foo', 2 => 'bar']
        ))->isRequired());
        assert($form->valid() != true);
        $_POST = ['test' => [1]];
        $form = new class extends Formulaic\Post {};
        $form[] = new Wrapper((new Formulaic\Checkbox\Group(
            'test',
            [1 => 'foo', 2 => 'bar']
        ))->isRequired());
        assert($form->valid());
        assert(trim("$form") == <<<EOT
<form action="" method="post">
<label for="test-1"><input checked id="test-1" name="test[]" type="checkbox" value="1"> foo</label>
<label for="test-2"><input id="test-2" name="test[]" type="checkbox" value="2"> bar</label>
</form>
EOT
        );
    };

    /** Groups of radio buttons */
    yield function () {
        $_POST = [];
        $form = new class extends Formulaic\Post {};
        $form[] = (new Formulaic\Radio\Group(
            'test',
            [1 => 'foo', 2 => 'bar']
        ))->isRequired();
        assert($form->valid() != true);
        $_POST = ['test' => 1];
        $form = new class extends Formulaic\Post {};
        $form[] = new Wrapper((new Formulaic\Radio\Group(
            'test',
            [1 => 'foo', 2 => 'bar']
        ))->isRequired());
        assert($form->valid());
        assert($form['test']->getValue()[0] === 1);
        assert(trim("$form") == <<<EOT
<form action="" method="post">
<label for="test-1"><input checked id="test-1" name="test" required type="radio" value="1"> foo</label>
<label for="test-2"><input id="test-2" name="test" required type="radio" value="2"> bar</label>
</form>
EOT
        );
    };

    /** Element groups can be correctly transformed */
    yield function () {
        $model = new class {
            public int $test;
        };
        $_POST = ['test' => 2];
        $form = new class extends Formulaic\Post {
            public function __construct()
            {
                $this[] = (new Formulaic\Radio\Group('test', [1 => 'foo', 2 => 'bar']))
                    ->withTransformer(fn (array $value) : int => (int)$value[0]);
            }
        };
        $form->bind($model);
        assert($model->test === 2);
    };
};

