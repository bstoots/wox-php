<?php

namespace Bstoots\WOX\Serial;

/**
 * 
 */
class SimpleWriter extends Writer  {

  /**
   * Returns all publicly accessible object fields via generator
   * @param  object $ob
   * @return array containing a single element in the form of:
   *         ['key' => 'value']
   */
  protected function yieldFields($ob) {
    // @TODO - WOX Java implementation uses reflection to grab all properties regardless of 
    //         visibility.  I'm not in love with this so I'm going to circle back to it.
    //         For now only public properties are serializable.
    foreach ( get_object_vars($ob) as $key => $value ) {
      yield [$key => $value];
    }
  }

}
