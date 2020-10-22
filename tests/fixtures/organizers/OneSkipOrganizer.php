<?php

namespace LightServicePHP\Fixtures\Organizers;

use LightServicePHP\Fixtures\Actions\AddsOneAction;
use LightServicePHP\Fixtures\Actions\NextActionAction;

class OneSkipOrganizer {
    use \LightServicePHP\Organizer;

    public static function call($number) {
        return self::with(['number' => $number])->reduce(
            AddsOneAction::class,
            NextActionAction::class,
            AddsOneAction::class
        );
    }
}
