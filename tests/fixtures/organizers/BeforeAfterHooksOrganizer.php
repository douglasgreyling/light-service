<?php

namespace LightServicePHP\Fixtures\Organizers;

use LightServicePHP\Fixtures\Actions\SetsAAction;

class BeforeAfterHooksOrganizer {
    use \LightServicePHP\Organizer;

    public function before_each($context) {
        $context->a[] = 'before';
    }

    public function after_each($context) {
        $context->a[] = 'after';
    }

    public static function call() {
        return self::with(['a' => []])->reduce(
            SetsAAction::class
        );
    }
}
