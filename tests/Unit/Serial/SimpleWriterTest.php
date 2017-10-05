<?php
declare(strict_types=1);

namespace Bstoots\WOX\Tests\Unit\Serial;

use PHPUnit\Framework\TestCase;
use Bstoots\WOX\Tests\Stubs\{Student, Course, Product};
use Bstoots\WOX\Serial\{SimpleWriter, XmlUtil};

/**
 * @covers SimpleWriter
 */
final class SimpleWriterTest extends TestCase {
  
  public function testConstruct() {
    $writer = new SimpleWriter();
    $this->assertInstanceOf(SimpleWriter::class, $writer);
  }

  public function testNullObject() {
    $student = null;
    $writer = new SimpleWriter();
    $expectedXml = '<?xml version="1.0"?><object/>';
    $this->assertEquals(
      XmlUtil::pretty($expectedXml),
      XmlUtil::pretty($writer->write($student))
    );
  }

  public function testStudent() {
    $courses = [];
    $courses[] = new Course([
      'code' => 6756,
      'name' => "XML and Related Technologies",
      'term' => 2
    ]);
    $courses[] = new Course([
      'code' => 9865,
      'name' => "Object Oriented Programming",
      'term' => 2
    ]);
    $courses[] = new Course([
      'code' => 1134,
      'name' => "E-Commerce Programming",
      'term' => 3
    ]);
    $student = new Student([
      'name' => "Carlos Jaimez",
      'registrationNumber' => 76453,
      'courses' => $courses
    ]);
    $writer = new SimpleWriter();
    // http://woxserializer.sourceforge.net/quick.html
    $expectedXml = <<<XML
<object type="Student" id="0">
    <field name="name" type="string" value="Carlos Jaimez" />
    <field name="registrationNumber" type="int" value="76453" />
    <field name="courses">
        <object type="array" elementType="Course" length="3" id="1">
            <object type="Course" id="2">
                <field name="code" type="int" value="6756" />
                <field name="name" type="string" value="XML and Related Technologies" />
                <field name="term" type="int" value="2" />
            </object>
            <object type="Course" id="3">
                <field name="code" type="int" value="9865" />
                <field name="name" type="string" value="Object Oriented Programming" />
                <field name="term" type="int" value="2" />
            </object>
            <object type="Course" id="4">
                <field name="code" type="int" value="1134" />
                <field name="name" type="string" value="E-Commerce Programming" />
                <field name="term" type="int" value="3" />
            </object>
        </object>
    </field>
</object>
XML;
    // 
    $this->assertEquals(
      XmlUtil::pretty($expectedXml),
      XmlUtil::pretty($writer->write($student))
    );
  }

  public function testProduct() {
    $product = new Product([
      'name' => "Corn Flakes",
      'price' => 3.98,
      'grams' => 500,
      'registered' => true,
    ]);
    $writer = new SimpleWriter();
    // http://woxserializer.sourceforge.net/primitives.html
    // @TODO - PHP doesn't have an abstraction for char such as:
    // <field name="category" type="char" value="\u0041" />
    // I can look into creating one but for now I'm just not touching this
    $expectedXml = <<<XML
<object type="Product" id="0">
    <field name="name" type="string" value="Corn Flakes" />
    <field name="price" type="double" value="3.98" />
    <field name="grams" type="int" value="500" />
    <field name="registered" type="boolean" value="true" />
</object>   
XML;
    // 
    $this->assertEquals(
      XmlUtil::pretty($expectedXml),
      XmlUtil::pretty($writer->write($product))
    );
  }

  public function testDoubleArray() {
    $doubles = [12.45, 878.98, 987.98, 435.87, 537.87, 89.0, 0.0, 667.332];
    $writer = new SimpleWriter();
    // http://woxserializer.sourceforge.net/1dPrimitiveArrays.html
    // $expectedXml = '<object type="array" elementType="double" length="8" id="0">12.45 878.98 987.98 435.87 537.87 89.0 0.0 667.332</object>';
    // @NOTE - PHP truncates trailing zeros if they aren't necessary.  This shouldn't affect the deserialization in
    //         other languages as far as I know but something to be aware of.
    $expectedXml = '<object type="array" elementType="double" length="8" id="0">12.45 878.98 987.98 435.87 537.87 89 0 667.332</object>';
    // 
    $this->assertEquals(
      XmlUtil::pretty($expectedXml),
      XmlUtil::pretty($writer->write($doubles))
    );
  }

}
