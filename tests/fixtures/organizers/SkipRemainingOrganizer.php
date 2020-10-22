<?php

namespace LightServicePHP\Fixtures\Organizers;

use LightServicePHP\Fixtures\Actions\AddsOneAction;
use LightServicePHP\Fixtures\Actions\SkipRemainingAction;

class SkipRemainingOrganizer {
    use \LightServicePHP\Organizer;

    public static function call($number) {
        return self::with(['number' => $number])->reduce(
            AddsOneAction::class,
            SkipRemainingAction::class,
            AddsOneAction::class
        );
    }
}
