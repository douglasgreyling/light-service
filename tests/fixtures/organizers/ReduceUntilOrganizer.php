<?php

namespace LightServicePHP\Fixtures\Organizers;

use LightServicePHP\Fixtures\Actions\AddsOneAction;

class ReduceUntilOrganizer {
    use \LightServicePHP\Organizer;

    public static function call($number) {
        return self::with(['number' => $number])->reduce(
            AddsOneAction::class,
            self::reduce_until(function($context) { return 3 < $context->number; }, [
                AddsOneAction::class,
            ])
        );
    }
}
