<?php

namespace Bstoots\WOX\Serial;

/**
 * 
 */
trait TypeMapping {
  
  //  integer, float, string or boolean

  public static function mapPhpToWox($phpType) {
    switch ($phpType) {
      // 'byte.class' => "byte",
      // 'short.class' => "short",
      case 'int':
      case 'integer':
        return 'int';
      // 'long.class' => "long",
      case 'double':
      case 'float':
        return 'double';
      // 'double.class' => "double",
      // 'char.class' => "char",
      case 'bool':
      case 'boolean':
        return 'boolean';
      // 'Byte.class' => "byte",
      // 'Short.class' => "short",
      // 'Integer.class' => "int",
      // 'Long.class' => "long",
      // 'Float.class' => "float",
      // 'Double.class' => "double",
      // 'Character.class' => "char",
      // 'Boolean.class' => "boolean",
      //initialise map of Java data types (String, Class and Array) to WOX data types
      case 'string':
        return 'string';
      // 'Class.class' => "class",
      //mapJavaToWOX.put(Array.class, "array");
      default:
        return null;
    }
  }

  public static function mapArrayPhpToWox($phpType) {

  }

  //**********************************************************
  /**Map from WOX types to Java types*/
  public static $MAP_WOX_TO_PHP = [
    //initialise map of WOX data types to Java data types
    "byte" =>  "byte.class",
    "short" =>  "short.class",
    "int" =>  "int.class",
    "long" =>  "long.class",
    "float" =>  "float.class",
    "double" =>  "double.class",
    "char" =>  "char.class",
    "boolean" =>  "boolean.class",
    "byteWrapper" =>  "Byte.class",
    "shortWrapper" =>  "Short.class",
    "intWrapper" =>  "Integer.class",
    "longWrapper" =>  "Long.class",
    "floatWrapper" =>  "Float.class",
    "doubleWrapper" =>  "Double.class",
    "charWrapper" =>  "Character.class",
    "booleanWrapper" =>  "Boolean.class",
    //initialise map of WOX data types (String, Class and Array) to Java data types
    "string", "String.class",
    "class", "Class.class",
    //mapWOXToJava.put("array", Array.class);
  ];

