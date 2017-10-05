<?php

namespace Bstoots\WOX\Serial;

use ArrayObject;
use ReflectionClass;

abstract class Util {

  /**
   * Converts passed value into a string if possible
   * 
   * @param  mixed $stringMeMaybe 
   * @return mixed Returns a string if possible, otherwise the original value
   */
  public static function stringify($stringMeMaybe) {
    if (static::isStringable($stringMeMaybe)) {
      if (is_string($stringMeMaybe)) {
        return $stringMeMaybe;
      }
      else {
        return var_export($stringMeMaybe, true);
      }
    }
    // 
    // else if - Objects that can be easily converted to strings
    // 
    else {
      return $stringMeMaybe;
    }
  }

  public static function isStringable($stringMeMaybe) {
    if (is_scalar($stringMeMaybe)) {
      return true;
    }
    // 
    // else if - Objects that can be easily converted to strings
    // 
    else {
      return false;
    }
  }

  public static function isStringableType($type) {
    switch ($type) {
      case "bool":
      case "boolean":
      case "int":
      case "integer":
      case "double":
      case "float":
      case "string":
        return true;
      default:
        return false;
    }
  }

  public static function isPrimitiveType($type) {
    return static::isStringableType($type);
  }

  /**
   * [getType description]
   * @param  [type] $typeMeMaybe [description]
   * @return [type]              [description]
   */
  public static function getType($typeMeMaybe, $basenameOnly = true): string {
    $type = gettype($typeMeMaybe);
    switch ($type) {
      case "object":
        if ($basenameOnly === true) {
          return basename(get_class($typeMeMaybe));
        }
        else {
          return get_class($typeMeMaybe);
        }
      case "resource":
        return get_resource_type($typeMeMaybe);
      default:
        return $type;
    }
  }

  public static function getArrayType($array): string {
    if (!is_array($array) && !($array instanceof ArrayObject)) {
      throw new \Exception('Not an array: ' . var_export($array, true));
    }
    $types = [];
    foreach ($array as $value) {
      $type = static::getType($value, false);
      $types[$type] = $type;
    }
    if (count($types) > 1) {
      throw new \Exception('Too many types in array: ' . var_export($types, true));
    }
    else {
      return basename(reset($types));
    }
  }

  public static function isPrimitive($value): bool {
    return is_scalar($value);
  }

  public static function isPrimitiveArray($array): bool {
    if (!is_array($array) && !($array instanceof ArrayObject)) {
      return false;
    }
    foreach ($array as $value) {
      if (!static::isPrimitive($value)) {
        return false;
      }
    }
    return true;
  }

  public static function castToType($value, $type) {
    switch ($type) {
      case 'int':
      case 'integer':
        return intval($value, 10);
      case 'double':
      case 'float':
        return floatval($value);
      case 'bool':
      case 'boolean':
        return boolval($value);
      case 'string':
        return strval($value);
      default:
        return $value;
    }
  }

  public static function getClassShortName($object): string {
    $reflect = new ReflectionClass($object);
    return $reflect->getShortName();
  }

}
