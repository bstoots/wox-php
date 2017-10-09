<?php

namespace Bstoots\WOX\Contracts;

/**
 * More or less the same as the built-in ArrayAccess interface but we include
 * the getAllowedOffsets() function to allow Writers to determine available
 * field names.
 */
interface ArrayAccess extends \ArrayAccess {
  
  /**
   * Returns an array of field/property names for this object
   * @return array
   */
  public function getAllowedOffsets(): array;

  public function offsetExists($key): bool;

  public function offsetGet($key);

  public function offsetSet($key, $value);

  public function offsetUnset($key);

}
