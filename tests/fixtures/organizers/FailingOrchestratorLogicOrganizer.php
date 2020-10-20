<?php

require_once 'src/organizer.php';
require_once 'tests/fixtures/actions/AddsOneAction.php';
require_once 'tests/fixtures/actions/FailingAction.php';

class FailingOrchestratorLogicOrganizer {
    use LightServicePHP\Organizer;

    public static function call($number) {
        return self::with(['number' => $number])->reduce(
            AddsOneAction::class,
            AddsOneAction::class,
            AddsOneAction::class,
            AddsOneAction::class,
            self::reduce_if(function($context) { return 3 < $context->number; }, [
                AddsOneAction::class,
                FailingAction::class
            ]),
            AddsOneAction::class,
        );
    }
}
