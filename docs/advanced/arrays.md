# Handling complex arrays

When dealing with form data, you'll often encounter complex arrays in, let's
say, a `$_POST`. Consider the following real-world example:

```html
<form method="post">
    <input type="checkbox" name="interests[movies][]" value="action"> Action
    <input type="checkbox" name="interests[movies][]" value="comedy"> Comedy
    <input type="checkbox" name="interests[movies][]" value="romantic"> Romantic

    <input type="checkbox" name="interests[music][]" value="rock"> Rock
    <input type="checkbox" name="interests[music][]" value="pop"> Pop
    <input type="checkbox" name="interests[music][]" value="hiphop"> Hiphop
</form>
```

In our form, we want to logically group the interests in "movies" and "music",
since they're all types of interest.

In Formulaic, this kind of grouping is handled in a transparent manner. Assuming
the "interests" example from above:

- `$form['interests']` is set on the main form;
- Within the group, we have groups of checkboxes called "movies" and "music".

We cannot simply say `$form['interests']['movies'] = new Checkbox\Group`,
since mapping user supplied data back to the correct elements would be tricky.
Instead, Formulaic supplies a special `Group` class you can use for this.

> Actually, the `Fieldset` extends the `Group` class, but doesn't offer the
> inheritance functionality associated with data keys. Fieldsets are logical
> groupings for end-users and have no bearing on how the backend chooses to
> handle the supplied data.

Onwards, then:

```php
<?php

use Monolyth\Formulaic\Post;
use Monolyth\Formulaic\Element;
use Monolyth\Formulaic\Checkbox;

class MyForm extends Post
{
    public function __construct()
    {
        $this[] = new Element\Group('interests', function($group) {
            $group[] = new Checkbox\Group(
                'movies',
                [
                    'action' => 'Action',
                    'comedy' => 'Comedy',
                    'romantic' => 'Romantic',
                ]
            );
            $group[] = new Checkbox\Group(
                'music',
                [
                    'rock' => 'Rock',
                    'pop' => 'Pop',
                    'hiphop' => 'Hiphop',
                ]
            );
        });
    }
}
```

Now, when Formulaic sees `$_POST['interests']['movies'][] = 'action'`, it
knows which group to map it to.

You can nest groups as deep as you want, but for your own sanity one or two
levels should be the maximum.

The elements defined in a group are available under their fully qualified key,
or parts of it. Note that for duplicate keys Formulaic resolves these in a
first-found, first-returned manner.

Hence:

```php
<?php

$form['interestes']['movies'] === $form['movies']; // true.

```

