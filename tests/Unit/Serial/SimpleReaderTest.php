<?php
declare(strict_types=1);

namespace Bstoots\WOX\Tests\Unit\Serial;

use PHPUnit\Framework\TestCase;
use Bstoots\WOX\Tests\Stubs\{Student, Course, Product};
use Bstoots\WOX\Serial\{SimpleReader, XmlUtil};
use StdClass;

/**
 * @covers SimpleReader
 */
final class SimpleReaderTest extends TestCase {

  public function testConstruct() {
    $reader = new SimpleReader();
    $this->assertInstanceOf(SimpleReader::class, $reader);
  }

  public function testEmptyObject() {
    $reader = new SimpleReader();
    $xml = '<?xml version="1.0"?><object/>';
    $this->assertEquals(new StdClass(), $reader->read($xml));
  }

  public function testCourse() {
    $course = new Course([
      'code' => 6756,
      'name' => "XML and Related Technologies",
      'term' => 2
    ]);
    $reader = new SimpleReader([
      'classMap' => [
        'Course' => Course::class
      ]
    ]);
    // http://woxserializer.sourceforge.net/quick.html
    $xml = <<<XML
<object type="Course" id="0">
    <field name="code" type="int" value="6756" />
    <field name="name" type="string" value="XML and Related Technologies" />
    <field name="term" type="int" value="2" />
</object>
XML;
    // 
    // var_dump($reader->read($xml));
    $this->assertEquals($course, $reader->read($xml));
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
    $reader = new SimpleReader([
      'classMap' => [
        'Student' => Student::class,
        'Course'  => Course::class
      ]
    ]);
    // http://woxserializer.sourceforge.net/quick.html
    $xml = <<<XML
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
    // var_dump($reader->read($xml));
    $this->assertEquals($student, $reader->read($xml));
  }

  public function testProduct() {
    $product = new Product([
      'name' => "Corn Flakes",
      'price' => 3.98,
      'grams' => 500,
      'registered' => true,
    ]);
    $reader = new SimpleReader([
      'classMap' => [
        'Product' => Product::class
      ]
    ]);
    // http://woxserializer.sourceforge.net/primitives.html
    // @TODO - PHP doesn't have an abstraction for char such as:
    // <field name="category" type="char" value="\u0041" />
    // I can look into creating one but for now I'm just not touching this
    $xml = <<<XML
<object type="Product" id="0">
    <field name="name" type="string" value="Corn Flakes" />
    <field name="price" type="double" value="3.98" />
    <field name="grams" type="int" value="500" />
    <field name="registered" type="boolean" value="true" />
</object>   
XML;
    // 
    // var_dump($reader->read($xml));
    $this->assertEquals($product, $reader->read($xml));
  }

  public function testDoubleArray() {
    $doubles = [12.45, 878.98, 987.98, 435.87, 537.87, 89.0, 0.0, 667.332];
    $reader = new SimpleReader();
    // http://woxserializer.sourceforge.net/1dPrimitiveArrays.html
    // $expectedXml = '<object type="array" elementType="double" length="8" id="0">12.45 878.98 987.98 435.87 537.87 89.0 0.0 667.332</object>';
    // @NOTE - PHP truncates trailing zeros if they aren't necessary.  This shouldn't affect the deserialization in
    //         other languages as far as I know but something to be aware of.
    $xml = '<object type="array" elementType="double" length="8" id="0">12.45 878.98 987.98 435.87 537.87 89 0 667.332</object>';
    // 
    // var_dump($reader->read($xml));
    $this->assertEquals($doubles, $reader->read($xml));
  }

}
