<?php
    declare(strict_types=1);

    class CachedEnum {
        public $mapFromNamesToValues = array();
        public $mapFromValuesToNames = array();
        public $mapFromLCNamesToValues = array();
    }

    // based on https://stackoverflow.com/a/254543/1238406
    abstract class BasicEnum {
        private static $cachedEnums = array(); // an array of CachedEnum objects

        // returns an associative array of values, keyed on the names of the enum
        public static function Values(): array {
            return self::GetCachedEnum()->mapFromNamesToValues;
        }

        // returns an associate array of names, keyed on the numeric values of the enum
        public static function Names(): array {
            return self::GetCachedEnum()->mapFromValuesToNames;
        }

        // returns an associate array of values, keyed on lowercased names of the enum
        public static function RelaxedValues(): array {
            return self::GetCachedEnum()->mapFromLCNamesToValues;
        }

        private static function GetCachedEnum(): CachedEnum {
            $calledClass = get_called_class();
            $cachedEnum = @self::$cachedEnums[$calledClass];
            if ($cachedEnum != null) {
                return $cachedEnum;
            }

            $map = (new ReflectionClass($calledClass))->GetConstants();
            
            $cachedEnum = new CachedEnum();
            $cachedEnum->mapFromNamesToValues = $map;
            $cachedEnum->mapFromValuesToNames = array_flip($map);
            $cachedEnum->mapFromLCNamesToValues = array_change_key_case($map);
            self::$cachedEnums[$calledClass] = $cachedEnum;

            return $cachedEnum;
        }

        public static function IsValidName(string $name, $strict = false): bool {
            if ($strict) {
                $values = self::Values();
                return array_key_exists($name, $values);
            }

            $values = self::RelaxedValues();
            return array_key_exists($name, $values);
        }

        public static function IsValidValue(int $value, $strict = true): bool {
            $names = self::Names();
            return array_key_exists($value, $names);
        }

        public static function Name(int $value): string {
            return self::Names()[$value];
        }

        public static function Value(string $name): int {
            return self::Values()[$name];
        }

    }

?>