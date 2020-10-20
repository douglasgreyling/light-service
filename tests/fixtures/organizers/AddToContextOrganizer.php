<?php

require_once 'src/organizer.php';
require_once 'tests/fixtures/actions/AddsOneAction.php';

class AddToContextOrganizer {
    use LightServicePHP\Organizer;

    public static function call() {
        return self::with([])->reduce(
            self::add_to_context(['number' => 0]),
            AddsOneAction::class
        );
    }
}
