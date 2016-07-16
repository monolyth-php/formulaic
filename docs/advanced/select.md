# Select boxes

There are two ways to add select boxes to a Formulaic form.

## Shorthand (usually good enough)

```php
<?php

$this[] = new Monolyth\Formulaic\Select('selectname', [
    1 => 'option 1',
    2 => 'option 2',
]);

```

Simply pass a hash of key/value pairs. Mosttimes, you won't need to do
anything fancy with your `<option>`s anyway.

## Low-level (for fine-grained tweaking)

```
<?php

$this[] = new Monolyth\Formulaic\Select('selectname', function($select) {
    $select[] = new Monolyth\Formulaic\Select\Option(1, 'option 1');
    $select[] = new Monolyth\Formulaic\Select\Option(2, 'option 2');
});

```

More typing, but the `Option` class extends `Element` so you get most of the
goodies associated with it.

