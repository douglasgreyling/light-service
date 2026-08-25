<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\AddsOneAction;
use LightService\Fixtures\Actions\RollbackAction;

class ReduceTwiceAfterRollbackOrganizer
{
    use \LightService\Organizer;

    // The second reduce runs against a context that has already rolled back.
    public static function call()
    {
        $organizer = self::with(['number' => 1]);

        $organizer->reduce(RollbackAction::class);

        return $organizer->reduce(AddsOneAction::class);
    }
}
