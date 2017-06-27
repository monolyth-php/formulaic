<?php

namespace Monolyth\Formulaic\Test;

use Monolyth\Formulaic;

/**
 * Element-specific tests
 */
class ElementTest
{
    /**
     * Elements without conditions are always valid {?}, whilst required
     * elements must have a value {?} or validation will fail {?}.
     */
    public function testDefaultTests()
    {
        $input = new Formulaic\Text('test');
        yield assert($input->valid());

        // Required:
        $input->isRequired();
        $input->setValue('foo');
        yield assert($input->valid());
        $input->setValue(null);
        yield assert($input->valid() != true);
    }

    /**
     * Generic buttons.
     */
    public function testButton()
    {
        $button = new Formulaic\Button('B');
        yield assert("$button" == '<button type="button">B</button>');
    }

    /**
     * Reset buttons.
     */
    public function testButtonReset()
    {
        $button = new Formulaic\Button\Reset('B');
        yield assert("$button" == '<button type="reset">B</button>');
    }

    /**
     * Submit buttons.
     */
    public function testButtonSubmit()
    {
        $button = new Formulaic\Button\Submit('B');
        yield assert("$button" == '<button type="submit">B</button>');
    }

    /**
     * Checkboxes.
     */
    public function testCheckbox()
    {
        $input = new Formulaic\Checkbox('test');
        yield assert("$input" == '<input type="checkbox" value="1">');
    }

    /**
     * Checkbox groups.
     */
    public function testCheckboxGroup()
    {
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
        yield assert("$group" == $out);
    }

    /**
     * Dates.
     */
    public function testDate()
    {
        $input = new Formulaic\Date('test');
        yield assert("$input" == '<input type="date">');
        $input->setMin('2010-01-01')->setMax('2012-01-01');
        $input->setValue('2009-01-01');
        yield assert($input->valid() != true);
        $input->setValue('2013-01-01');
        yield assert($input->valid() != true);
        $input->setValue('2011-01-01');
        yield assert($input->valid());
        $input->setValue('something illegal');
        yield assert($input->valid() != true);
    }

    /**
     * Datetime.
     */
    public function testDatetime()
    {
        $input = new Formulaic\Datetime('test');
        yield assert("$input" == '<input type="datetime">');
        $input->setMin('2009-01-01 12:00:00')->setMax('2009-01-02 12:00:00');
        $input->setValue('2009-01-01');
        yield assert($input->valid() != true);
        $input->setValue('2009-01-02 13:00:00');
        yield assert($input->valid() != true);
        $input->setValue('2009-01-01 13:59:00');
        yield assert($input->valid());
        $input->setValue('something illegal');
        yield assert($input->valid() != true);
    }

    /**
     * Email.
     */
    public function testEmail()
    {
        $input = new Formulaic\Email('test');
        yield assert("$input" == '<input type="email">');
        $input->setValue('not an email');
        yield assert($input->valid() != true);
        $input->setValue('foo@bar.com');
        yield assert($input->valid());
    }

    /**
     * Files.
     */
    public function testFile()
    {
        $input = new Formulaic\File('test');
        yield assert("$input" == '<input type="file">');
    }

    /**
     * Hidden inputs.
     */
    public function testHidden()
    {
        $input = new Formulaic\Hidden('test');
        yield assert("$input" == '<input type="hidden">');
    }

    /**
     * Numbers.
     */
    public function testNumber()
    {
        $input = new Formulaic\Number('test');
        $input->setValue('42');
        yield assert("$input" == '<input step="1" type="number" value="42">');
        $input->setValue('foo');
        yield assert($input->valid() != true);
        $input->setMin(9);
        $input->setValue(8);
        yield assert($input->valid() != true);
        $input->setValue(15);
        yield assert($input->valid());
        $input->setMax(14);
        yield assert($input->valid() != true);
        $input->setValue(13);
        yield assert($input->valid());
        $input->setStep(2);
        yield assert($input->valid());
        $input->setValue(12);
        yield assert($input->valid() != true);
        $input->setStep(.5);
        $input->setValue(12.5);
        yield assert($input->valid());
        $input->setValue(12.4);
        yield assert($input->valid() != true);
    }