  /**Map from Java array types to WOX array types*/
  public static $MAP_ARRAY_PHP_TO_WOX = [
    //initialise map of Java primitive arrays to WOX arrays
    // byte[].class, "byte[]",
    // byte[][].class, "byte[][]",
    // byte[][][].class, "byte[][][]",
    // short[].class, "short[]",
    // short[][].class, "short[][]",
    // short[][][].class, "short[][][]",
    // int[].class, "int[]",
    // int[][].class, "int[][]",
    // int[][][].class, "int[][][]",
    // long[].class, "long[]",
    // long[][].class, "long[][]",
    // long[][][].class, "long[][][]",
    // float[].class, "float[]",
    // float[][].class, "float[][]",
    // float[][][].class, "float[][][]",
    // double[].class, "double[]",
    // double[][].class, "double[][]",
    // double[][][].class, "double[][][]",
    // char[].class, "char[]",
    // char[][].class, "char[][]",
    // char[][][].class, "char[][][]",
    // boolean[].class, "boolean[]",
    // boolean[][][].class, "boolean[][]",
    // boolean[][][].class, "boolean[][][]",
    //initialise map of Java wrapper arrays to WOX arrays
    // Byte[].class, "byteWrapper[]",
    // Byte[][].class, "byteWrapper[][]",
    // Byte[][][].class, "byteWrapper[][][]",
    // Short[].class, "shortWrapper[]",
    // Short[][].class, "shortWrapper[][]",
    // Short[][][].class, "shortWrapper[][][]",
    // Integer[].class, "intWrapper[]",
    // Integer[][].class, "intWrapper[][]",
    // Integer[][][].class, "intWrapper[][][]",
    // Long[].class, "longWrapper[]",
    // Long[][].class, "longWrapper[][]",
    // Long[][][].class, "longWrapper[][][]",
    // Float[].class, "floatWrapper[]",
    // Float[][].class, "floatWrapper[][]",
    // Float[][][].class, "floatWrapper[][][]",
    // Double[].class, "doubleWrapper[]",
    // Double[][].class, "doubleWrapper[][]",
    // Double[][][].class, "doubleWrapper[][][]",
    // Character[].class, "charWrapper[]",
    // Character[][].class, "charWrapper[][]",
    // Character[][][].class, "charWrapper[][][]",
    // Boolean[].class, "booleanWrapper[]",
    // Boolean[][].class, "booleanWrapper[][]",
    // Boolean[][][].class, "booleanWrapper[][][]",
    //initialise map of Java arrays of Class and String to WOX arrays
    // Class[].class, "class[]",
    // Class[][].class, "class[][]",
    // Class[][][].class, "class[][][]",
    // String[].class, "string[]",
    // String[][].class, "string[][]",
    // String[][][].class, "string[][][]",
  ];
  /**Map from WOX array types to Java array types*/
  public static $MAP_ARRAY_WOX_TO_JAVA = [
    //initialise map of WOX primitive arrays to Java arrays
    // "byte[]", byte[].class,
    // "byte[][]", byte[][].class,
    // "byte[][][]", byte[][][].class,
    // "short[]", short[].class,
    // "short[][]", short[][].class,
    // "short[][][]", short[][][].class,
    // "int[]", int[].class,
    // "int[][]", int[][].class,
    // "int[][][]", int[][][].class,
    // "long[]", long[].class,
    // "long[][]", long[][].class,
    // "long[][][]", long[][][].class,
    // "float[]", float[].class,
    // "float[][]", float[][].class,
    // "float[][][]", float[][][].class,
    // "double[]", double[].class,
    // "double[][]", double[][].class,
    // "double[][][]", double[][][].class,
    // "char[]", char[].class,
    // "char[][]", char[][].class,
    // "char[][][]", char[][][].class,
    // "boolean[]", boolean[].class,
    // "boolean[][]", boolean[][].class,
    // "boolean[][][]", boolean[][][].class,
    //initialise map of WOX wrapper arrays to Java wrapper arrays
    // "byteWrapper[]", Byte[].class,
    // "byteWrapper[][]", Byte[][].class,
    // "byteWrapper[][][]", Byte[][][].class,
    // "shortWrapper[]", Short[].class,
    // "shortWrapper[][]", Short[][].class,
    // "shortWrapper[][][]", Short[][][].class,
    // "intWrapper[]", Integer[].class,
    // "intWrapper[][]", Integer[][].class,
    // "intWrapper[][][]", Integer[][][].class,
    // "longWrapper[]", Long[].class,
    // "longWrapper[][]", Long[][].class,
    // "longWrapper[][][]", Long[][][].class,
    // "floatWrapper[]", Float[].class,
    // "floatWrapper[][]", Float[][].class,
    // "floatWrapper[][][]", Float[][][].class,
    // "doubleWrapper[]", Double[].class,
    // "doubleWrapper[][]", Double[][].class,
    // "doubleWrapper[][][]", Double[][][].class,
    // "charWrapper[]", Character[].class,
    // "charWrapper[][]", Character[][].class,
    // "charWrapper[][][]", Character[][][].class,
    // "booleanWrapper[]", Boolean[].class,
    // "booleanWrapper[][]", Boolean[][].class,
    // "booleanWrapper[][][]", Boolean[][][].class,
    //initialise map of WOX arrays of Class and String to Java arrays
    // "class[]", Class[].class,
    // "class[][]", Class[][].class,
    // "class[][][]", Class[][][].class,
    // "string[]", String[].class,
    // "string[][]", String[][].class,
    // "string[][][]", String[][][].class,
  ];

}
