<?php

namespace LightServicePHP\Fixtures\Actions;

class FailAndReturnAction {
    use \LightServicePHP\Action;

    private function executed($context) {
        $context->fail_and_return('foo');
        $context->one = true;
    }
}
