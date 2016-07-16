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
of readable names to bits. The optional third parameter passed to the `Bitflag`
constructor can be a "model"-type object to use internally. The implementation
is up to you, but the assumption is that the checked keys are `true` whilst
unchecked keys are `false`.

> If no binding was specified, Formulaic will use a `StdClass`.

Hence, building on our previous example:

```php
<?php

$model = new Superhero;
$form = new MyForm;
$form->bind($model);
// If "Batman" was checked:
var_dump($model->superhero->batman); // true
var_dump($model->superhero->daredevil); // false
```

It's up to your code to actually convert the checked bits into a byte again;
Formulaic by design doesn't care about bit values since they would amount to
"magic numbers" outside of the model context.

## Undefined values
A bitflag element silently ignores unknown values tossed at it:

```php
<?php

// We want a form that just asks if we prefer Batman or Superman (e.g. for some
// poll module), but elsewhere the user is able to choose all 5 heroes (e.g. on
// a settings page).

class PollForm extends Post
{
    public function __construct()
    {
        $this[] = new Bitflag('superhero', [
            'batman' => 'Batman',
            'superman' => 'Superman',
        ]);
    }
}

// On posting, the user only selected Batman from the two options:
$_POST['superhero'] = ['batman'];
$form = new PollForm;
// Assuming the user previously selected Batman, Superman and Spiderman:
$form['superhero']->setValue(['batman', 'superman', 'spiderman']);
var_dump(isset($form['superhero']->getValue()->spiderman)); // false
```

The underlying idea is that typically a bitflag will contain multiple yes/no
choices, but users won't want to edit them all in one form per se. So you just
pass in the complete value containing all flags, allow the user to edit those
supplied in the form, and your handler or model can just persist the resulting
value object back to your storage, using only defined properties.

Note that "known" bits are available as booleans, i.e. if in the previous
example `'superman'` wasn't set, it _will_ be available as a property, only
false:

```php
<?php

var_dump(isset($form['superhero']->getValue()->superman)); // true
var_dump($form['superhero']->getValue()->superman); // false
```

