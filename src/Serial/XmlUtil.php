<?php

namespace Bstoots\WOX\Serial;

abstract class XmlUtil {

  /**
   * Reformat XML string to a single line
   * Note: This is dependent on the underlying libxml implementation
   * and therefore may not work on certain platforms, such as Windows.
   * 
   * @param  string $xml String of XML data
   * @return string      Formatted string of XML data
   */
  public static function linearize(string $xml) {
    $doc = new \DOMDocument();
    $doc->preserveWhiteSpace = false;
    $doc->formatOutput = false;
    $doc->loadXML($xml);
    return trim($doc->saveXML());
  }

  /**
   * Reformat XML string with one element per line
   * Note: This is dependent on the underlying libxml implementation
   * and therefore may not work on certain platforms, such as Windows.
   * 
   * @param  string $xml String of XML data
   * @return string      Formatted string of XML data
   */
  public static function pretty(string $xml) {
    $doc = new \DOMDocument();
    $doc->preserveWhiteSpace = false;
    $doc->formatOutput = true;
    $doc->loadXML($xml);
    return trim($doc->saveXML());
  }

}
