<?php

namespace LightServicePHP\Fixtures\Organizers;

use LightServicePHP\Fixtures\Actions\AddsOneAction;
use LightServicePHP\Fixtures\Actions\RollbackAction;

class RollbackOrchestratorLogicOrganizer {
    use \LightServicePHP\Organizer;

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
