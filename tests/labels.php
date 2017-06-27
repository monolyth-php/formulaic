<?php

use Monolyth\Formulaic;

/**
 * Label tests
 */
return function () : Generator {
    /**
     * Labels can have an element.
     */
    yield function () {
        $input = new Formulaic\Text('test');
        $label = new Formulaic\Label('Label', $input);
        yield assert("$label" == <<<EOT
<label for="test">Label</label>
<input id="test" name="test" type="text">
EOT
        );
    };
};

