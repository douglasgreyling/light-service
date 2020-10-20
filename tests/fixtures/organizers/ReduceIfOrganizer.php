<?php

require_once 'src/organizer.php';
require_once 'tests/fixtures/actions/AddsOneAction.php';

class ReduceIfOrganizer {
    use LightServicePHP\Organizer;

    public static function call($number) {
        return self::with(['number' => $number])->reduce(
            AddsOneAction::class,
            self::reduce_if(function($context) { return 1 < $context->number; }, [
              AddsOneAction::class,
            ]),
            AddsOneAction::class
        );
    }
}
