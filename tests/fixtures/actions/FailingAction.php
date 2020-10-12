<?php

require_once 'src/Action.php';

class FailingAction {
    use LightServicePHP\Action;

    private function executed($context) {
      $context->fail('foo');
    }
}
