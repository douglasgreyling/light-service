<?php

require_once 'src/organizer.php';
require_once 'tests/fixtures/AddsOneAction.php';
require_once 'tests/fixtures/FailingAction.php';

class FailingOrganizer {
    use LightServicePHP\Organizer;

    public static function call($number) {
        return self::with(['number' => $number])->reduce(
            AddsOneAction::class,
            FailingAction::class,
            AddsOneAction::class
        );
    }
}
