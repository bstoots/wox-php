<?php
declare(strict_types=1);

namespace Bstoots\WOX\Tests\Unit\Serial;

use PHPUnit\Framework\TestCase;
use Bstoots\WOX\Tests\Stubs\{Building, Course, Product, Student};
use Bstoots\WOX\Serial\{ArrayAccessWriter, XmlUtil};

/**
 * @covers ArrayAccessWriter
 */
final class ArrayAccessWriterTest extends TestCase {
  
  public function testConstruct() {
    $writer = new ArrayAccessWriter();
    $this->assertInstanceOf(ArrayAccessWriter::class, $writer);
  }

  public function testNullObject() {
    $student = null;
    $writer = new ArrayAccessWriter();
    $expectedXml = '<?xml version="1.0"?><object/>';
    $this->assertEquals(
      XmlUtil::pretty($expectedXml),
      XmlUtil::pretty($writer->write($student))
    );
  }

  public function testBuilding() {
    $building = new Building([
      'number' => 1234,
      'name' => 'Science Hall',
      'description' => 'Stand back, we\'re doing science!'
    ]);
    $writer = new ArrayAccessWriter();
    $expectedXml = '<?xml version="1.0"?>
<object type="Building" id="0">
  <field name="number" type="int" value="1234"/>
  <field name="name" type="string" value="Science Hall"/>
  <field name="description" type="string" value="Stand back, we\'re doing science!"/>
</object>';
    // 
    // var_dump(XmlUtil::pretty($writer->write($building)));
    $this->assertEquals(
      XmlUtil::pretty($expectedXml),
      XmlUtil::pretty($writer->write($building))
    );
  }

  public function testNonArrayAccess() {
    $object = new \StdClass();
    $object->foo = 'bar';
    $writer = new ArrayAccessWriter();
    $expectedXml = '<?xml version="1.0"?>
<object type="stdClass" id="0">
  <field name="foo" type="string" value="bar"/>
</object>';
    // 
    // var_dump(XmlUtil::pretty($writer->write($object)));
    $this->assertEquals(
      XmlUtil::pretty($expectedXml),
      XmlUtil::pretty($writer->write($object))
    );
  }

}
