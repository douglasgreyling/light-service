<?php

namespace LightServicePHP\Fixtures\Organizers;

use LightServicePHP\Fixtures\Actions\IterateAction;

class IterateOrganizer {
    use \LightServicePHP\Organizer;

    public static function call($context) {
        return self::with($context)->reduce(
            self::iterate('numbers', [
                IterateAction::class,
            ])
        );
    }
}
