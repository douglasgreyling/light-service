<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\AddsOneAction;
use LightService\Fixtures\Actions\FailingAction;

class FailingOrganizer {
    use \LightService\Organizer;

    public static function call($number) {
        return self::with(['number' => $number])->reduce(
            AddsOneAction::class,
            FailingAction::class,
            AddsOneAction::class
        );
    }
}
