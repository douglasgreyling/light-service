<?php

require_once 'src/Action.php';

class IterateAction {
    use LightServicePHP\Action;

    private $expects  = ['number'];
    private $promises = ['number'];

    private function executed($context) {
        $context->sum += $context->number;
    }
}
