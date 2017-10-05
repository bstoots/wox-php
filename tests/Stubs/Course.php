<?php

namespace Bstoots\WOX\Tests\Stubs;

class Course {
  
  /**
   * @var integer
   */
  public $code;

  /**
   * @var string
   */
  public $name;

  /**
   * @var integer
   */
  public $term;

  public function __construct(array $init = []) {
    $this->code = (array_key_exists('code', $init)) ? $init['code'] : null;
    $this->name = (array_key_exists('name', $init)) ? $init['name'] : null;
    $this->term = (array_key_exists('term', $init)) ? $init['term'] : null;
  }

}
