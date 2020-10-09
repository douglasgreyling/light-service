<?php

require_once 'src/Action.php';

class NoExecutedFunctionAction {
  use LightServicePHP\Action;

  private static function expects() {
    return ['a', 'b'];
  }

  private static function promises() {
    return ['c'];
  }

  private static function adds($a, $b) {
    return $a + $b;
  }
}
