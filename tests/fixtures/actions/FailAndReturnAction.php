<?php

namespace LightService\Fixtures\Actions;

class FailAndReturnAction {
    use \LightService\Action;

    private function executed($context) {
        $context->fail_and_return('foo');
        $context->one = true;
    }
}
