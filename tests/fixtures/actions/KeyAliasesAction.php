<?php

require_once 'src/Action.php';

class KeyAliasesAction {
    use LightServicePHP\Action;

    private $expects = 'num_alias';

    private function executed($context) {
        $context->num_alias += 1;
    }
}
