<?php

class GroupTest extends PHPUnit_Framework_TestCase
{
    public function testGroupInGroup()
    {
        $this->expectOutputString(<<<EOT
<form action="" id="test" method="post">
<input id="test-foo-bar-baz" name="foo[bar][baz]" type="text" value="fizzbuz">
</form>
EOT
        );
        $_POST['foo'] = ['bar' => ['baz' => 'fizzbuz']];
        $form = new PostForm;
        $form->attribute('id', 'test');
        $form[] = new Formulaic\Element\Group('foo', function ($group) {
            $group[] = new Formulaic\Element\Group('bar', function ($group) {
                $group[] = new Formulaic\Text('baz');
            });
        });
        $this->assertEquals('fizzbuz', $form['foo']['bar']['baz']->getValue());
        echo $form;
    }

    public function testCheckboxGroup()
    {
        $this->expectOutputString(<<<EOT
<form action="" method="post">
<div>
<label for="test-1"><input checked id="test-1" name="test[]" type="checkbox" value="1"> foo</label>
<label for="test-2"><input id="test-2" name="test[]" type="checkbox" value="2"> bar</label>
</div>
</form>
EOT
        );
        $form = new PostForm;
        $form[] = (new Formulaic\Checkbox\Group(
            'test',
            [1 => 'foo', 2 => 'bar']
        ))->isRequired();
        $this->assertNotTrue($form->valid());
        $_POST['test'] = [1];
        $form = new PostForm;
        $form[] = (new Formulaic\Checkbox\Group(
            'test',
            [1 => 'foo', 2 => 'bar']
        ))->isRequired();
        $this->assertTrue($form->valid());
        echo $form;
    }

    public function testRadioGroup()
    {
        $this->expectOutputString(<<<EOT
<form action="" method="post">
<div>
<label for="test-1"><input checked id="test-1" name="test" required="1" type="radio" value="1"> foo</label>
<label for="test-2"><input id="test-2" name="test" required="1" type="radio" value="2"> bar</label>
</div>
</form>
EOT
        );
        $form = new PostForm;
        $form[] = (new Formulaic\Radio\Group(
            'test',
            [1 => 'foo', 2 => 'bar']
        ))->isRequired();
        $this->assertNotTrue($form->valid());
        $_POST['test'] = 1;
        $form = new PostForm;
        $form[] = (new Formulaic\Radio\Group(
            'test',
            [1 => 'foo', 2 => 'bar']
        ))->isRequired();
        $this->assertTrue($form->valid());
        echo $form;
    }

    public function testBitflag()
    {
        $bit = new Formulaic\Bitflag('superhero', [
            1 => 'Batman',
            2 => 'Superman',
            4 => 'Spiderman',
            8 => 'The Hulk',
            16 => 'Daredevil',
        ]);
        $bit->setValue([1, 2, 4]);
        $this->assertTrue($bit['Batman']->getElement()->checked());
        $this->assertTrue($bit[1]->getElement()->checked());
        $this->assertTrue($bit[2]->getElement()->checked());
        $this->assertNotTrue($bit[3]->getElement()->checked());
        $this->assertNotTrue($bit[4]->getElement()->checked());
    }

    public function testNonsuppliedBitflagsAreLeftAlone()
    {
        $bit = new Formulaic\Bitflag('superhero', [
            'spidey' => 'Spiderman',
            'hulk' => 'The Hulk',
            'daredevil' => 'Daredevil',
        ]);
        $bit->setDefaultValue(['superman']);
        $bit->setValue(['hulk']);
        $this->assertTrue($bit->getValue()->hulk);
        $this->assertFalse(isset($bit->getValue()->superman));
    }
}

