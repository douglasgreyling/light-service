<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\SetsAAction;

class AroundHooksOrganizer {
    use \LightService\Organizer;

    public function around_each($context) {
        $context->a[] = 'around';
    }

    public static function call() {
        return self::with(['a' => []])->reduce(
            SetsAAction::class
        );
    }
}
