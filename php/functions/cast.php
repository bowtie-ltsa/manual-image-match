<?php
    /**
     * Class casting. thx to https://stackoverflow.com/a/9812059/1238406
     *
     * @param object $sourceObject
     * @param string|object $destination
     * @return object
     */
    function cast($sourceObject, $destination)
    {
        if (is_string($destination)) {
            $destination = new $destination();
        }
        $sourceReflection = new ReflectionObject($sourceObject);
        $destinationReflection = new ReflectionObject($destination);
        $sourceProperties = $sourceReflection->getProperties();
        foreach ($sourceProperties as $sourceProperty) {
            $sourceProperty->setAccessible(true);
            $name = $sourceProperty->getName();
            $value = $sourceProperty->getValue($sourceObject);
            if ($destinationReflection->hasProperty($name)) {
                $propDest = $destinationReflection->getProperty($name);
                $propDest->setAccessible(true);
                $propDest->setValue($destination,$value);
            } else {
                $destination->$name = $value;
            }
        }
        return $destination;
    }

    function casteach($sourceList, $destination) {
        if (!is_string($destination)) {
            $destination = get_class($destination);
        }
        $out = array();
        foreach($sourceList as $key => $value) {
            $out[$key] = cast($value, $destination);
        }
        return $out;
    }
?>