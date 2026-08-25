<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\AddsOneAction;
use LightService\Fixtures\Actions\RollbackAction;

class RollbackAtEndOfChainOrganizer
{
    use \LightService\Organizer;

    // The rolling-back action is the last in the chain, so there is no further
    // pass of the loop in which to notice it.
    public static function call($number)
    {
        return self::with(['number' => $number])->reduce(
            AddsOneAction::class,
            AddsOneAction::class,
            RollbackAction::class
        );
    }
}
