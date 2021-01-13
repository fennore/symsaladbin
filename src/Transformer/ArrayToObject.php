<?php

namespace App\Creator;

use ArrayObject;

/**
 * Recursively convert an array to an ArrayObject
 */
class ArrayToObject implements CreatorInterface
{

    public function create(array $array): ArrayObject
    {
        return $this->setAsArrayObject($array);
    }

    private function setAsArrayObject($value) {
        if(!is_array($value)) {
            return $value;
        }
        $mapping = array_map(fn ($item) => $this->setAsArrayObject($item), $value);
        return new ArrayObject($mapping, ArrayObject::STD_PROP_LIST);
    }
}
