<?php

namespace LightService\Fixtures\Actions;

class SkipRemainingAction {
    use \LightService\Action;

    private function executed($context) {
        $context->skip_remaining("Skipped remaining actions");
    }
}
