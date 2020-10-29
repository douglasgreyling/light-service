<?php

namespace LightService\Fixtures\Actions;

class SetsAAction {
    use \LightService\Action;

    private function executed($context) {
        $context->a[] = 'action';
    }
}