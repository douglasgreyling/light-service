<?php

namespace LightServicePHP\Fixtures\Organizers;

use LightServicePHP\Fixtures\Actions\AddsOneAction;
use LightServicePHP\Fixtures\Actions\FailingAction;

class FailingOrganizer {
    use \LightServicePHP\Organizer;

    public static function call($number) {
        return self::with(['number' => $number])->reduce(
            AddsOneAction::class,
            FailingAction::class,
            AddsOneAction::class
        );
    }
}
