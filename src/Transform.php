<?php

namespace Monolyth\Formulaic;

use ReflectionFunction;

/**
 * Typically, there are likely to be mismatches between your form data (usually
 * just strings) and your models, which will be more sophisticated. Transformers
 * allow for massaging of this data.
 */
trait Transform
{
    private array $transformers;

    /**
     * Specify one or more transformers used to transform in- and output to
     * values compatible with your model.
     *  
     * @param callable ...$transformers
     * @return object Self
     */
    public function withTransformers(callable ...$transformers) : object
    {
        array_walk($transformers, function (callable $transformer) : void {
            $this->withTransformer($transformer);
        });
        return $this;
    }       
    
    /**     
     * Specify a transformer used to transform in- or output to values
     * compatible with your model.
     *
     * @param callable ...$transformers
     * @return object Self
     */
    public function withTransformer(callable $transformer) : object
    {
        $reflection = new ReflectionFunction($transformer);
        $parameter = $reflection->getParameters()[0];
        $type = '*';
        if ($parameter->hasType()) {
            $type = $parameter->getType()->getName();
        }
        // PHP inconsistency...
        if ($type === 'integer') {
            $type = 'int';
        }
        $this->transformers[$type] = $transformer;
        return $this;
    }

    /**
     * Internal method performing the actual transformation.
     *  
     * @param mixed $value Element's current value.
     * @return mixed Transformed value, or original if no suitable
     *  transformation was found. This might trigger a `TypeError`.
     */
    protected function transform(mixed $value) : mixed
    {
        if (is_object($value)) {
            $types = [get_class($value)]; 
            $types = array_merge($types, array_values(class_parents($value)));
            $types = array_merge($types, array_values(class_implements($value)));
        } else {
            $type = gettype($value);
            // PHP inconsistency...
            if ($type === 'integer') {
                $type = 'int';
            }
            $types = [$type];
        }
        $types[] = '*';
        foreach ($types as $type) {
            if (isset($this->transformers[$type])) {
                return $this->transformers[$type]($value);
            }
        }
        return $value;
    }
}

