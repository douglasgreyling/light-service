<?php

require_once 'src/organizer.php';
require_once 'tests/fixtures/actions/IterateAction.php';

class IterateOrganizer {
    use LightServicePHP\Organizer;

    public static function call($context) {
        return self::with($context)->reduce(
            self::iterate('numbers', [
                IterateAction::class,
            ])
        );
    }
}
