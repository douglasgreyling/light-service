<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\AddsOneAction;
use LightService\Fixtures\Actions\FailingAction;

class ReduceUntilStopsOnFailureOrganizer
{
    use \LightService\Organizer;

    public static $predicate_calls = 0;

    public static function call()
    {
        self::$predicate_calls = 0;

        return self::with(['number' => 0])->reduce(
            self::reduceUntil(
                function ($context) {
                    // The predicate can never be satisfied once the context has
                    // failed. This guard keeps a regression to a failing test
                    // rather than a suite that hangs.
                    if (20 < ++self::$predicate_calls) {
                        return true;
                    }

                    return 10 <= $context->number;
                },
                [AddsOneAction::class, FailingAction::class]
            )
        );
    }
}
