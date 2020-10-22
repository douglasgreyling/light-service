<?php

namespace LightServicePHP\Fixtures\Organizers;

use LightServicePHP\Fixtures\Actions\AddsOneAction;

class ExecuteOrganizer {
    use \LightServicePHP\Organizer;

    public static function call($number) {
        return self::with(['number' => $number])->reduce(
            AddsOneAction::class,
            self::execute(function($context) { $context->number += 1; })
        );
    }
}
