<?php

use Monolyth\Formulaic;
use Gentry\Gentry\Wrapper;

/**
 * Element-specific tests
 */
return function () : Generator {
    /** Basic elements */
    yield function () : Generator {
        $input = new Wrapper(new Formulaic\Text('test'));
        /** Elements without conditions are always valid */
        yield function () use ($input) {
            assert($input->valid());
        };

        $input->isRequired();
        /** Required elements without a value are rejected */
        yield function () use ($input) {
            $input->setValue(null);
            assert($input->valid() != true);
        };
        /** Required elements with a value are accepted */
        yield function () use ($input) {
            $input->setValue('foo');
            assert($input->valid());
        };
    };

    /** Buttons */
    yield function () : Generator {
        /** Generic buttons render */
        yield function () {
            $button = new Wrapper(new Formulaic\Button('B'));
            assert(trim("$button") == '<button type="button">B</button>');
        };

        /** Reset buttons render */
        yield function () {
            $button = new Wrapper(new Formulaic\Button\Reset('B'));
            assert(trim("$button") == '<button type="reset">B</button>');
        };

        /** Submit buttons render */
        yield function () {
            $button = new Wrapper(new Formulaic\Button\Submit('B'));
            assert(trim("$button") == '<button type="submit">B</button>');
        };
    };

    /** Checkboxes */
    yield function () {
        $input = new Wrapper(new Formulaic\Checkbox('test'));
        assert(trim("$input") == '<input id="test-1" name="test" type="checkbox" value="1">');
    };

    /** Checkbox groups */
    yield function () {
        $out = <<<EOT
<label for="test-1"><input id="test-1" name="test[]" type="checkbox" value="1"> Option 1</label>
<label for="test-2"><input id="test-2" name="test[]" type="checkbox" value="2"> Option 2</label>
EOT;
        $group = new Wrapper(new Formulaic\Checkbox\Group(
            'test',
            [
                1 => 'Option 1',
                2 => 'Option 2',
            ]
        ));
        assert(trim("$group") == $out);
    };

    /** Dates */
    yield function () {
        $input = new Wrapper(new Formulaic\Date('test'));
        assert(trim("$input") == '<input id="test" name="test" type="date">');
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
        $input = new Wrapper(new Formulaic\Datetime('test'));
        assert(trim("$input") == '<input id="test" name="test" type="datetime">');
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
        $input = new Wrapper(new Formulaic\Email('test'));
        assert(trim("$input") == '<input id="test" name="test" type="email">');
        $input->setValue('not an email');
        assert($input->valid() != true);
        $input->setValue('foo@bar.com');
        assert($input->valid());
    };

    /** Files */
    yield function () {
        $input = new Wrapper(new Formulaic\File('test'));
        assert(trim("$input") == '<input id="test" name="test" type="file">');
    };

    /** Hidden inputs */
    yield function () {
        $input = new Wrapper(new Formulaic\Hidden('test'));
        assert(trim("$input") == '<input id="test" name="test" type="hidden">');
    };

    /** Numbers */
    yield function () {
        $input = new Wrapper(new Formulaic\Number('test'));
        $input->setValue('42');
        assert(trim("$input") == '<input id="test" name="test" step="1" type="number" value="42">');
        assert($input->valid());
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
        $input = new Wrapper(new Formulaic\Password('test'));
        $input->setValue('secret');
        assert(trim("$input") == '<input id="test" name="test" type="password">');
    };

    /** Radio buttons */
    yield function () {
        $input = new Wrapper(new Formulaic\Radio('test'));
        assert(trim("$input") == '<input id="test-1" name="test" type="radio" value="1">');
    };

    /** Search boxes */
    yield function () {
        $input = new Wrapper(new Formulaic\Search('test'));
        assert(trim("$input") == '<input id="test" name="test" type="search">');
    };

    /** Simple select boxes */
    yield function () {
        $input = new Wrapper(new Formulaic\Select('test', [1 => 'foo', 2 => 'bar']));
        assert(trim("$input") == <<<EOT
<select id="test" name="test">
<option value="1">foo</option>
<option value="2">bar</option>
</select>
EOT
        );
    };

    /** Manually built select boxes */
    yield function () {
        $input = new Wrapper(new Formulaic\Select('test', function ($select) {
            $select[] = new Formulaic\Select\Option(1, 'foo');
            $select[] = new Formulaic\Select\Option(2, 'bar');
        }));
        assert(trim("$input") == <<<EOT
<select id="test" name="test">
<option value="1">foo</option>
<option value="2">bar</option>
</select>
EOT
        );
    };
    
    /** Telephone numbers */
    yield function () {
        $input = new Wrapper(new Formulaic\Tel('test'));
        $input->setValue('612345678');
        assert(trim("$input") == '<input id="test" name="test" pattern="00?[0-9]+" type="tel" value="0612345678">');
        $input->setValue('foo');
        assert($input->valid() != true);
    };

    /** Text elements with HTML encoded values */
    yield function () {
        $input = new Wrapper(new Formulaic\Text('test'));
        $input->setValue('"');
        assert(trim("$input") == '<input id="test" name="test" type="text" value="&quot;">');
    };

    /** Textareas with HTML encoded values */
    yield function () {
        $input = new Wrapper(new Formulaic\Textarea('test'));
        $input->setValue('"');
        assert(trim("$input") == '<textarea id="test" name="test">
&quot;
</textarea>');
    };

    /** Time elements */
    yield function () : Generator {
        $input = new Wrapper(new Formulaic\Time('test'));

        /** A non-valid value gets rejected */
        yield function () use ($input) {
            $input->setValue('bla');
            assert($input->valid() != true);
        };
        /** A valid value gets accepted */
        yield function () use ($input) {
            $input->setValue('12:00:00');
            assert($input->valid());
        };
        /** We can `__toString` the element */
        yield function () use ($input) {
            assert(trim("$input") == '<input id="test" name="test" type="time" value="12:00:00">');
        };

        $input = new Wrapper(new Formulaic\Time('test'));
        /** We can require a time to be in the past */
        yield function () use ($input) : Generator {
            $input->isInPast();
            /** One in the future is rejected */
            yield function () use ($input) {
                $input->setValue('+100 seconds');
                assert($input->valid() != true);
            };
            /** One in the past is accepted */
            yield function () use ($input) {
                $input->setValue('-100 seconds');
                assert($input->valid());
            };
        };

        $input = new Wrapper(new Formulaic\Time('test'));
        /** We can require a time to be in the future */
        yield function () use ($input) : Generator {
            $input->isInFuture();
            /** One in the past is rejected */
            yield function () use ($input) {
                $input->setValue('-100 seconds');
                assert($input->valid() != true);
            };
            /** One in the future is accepted */
            yield function () use ($input) {
                $input->setValue('+100 seconds');
                assert($input->valid());
            };
        };
    };

    /** URLs */
    yield function () : Generator {
        $input = new Wrapper(new Formulaic\Url('test'));
        /** A non-valid URL gets rejected */
        yield function () use ($input) {
            $input->setValue('not an url');
            assert($input->valid() != true);
        };
        /** A valid URL gets accepted */
        yield function () use ($input) {
            $input->setValue('http://google.com');
            assert($input->valid());
        };
        /** We can `__toString` the element */
        yield function () use ($input) {
            assert(trim("$input") == <<<EOT
<input id="test" name="test" placeholder="http://" type="url" value="http://google.com">
EOT
            );
        };
    };
};

