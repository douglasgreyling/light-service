<?php

require_once 'src/Action.php';

class AddsOneAction {
    use LightServicePHP\Action;

    private $expects  = ['number'];
    private $promises = ['number'];

    private function executed($context) {
        $context->number += 1;
    }
}
