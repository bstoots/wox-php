<?php

namespace Bstoots\WOX\Serial;

interface ObjectReader extends Serial {
  /**
   * This method reads a JDOM element, and returns a live object.
   * @param xob The JDOM element that represents the object.
   * @return A live object.
   */
  public function read($xob);
}