    /**
     * Passwords.
     */
    public function testPassword()
    {
        $input = new Formulaic\Password('test');
        $input->setValue('secret');
        yield assert("$input" == '<input type="password">');
    }

    /**
     * Radio buttons.
     */
    public function testRadio()
    {
        $input = new Formulaic\Radio('test');
        yield assert("$input" == '<input type="radio" value="1">');
    }

    /**
     * Search boxes.
     */
    public function testSearch()
    {
        $input = new Formulaic\Search('test');
        yield assert("$input" == '<input type="search">');
    }

    /**
     * Simple select boxes.
     */
    public function testSelectSimple()
    {
        $input = new Formulaic\Select('test', [1 => 'foo', 2 => 'bar']);
        yield assert("$input" == <<<EOT
<select>
<option value="1">foo</option>
<option value="2">bar</option>
</select>
EOT
        );
    }

    /**
     * Manually built select boxes.
     */
    public function testSelectManual()
    {
        $input = new Formulaic\Select('test', function ($select) {
            $select[] = new Formulaic\Select\Option(1, 'foo');
            $select[] = new Formulaic\Select\Option(2, 'bar');
        });
        yield assert("$input" == <<<EOT
<select>
<option value="1">foo</option>
<option value="2">bar</option>
</select>
EOT
        );
    }
    
    /**
     * Select boxes with a name.
     */
    public function testSelectWithName()
    {
        $input = new Formulaic\Select('test', [1 => 'foo']);
        yield assert("$input" == <<<EOT
<select id="test" name="test">
<option value="1">foo</option>
</select>
EOT
        );
    }

    /**
     * Telephone numbers.
     */
    public function testTel()
    {
        $input = new Formulaic\Tel('test');
        $input->setValue('612345678');
        yield assert("$input" == '<input type="tel" value="0612345678">');
        $input->setValue('foo');
        yield assert($input->valid() != true);
    }

    /**
     * Text elements with HTML encoded values.
     */
    public function testText()
    {
        $input = new Formulaic\Text('test');
        $input->setValue('"');
        yield assert("$input" == '<input type="text" value="&quot;">');
    }

    /**
     * Textareas with HTML encoded values.
     */
    public function testTextarea()
    {
        $input = new Formulaic\Textarea('test');
        $input->setValue('"');
        yield assert("$input" == '<textarea>&quot;</textarea>');
    }

    /**
     * Time elements.
     */
    public function testTime()
    {
        $input = new Formulaic\Time('test');
        $input->setValue('bla');
        yield assert($input->valid() != true);
        $input->setValue('12:00:00');
        yield assert($input->valid());
        yield assert("$input" == '<input type="time" value="12:00:00">');

        // Test require past time
        $input = new Formulaic\Time('test');
        $input->isInPast();
        $input->setValue(time() + 100);
        yield assert($input->valid() != true);
        $input->setValue(time() - 100);
        yield assert($input->valid());

        // Test require future time
        $input = new Formulaic\Time('test');
        $input->isInFuture();
        $input->setValue(time() - 100);
        yield assert($input->valid() != true);
        $input->setValue(time() + 100);
        yield assert($input->valid());
    }

    /**
     * URLs.
     */
    public function testUrl()
    {
        $input = new Formulaic\Url('test');
        $input->setValue('not an url');
        yield assert($input->valid() != true);
        $input2 = new Formulaic\Url('test');
        $input2->setValue('http://google.com');
        yield assert($input2->valid());
        yield assert("$input\n$input2" == <<<EOT
<input placeholder="http://" type="url" value="http://not an url">
<input placeholder="http://" type="url" value="http://google.com">
EOT
        );
    }
}

