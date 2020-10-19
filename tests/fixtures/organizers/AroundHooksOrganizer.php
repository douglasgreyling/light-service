<?php

require_once 'src/organizer.php';

require_once 'tests/fixtures/actions/SetsAAction.php';

class AroundHooksOrganizer {
    use LightServicePHP\Organizer;

    public function around_each($context) {
        $context->a[] = 'around';
    }

    public static function call() {
        return self::with(['a' => []])->reduce(
            Action::class
        );
    }
}
