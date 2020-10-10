<?php

require_once 'src/organizer.php';
require_once 'tests/fixtures/DoesNothingAction.php';

class DoesNothingOrganizer {
    use LightServicePHP\Organizer;

    public static function call($context) {
        return self::with($context)->reduce(DoesNothingAction::class);
    }
}
