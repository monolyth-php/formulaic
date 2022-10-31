# Formulaic
Object-oriented form utilities for PHP8.1+

HTML forms suck. Well, no, they're superduper handy, but writing them and
validating them server-side can be a pain. Formulaic offers a set of utilities
to ease that pain.

## Basic usage
Define a form with some fields and other requirements:

```php
<?php

use Monolyth\Formulaic\Get;
use Monolyth\Formulaic\Search;
use Monolyth\Formulaic\Button\Submit;

class MyForm extends Get
{
    public function __construct()
    {
        $this[] = (new Search('q'))->isRequired();
        $this[] = new Submit('Go!', 'submit');
    }
}
```

In your template, either use the API to manually tweak your output, or simply
`__toString` the form to use the defaults:

```php
<?php

$form = new MyForm;
echo $form;
```

You can `__toString` individual fields:

```php
<?php

$form = new MyForm;

?>
<form name="search" method="get">
    <!-- These two yield identical output using MyForm above: -->
    <?=$form[0]?>
    <?=$form['q']?>
</form>
```

To validate your form:

```php
<?php

$form = new MyForm;
if ($form->valid()) {
    // ...Perform the search...
}
```

To get a list of errors:

```php
<?php

$form = new MyForm;
if ($errors = $form->errors()) {
    // ...Do error handling, or give feedback...
}
```

Forms can contain fieldsets:

```php
<?php

use Monolyth\Formulaic\Get;
use Monolyth\Formulaic\Fieldset;
use Monolyth\Formulaic\Search;
use Monolyth\Formulaic\Button\Submit;

class MyForm extends Get
{
    public function __construct()
    {
        $this[] = new Fieldset('Global search', function($fieldset) {
            $fieldset[] = new Search('q');
        });
        $this[] = new Fieldset('Search by ID', function($fieldset) {
            $fieldset[] = new Search('id');
        });
        $this[] = new Submit('Go!');
    }
}
```

And in your output:

```php
<form method="get">
    <?=$form['Global search']?>
    <?=$form['Search by ID']?>
    <?=$form['submit']?>
</form>
```

## Custom elements in forms
Simply add strings to the form; they will be outputted verbatim:

```php
<?php

// ...
class Form extends Get
{
    public function __construct()
    {
        $this[] = new Radio('foo');
        $this[] = '<h1>custom HTML element!</h1>';
    }
}
```

## Under the hood
As you will have guessed, the `Post` and `Get` forms look at posted and get data
respectively. This means any matching data in the superglobal (which, for
`Post`, includes `$_FILES`) is automagically set on the form. For elements in
groups (excluding fieldsets), Formulaic assumes they will be in a sub-array:

```php
<?php

use Monolyth\Formulaic\{ Get, Element\Group, Text };

class Form extends Get
{
    public function __construct()
    {
        $this[] = new Group('foo', function ($group) {
            $group[] = new Text('bar');
        });
    }
}
```

This will match `$_GET['foo']['bar']` for a value.

For checkbox groups (a set of related checkboxes, e.g. for settings), the values
are presumed to be in their own array. E.g. with a checkbox group named 'foo'
the values will be passed as `$_POST['foo'] = [1, 2, 3]`.

## Adding tests
Form elements can contain tests, which the `vaild()` and `error()` methods use
to produce output. A number of tests (like `isRequired()`) are pre-supplied, but
you can easily add your own via the `addTest` method on elements:

```php
<?php

$input = new Text('foo');
$input->addTest(fn ($value) => $value == 'bar');
```

The above test will fail unless the user enters "bar" into the text input.

## Binding models
Where Formulaic also really shines is in propagating the form data to your
models. All the boilerplate code containing numerous `isset` calls? Gone!

Your model is an object. Literally any object. What you want is for any
property's previously filled value to be automatically set on your form, and for
any value entered by the user to be updated on the object (which you can then
persist to a database or whatever, that's up to you). _Guess what? It's easy!_

```php
<?php

class MyFrom extends Post
{
    //... define the form
    public function __construct()
    {
        $this[] = new Text('foo');
    }
}

$model = new stdClass;
$model->foo = 'bar';
$form = new MyForm;
$form->bind($model);
```

In the above example, the form in question - when `__toString`ed - will have a
default value of `"bar"` for the `foo` input. If `$_POST['foo']` happens to
contain `"buzz"`, it will instead contain _that_. Even better, after the call to
`bind` it will also be so that `$model->foo === 'buzz'` equals true. Awesome!
That's a gazillion lines of code you no longer have to think about!

Binding can be done on any level, just remember that it needs to be on an object
and that its (sub)properties must match the element's names.

You'll notice that this ties the model structure to the form buildup; however,
that doesn't matter. The form elements are displayed "as is", it's just their
names that need to match the model.

## Transforming data
In the real world, model objects are often a lot more complicated than HTML
forms, which basically deal with strings. Enter transformers: Formulaic's way of
converting data to and from your models.

All elements support the `withTransformer` method, which basically accepts a
callable. The idea here is that the callable's argument is type hinted (so as to
determine which transformer to use) and it returns a suitable value based on
that type. An acceptable transformer for a certain situation might be:

```php
<?php

class MyModel
{
    public Foo $foo;
}

class MyForm extends Post
{
    public function __construct()
    {
        $this[] = (new Text('foo'))
            ->withTransformer(fn(string $value) => new Foo($value));
    }
}

$model = new MyModel;
$form = new MyForm;
$form->bind($model);
echo get_class($model->foo); // Foo
```

You can define multiple transformers in one go with the `withTransformers`
method (note the plural). Each argument is a callable.

Typically, you'll need two transformers: one from the model to the form, and one
from the form back to the model. In some cases, the input may vary depending on
the complexity of your project; define as many transformers as you need.

The input type hint may be a _union_ in which case the transformer is valid for
multiple types. Intersection type hints are not supported as they wouldn't
really make sense in a transformation context.

