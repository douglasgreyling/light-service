<?php

require_once 'src/Action.php';

class MissingPromisesAction {
  use LightServicePHP\Action;

  private static function expects() {
    return ['a', 'b'];
  }

  private static function promises() {
    return ['c'];
  }

  public static function executed($context) {
    $a = $context['a'];
    $b = $context['b'];

    $c = self::adds($a, $b);
  }

  private static function adds($a, $b) {
    return $a + $b;
  }
}
