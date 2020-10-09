<?php

require 'src/action.php';

class MyAction {
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

    $context['c'] = $c;
  }

  private static function adds($a, $b) {
    return $a + $b;
  }
}

var_dump(MyAction::call(['a' => 1, 'b' => 2]));
