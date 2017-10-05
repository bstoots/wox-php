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
