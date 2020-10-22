<?php

namespace LightServicePHP\Fixtures\Organizers;

use LightServicePHP\Fixtures\Actions\SetsAAction;

class AroundHooksOrganizer {
    use \LightServicePHP\Organizer;

    public function around_each($context) {
        $context->a[] = 'around';
    }

    public static function call() {
        return self::with(['a' => []])->reduce(
            SetsAAction::class
        );
    }
}
