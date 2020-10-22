<?php

namespace LightServicePHP\Fixtures\Actions;

class FailingAction {
    use \LightServicePHP\Action;

    private function executed($context) {
        $context->fail('foo');
    }
}
