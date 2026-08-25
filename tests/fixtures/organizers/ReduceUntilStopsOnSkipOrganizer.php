<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\AddsOneAction;
use LightService\Fixtures\Actions\SkipRemainingAction;

class ReduceUntilStopsOnSkipOrganizer
{
    use \LightService\Organizer;

    public static $predicate_calls = 0;

    public static function call()
    {
        self::$predicate_calls = 0;

        return self::with(['number' => 0])->reduce(
            self::reduceUntil(
                function ($context) {
                    if (20 < ++self::$predicate_calls) {
                        return true;
                    }

                    return 10 <= $context->number;
                },
                [AddsOneAction::class, SkipRemainingAction::class]
            )
        );
    }
}
