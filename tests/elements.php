<?php

use Monolyth\Formulaic;

/**
 * Element-specific tests
 */
return function () : Generator {
    /**
     * Elements without conditions are always valid, whilst required
     * elements must have a value or validation will fail
     */
    yield function () {
        $input = new Formulaic\Text('test');
        assert($input->valid());

        // Required:
        $input->isRequired();
        $input->setValue('foo');
        assert($input->valid());
        $input->setValue(null);
        assert($input->valid() != true);
    };

    /** Generic buttons */
    yield function () {
        $button = new Formulaic\Button('B');
        assert("$button" == '<button type="button">B</button>');
    };

    /** Reset buttons */
    yield function () {
        $button = new Formulaic\Button\Reset('B');
        assert("$button" == '<button type="reset">B</button>');
    };

    /** Submit buttons */
    yield function () {
        $button = new Formulaic\Button\Submit('B');
        assert("$button" == '<button type="submit">B</button>');
    };

    /** Checkboxes */
    yield function () {
        $input = new Formulaic\Checkbox('test');
        assert("$input" == '<input id="test-1" name="test" type="checkbox" value="1">');
    };

    /** Checkbox groups */
    yield function () {
        $out = <<<EOT
<div>
<label for="test-1"><input id="test-1" name="test[]" type="checkbox" value="1"> Option 1</label>
<label for="test-2"><input id="test-2" name="test[]" type="checkbox" value="2"> Option 2</label>
</div>
EOT;
        $group = new Formulaic\Checkbox\Group(
            'test',
            [
                1 => 'Option 1',
                2 => 'Option 2',
            ]
        );
        assert("$group" == $out);
    };

    /** Dates */
    yield function () {
        $input = new Formulaic\Date('test');
        assert("$input" == '<input id="test" name="test" type="date">');
        $input->setMin('2010-01-01')->setMax('2012-01-01');
        $input->setValue('2009-01-01');
        assert($input->valid() != true);
        $input->setValue('2013-01-01');
        assert($input->valid() != true);
        $input->setValue('2011-01-01');
        assert($input->valid());
        $input->setValue('something illegal');
        assert($input->valid() != true);
    };

    /** Datetime */
    yield function () {
        $input = new Formulaic\Datetime('test');
        assert("$input" == '<input id="test" name="test" type="datetime">');
        $input->setMin('2009-01-01 12:00:00')->setMax('2009-01-02 12:00:00');
        $input->setValue('2009-01-01');
        assert($input->valid() != true);
        $input->setValue('2009-01-02 13:00:00');
        assert($input->valid() != true);
        $input->setValue('2009-01-01 13:59:00');
        assert($input->valid());
        $input->setValue('something illegal');
        assert($input->valid() != true);
    };

    /** Email */
    yield function () {
        $input = new Formulaic\Email('test');
        assert("$input" == '<input id="test" name="test" type="email">');
        $input->setValue('not an email');
        assert($input->valid() != true);
        $input->setValue('foo@bar.com');
        assert($input->valid());
    };

    /** Files */
    yield function () {
        $input = new Formulaic\File('test');
        assert("$input" == '<input id="test" name="test" type="file">');
    };

    /** Hidden inputs */
    yield function () {
        $input = new Formulaic\Hidden('test');
        assert("$input" == '<input id="test" name="test" type="hidden">');
    };

    /** Numbers */
    yield function () {
        $input = new Formulaic\Number('test');
        $input->setValue('42');
        assert("$input" == '<input id="test" name="test" step="1" type="number" value="42">');
        $input->setValue('foo');
        assert($input->valid() != true);
        $input->setMin(9);
        $input->setValue(8);
        assert($input->valid() != true);
        $input->setValue(15);
        assert($input->valid());
        $input->setMax(14);
        assert($input->valid() != true);
        $input->setValue(13);
        assert($input->valid());
        $input->setStep(2);
        assert($input->valid());
        $input->setValue(12);
        assert($input->valid() != true);
        $input->setStep(.5);
        $input->setValue(12.5);
        assert($input->valid());
        $input->setValue(12.4);
        assert($input->valid() != true);
    };

    /** Passwords */
    yield function () {
        $input = new Formulaic\Password('test');
        $input->setValue('secret');
        assert("$input" == '<input id="test" name="test" type="password">');
    };

    /** Radio buttons */
    yield function () {
        $input = new Formulaic\Radio('test');
        assert("$input" == '<input id="test" name="test" type="radio" value="1">');
    };

    /** Search boxes */
    yield function () {
        $input = new Formulaic\Search('test');
        assert("$input" == '<input id="test" name="test" type="search">');
    };

    /** Simple select boxes */
    yield function () {
        $input = new Formulaic\Select('test', [1 => 'foo', 2 => 'bar']);
        assert("$input" == <<<EOT
<select id="test" name="test">
<option value="1">foo</option>
<option value="2">bar</option>
</select>
EOT
        );
    };

    /** Manually built select boxes */
    yield function () {
        $input = new Formulaic\Select('test', function ($select) {
            $select[] = new Formulaic\Select\Option(1, 'foo');
            $select[] = new Formulaic\Select\Option(2, 'bar');
        });
        assert("$input" == <<<EOT
<select id="test" name="test">
<option value="1">foo</option>
<option value="2">bar</option>
</select>
EOT
        );
    };
    
    /** Telephone numbers */
    yield function () {
        $input = new Formulaic\Tel('test');
        $input->setValue('612345678');
        assert("$input" == '<input id="test" name="test" type="tel" value="0612345678">');
        $input->setValue('foo');
        assert($input->valid() != true);
    };

    /** Text elements with HTML encoded values */
    yield function () {
        $input = new Formulaic\Text('test');
        $input->setValue('"');
        assert("$input" == '<input id="test" name="test" type="text" value="&quot;">');
    };

    /** Textareas with HTML encoded values */
    yield function () {
        $input = new Formulaic\Textarea('test');
        $input->setValue('"');
        assert("$input" == '<textarea id="test" name="test">&quot;</textarea>');
    };

    /** Time elements */
    yield function () {
        $input = new Formulaic\Time('test');
        $input->setValue('bla');
        assert($input->valid() != true);
        $input->setValue('12:00:00');
        assert($input->valid());
        assert("$input" == '<input id="test" name="test" type="time" value="12:00:00">');

        // Test require past time
        $input = new Formulaic\Time('test');
        $input->isInPast();
        $input->setValue(time() + 100);
        assert($input->valid() != true);
        $input->setValue(time() - 100);
        assert($input->valid());

        // Test require future time
        $input = new Formulaic\Time('test');
        $input->isInFuture();
        $input->setValue(time() - 100);
        assert($input->valid() != true);
        $input->setValue(time() + 100);
        assert($input->valid());
    };

    /** URLs */
    yield function () {
        $input = new Formulaic\Url('test');
        $input->setValue('not an url');
        assert($input->valid() != true);
        $input2 = new Formulaic\Url('test');
        $input2->setValue('http://google.com');
        assert($input2->valid());
        assert("$input\n$input2" == <<<EOT
<input id="test" name="test" placeholder="http://" type="url" value="http://not an url">
<input id="test" name="test" placeholder="http://" type="url" value="http://google.com">
EOT
        );
    };
};

