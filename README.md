# wox-php

[![Build Status](https://travis-ci.org/bstoots/wox-php.svg?branch=master)](https://travis-ci.org/bstoots/wox-php)

WOX is an XML serializer for PHP

(based on http://woxserializer.sourceforge.net/)

## Features

Some of the WOX main features are listed below:

- Easy to use. The Easy class provides serialization and de-serialization methods.
- Simple XML. The XML generated is simple, easy to understand, and language independent.
- Requires no class modifications. Classes do not require to have default constructors, getters or setters.
- Field visibility. Currently only serializes public properties.  This may change in the future to match Java and C# implementations.
- Interoperability Java, C#, and now PHP. WOX can (de)serialize Java, C#, or PHP objects to XML, and reconstruct the XML back to any of the supported languages.
- Standard XML object representation. The goal is parity between Java, C#, and PHP.  Possibly others in the future.
- WOX data types. The WOX mapping table specifies how primitive data types are mapped to WOX data types.
- Robust to class changes. Defaults will be used for newly added fields.
- Arrays. Handles arrays and multi-dimensional arrays of primitives and Objects.
- Class and Type. Objects of these classes are saved by their String name.
- Small footprint. Strives to use only PHP built-ins where ever possible.

## Usage

TODO

## Example

```php
use Bstoots\WOX\Serial\{SimpleWriter, SimpleReader};

$doubles = [12.45, 878.98, 987.98, 435.87, 537.87, 89.0, 0.0, 667.332];

$writer = new SimpleWriter();
$xml = $writer->write($doubles);
var_dump($xml);
// string(138) "<?xml version="1.0"?>
// <object type="array" elementType="double" length="8" id="0">12.45 878.98 987.98 435.87 537.87 89 0 667.332</object>
// "

$reader = new SimpleReader();
$sameDoubles = $reader->read($xml);
var_dump($sameDoubles);
// array(8) {
//   [0]=>
//   float(12.45)
//   [1]=>
//   float(878.98)
//   [2]=>
//   float(987.98)
//   [3]=>
//   float(435.87)
//   [4]=>
//   float(537.87)
//   [5]=>
//   float(89)
//   [6]=>
//   float(0)
//   [7]=>
//   float(667.332)
// }
```
