<?php

namespace LightServicePHP\Fixtures\Organizers;

use LightServicePHP\Fixtures\Actions\AddsOneAction;

class AddToContextOrganizer {
    use \LightServicePHP\Organizer;

    public static function call() {
        return self::with([])->reduce(
            self::add_to_context(['number' => 0]),
            AddsOneAction::class
        );
    }
}
