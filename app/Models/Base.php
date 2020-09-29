<?php

namespace App\Models;

use Exception;

class Base
{
    public function __get($property)
    {
        if (!property_exists($this, $property)) {
            throw new Exception(__METHOD__ . ': Trying to access non-existent property (' . $property . ')', 2);
        }

        if ('_' === $property[0]) {
            throw new Exception(__METHOD__ . ': Trying to access _private property (' . $property . ')', 2);
        }

        return $this->$property;
    }

    public function __set($property, $value)
    {
        if (!property_exists($this, $property)) {
            throw new Exception(__METHOD__ . ': Trying to access non-existent property (' . $property . ')', 3);
        }

        if ('_' === $property[0]) {
            throw new Exception(__METHOD__ . ': Trying to access _private property (' . $property . ')', 3);
        }

        $this->$property = $value;
    }

}
