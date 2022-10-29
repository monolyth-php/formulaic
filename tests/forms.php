<?php

use Gentry\Gentry\Wrapper;
use Monolyth\Formulaic\Get;
use Monolyth\Formulaic\Post;
use Monolyth\Formulaic\Text;
use Monolyth\Formulaic\Button\Submit;
use Monolyth\Formulaic\Fieldset;
use Monolyth\Formulaic\File;
use Monolyth\Formulaic\Search;
use Monolyth\Formulaic\Element\Group;
use Monolyth\Formulaic\Label;
use Monolyth\Formulaic\Checkbox;
use Monolyth\Formulaic\Radio;

$_SERVER['REQUEST_METHOD'] = 'POST';

/** Global form tests */
return function () : Generator {
    /** A basic form without any elements should render just the form tags */
    yield function () {
        $form = new Wrapper(new class extends Get {});
        assert("$form" == '<form action="" method="get">
</form>');
    };

    /** A basic form with input and button should render correctly */
    yield function () {
        $out = <<<EOT
<form action="" method="get">
<input id="test" name="test" type="text">
<button type="submit">go</button>
</form>
EOT;
        $form = new Wrapper(new class extends Get {});
        $form[] = new Text('test');
        $form[] = new Submit('go');
        assert("$form" == $out);
    };

    /** Forms can also have fieldsets */
    yield function () {
        $out = <<<EOT
<form action="" method="get">
<fieldset>
<legend>Hello world!</legend>
<input id="test" name="test" type="text">
</fieldset>
</form>
EOT;
        $form = new Wrapper(new class extends Get {});
        $form[] = new Fieldset('Hello world!', function($fieldset) {
            $fieldset[] = new Text('test');
        });
        assert("$form" === $out);
    };

    /** Fields in a form can be referenced by name */
    yield function () {
        $form = new Wrapper(new class extends Get {});
        $form[] = new Text('mytextfield');
        assert($form['mytextfield'] instanceof Text);
    };

    /** Forms can be of type POST */
    yield function () {
        $form = new Wrapper(new class extends Post {});
        assert("$form" == '<form action="" method="post">
</form>');
    };

    /** Post forms can contain files */
    yield function () {
        $out = <<<EOT
<form action="" enctype="multipart/form-data" method="post">
<input id="test" name="test" type="file">
</form>
EOT;
        $form = new Wrapper(new class extends Post {});
        $form[] = new File('test');
        assert("$form" == $out);
    };

    /** Named forms cause elements to inherit the name */
    yield function () {
        $out = <<<EOT
<form action="" id="test" method="get">
<input id="test-bla" name="bla" type="text">
</form>
EOT;
        $form = new Wrapper(new class extends Get {
            protected array $attributes = ['id' => 'test'];
        });
        $form[] = new Text('bla');
        assert("$form" == $out);
    };

    /** $_GET auto-populates a GET form */
    yield function () {
        $_GET['q'] = 'query';
        $form = new Wrapper(new class Extends Get {});
        $form[] = new Search('q');
        assert('query' ==  $form['q']->getValue());
    };

    /** $_POST auto-populates a POST form */
    yield function () {
        $_POST['q'] = 'query';
        $form = new Wrapper(new class extends Post {});
        $form[] = new Search('q');
        assert('query' == $form['q']->getValue());
    };

    /** Groups also get auto-populated */
    yield function () {
        $_POST['foo'] = ['bar' => 'baz'];
        $form = new Wrapper(new class extends Post {});
        $form[] = new Group('foo', function($group) {
            $group[] = new Text('bar');
        });
        assert('baz' == $form['foo']['bar']->getValue());
    };

    /** Forms with conditions validate correctly */
    yield function () {
        $_POST = [];
        $form = new Wrapper(new class extends Post {});
        $form[] = (new Text('foo'))->isRequired();
        $form[] = (new Text('bar'))->isRequired();
        assert($form->valid() != true);
        assert($form->errors() == [
            'foo' => ['required'],
            'bar' => ['required'],
        ]);
        $_POST = ['foo' => 1, 'bar' => 2];
        $form = new Wrapper(new class extends Post {});
        $form[] = (new Text('foo'))->isRequired();
        $form[] = (new Text('bar'))->isRequired();
        assert($form->valid());
        assert(1 == $form['foo']->getValue());
        assert(2 == $form['bar']->getValue());
    };

    /** More complex forms also get filled correctly */
    yield function () {
        $_POST = [];
        $theform = function () {
            return new Wrapper(new class extends Post {
                public function __construct()
                {
                    $this[] = new Label(
                        'Test',
                        (new Text('foo'))->isRequired()
                    );
                    $this[] = new Label(
                        'Group of radio buttons',
                        (new Radio\Group('radios', [1 => 'foo', 2 => 'bar']))
                    );
                    $this[] = new Label(
                        'Group of checkboxes',
                        (new Checkbox\Group(
                            'checkboxes',
                            [1 => 'foo', 2 => 'bar', 3 => 'baz']
                        ))
                    );
                }
            });
        };
        $form = $theform();
        assert($form->valid() !== true);
        $_POST = ['foo' => 'Foo', 'radios' => 1, 'checkboxes' => [2, 3]];
        $form = $theform();
        assert($form->valid() === true);
    };
};

