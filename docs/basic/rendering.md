# Custom rendering

We already saw earlier how you can manually render complicated forms. However,
what if you need _all_ forms in a project to adhere to a certain output format?
E.g. in `<table>`s instead of `<div>`s?

> Using tables in layouts is of course not something we recommend in general,
> but you know how clients and their wishes can be.

Simple: every `__toString` in Formulaic is in a trait, acting as a kind of
template. Just drop in your own and make sure your autoloader looks at your
project-specific custom trait before autoloading the default one.

You can also use PHP's `class_alias` method to accomplish this:

```php
<?php

class_alias('My\Custom\Fieldset', 'Monolyth\Formulaic\Fieldset\Tostring');
```

For more complicated projects with multiple default `__toString`
implementations, simply `use` the toString-trait you need on a per-form basis,
optionally also overriding the default for all other forms. PHP traits are
handy like that.

## Using templates
Formulaic is _not_ a templating engine, but if you prefer to use one for your
`__toString` implementation, be our guest. `__toString` doesn't care either way
as long as a string is returned. We won't describe how to plugin something like
Twig or Smarty here, but assuming you know your templating engine of choice well
it should be trivial.

Alternatively, you could write a template (e.g. in Twig) and include it with a
`form` variable or such.

