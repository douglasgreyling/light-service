<?php

require_once 'src/organizer.php';
require_once 'tests/fixtures/AddsOneAction.php';

class SuccessfulOrganizer {
    use LightServicePHP\Organizer;

    public static function call($number) {
        return self::with(['number' => $number])->reduce(
            AddsOneAction::class,
            AddsOneAction::class,
            AddsOneAction::class
        );
    }
}
