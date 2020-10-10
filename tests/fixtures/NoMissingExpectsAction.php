<?php

require_once 'src/Action.php';

class NoMissingExpectsAction {
  use LightServicePHP\Action;

  private $promises = ['c'];

  private function executed() {
    $a = $this->context['a'];
    $b = $this->context['b'];

    $c = $this->adds($a, $b);

    $this->context['c'] = $c;
  }

  private function adds($a, $b) {
    return $a + $b;
  }
}
