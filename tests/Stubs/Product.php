<?php

namespace Bstoots\WOX\Tests\Stubs;

class Product {
  
  /**
   * @var string
   */
  public $name;

  /**
   * @var float
   */
  public $price;

  /**
   * @var integer
   */
  public $grams;

  /**
   * @var boolean
   */
  public $registered;

  /**
   * @var char
   */
  // @TODO - PHP doesn't have an abstraction for char such as:
  // <field name="category" type="char" value="\u0041" />
  // I can look into creating one but for now I'm just not touching this
  // public $category;

  public function __construct(array $init = []) {
    $this->name = (array_key_exists('name', $init)) ? $init['name'] : null;
    $this->price = (array_key_exists('price', $init)) ? $init['price'] : null;
    $this->grams = (array_key_exists('grams', $init)) ? $init['grams'] : null;
    $this->registered = (array_key_exists('registered', $init)) ? $init['registered'] : null;
    // $this->category = (array_key_exists('category', $init)) ? $init['category'] : null;
  }

}
