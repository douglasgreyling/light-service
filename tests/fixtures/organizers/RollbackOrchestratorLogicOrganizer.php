<?php

require_once 'src/organizer.php';
require_once 'tests/fixtures/actions/AddsOneAction.php';
require_once 'tests/fixtures/actions/RollbackAction.php';

class RollbackOrchestratorLogicOrganizer {
    use LightServicePHP\Organizer;

    public static function call($number) {
        return self::with(['number' => $number])->reduce(
            AddsOneAction::class,
            AddsOneAction::class,
            AddsOneAction::class,
            AddsOneAction::class,
            self::reduce_if(function($context) { return 3 < $context->number; }, [
                AddsOneAction::class,
                RollbackAction::class
            ]),
            AddsOneAction::class,
        );
    }
}