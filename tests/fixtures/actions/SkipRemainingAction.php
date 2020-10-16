<?php

require_once 'src/Action.php';

class SkipRemainingAction {
    use LightServicePHP\Action;

    private function executed($context) {
        $context->skip_remaining("Skipped remaining actions");
    }
}
