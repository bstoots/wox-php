<?php

namespace Bstoots\WOX\Serial;

use DOMDocument;
use DOMNode;
use ArrayObject;
use SplObjectStorage;

/**
 * 
 */
abstract class Writer implements ObjectWriter {
  use TypeMapping;

  /**
   * @var DOMDocument
   */
  protected $dom;

  /**
   * @var SplObjectStorage
   */
  protected $map;

  /**
   * @var integer
   */
  protected $count = 0;
  
  /**
   * @var boolean
   */
  protected $writePrimitiveTypes = true;

  /**
   * @var boolean
   */
  protected $doStatic = true;

  // not much point writing out final values - at least yet -
  // the reader is not able to set them (though there's probably
  // a hidden way of doing this
  protected $doFinal = false;

  /**
   * Yields a single key/value pair for each serializable field
   * @param  object $ob
   * @return array
   */
  abstract protected function yieldFields($ob);

  /**
   * @param array $config Configuration array with the following structure:
   * [
   *   writePrimitiveTypes - bool - TBD
   *   doStatic            - bool - True to serialize static properties
   *   doFinal             - bool - True to serialize final properties
   * ]
   */
  public function __construct(array $config = []) {
    if (array_key_exists('writePrimitiveTypes', $config)) {
      $this->writePrimitiveTypes = $config['writePrimitiveTypes'];
    }
    if (array_key_exists('doStatic', $config)) {
      $this->doStatic = $config['doStatic'];
    }
    if (array_key_exists('doFinal', $config)) {
      $this->doFinal = $config['doFinal'];
    }
  }

  /**
   * Public entry point into SimpleWriter.  Takes an array or object and 
   * serializes it to a WOX XML string
   * @param  (array|object) $ob Array or object
   * @return string
   */
  public function write($ob): string {
    if ($ob !== null && !is_array($ob) && !is_object($ob)) {
      throw new \Exception('write only accepts arrays or objects, got: ' . var_export($ob, true));
    }
    $this->dom = new DOMDocument();
    $this->map = new SplObjectStorage();
    $this->count = 0;
    $element = $this->writeElement($ob, $this->dom);
    $this->dom->appendChild($element);
    return $this->dom->saveXML();
  }

  /**
   * Parses an array or object returning an equivalent DOMNode
   * @param  (array|object) $ob
   * @return DOMNode
   */
  private function writeElement($ob): DOMNode {
    if ($ob === null) {
      $element = $this->dom->createElement(static::OBJECT);
      return $element;
    }

    // Convert associative arrays to objects to avoid misuse
    if (Util::isAssoc($ob)) {
      $ob = (object) $ob;
    }

    if (is_array($ob)) {
      $ob = new ArrayObject($ob);
    }

    // @TODO - This enables references to be set within the document.  I want to re-enable this
    //         after I sort out a few other things.
    // if the object is already in the map, print its IDREF and not the whole object
    // <object idref="5" />
    // if ($this->map->contains($ob) === true) {
    //   $element = $this->dom->createElement(static::OBJECT);
    //   $element->setAttribute(static::IDREF, $this->map->offsetGet($ob));
    //   // $this->dom->appendChild($element);
    //   return $element;
    // }

    // a previously unseen object... put is in the map
    $this->map->attach($ob, $this->count++);

    //check if the object can go to string (wrapper objects - Integer, Boolean, etc.)
    if ( Util::isStringable($ob) ) {
      $element = $this->dom->createElement(static::OBJECT);
      // @TODO - When will this case ever be encountered?
      $woxType = 'SOMETHING';
      $element->setAttribute(static::TYPE, $woxType);
      $element->setAttribute(static::VALUE, Util::stringify($ob));
    }
    else if ($ob instanceof ArrayObject) {
      $element = $this->writeArray($ob);
    }
    else {
      $element = $this->dom->createElement(static::OBJECT);
      // @TODO - Instead of hard-coding basename here we may want to do some sort of 
      //         reverse classMap as we did in the SimpleReader implementation.
      $element->setAttribute(static::TYPE, Util::getType($ob, Util::TYPE_MODE_SHORT));
      $this->writeFields($ob, $element, $this->dom);
    }
    $id = '';
    // @TODO - Reimplement reference lookups
    // if (is_object($ob) && $this->map->offsetExists($ob)) {
    //   $id = $this->map->offsetGet($ob);
    // }
    $element->setAttribute(static::ID, $this->map->offsetGet($ob));
    return $element;
  }

