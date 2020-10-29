<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\AddsOneAction;

class AddToContextOrganizer {
    use \LightService\Organizer;

    public static function call() {
        return self::with([])->reduce(
            self::add_to_context(['number' => 0]),
            AddsOneAction::class
        );
    }
}
