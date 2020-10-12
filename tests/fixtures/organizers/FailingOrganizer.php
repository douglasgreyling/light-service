<?php

require_once 'src/organizer.php';
require_once 'tests/fixtures/actions/AddsOneAction.php';
require_once 'tests/fixtures/actions/FailingAction.php';

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