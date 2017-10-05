<?php

namespace Bstoots\WOX\Tests\Stubs;

class Student {
  
  /**
   * @var string
   */
  public $name;

  /**
   * @var integer
   */
  public $registrationNumber;

  /**
   * @var Course[]
   */
  public $courses;

  public function __construct(array $init = []) {
    $this->name = (array_key_exists('name', $init)) ? $init['name'] : null;
    $this->registrationNumber = (array_key_exists('registrationNumber', $init)) ? $init['registrationNumber'] : null;
    $this->courses = (array_key_exists('courses', $init)) ? $init['courses'] : null;
  }

}
