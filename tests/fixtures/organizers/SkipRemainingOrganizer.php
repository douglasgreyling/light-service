<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\AddsOneAction;
use LightService\Fixtures\Actions\SkipRemainingAction;

class SkipRemainingOrganizer {
    use \LightService\Organizer;

    public static function call($number) {
        return self::with(['number' => $number])->reduce(
            AddsOneAction::class,
            SkipRemainingAction::class,
            AddsOneAction::class
        );
    }
}
