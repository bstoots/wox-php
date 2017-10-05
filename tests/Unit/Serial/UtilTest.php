<?php
declare(strict_types=1);

namespace Bstoots\WOX\Tests\Unit\Serial;

use ArrayObject;
use PHPUnit\Framework\TestCase;
use Bstoots\WOX\Tests\Stubs\{Student, Course};
use Bstoots\WOX\Serial\{Util};

/**
 * @covers Util
 */
final class UtilTest extends TestCase {
  
  /**
   * @dataProvider primitiveProvider
   */
  public function testIsPrimitiveTrue($string, $value) {
    $this->assertTrue(Util::isPrimitive($value));
  }

  /**
   * @dataProvider nonPrimitiveProvider
   */
  public function testIsPrimitiveFalse($value) {
    $this->assertFalse(Util::isPrimitive($value));
  }

  /**
   * @dataProvider primitiveProvider
   */
  public function testIsPrimitiveArrayTrue($string, $value) {
    $array = array_fill(0, 5, $value);
    $arrayObject = new ArrayObject($array);
    $this->assertTrue(Util::isPrimitiveArray($array));
    $this->assertTrue(Util::isPrimitiveArray($arrayObject));
  }

  /**
   * @dataProvider nonPrimitiveProvider
   */
  public function testIsPrimitiveArrayFalse($value) {
    $array = array_fill(0, 5, $value);
    $arrayObject = new ArrayObject($array);
    $this->assertFalse(Util::isPrimitiveArray($array));
    $this->assertFalse(Util::isPrimitiveArray($arrayObject));
  }

  /**
   * @dataProvider primitiveProvider
   */
  public function testStringifySuccess($string, $value) {
    $this->assertEquals($string, Util::stringify($value));
  }

  /**
   * @dataProvider nonPrimitiveProvider
   * @expectedException \Exception
   */
  public function testStringifyException($value) {
    Util::stringify($value);
  }

  /**
   * @dataProvider primitiveProvider
   */
  public function testIsStringableTrue($string, $value) {
    $this->assertTrue(Util::isStringable($value));
  }

  /**
   * @dataProvider nonPrimitiveProvider
   */
  public function testIsStringableFalse($value) {
    $this->assertFalse(Util::isStringable($value));
  }

  /**
   * @dataProvider primitiveProvider
   */
  public function testIsStringableTypeTrue($string, $value) {
    $this->assertTrue(Util::isStringableType(gettype($value)));
  }

  /**
   * @dataProvider nonPrimitiveProvider
   */
  public function testIsStringableTypeFalse($value) {
    $this->assertFalse(Util::isStringableType(gettype($value)));
  }

  /**
   * @dataProvider primitiveProvider
   */
  public function testIsPrimitiveTypeTrue($string, $value) {
    $this->assertTrue(Util::isStringableType(gettype($value)));
  }

  /**
   * @dataProvider nonPrimitiveProvider
   */
  public function testIsPrimitiveTypeFalse($value) {
    $this->assertFalse(Util::isStringableType(gettype($value)));
  }

  /**
   * @dataProvider getTypeProvider
   */
  public function testGetTypeSuccess($expected, $value, $mode) {
    $this->assertEquals($expected, Util::getType($value, $mode));
  }

  /**
   * @dataProvider getTypeProvider
   */
  public function testGetArrayTypeSuccess($expected, $value, $mode) {
    $array = array_fill(0, 5, $value);
    $arrayObject = new ArrayObject($array);
    $this->assertEquals($expected, Util::getArrayType($array, $mode));
    $this->assertEquals($expected, Util::getArrayType($arrayObject, $mode));
  }

  /**
   * @expectedException \Exception
   */
  public function testGetArrayTypeException() {
    $array = array_fill(0, 4, new Student());
    $array[] = new Course();
    Util::getArrayType($array);
  }

  /**
   * @dataProvider castToTypeProvider
   */
  public function testCastToType($expected, $value, $type) {
    $this->assertEquals($expected, Util::castToType($value, $type));
  }

  // 
  // Data Providers
  // 

  public static function primitiveProvider() {
    return [
      ['42', 42],
      ['99999.99', 99999.99],
      ['foo', 'foo'],
      ['true', true],
    ];
  }

  public static function nonPrimitiveProvider() {
    return [
      [null],
      [['foo' => 'bar']],
      [new Course()],
    ];
  }

  public static function getTypeProvider() {
    return [
      [Util::TYPE_NULL, null, Util::TYPE_MODE_DEFAULT],
      ['integer', 42, Util::TYPE_MODE_DEFAULT],
      ['integer', 42, Util::TYPE_MODE_SHORT],
      ['integer', 42, Util::TYPE_MODE_LONG],
      ['double', 3.14, Util::TYPE_MODE_DEFAULT],
      ['double', 3.14, Util::TYPE_MODE_SHORT],
      ['double', 3.14, Util::TYPE_MODE_LONG],
      ['string', 'foo', Util::TYPE_MODE_DEFAULT],
      ['string', 'foo', Util::TYPE_MODE_SHORT],
      ['string', 'foo', Util::TYPE_MODE_LONG],
      ['string', '', Util::TYPE_MODE_DEFAULT],
      ['string', '', Util::TYPE_MODE_SHORT],
      ['string', '', Util::TYPE_MODE_LONG],
      ['boolean', true, Util::TYPE_MODE_DEFAULT],
      ['boolean', true, Util::TYPE_MODE_SHORT],
      ['boolean', true, Util::TYPE_MODE_LONG],
      ['boolean', false, Util::TYPE_MODE_DEFAULT],
      ['boolean', false, Util::TYPE_MODE_SHORT],
      ['boolean', false, Util::TYPE_MODE_LONG],
      ['boolean', false, Util::TYPE_MODE_DEFAULT],
      ['boolean', false, Util::TYPE_MODE_SHORT],
      ['boolean', false, Util::TYPE_MODE_LONG],
      ['array', [], Util::TYPE_MODE_DEFAULT],
      ['array', [], Util::TYPE_MODE_SHORT],
      ['array', [], Util::TYPE_MODE_LONG],
      ['object', new Course(), Util::TYPE_MODE_DEFAULT],
      ['Course', new Course(), Util::TYPE_MODE_SHORT],
      [Course::class, new Course(), Util::TYPE_MODE_LONG],
    ];
  }

  public static function castToTypeProvider() {
    return [
      [42, '42', 'integer'],
      [42, 42.0, 'integer'],
      [3.14, '3.14', 'double'],
      // booleans
      [true, 1, 'boolean'],
      [true, 42, 'boolean'],
      [true, 4.2, 'boolean'],
      [true, "string", 'boolean'],
      [true, "1", 'boolean'],
      [true, [1, 2], 'boolean'],
      [true, new \StdClass(), 'boolean'],
      [true, 'true', 'boolean'],
      // Careful of this one
      [true, 'true', 'boolean'],
      [true, 'trueishsortof', 'boolean'],
      // Careful of this one
      [true, 'false', 'boolean'],
      [true, 'falseish', 'boolean'],
      [false, 0, 'boolean'],
      [false, 0.0, 'boolean'],
      [false, "", 'boolean'],
      [false, "0", 'boolean'],
      [false, [], 'boolean'],
    ];
  }

}
