<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\DoesNothingAction;

class DoesNothingOrganizer {
    use \LightService\Organizer;

    public static function call($context) {
        return self::with($context)->reduce(DoesNothingAction::class);
    }
}
