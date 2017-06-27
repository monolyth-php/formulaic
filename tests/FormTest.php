<?php

namespace Monolyth\Formulaic\Test;

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

/**
 * Global form tests
 */
class FormTest
{
    /**
     * A basic form without any elements should render just the form tags.
     */
    public function testEmptyForm()
    {
        $form = new class extends Get {};
        yield assert("$form" == '<form action="" method="get"></form>');
    }

    /**
     * A basic form with input and button should render correctly.
     */
    public function testFormWithInputAndButton()
    {
        $out = <<<EOT
<form action="" method="get">
<div><input id="test" name="test" type="text"></div>
<div><button type="submit"></button></div>
</form>
EOT;
        $form = new class extends Get {};
        $form[] = new Text('test');
        $form[] = new Submit;
        yield assert("$form" == $out);
    }

    /**
     * Forms can also have fieldsets.
     */
    public function testFormWithFieldset()
    {
        $out = <<<EOT
<form action="" method="get">
<fieldset>
<legend>Hello world!</legend>
<div><input id="test" name="test" type="text"></div>
</fieldset>
</form>
EOT;
        $form = new class extends Get {};
        $form[] = new Fieldset('Hello world!', function($fieldset) {
            $fieldset[] = new Text('test');
        });
        yield assert("$form" == $out);
    }

    /**
     * Fields in a form can be referenced by name.
     */
    public function testReferenceByName()
    {
        $form = new class extends Get {};
        $form[] = new Text('mytextfield');
        yield assert($form['mytextfield'] instanceof Text);
    }

    /**
     * Forms can be of type POST.
     */
    public function testPostForm()
    {
        $form = new class extends Post {};
        yield assert("$form" == '<form action="" method="post"></form>');
    }

    /**
     * Post forms can contain files.
     */
    public function testPostFormWithFile()
    {
        $out = <<<EOT
<form action="" enctype="multipart/form-data" method="post">
<div><input id="test" name="test" type="file"></div>
</form>
EOT;
        $form = new class extends Post {};
        $form[] = new File('test');
        yield assert("$form" == $out);
    }

    /**
     * Named forms cause elements to inherit the name.
     */
    public function testNamedFormInherits()
    {
        $out = <<<EOT
<form action="" id="test" method="get">
<div><input id="test-bla" name="bla" type="text"></div>
</form>
EOT;
        $form = new class extends Get {
            protected $attributes = ['id' => 'test'];
        };
        $form[] = new Text('bla');
        yield assert("$form" == $out);
    }

    /**
     * $_GET auto-populates a GET form.
     */
    public function testPopulateGet()
    {
        $_GET['q'] = 'query';
        $form = new class Extends Get {};
        $form[] = new Search('q');
        yield assert('query' ==  $form['q']->getValue());
    }

    /**
     * $_POST auto-populates a POST form.
     */
    public function testPopulatePost()
    {
        $_POST['q'] = 'query';
        $form = new class extends Post {};
        $form[] = new Search('q');
        yield assert('query' == $form['q']->getValue());
    }

    /**
     * Groups also get auto-populated.
     */
    public function testPopulateGrouped()
    {
        $_POST['foo'] = ['bar' => 'baz'];
        $form = new class extends Post {};
        $form[] = new Group('foo', function($group) {
            $group[] = new Text('bar');
        });
        yield assert('baz' == $form['foo']['bar']->getValue());
    }

    /**
     * Forms with conditions validate correctly.
     */
    public function testErrors()
    {
        $_POST = [];
        $form = new class extends Post {};
        $form[] = (new Text('foo'))->isRequired();
        $form[] = (new Text('bar'))->isRequired();
        yield assert($form->valid() != true);
        yield assert($form->errors() == [
            'foo' => ['required'],
            'bar' => ['required'],
        ]);
        $_POST = ['foo' => 1, 'bar' => 2];
        $form = new class extends Post {};
        $form[] = (new Text('foo'))->isRequired();
        $form[] = (new Text('bar'))->isRequired();
        yield assert($form->valid());
        yield assert(1 == $form['foo']->getValue());
        yield assert(2 == $form['bar']->getValue());
    }

    /**
     * More complex forms also get filled correctly.
     */
    public function testComplexForm()
    {
        $_POST = [];
        $form = new class extends Post {
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
        };
        yield assert($form->valid() != true);
        $_POST = ['foo' => 'Foo', 'radios' => 1, 'checkboxes' => [2, 3]];
        $form = new class extends Post {
            public function __construct()
            {
                $this[] = new Fieldset('Test', function ($fieldset) {
                    $fieldset[] = new Label(
                        'Test',
                        (new Text('foo'))->isRequired()
                    );
                    $fieldset[] = new Label(
                        'Group of radio buttons',
                        (new Radio\Group('radios', [1 => 'foo', 2 => 'bar']))
                    );
                    $fieldset[] = new Label(
                        'Group of checkboxes',
                        (new Checkbox\Group(
                            'checkboxes',
                            [1 => 'foo', 2 => 'bar', 3 => 'baz']
                        ))
                    );
                });
            }
        };
        yield assert($form->valid());
    }
}

