<?php

require_once 'src/Action.php';

class UnexpectedErrorAction {
  use LightServicePHP\Action;

  private static function expects() {
    return ['a', 'b'];
  }

  private static function promises() {
    return ['c'];
  }

  public static function executed($context) {
    throw new Exception('Foo');
  }

  private static function adds($a, $b) {
    return $a + $b;
  }
}
