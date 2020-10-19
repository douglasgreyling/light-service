<?php

require_once 'src/organizer.php';

require_once 'tests/fixtures/actions/SetsAAction.php';

class BeforeAfterHooksOrganizer {
    use LightServicePHP\Organizer;

    public function before_each($context) {
        $context->a[] = 'before';
    }

    public function after_each($context) {
        $context->a[] = 'after';
    }

    public static function call() {
        return self::with(['a' => []])->reduce(
            Action::class
        );
    }
}
