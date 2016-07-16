# Formulaic
Object-oriented form utilities for PHP5.4+

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

See the [full documentation](http://monolyth.monomelodies.nl/formulaic/docs/)
for all other options.

