<?php
declare(strict_types=1);

namespace Bstoots\WOX\Tests\Unit\Serial;

use PHPUnit\Framework\TestCase;
use Bstoots\WOX\Tests\Stubs\{Student, Course};
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

  public function testObject() {
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

}
