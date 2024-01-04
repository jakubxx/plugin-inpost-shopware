<?php

namespace WebLivesInPost\Models\Traits;

use ReflectionClass;
use ReflectionException;

trait CanBeArrayTrait
{
    /**
     * Transforms object to Array.
     * @return array
     * @throws ReflectionException
     */
    public function __toArray(): array
    {
        return $this->objToArr($this);
    }

    /**
     * Transforms object to Json.
     * @return string
     * @throws ReflectionException
     */
    public function __toJson(): string
    {
        return json_encode($this->__toArray());
    }

    /**
     * Recursion method to recursively transform object into an array for HTTP request purposes.
     * @param $data
     * @return array
     * @throws ReflectionException
     */
    private function objToArr($data): array
    {
        $arr = [];

        if (is_object($data)) {
            $ref = new ReflectionClass($data);
            $props = $ref->getProperties();
            if (empty($props)) {
                $ref = $ref->getParentClass();
            }

            foreach ($ref->getProperties() as $prop) {
                $prop->setAccessible(true);
                $name = $prop->getName();
                $value = $prop->getValue($data);
                $prop->setAccessible(false);

                if (is_object($value) || is_array($value)) {
                    $arr[$name] = $this->objToArr($value);
                } elseif (!empty($value)) {
                    $arr[$name] = $value;
                }
            }
        } elseif (is_array($data)) {
            foreach ($data as $name => $value) {
                if (is_object($value) || is_array($value)) {
                    $arr[$name] = $this->objToArr($value);
                } elseif (!empty($value)) {
                    $arr[$name] = $value;
                }
            }
        } elseif (!empty($data)) {
            return $data;
        }

        return $arr;
    }
}
