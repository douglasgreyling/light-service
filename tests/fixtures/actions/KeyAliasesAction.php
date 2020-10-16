<?php

require_once 'src/Action.php';

class KeyAliasesAction {
    use LightServicePHP\Action;

    private $expects  = 'an_alias_for_a';
    private $promises = 'b';

    private function executed($context) {
        $context['b'] = $context->an_alias_for_a + 1;
    }
}
