<?php

namespace Bstoots\WOX\Tests\Stubs;

use Bstoots\WOX\Contracts\ArrayAccess;

class Building implements ArrayAccess {
  
  /**
   * @var array
   */
  protected $data;

  public function __construct(array $init = []) {
    $this->data = array_intersect_key($init, array_flip($this->getAllowedOffsets()));
  }

  public function getAllowedOffsets(): array {
    return [
      'number', 'name', 'description'
    ];
  }

  public function offsetExists($key): bool {
    if (array_key_exists($key, $this->data)) {
      return true;
    }
    else {
      return false;
    }
  }

  public function offsetGet($key) {
    if (array_key_exists($key, $this->data)) {
      return $this->data[$key];
    }
  }

  public function offsetSet($key, $value) {
    if (in_array($key, $this->getAllowedOffsets())) {
      $this->data[$key] = $value;
    }
  }

  public function offsetUnset($key) {
    if (array_key_exists($key, $this->data)) {
      unset($this->data[$key]);
    }
  }

}
