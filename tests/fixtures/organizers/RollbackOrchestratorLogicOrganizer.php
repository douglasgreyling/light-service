<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\AddsOneAction;
use LightService\Fixtures\Actions\RollbackAction;

class RollbackOrchestratorLogicOrganizer {
    use \LightService\Organizer;

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
