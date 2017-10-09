<?php

namespace Bstoots\WOX\Serial;

/**
 * 
 */
class ArrayAccessWriter extends Writer  {

  /**
   * Returns all publicly accessible object fields via generator
   * @param  object $ob
   * @return array containing a single element in the form of:
   *         ['key' => 'value']
   */
  protected function yieldFields($ob) {
    // If the object implements ArrayAccess use getAllowedOffsets()
    if ($ob instanceof \Bstoots\WOX\Contracts\ArrayAccess) {
      foreach ( $ob->getAllowedOffsets() as $key ) {
        yield [$key => $ob[$key]];
      }
    }
    // Otherwise just get the public fields via get_object_vars()
    else {
      foreach ( get_object_vars($ob) as $key => $value ) {
        yield [$key => $value];
      }
    }
  }

}
