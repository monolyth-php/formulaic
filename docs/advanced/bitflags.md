# Bitflags
A common operation is to have a group of checkboxes with related settings, which
are internally stored as a _bitflag_. We're not going to go into details here on
what those are, but if you don't already know:

> A byte is composed of bits, i.e. 0 or 1 values. Each zero means "no", each one
> means "yes". Hence, the operation `385 & 1` evaluates to `true`, since `1` is
> the result. Via this technique you can efficiently store a bunch of yes/no
> settings in one big integer. Computers like that kind of stuff.

Formulaic supports the special `Bitflag` element to handles these cases. It's
essentially an extension of `Checkbox\Group`.

Let's look at a quick example:

```php
<?php

use Monolyth\Formulaic\Post;
use Monolyth\Formulaic\Bitflag;

class MyForm extends Post
{
    public function __construct()
    {
        $this[] = new Bitflag('superhero', [
            'batman' => 'Batman',
            'superman' => 'Superman',
            'spiderman' => 'Spiderman',
            'hulk' => 'The Hulk',
            'daredevil' => 'Daredevil',
        ]);
    }
}
```

With the above example, you could do the following in your code:

```php
<?php

$form = new MyForm;
$form['superhero']->setValue(['batman', 'superman', 'hulk']);
$form['superhero'][0]->checked(); // true
// Or reference by label:
$form['superhero']['Batman']->checked(); // true
```

## Binding models
If a model was bound, it is its own responsibility to convert the bound
`superhero` back into a byte if needed. Formulaic doesn't care about the mapping
of readable names to bits. To convert, you can use the `withTransformer` helper.
To convert input to a valid value for the model, typehint with `ArrayObject` For
the converse, type hint whatever the model is supplying. An example:

```php
<?php

use Monolyth\Formulaic\Post;
use Monolyth\Formulaic\Bitflag;

class MyForm extends Post
{
    public function __construct()
    {
        $this[] = (new Bitflag('superhero', [
            'batman' => 'Batman',
            'superman' => 'Superman',
            'spiderman' => 'Spiderman',
            'hulk' => 'The Hulk',
            'daredevil' => 'Daredevil',
        ]))->withTransformer(function (ArrayObject $input) : stdClass {
            $property = (object)[
                'batman' => false,
                'superman' => false,
                'spiderman' => false,
                'hulk' => false,
                'daredevil' => false,
            ];
            foreach ($input as $name) {
                $property->$name = true;
            }
            return $property;
        })->withTransformer(stdClass $output) : ArrayObject {
            $raw = [];
            foreach ($output as $key => $value) {
                if ($value) {
                    $raw[] = $key;
                }
            }
            return $raw;
        });
    }
}

class Comic
{
    stdClass $superhero;
}

$_POST = ['superhero' => ['batman']];
$model = new Comic;
$form = new MyForm;
$form->bind($model);
// If "Batman" was checked:
var_dump($model->superhero->batman); // true
var_dump($model->superhero->daredevil); // false
```

It's up to your code to actually convert the checked bits into a byte again;
Formulaic by design doesn't care about bit values since they would amount to
"magic numbers" outside of the model context. A common strategy is to not use
a simple `stdClass` but a custom type that can be `__toString`'ed.

## Undefined values
A bitflag element silently ignores unknown values tossed at it. If you need to
split a single bit value over multiple `Bitflag` elements (e.g. because they
belong in different forms), it is up to you to add "type objects" that only work
on the relevant bits and leave the others alone.

