<?php

namespace LightServicePHP\Fixtures\Actions;

class SetsAAction {
    use \LightServicePHP\Action;

    private function executed($context) {
        $context->a[] = 'action';
    }
}