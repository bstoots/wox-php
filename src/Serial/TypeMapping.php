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
        return $phpType;
    }
  }

  public static function mapWoxToPhp($woxType) {
    switch ($woxType) {
      case 'int':
        return 'integer';
      // 'long.class' => "long",
      case 'double':
      case 'float':
        return 'double';
      // 'double.class' => "double",
      // 'char.class' => "char",
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
        return $woxType;
    }
  }

}
