<?php

namespace Bstoots\WOX\Serial;

interface ObjectWriter extends Serial {
  /**
   * This method takes a Java object and converts it to a JDOM element.
   * @param o The java object to be written.
   * @return A JDOM Element representing the java object.
   */
  public function write($ob): string;
}
