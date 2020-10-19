<?php

require_once 'src/Action.php';

class Action {
    use LightServicePHP\Action;

    private function executed($context) {
        $context->a[] = 'action';
    }
}