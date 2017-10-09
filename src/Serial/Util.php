<?php

namespace Bstoots\WOX\Serial;

use ArrayObject;
use ReflectionClass;

abstract class Util {

  // Mode flags passed to getType() and getArrayType()
  const TYPE_MODE_DEFAULT = 'DEFAULT';
  const TYPE_MODE_SHORT   = 'SHORT';
  const TYPE_MODE_LONG    = 'LONG';

  // Check for null against this
  const TYPE_NULL = 'NULL';

  /**
   * Alias for is_scalar()
   * @param  mixed  $value
   * @return bool
   */
  public static function isPrimitive($value): bool {
    return is_scalar($value);
  }

  /**
   * Is the passed value an array and does this array contain only the same type of primitives
   * @param  mixed $array
   * @return bool
   */
  public static function isPrimitiveArray($array): bool {
    if (!is_array($array) && !($array instanceof ArrayObject)) {
      return false;
    }
    // @TODO - What do we want to do if the array is empty?  How about this for now.
    if (empty($array)) {
      return false;
    }
    $firstType = static::getType(reset($array), static::TYPE_MODE_DEFAULT);
    foreach ($array as $value) {
      if (!static::isPrimitive($value)) {
        return false;
      }
      else if ( ($badType = static::getType($value, static::TYPE_MODE_DEFAULT)) !== $firstType ) {
        break;
      }
    }
    if ($badType !== $firstType) {
      throw new \Exception(
        'Too many types in array: ' . 
        var_export($firstType, true) . ' and ' . 
        var_export($badType, true) . ', possibly more'
      );
    }
    else {
      return true;
    }
  }

  /**
   * Converts passed value into a string
   * 
   * @param  mixed  $stringMeMaybe 
   * @return string Returns a string
   */
  public static function stringify($stringMeMaybe): string {
    if (static::isStringable($stringMeMaybe)) {
      if (is_string($stringMeMaybe)) {
        return $stringMeMaybe;
      }
      else {
        return var_export($stringMeMaybe, true);
      }
    }
    else {
      throw new \Exception('Unable to stringify value of type: ' . static::getType($stringMeMaybe, static::TYPE_MODE_LONG));
    }
  }

  /**
   * Is the passed value able to be converted to a string?
   * @param  mixed  $stringMeMaybe
   * @return bool
   */
  public static function isStringable($stringMeMaybe): bool {
    // integer, double (float), string or boolean
    if (static::isPrimitive($stringMeMaybe)) {
      return true;
    }
    else {
      return false;
    }
  }

  /**
   * Is the passed type label able to be converted to a string?
   * @param  string  $type Type name adheres to labels used by gettype()
   * @return bool
   */
  public static function isStringableType(string $type): bool {
    // Anything that will cause is_scalar() to return true:
    // integer, double (float), string or boolean
    switch ($type) {
      case "integer":
      case "double":
      case "string":
      case "boolean":
        return true;
      default:
        return false;
    }
  }

  /**
   * Is the passed type label what WOX would consider a "primitive" type?
   * This is effectively an alias of isStringableType() right now
   * @param  string  $type Type name adheres to labels used by gettype()
   * @return bool
   */
  public static function isPrimitiveType(string $type): bool {
    return static::isStringableType($type);
  }

  /**
   * Get type of the passed value with varying levels of specificity 
   * @param  mixed $typeMeMaybe
   * @param  string $mode        See TYPE_MODE_* consts for valid values
   * @return string The type according to gettype().  If SHORT or LONG are used and the passed value is
   *                an object or resource more details will be returned.
   */
  public static function getType($typeMeMaybe, $mode = 'DEFAULT'): string {
    if (!in_array($mode, [static::TYPE_MODE_DEFAULT, static::TYPE_MODE_SHORT, static::TYPE_MODE_LONG])) {
      throw new \Exception('Invalid mode: ' . var_export($mode, true) . ', supplied to getType()');
    }
    $type = gettype($typeMeMaybe);
    // If $mode is DEFAULT just return a standard gettype() response
    if ($mode === static::TYPE_MODE_DEFAULT) {
      return $type;
    }
    // Otherwise drill down for more details
    switch ($type) {
      case "object":
        if ($mode === static::TYPE_MODE_SHORT) {
          return (new ReflectionClass($typeMeMaybe))->getShortName();
        }
        else if ($mode === static::TYPE_MODE_LONG) {
          return get_class($typeMeMaybe);
        }
      case "resource":
        // Right now we get the same response for TYPE_MODE_SHORT and TYPE_MODE_LONG
        return get_resource_type($typeMeMaybe);
      default:
        return $type;
    }
  }

  /**
   * Get type of the passed array with varying levels of specificity.
   * Also asserts that all array items share the same type
   * @param  mixed $array
   * @param  string $mode See TYPE_MODE_* consts for valid values
   * @return string
   */
  public static function getArrayType($array, $mode = 'DEFAULT'): string {
    if (!is_array($array) && !($array instanceof ArrayObject)) {
      throw new \Exception('Not an array: ' . var_export($array, true));
    }
    // @TODO - What do we want to do if the array is empty?  How about this for now.
    if (empty($array)) {
      return static::TYPE_NULL;
    }
    $firstType = static::getType(reset($array), static::TYPE_MODE_LONG);
    foreach ($array as $value) {
      if ( ($badType = static::getType($value, static::TYPE_MODE_LONG)) !== $firstType ) {
        break;
      }
    }
    if ($badType !== $firstType) {
      throw new \Exception(
        'Too many types in array: ' . 
        var_export($firstType, true) . ' and ' . 
        var_export($badType, true) . ', possibly more'
      );
    }
    else {
      return static::getType($array[0], $mode);
    }
  }

  /**
   * Cast value to specified type
   * @NOTE - There are a ton of gotchas in here.  Probably need to revisit.
   * @param  mixed $value
   * @param  string $type  A scalar type to convert to
   * @return mixed
   */
  public static function castToType($value, string $type) {
    switch ($type) {
      case 'integer':
        return intval($value, 10);
      case 'double':
        return floatval($value);
      case 'boolean':
        return boolval($value);
      case 'string':
        return strval($value);
      default:
        return $value;
    }
  }

  /**
   * Is the passed value either an array or ArrayObject?
   * @param  mixed  $array
   * @return bool
   */
  public static function isAssoc($array): bool {
    if (!is_array($array) && !($array instanceof ArrayObject)) {
      return false;
    }
    return count(array_filter(array_keys($array), 'is_string')) > 0;
  }

}