  /**
   * Writes fields from object to node
   * @param  object  $ob
   * @param  DOMNode $parent
   * @return void
   */
  private function writeFields($ob, DOMNode $parent) {
    foreach ($this->yieldFields($ob) as $field) {
      // Uses more memory but lessens array access.  Not sure which is better yet.
      $name = key($field);
      $value = reset($field);
      //if the field is a primitive data type (int, float, etc.)
      $field = $this->dom->createElement(static::FIELD);
      if ( Util::isStringable($value) ) {
        $field->setAttribute(static::NAME, $name);
        $field->setAttribute(static::TYPE, static::mapPhpToWox(Util::getType($value, Util::TYPE_MODE_SHORT)));
        $field->setAttribute(static::VALUE, Util::stringify($value));
      }
      // 
      else if ($value === null) {
        // @TODO - Not sure how I want to handle this yet.  Ideally we would encode the nullness
        //         in the XML structure.  We will know the name but not the type since type
        //         determination is done on the fly.  Have to mull it over but for now just
        //         skip the element altogether.
        continue;
      }
      // if the field is in the map (it could be a Wrapper or a String object)
      // this aims to have a more compact encoding in the XML file.
      else if (/*mapJavaToWOX.get(fields[i].getType()) != null*/ false) {
        // @TODO - Do we actually need this case at all?
      }
      // if the field is NOT a primitive data type (e.g. it is an object)
      else {
        $field->setAttribute(static::NAME, $name);
        $field->appendChild($this->writeElement($value, $this->dom));
      }
      $parent->appendChild($field);
    }

  }

  /**
   * This method writes an array: primitive or object array
   * @param  (array|object) $ob
   * @return DOMNode
   */
  private function writeArray($ob): DOMNode {
    // a primitive array is an array of any of the following:
    // int, float, boolean, etc
    // These arrays can go easily to a string with spaces separating their elements.
    if (Util::isPrimitiveArray($ob)) {
      return $this->writePrimitiveArray($ob);
    }
    else {
      return $this->writeObjectArray($ob);
    }
  }

  /**
   * This method writes an array of primitives
   * @param  (array|object) $ob
   * @return DOMNode
   */
  private function writePrimitiveArray($ob): DOMNode {
    // Element el = new Element(OBJECT);
    $element = $this->dom->createElement(static::OBJECT);
    // el.setAttribute(TYPE, ARRAY);
    $element->setAttribute(static::TYPE, static::ARRAY);
    //it gets the correct WOX type from the map, in case there is one
    //for example for int[][].class it will get int[][]
    // String woxType = (String)mapJavaToWOX.get(ob.getClass().getComponentType());
    $woxType = static::mapPhpToWox(Util::getArrayType($ob, Util::TYPE_MODE_SHORT));
    // el.setAttribute(ELEMENT_TYPE, woxType);
    $element->setAttribute(static::ELEMENT_TYPE, $woxType);
    $element->setAttribute(static::LENGTH, count($ob));
    // 
    $element->appendChild(
      $this->dom->createTextNode(
        $this->arrayString($ob, count($ob))
      )
    );
    return $element;
  }

  /**
   * This method writes an object array
   * @param  (array|object) $ob
   * @return DOMNode
   */
  private function writeObjectArray($ob): DOMNode {
      // Element el = new Element(OBJECT);
      $element = $this->dom->createElement(static::OBJECT);
      // el.setAttribute(TYPE, ARRAY);
      $element->setAttribute(static::TYPE, static::ARRAY);
      return $this->writeObjectArrayGeneric($ob, $element);
  }

  /**
   * This method writes an object array
   * @param  (array|object) $ob
   * @param  DOMNode $element Parent node
   * @return DOMNode
   */
  private function writeObjectArrayGeneric($ob, DOMNode $element): DOMNode {
    $arrayType = Util::getArrayType($ob, Util::TYPE_MODE_SHORT);
    $woxType = static::mapPhpToWox($arrayType);
    if ($woxType === null) {
      $woxType = $arrayType;
    }
    $element->setAttribute(static::ELEMENT_TYPE, $woxType);
    foreach ($ob as $value) {
      $element->appendChild($this->writeElement($value));
    }
    $element->setAttribute(static::LENGTH, count($ob));
    return $element;
  }

  /**
   * This method writes an array to a string
   * @param  (array|ArrayObject) $ob
   * @param  int                 $len
   * @return string
   */
  private function arrayString($ob, int $len): string {
    if ($ob instanceof ArrayObject) {
      $ob = $ob->getArrayCopy();
    }
    // @TODO - This is such a bad idea ... what happens if the the array contains strings with spaces?
    return implode(' ', $ob);
  }

}
