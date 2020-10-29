<?php

namespace LightService\Fixtures\Actions;

class KeyAliasesAction {
    use \LightService\Action;

    private $expects = 'num_alias';

    private function executed($context) {
        $context->num_alias += 1;
    }
}
