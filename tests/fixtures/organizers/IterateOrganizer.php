<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\IterateAction;

class IterateOrganizer {
    use \LightService\Organizer;

    public static function call($context) {
        return self::with($context)->reduce(
            self::iterate('numbers', [
                IterateAction::class,
            ])
        );
    }
}
