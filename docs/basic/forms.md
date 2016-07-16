# Create forms

Create a form by defining and instantiating a class extending one of the
supplied template classes `Monolyth\Formulaic\Get` or `Monolyth\Formulaic\Post`:

```php
<?php

class MyForm extends Monolyth\Formulaic\Get
{
}
```

Formulaic forms extend the `ArrayObject` class, so to add fields simply assign
them (this could also be done "on the fly" after instantiation). Formulaic
places no restrictions on _what_ you add to the form, but usually you'll want to
either use or extend one of the supplied types. But in theory it could be
anything offering a similar interface. Note that some utility methods type check
the fields added, though.

```php
<?php

// ...inside class definition of course...
public function __construct()
{
    // We usually do this in the constructor, since normally forms have a
    // fixed amount of fields.
    $this[] = new Monolyth\Formulaic\Text('mytextfield');
}
```

Have a browse through all the types supplied. Formulaic won't complain if you do
something illegal (like adding a `Monolyth\Formulaic\Option` outside of a
`Monolyth\Formulaic\Select`) so it's up to you to add sensible stuff.

At the most basic level, you can now do a `__toString` on your form instance:

```php
<?php

$form = new MyForm;
echo $form;
```

## Adding fieldsets
Adding fieldsets works similar. The first argument to the `Fieldset` constructor
is the legend (set to `null` to ignore), the second argument is a callback
taking a single parameter: your new fieldset. Hence:

```php
<?php

public function __construct()
{
    $this[] = new Monolyth\Formulaic\Fieldset('The legend', function($fieldset) {
        $fieldset[] = new Formulaic\Text('mytextfield');
    });
}
```

## Adding labels
Adding a label to a form element is good practice from a usability perspective.
Formulaic makes this, of course, easy:

```php
<?php

// ...in constructor...
$this[] = new Monolyth\Formulaic\Label(
    'Check out this field!',
    new Monolyth\Formulaic\Text('mytextfield')
);
```

Now, this will work as expected:

```php
<?php

echo $form['mytextfield']; // echoes label with input
echo $form['Check out this field!']; // same
```

The default `__toString` implementation is to echo the label first, then the
field, except for checkbox-style elements which are inverted.

## Manual output

Quite often, the extremely simple `__toString` Formulaic supplies for forms
won't cut it. Luckily, you can directly access your `$form` instance:

```php
<form>
    <?=$form[0]?>
</form>
```

Or, which is usually more convenient:

```php
<form>
    <?=$form['mytextfield']?>
</form>
```

This recurses through any fieldsets your form has.

## Custom attributes
Form elements offer a variety of useful helpers depending on their type, but
they also all expose a lower level helper call `Element::attribute`. Simply use
this to register HTML attributes on your form, fieldset, button or element:

```php
<?php

//... in constructor...
$this[] = (new Text)->attribute('data-something', 'foo');
```

Most helper methods are _chainable_, meaning they simply return `$this`. (The
validation test methods are a notable exception.)

For attributes on forms - where they're particularly useful, like `action` and
so on - you may use the `$attributes` property. It is a hash of key/value pairs.

> To output the attribute without any value - e.g. `<form novalidate>` - use
> the value `null`. To specifically disable an attribute, use `false`. This is
> mostly useful if you want to suppress the default empty `action` attribute.

