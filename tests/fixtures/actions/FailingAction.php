<?php

namespace LightService\Fixtures\Actions;

class FailingAction {
    use \LightService\Action;

    private function executed($context) {
        $context->fail('foo');
    }
}
