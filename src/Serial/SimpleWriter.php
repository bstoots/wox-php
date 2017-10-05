<?php

namespace Bstoots\WOX\Serial;

use DOMDocument;
use ArrayObject;
use SplObjectStorage;

/**
 * 
 */
class SimpleWriter implements ObjectWriter {
  use TypeMapping;

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

  public function write($ob): string {
    $this->initialize();
    $element = $this->writeElement($ob, $this->dom);
    $this->dom->appendChild($element);
    return $this->dom->saveXML();
  }

  private function initialize() {
    $this->dom = new DOMDocument();
    $this->map = new SplObjectStorage();
    $this->count = 0;
    $this->writePrimitiveTypes = true;
    $this->doStatic = true;
  }

  /**
   * This is the only public method of <code>SimpleWriter</code>.
   * This method is the entry point to write an object to XML. It actually
   * returns a JDOM Element representing the object passed as parameter.
   * The JDOM element is a standard XML representation defined by WOX. It
   * can be used to be stored in an XML file, or any other storage media.
   * @param ob The java object to be serialized.
   * @return The serialized object as a JDOM element.
   */
  private function writeElement(/*Object*/ $ob) /*: Element*/ {
    //$this->dom = new DOMDocument();
    if ($ob === null) {
      $element = $this->dom->createElement(static::OBJECT);
      // $this->dom->appendChild($element);
      return $element;
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
    if ( is_string(($obString = Util::stringify($ob))) ) {
      // el = new Element(OBJECT);
      $element = $this->dom->createElement(static::OBJECT);
      // String woxType = (String)mapJavaToWOX.get(ob.getClass());
      $woxType = 'SOMETHING';
      // el.setAttribute(TYPE, woxType);
      $element->setAttribute(static::TYPE, $woxType);
      // el.setAttribute(VALUE, stringify(ob));
      $element->setAttribute(static::VALUE, $obString);
    }
    else if ($ob instanceof ArrayObject) {
      $element = $this->writeArray($ob);
    }
    // //this is to handle ArrayList objects (added 25 April)
    // else if (ob instanceof java.util.ArrayList){
    //     el = writeArrayList(ob);
    // }
    // //this is to handle HashMap objects (added 30 April)
    // else if (ob instanceof java.util.HashMap){
    //     el = writeHashMap(ob);
    // }
    else {
      $element = $this->dom->createElement(static::OBJECT);
      $element->setAttribute(static::TYPE, basename(get_class($ob)));
      $this->writeFields($ob, $element, $this->dom);
    }
    $id = '';
    // if (is_object($ob) && $this->map->offsetExists($ob)) {
    //   $id = $this->map->offsetGet($ob);
    // }
    $element->setAttribute(static::ID, $this->map->offsetGet($ob));
    // $this->dom->appendChild($element);
    return $element;
  }

  /**
   * This method writes the fields of an object
   * @param o The object where the fields are.
   * @param parent The JDOM Element where they will be added to.
   */
  private function writeFields($ob, $parent) {
    // get the class of the object
    // get its fields
    // then get the value of each one
    // and call write to put the value in the Element

    //Class cl = o.getClass();
    // Field[] fields = getFields(cl);
    $fields = array_keys(get_object_vars($ob));
    foreach ($fields as $name) {
      // @TODO - WOX Java implementation uses reflection to grab all properties regardless of 
      //         visibility.  I'm not in love with this so I'm going to circle back to it.
      //         For now only public properties are serializable.
      $value = $ob->$name;
      //if the field is a primitive data type (int, float, char, etc.)
      $field = $this->dom->createElement(static::FIELD);
      if ( is_string(($valueString = Util::stringify($ob->$name))) ) {
        $field->setAttribute(static::NAME, $name);
        $field->setAttribute(static::TYPE, static::mapPhpToWox(Util::getType($ob->$name)));
        $field->setAttribute(static::VALUE, $valueString);
      }
      // if the field is in the map (it could be a Wrapper or a String object)
      // this aims to have a more compact encoding in the XML file.
      else if (/*mapJavaToWOX.get(fields[i].getType()) != null*/ false) {
        // @TODO 
      }
      // if the field is NOT a primitive data type (e.g. it is an object)
      else {
        $field->setAttribute(static::NAME, $name);
        $field->appendChild($this->writeElement($ob->$name, $this->dom));
      }
      $parent->appendChild($field);
    }

    // for ($i = 0; i < fields.length; i++) {
      // if ((doStatic || !Modifier.isStatic(fields[i].getModifiers())) &&
      //               (doFinal || !Modifier.isFinal(fields[i].getModifiers())))
      //           try {
      //               fields[i].setAccessible(true);
      //               name = fields[i].getName();
      //               // need to handle shadowed fields in some way...
      //               // one way is to add info about the declaring class
      //               // but this will bloat the XML file if we di it for
      //               // every field - might be better to just do it for
      //               // the shadowed fields
      //               // name += "." + fields[i].getDeclaringClass().getName();
      //               // fields[i].
      //               Object value = fields[i].get(o);
      //               Element field = new Element(FIELD);
      //               field.setAttribute(NAME, name);                    
      //               if (shadowed(fields, name)) {
      //                   field.setAttribute(DECLARED, fields[i].getDeclaringClass().getName());
      //               }
      //               //if the field is a primitive data type (int, float, char, etc.)
      //               if (fields[i].getType().isPrimitive()) {
      //                   // this is not always necessary - so it's optional
      //                   if (writePrimitiveTypes) {
      //                       field.setAttribute(TYPE, fields[i].getType().getName());
      //                   }
      //                   //if it is a char primitive, then we must store its unicode value (June 2007)
      //                   if (fields[i].getType().getName().equals("char")){
      //                       //System.out.println("IT IS A CHAR...");
      //                       Character myChar = (Character)value;
      //                       String unicodeValue = getUnicodeValue(myChar);
      //                       field.setAttribute(VALUE, unicodeValue);
      //                   }
      //                   //for the rest of the primitives, we store their values as string
      //                   else{
      //                       field.setAttribute(VALUE, value.toString());
      //                   }

      //               }
      //               //if the field is in the map (it could be a Wrapper or a String object)
      //               //this aims to have a more compact encoding in the XML file.
      //               else if (mapJavaToWOX.get(fields[i].getType())!=null){
      //                   String woxType = (String)mapJavaToWOX.get(value.getClass());
      //                   field.setAttribute(TYPE, woxType);
      //                   field.setAttribute(VALUE, stringify(value));
      //               }
      //               //if the field is NOT a primitive data type (e.g. it is an object)
      //               else {
      //                   field.addContent(write(value));

      //               }
      //               parent.addContent(field);

      //           }
      //           catch (\Exception e) {
      //               e.printStackTrace();
      //               System.out.println(e);
      //               // at least comment on what went wrong
      //               parent.addContent(new Comment(e.toString()));
      //           }
      //   }
        // 
  }

  /**
   * This method writes an array: primitive or object array.
   * @param ob The object to be serialized (it should be an array)
   * @return A JDOM Element
   */
  private function writeArray(/*Object*/ $ob)/*: Element*/ {
    //a primitive array is an array of any of the following:
    //byte, short, int, long, float, double, char, boolean,
    //Byte, Short, Integer, Long, Float, Double, Character, Boolean, and Class
    //These arrays can go easily to a string with spaces separating their elements.
    if (Util::isPrimitiveArray($ob)) {
      //System.out.println("-----------------PRIMITIVE ARRAY------------------");
      return $this->writePrimitiveArray($ob);
    }
    else {
      //System.out.println("-----------------NOT A PRIMITIVE ARRAY------------------");
      return $this->writeObjectArray($ob);
    }
  }

  /**
   * This method writes an array of primitives.
   * @param ob The object to be serialized (it should be an array)
   * @return A JDOM Element
   */
  private function writePrimitiveArray(/*Object*/ $ob)/*: Element*/ {
      // Element el = new Element(OBJECT);
      $element = $this->dom->createElement(static::OBJECT);
      // el.setAttribute(TYPE, ARRAY);
      $element->setAttribute(static::TYPE, static::ARRAY);
      //it gets the correct WOX type from the map, in case there is one
      //for example for int[][].class it will get int[][]
      // String woxType = (String)mapJavaToWOX.get(ob.getClass().getComponentType());
      $woxType = static::mapPhpToWox(Util::getArrayType($ob));
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
   * This method writes an object array.
   * @param ob The object to be serialized (it should be an array of objects)
   * @return A JDOM Element
   */
  private function writeObjectArray(/*Object*/ $ob)/*: Element*/ {
      // Element el = new Element(OBJECT);
      $element = $this->dom->createElement(static::OBJECT);
      // el.setAttribute(TYPE, ARRAY);
      $element->setAttribute(static::TYPE, static::ARRAY);
      return $this->writeObjectArrayGeneric($ob, $element);
  }

  /**
   * This method writes an object array.
   * @param ob The object to be serialized (it should be an array of objects)
   * @return A JDOM Element
   */
  private function writeObjectArrayGeneric(/*Object*/ $ob, /*Element*/ $element)/*: Element*/ {
      //Element el = new Element(ARRAY);
      //Element el = new Element(OBJECT);
      //el.setAttribute(TYPE, ARRAY);
      // el.setAttribute
      // int[].class.
      // Array.
      //el.setAttribute(TYPE, ob.getClass().getComponentType().getName());
      //it gets the correct WOX type from the map, in case there is one
      //for example for int[][].class it will get int[][]
      // String woxType = (String)mapJavaToWOX.get(ob.getClass().getComponentType());
      $arrayType = Util::getArrayType($ob);
      $woxType = static::mapPhpToWox($arrayType); //static::MAP_PHP_TO_WOX[gettype($ob)];
      if ($woxType === null) {
        $woxType = $arrayType;
      }
      //System.out.println("Type found in mapJavaToWOX: " + woxType);
      //el.setAttribute(TYPE, woxType);
      $element->setAttribute(static::ELEMENT_TYPE, $woxType);
      // int len = Array.getLength(ob);
      // el.setAttribute(LENGTH, "" + len);
      // for (int i = 0; i < len; i++) {
      //     el.addContent(write(Array.get(ob, i)));
      // }
      foreach ($ob as $value) {
        $element->appendChild($this->writeElement($value));
      }
      $element->setAttribute(static::LENGTH, count($ob));
      return $element;
  }

  /**
   * This method writes an array.
   * @param ob The array to be serialized
   * @param len The lenght of the array
   * @return A String with the array serialized
   */
  private function arrayString(/*Object*/ $ob, int $len)/*: String*/ {
      if ($ob instanceof ArrayObject) {
        $ob = $ob->getArrayCopy();
      }
      // @TODO - This is such a bad idea ... what happens if the the array contains strings with spaces?
      return implode(' ', $ob);
      /*
      StringBuffer sb = new StringBuffer();
      for (int i = 0; i < len; i++) {
          if (i > 0) {
              sb.append(" ");
          }
          //if it is an array of Class objects
          if (ob instanceof Class[]){
              //we have to handle null values for arrays of Class objects
              Class arrayElement = (Class)Array.get(ob, i);
              if (arrayElement != null){
                  //get the correct WOX type if it exists
                  String woxType = (String)mapJavaToWOX.get(arrayElement);
                  if (woxType != null){
                      sb.append(woxType);
                  }
                  else{
                      //get the correct WOX array type if it exists
                      woxType = (String)mapArrayJavaToWOX.get(arrayElement);
                      if (woxType != null){
                          sb.append(woxType);
                      }
                      else{
                          sb.append(arrayElement.getName());
                      }

                  }
              }
              else{
                  sb.append("null");
              }
          }
          //if it is an array of char, we must get its unicode representation (June 2007)
          else if ( (ob instanceof char[]) || (ob instanceof Character[]) ){
              //we have to handle null values for arrays of Character
              //this is not necessary for arrays of primitive char because null values do not exist!
              Object arrayElement = Array.get(ob, i);
              if (arrayElement != null){
                  Character myChar = (Character)Array.get(ob, i);
                  sb.append(getUnicodeValue(myChar));
              }
              else{
                  sb.append("null");
              }
          }
          //for the rest of data types, we just append the values as string
          //it also includes wrapper data types: Integer, Short, Boolean, etc.
          else{
              //we have to handle null values for arrays of wrappers and arrays of Class objects
              //this is not necessary for arrays of primitives because null values do not exist!
              Object arrayElement = Array.get(ob, i);
              if (arrayElement != null){
                  sb.append(arrayElement.toString());
              }
              else{
                  sb.append("null");
              }

          }
      }
      return sb.toString();
      */
  }

    // 
    // Java Implementation
    // 

  // /**
  //  * It serializes a HashMap to XML. It returns a JDOM Element representing the HashMap.
  //  * @param ob The object to be serialized (it should be a HashMap)
  //  * @return A JDOM Element.
  //  */
  // private function writeHashMap(/*Object*/ $ob) /*: Element*/ {
  //   //Element el = new Element(ARRAYLIST);
  //   Element el = new Element(OBJECT);
  //   //the type for this object is "map"
  //   el.setAttribute(TYPE, MAP);
  //   //we already know it is an ArrayList, so, we get the underlying array
  //   //and pass it to the "writeObjectArrayGeneric" method to get it serialized
  //   HashMap hashMap = (HashMap)ob;
  //   //get the entry set
  //   Set keys = hashMap.entrySet();
  //   //el.setAttribute(LENGTH, "" + keys.size());
  //   //for each element in the entry set I have to create an entry object
  //   Iterator it = keys.iterator();
  //   while (it.hasNext()){
  //       Map.Entry entryMap= (Map.Entry)it.next();
  //       el.addContent(writeMapEntry(entryMap));
  //       //System.out.println("*KEY* " + entryMap.getKey() + ", *OBJECT* " + entryMap.getValue());
  //   }
  //   return el;
  // }

  //   /**
  //    * This method writes a map entry.
  //    * @param ob The object to be serialized (it should be an Entry)
  //    * @return A JDOM Element
  //    */
  //   private Element writeMapEntry(Object ob){
  //       //Element el = new Element(ARRAYLIST);
  //       Element el = new Element(OBJECT);
  //       //the type for this object is "map"
  //       el.setAttribute(TYPE, ENTRY);
  //       //lets cast the object to a Map.Entry
  //       Map.Entry entry = (Map.Entry)ob;
  //       el.addContent(writeMapEntryKey(entry.getKey()));
  //       el.addContent(writeMapEntryKey(entry.getValue()));
  //       return el;
  //   }

  //   /**
  //    * This method writes a map entry key.
  //    * @param ob The object to be serialized (it should be an Entry Key)
  //    * @return A JDOM Element
  //    */
  //   private Element writeMapEntryKey(Object ob){
  //       //Element el = new Element(ARRAYLIST);
  //       /*Element el = new Element(FIELD);
  //       //the type for this object is "map"
  //       el.setAttribute(TYPE, KEY);
  //       el.addContent(write(ob));
  //       return el;*/
  //       return write(ob);
  //   }

  //   /**
  //    * This method writes a map entry value.
  //    * @param ob The object to be serialized (it should be an Entry Value)
  //    * @return A JDOM Element
  //    */
  //   private Element writeMapEntryValue(Object ob){
  //       //Element el = new Element(ARRAYLIST);
  //       /*Element el = new Element(FIELD);
  //       //the type for this object is "map"
  //       el.setAttribute(TYPE, VALUE);
  //       el.addContent(write(ob));
  //       return el;*/
  //       return write(ob);
  //   }

  //   /**
  //    * This method writes an ArrayList.
  //    * @param ob The object to be serialized (it should be an ArrayList)
  //    * @return A JDOM Element
  //    */
  //   private Element writeArrayList(Object ob){
  //       //Element el = new Element(ARRAYLIST);
  //       Element el = new Element(OBJECT);
  //       //the type for this object is "arrayList"
  //       el.setAttribute(TYPE, ARRAYLIST);
  //       //we already know it is an ArrayList, so, we get the underlying array
  //       //and pass it to the "writeObjectArrayGeneric" method to get it serialized
  //       ArrayList list = (ArrayList)ob;
  //       ob = list.toArray();        

  //       return writeObjectArrayGeneric(ob, el);
  //   }








  //   /**
  //    * This method writes an array of byte.
  //    * @param a The object array to be serialized
  //    * @return A String with the byte array encoded base64
  //    */
  //   private String byteArrayString(byte[] a, Element e) {
  //       byte[] target = EncodeBase64.encode(a);
  //       //set the lenght fro the new encoded array
  //       e.setAttribute(LENGTH, "" + target.length);
  //       String strTarget = new String(target);
  //       return strTarget;
  //   }


  //   /**
  //    * Gets the unicode value for a specified Character value
  //    * @param character The char value.
  //    * @return A string with the unicode value.
  //    */
  //   private static String getUnicodeValue(Character character){
  //       int asciiValue = (int)character.charValue();
  //       String hexValue = Integer.toHexString(asciiValue);
  //       String unicodeValue = "\\u" + fillWithZeros(hexValue);
  //       //System.out.println("ASCII: " + asciiValue + ", HEX: " + hexValue + ", UNICODE: " + unicodeValue);
  //       return unicodeValue;
  //   }


  //   /**
  //    * It fills with zeros a hexadecimal value, when needed.
  //    * @param hexValue The hexadecimal value to be filled.
  //    * @return A string filled with zeros if needed.
  //    */
  //   private static String fillWithZeros(String hexValue){
  //       int len = hexValue.length();
  //       switch (len){
  //           case 1:
  //               return ("000" + hexValue);
  //           case 2:
  //               return ("00" + hexValue);
  //           case 3:
  //               return ("0" + hexValue);
  //           default:
  //               return hexValue;
  //       }
  //   }


  //   private boolean shadowed(Field[] fields, String fieldName) {
  //       // count the number of fields with the name fieldName
  //       // return true if greater than 1
  //       int count = 0;
  //       for (int i = 0; i < fields.length; i++) {
  //           if (fieldName.equals(fields[i].getName())) {
  //               count++;
  //           }
  //       }
  //       return count > 1;
  //   }

  //   /**
  //    * It gets the string representation of an object.
  //    * @param ob The object to be converted.
  //    * @return The string value of the object
  //    */
  //   private static String stringify(Object ob) {
  //       //if it is a Class, we only get the class name
  //       if (ob instanceof Class) {
  //           //get the correct WOX type if it exists
  //           String woxType = (String)mapJavaToWOX.get((Class)ob);
  //           if (woxType!= null){
  //               return woxType;
  //           }
  //           else{
  //               return ((Class) ob).getName();
  //           }
  //           //return ((Class) ob).getName();
  //       }
  //       // if it is a Character we must get the unicode representation
  //       else if (ob instanceof Character){
  //           return (getUnicodeValue((Character)ob));
  //       }
  //       //if is is any of the other wrapper classes
  //       else{
  //           return ob.toString();
  //       }
  //   }

  //   /**
  //    * It gets the fields of a class, and return them as an array.
  //    * @param c The class to be used.
  //    * @return An array with the fields.
  //    */
  //   private static Field[] getFields(Class c) {
  //       Vector v = new Vector();
  //       while (!(c == null)) { // c.equals( Object.class ) ) {
  //           Field[] fields = c.getDeclaredFields();
  //           for (int i = 0; i < fields.length; i++) {
  //               // System.out.println(fields[i]);
  //               v.addElement(fields[i]);
  //           }
  //           c = c.getSuperclass();
  //       }
  //       Field[] f = new Field[v.size()];
  //       for (int i = 0; i < f.length; i++) {
  //           f[i] = (Field) v.get(i);
  //       }
  //       return f;
  //   }

  //   private static Object[] getValues(Object o, Field[] fields) {
  //       Object[] values = new Object[fields.length];
  //       for (int i = 0; i < fields.length; i++) {
  //           try {
  //               fields[i].setAccessible(true);
  //               values[i] = fields[i].get(o);
  //               //System.out.println(fields[i].getName() + "\t " + values[i]);
  //           } catch (Exception e) {
  //               System.out.println(e);
  //           }
  //       }
  //       return values;
  //   }


}
