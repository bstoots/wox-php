<?php

namespace Bstoots\WOX\Serial;

interface Serial {
  // use string consts to enforce consistency
  // between readers and writers
  /**This is the OBJECT tag used in the XML representation of an object*/
  const OBJECT = "object";
  /**This is the FIELD tag used in the XML representation of an object's field*/
  const FIELD = "field";
  /**This is the NAME attribute used in the XML representation of an object*/
  const NAME = "name";
  /**This is the TYPE attribute used in the XML representation of an object*/
  const TYPE = "type";
  /**This is the VALUE attribute used in the XML representation of an object*/
  const VALUE = "value";
  /**This is the ARRAY attribute used in the XML representation of an array*/
  const ARRAY = "array";
  /**This is the ARRAYLIST attribute used in the XML representation of a list*/
  const ARRAYLIST = "list";
  /**This is the ELEMENT_TYPE attribute used in the XML representation of an array*/
  const ELEMENT_TYPE = "elementType";
  /**This is the MAP attribute used in the XML representation of a map*/
  const MAP = "map";
  /**This is the KEY_TYPE attribute used in the XML representation of a map*/
  const KEY_TYPE = "keyType";
  /**This is the VALUE_TYPE attribute used in the XML representation of a map*/
  const VALUE_TYPE = "valueType";
  /**This is the ENTRY attribute used in the XML representation of a map*/
  const ENTRY = "entry";
  /**This is the KEY attribute used in the XML representation of a map*/
  const KEY = "key";
  /**This is the LENGTH attribute used in the XML representation of an array*/
  const LENGTH = "length";
  /**This is the ID attribute used in the XML representation of an object*/
  const ID = "id";
  /**This is the ID attribute used in the XML representation of an object*/
  const IDREF = "idref";
  // next is used to disambiguate shadowed fields
  /**This is the DECLARED attribute used in the XML representation of an object*/
  const DECLARED = "declaredClass";
}


// public interface Serial {
//     /**Array of classes that represent the classes of primitive arrays.*/
//     public static final Class[] primitiveArrays =
//             new Class[]{
//                 int[].class,
//                 boolean[].class,
//                 byte[].class,
//                 short[].class,
//                 long[].class,
//                 char[].class,
//                 float[].class,
//                 double[].class,
//                 //added Nov 2007 for wrappers
//                 Integer[].class,
//                 Boolean[].class,
//                 Byte[].class,
//                 Short[].class,
//                 Long[].class,
//                 Character[].class,
//                 Float[].class,
//                 Double[].class,
//                 //added Nov 2007 for Class.class
//                 Class[].class
//             };

//     /**Array of classes that represent the WOX primitive arrays.*/
//     const[] primitiveArraysWOX =
//             new String[]{
//                 "int",
//                 "boolean",
//                 "byte",
//                 "short",
//                 "long",
//                 "char",
//                 "float",
//                 "double",
//                 //added Nov 2007 for wrappers
//                 "intWrapper",
//                 "booleanWrapper",
//                 "byteWrapper",
//                 "shortWrapper",
//                 "longWrapper",
//                 "charWrapper",
//                 "floatWrapper",
//                 "doubleWrapper",
//                 //added Nov 2007 for Class.class
//                 "class"
//             };

//     // now declare the wrapper classes for each primitive object type
//     // note that this order must correspond to the order in primitiveArrays

//     // there may be a better way of doing this that does not involve
//     // wrapper objects (e.g. Integer is the wrapper of int), but I've
//     // yet to find it
//     // note that the creation of wrapper objects is a significant
//     // overhead
//     // example: reading an array of 1 million int (all zeros) takes
//     // about 900ms using reflection, versus 350ms hard-coded
//     /**Array of classes that represent the wrapper classes for primitives.*/
//     public static final Class[] primitiveWrappers =
//             new Class[]{
//                 Integer.class,
//                 Boolean.class,
//                 Byte.class,
//                 Short.class,
//                 Long.class,
//                 Character.class,
//                 Float.class,
//                 Double.class
//             };

//     /**Array of classes that represent the classes of primitives.*/
//     public static final Class[] primitives =
//             new Class[]{
//                 int.class,
//                 boolean.class,
//                 byte.class,
//                 short.class,
//                 long.class,
//                 char.class,
//                 float.class,
//                 double.class
//             };
// }