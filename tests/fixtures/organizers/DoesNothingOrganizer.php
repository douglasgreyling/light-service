<?php

namespace LightServicePHP\Fixtures\Organizers;

use LightServicePHP\Fixtures\Actions\DoesNothingAction;

class DoesNothingOrganizer {
    use \LightServicePHP\Organizer;

    public static function call($context) {
        return self::with($context)->reduce(DoesNothingAction::class);
    }
}
