# Groups of checkboxes or radio buttons

Often you will have related checkboxes or radio buttons (especially the last...)
in a form. Formulaic offers classes for these:

```php
<?php

use Monolyth\Formulaic\Checkbox;
use Monolyth\Formulaic\Radio;

$form[] = new Checkbox\Group('name', [1 => 'Option 1', 2 => 'Option 2']);
$form[] = new Radio\Group('name', [1 => 'Option 1', 2 => 'Option 2']);
```

As you can see, they work similar to `Select` elements, and also support the
callback-style instantiation for fine-grained tuning:

```php
<?php

use Monolyth\Formulaic\Checkbox;
use Monolyth\Formulaic\Label;

$form[] = new Checkbox\Group('name', function ($checkboxes) {
    $checkbox = new Checkbox;
    $checkbox->setValue(1);
    $this[] = new Label('Option 1', $checkbox);
    $checkbox = new Checkbox;
    $checkbox->setValue(2);
    $this[] = new Label('Option 2', $checkbox);
});
```

Note that Formulaic does _not_ verify if entries in a `Checkbox\Group` or
`Radio\Group` are actually checkboxes or radios. It's more of a convenience
container.

We can now perform validation on the group:

```php
<?php

$form['name']->isRequired();
// True if:
// - at least one (or exactly one in the case of a Radio\Group) is checked;
// - the value(s) assigned to the group are present in its members.
$form['name']->valid();

$form['name']->setValue(3);
$form['name']->valid(); // False; we only had values 1 or 2.
```

