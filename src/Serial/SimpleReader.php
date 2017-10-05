<?php

namespace Bstoots\WOX\Serial;

use DOMDocument;
use DOMElement;
use DOMNode;
use ArrayObject;
use SplObjectStorage;
use StdClass;

/**
 * 
 */
class SimpleReader implements ObjectReader {
  use TypeMapping;

  /**
   * @var DOMDocument
   */
  protected $dom;

  /**
   * @var array
   */
  protected $classMap = [];

  /**
   * @var SplObjectStorage
   */
  protected $map;

  /**
   * @param array $config Configuration array with the following structure:
   * [
   *   classMap - array - Keys are WOX type definitions, values are fully-
   *                      qualified class paths.
   * ]
   */
  public function __construct(array $config = []) {
    if (array_key_exists('classMap', $config)) {
      $this->classMap = $config['classMap'];
    }
  }

  /**
   * Public entry point into SimpleReader.  Takes an WOX XML string
   * and deserializes it into a PHP data structure.
   * @param  string $xob WOX-format XML string
   * @return (array|object)
   */
  public function read(string $xob) {
    $this->dom = new DOMDocument();
    $this->map = new SplObjectStorage();
    $this->dom->loadXML($xob);
    return $this->readElement($this->dom->documentElement);
  }

  /**
   * Parses a DOM node and determines whether it contains primitives, an array, 
   * or another object structure.
   * @param  DOMNode $xob
   * @return (array|object)
   */
  private function readElement(DOMNode $xob) {
    if (empty($xob)) {
      return null;
    }
    // @TODO - Reimplement this functionality
    else if ($this->reference($xob)) {
      return $this->map->offsetGet($xob->getAttribute(static::IDREF));
    }
    $id = $xob->getAttribute(static::ID);
    // 
    if ($this->isPrimitiveArray($xob)) {
      $ob = $this->readPrimitiveArray($xob, $id);
    }
    else if ($xob->getAttribute(static::TYPE) === static::ARRAY) {
      $ob = $this->readObjectArray($xob, $id);
    }
    else if ( Util::isStringableType($xob->getAttribute(static::TYPE)) ) {
      $ob = $this->readStringObject($xob, $id);
    }
    // assume we have a normal object with some fields to set
    else {
      $ob = $this->readObject($xob, $id);
    }
    return $ob;
  }

  /**
   * Checks if the node is an object reference
   * @param  DOMNode $xob
   * @return bool
   */
  private function reference(DOMNode $xob): bool {
    return $xob->hasAttribute(static::IDREF);
  }

  /**
   * Reads a node and generates proper object based on type
   * and then proceeds to set any fields it might have.
   * @param  DOMNode $xob
   * @param  int     $id  Node id (optional)
   * @return (array|object)
   */
  private function readObject(DOMNode $xob, $id)/*: Object*/ {
    $type = $xob->getAttribute(static::TYPE);
    if (array_key_exists($type, $this->classMap)) {
      $ob = new $this->classMap[$type]();
    }
    else {
      // @TODO - Might want to throw here instead or at least make this configurable
      $ob = new StdClass();
    }
    // 
    $this->setFields($ob, $xob);
    // @TODO - Cache the object in $this->map here so we can ref it later if needed
    return $ob;
  }

  /**
   * Sets the fields of an object
   * @param (array|object) &$ob The object to return (note this is byref)
   * @param DOMNode $xob
   * @return void
   */
  private function setFields(&$ob, DOMNode $xob) {
    foreach ($xob->childNodes as $childNode) {
      if ($childNode instanceof DOMElement) {
        $name = $childNode->getAttribute(static::NAME);
        $type = $childNode->getAttribute(static::TYPE);
        if (Util::isPrimitiveType($type)) {
          $ob->$name = Util::castToType($childNode->getAttribute(static::VALUE), $type);
        }
        else if ($type === static::ARRAY) {
          $ob = (array) $this->readElement($childNode);
        }
        else {
          // must be an object with only one child
          $ob->$name = $this->readElement($childNode);
        }
      }
    }
  }

  /**
   * Does this node contain an array?
   * @param  DOMNode $xob
   * @return boolean
   */
  private function isPrimitiveArray(DOMNode $xob): bool {
    if ($xob->getAttribute(static::TYPE) !== static::ARRAY) {
      return false;
    }
    if (!Util::isPrimitiveType($xob->getAttribute(static::ELEMENT_TYPE))) {
      return false;
    }
    // @TODO - This is such a bad idea ... what happens if the the array contains strings with spaces?
    $array = explode(' ', $xob->nodeValue);
    return is_array($array);
  }

  /**
   * Reads a primitive array from a node into a PHP array
   * @param  DOMNode $xob
   * @param  int     $id  (optional)
   * @return array
   */
  private function readPrimitiveArray(DOMNode $xob, $id) {
    $array = explode(' ', $xob->nodeValue);
    foreach ($array as $key => &$value) {
      $value = Util::castToType($value, $xob->getAttribute(static::ELEMENT_TYPE));
    }
    return $array;
  }

  /**
   * Reads an array of objects
   * @param  DOMNode $xob
   * @param  int     $id  (optional)
   * @return array
   */
  private function readObjectArray(DOMNode $xob, $id): array {
    $array = $this->readObjectArrayGeneric($xob, $id);
    // Caches object in $this->map for ref lookup
    $this->map->attach(new ArrayObject($array), $id);
    return $array;
  }

  /**
   * Reads an array of objects
   * @TODO - Doubt we even need this since PHP is so loosey-goosey with arrays
   * @param  DOMNode $xob
   * @param  int     $id  (optional)
   * @return array
   */
  private function readObjectArrayGeneric(DOMNode $xob, $id): array {
    $arrayTypeName = $xob->getAttribute(static::ELEMENT_TYPE);
    $array = [];
    // @TODO - Perhaps we want to do map caching here instead of in readObjectArray?
    //map.put(id, array);
    // now fill in the array
    foreach ($xob->childNodes as $childNode) {
      if ($childNode instanceof DOMElement) {
        $array[] = $this->readElement($childNode);
      }
    }
    return $array;
  }

}
