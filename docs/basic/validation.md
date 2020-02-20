# Input and validation

Before doing anything to your form's contents on page load, you'll want to
validate the data passed in. _Never trust user input._ Form validation can
normally be a quite tedious process, but Formulaic makes it easy.

```php
<?php

//...inside constructor...
$this[] = (new Monolyth\Formulaic\Text('mytextfield'))->isRequired();

//...inside handling code, e.g. a Model of sorts:
if ($form->valid()) {
    // All okay! Do something with the form data...
} else {
    $errors = $form->errors();
    // $errors is now an array of errors you can handle accordingly.
}
```

Some tests are shared among all elements, some are element-specific. Refer to
the API documentation for a list of all options.

Tests are added to elements using the `Element::addTest` method, which you can
also call directly to write custom tests with your specific business logic.
Adding a test is simple:

```php
<?php

$this[] = (new Monolyth\Formulaic\Text('mycustomlogic'))->addTest(
    'notokay',
    function($value) {
        return isThisOkay();
    }
);
```

The callback receives the element's current value and should return true if the
test passes, or else false. The name of the test is the error returned, so you
should give it a descriptive value. Note that subsequent tests of the same name
overwrite existing tests. That way you can also provide your own implementation
of `isRequired` easily, for instance.

## Loading data
But how does your form know what data it needs to validate? Remember how we've
been extending the `Monolyth\Formulaic\Get` base class? It won't surprise you to
learn there is also a `Monolyth\Formulaic\Post`. Depending on which of these two
you extend, the user supplied values in `$_GET` or `$_POST` are automatically
injected into the form's elements' values. It's also smart enough to look at
`$_FILES` if a `Post` form includes a `File` element or one extending it.

> Formulaic is smart, but not clairvoyant. It works on the assumption that the
> "source" superglobals are filled prior to instantiation (or, to be more
> precise, prior to the addition of fields).

If you need a different superglobal (`$_SESSION` perhaps?) or have another
source of data, you'll need to extend the `Form` class and supply your own
`offsetSet` implementation. Look at the implementations in `Get` and `Post` to
get an idea of what to do; essentially you just need to check if a value is
supplied with that element's name and if so, set it; also, you'll want to call
`valueSuppliedByUser(true)` on the element. Formulaic uses this internally (see
"Binding models" below).

## Specifying default values
Another common use-case is to set default values. Imagine a form for an
authenticated user that allows her to change, say, her date of birth. This is
where things normally get a bit tricky:

- The default value should be the currently known date of birth;
- On submission, and when failing validation (e.g. because the date is in the
  future, which unless you're called Michael J. Fox is impossible), we would
  _still_ want to default to the posted value so it's obvious to the user what
  went wrong.

Instead of forcing you to write complicated `if/then/else` statements
everywhere, you can simply set provided values manually:

```php
<?php

$myuserform = new UserForm;
// Assuming this exists:
$name = $myuserform['name']->getElement();
$name->setDefaultValue('Marijn');
```

In the above example, the value of the `$name` element will only be set to
`"Marijn"` if no other value was supplied (e.g. in `$_POST`).

You can of course also specify default values directly when defining the form:

```php
<?php

$this[] = (new Formulaic\Date('dob'))->setDefaultValue('1978-07-13');
```

This method is mostly useful for some sort of "ueber-default" that is
hard-coded. Generally, default values will come from an external source, e.g.
your RDBMS.

## Binding models
Speaking of which, Formulaic can do even better. A common use case is:

1. Query a database for a row;
2. Instantiate a model with the found row;
3. Define a form that specifies how the data can be changed;
4. On submit, instruct the model to save the changed data back into the
   database.

Normally, this would involve all sorts of `foreach` loops checking `$_POST`
keys, updating models, and finally calling some `save` method. Boring!

Formulaic allows you to `bind` a model to your form:

```php
<?php

// Assuming this gets submitted...
$_POST['name'] = 'Linus';
$form = new MyForm;
$user = new UserModel($id); // Assuming the constructor loads data for $id
$form->bind($user);
// Assuming $user had a $name propery containing "Marijn", it will now
// contain the new value "Linus"!
echo $user->name; // "Linus"
```

An element's `setValue` call automatically propagates the set value to the
associated model:

```php
<?php

$form['name']->getElement()->setValue('Chuck Norris');
echo $user->name; // "Chuck Norris"
```

> Note that for forms with default values supplied using the `setDefaultValue`
> method described above, these values will also be set on your model object.
> This is probably what you want (it's a default after all) but is good to be
> aware of.

The binding only works for fields that are both set in the form, as exist as
public properties on your model object. This means you can bind as often as
you like, supplying "sub-models" of related fields that are handled in one big
form. A trivial example could be:

```php
<?php

$name = new NameModel($id); // Loads firstname, lastname, username.
$contact = new ContactModel($id); // Loads email, telephone, IRC

echo $name->firstname; // Marijn
echo $contact->email; // marijn@monomelodies.nl

// Sample post data:
$_POST = ['firstname' => 'Santa', 'email' => 'santa@claus.com'];

$form = new UserForm; // Allows user to change all six fields mentioned above
$form->bind($name);
$form->bind($contact);

echo $name->firstname; // "Santa"
$name->save(); // Update name record

echo $contact->email; // "santa@claus.com";
$contact->save(); // Update contact record
```

(The `save` method used above is just an example; how your models work
internally is entirely up to you!)

It's also fine if models have "overlap" properties; they'll just all get bound
to the same form element.

## Complex models and transformations
A more complex model might decorate its properties, or otherwise expect a value
that Formulaic (since HTML forms contain strings by default) is not able to cast
on its own. This is especially problematic since PHP 7.4, since it adds type
hints to object properties.

Formulaic 1.10 introduces the concept of "transformers" for this purpose. Each
bindable element can specify one or more transformers using the following
syntax, for instance if the model expects an instance of `Carbon\Carbon` for
date fields:

```php
<?php

$field = (new Monolyth\Formulaic\Date('foo'))
    ->withTransformer(function (string $date) : Carbon\Carbon {
        return new Carbon\Carbon($date);
    });
```

The internal `transform` method tries to apply the most specific transformer
defined; to specify a "default-default-fallback" simply don't type hint the
parameter to the callable (this means "match anything"). If the supplied type
_already_ is valid, the transformer does nothing of course. The same is true for
callables that return an invalid type, although this feature will only work in
PHP 7.4 or higher.

Note that some elements (especially the grouped ones) will try to set non-string
values on your model.

`withTransformer` return the current element, so you can embed the calls in your
form definition's logic chain (`isRequired()` etc.).

