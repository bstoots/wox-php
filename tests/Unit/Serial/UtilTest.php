<?php
declare(strict_types=1);

namespace Bstoots\WOX\Tests\Unit\Serial;

use PHPUnit\Framework\TestCase;
use Bstoots\WOX\Tests\Stubs\{Student, Course};
use Bstoots\WOX\Serial\{Util};

/**
 * @covers Util
 */
final class UtilTest extends TestCase {
  
  /**
   * @dataProvider stringifyProvider
   */
  public function testStringify($expected, $value) {
    $this->assertEquals($expected, Util::stringify($value));
  }

  public static function stringifyProvider() {
    return [
      [null, null],
      ['foo', 'foo'],
      ['true', true],
      ['42', 42],
      ['99999.99', 99999.99],
    ];
  }

}
