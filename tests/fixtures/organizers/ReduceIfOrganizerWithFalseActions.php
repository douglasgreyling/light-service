<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\{AddsOneAction, AddsTwoAction};

class ReduceIfOrganizerWithFalseActions {
    use \LightService\Organizer;

    public static function call($number) {
        return self::with(['number' => $number])->reduce(
            AddsOneAction::class,
            self::reduce_if(function($context) { return 1 < $context->number; },
                [ AddsOneAction::class ],
                [ AddsTwoAction::class ],
            ),
            AddsOneAction::class
        );
    }
}
