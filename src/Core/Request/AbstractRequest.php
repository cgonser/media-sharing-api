<?php

namespace App\Core\Request;

abstract class AbstractRequest
{
    public function __get(string $property)
    {
        return $this->{$property};
    }

    public function __set(string $property, $value)
    {
        return $this->{$property} = $value;
    }

    public function __isset(string $property): bool
    {
        return (new \ReflectionProperty($this, $property))->isInitialized($this);
    }

    public function has(string $property): bool
    {
        return $this->__isset($property);
    }
}
