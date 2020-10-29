<?php

namespace LightService\Fixtures\Actions;

class NextActionAction {
    use \LightService\Action;

    private function executed($context) {
        $this->next_context();

        $context->d = 5;
    }

    private function adds($a, $b) {
        return $a + $b;
    }
}
