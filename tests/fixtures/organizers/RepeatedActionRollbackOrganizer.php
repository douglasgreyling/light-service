<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\AddsOneAction;
use LightService\Fixtures\Actions\ConditionalRollbackAction;

class RepeatedActionRollbackOrganizer
{
    use \LightService\Organizer;

    // The same action class appears twice; only the second one rolls back.
    public static function call($number)
    {
        return self::with(['number' => $number, 'rolled_back_actions' => []])->reduce(
            ConditionalRollbackAction::class,
            AddsOneAction::class,
            AddsOneAction::class,
            ConditionalRollbackAction::class
        );
    }
}
