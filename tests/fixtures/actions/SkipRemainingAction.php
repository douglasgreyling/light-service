<?php

namespace LightServicePHP\Fixtures\Actions;

class SkipRemainingAction {
    use \LightServicePHP\Action;

    private function executed($context) {
        $context->skip_remaining("Skipped remaining actions");
    }
}
