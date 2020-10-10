<?php

require_once 'src/Action.php';

class FailingAction {
    use LightServicePHP\Action;

    private function executed() {
      $this->context->fail('foo');
    }
}
