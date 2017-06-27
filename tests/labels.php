<?php

namespace Monolyth\Formulaic\Test;

use Monolyth\Formulaic;

/**
 * Label tests
 */
class LabelTest
{
    /**
     * Labels can have an element.
     */
    public function testLabelWithElement()
    {
        $input = new Formulaic\Text('test');
        $label = new Formulaic\Label('Label', $input);
        yield assert("$label" == <<<EOT
<label for="test">Label</label>
<input id="test" name="test" type="text">
EOT
        );
    }
}

