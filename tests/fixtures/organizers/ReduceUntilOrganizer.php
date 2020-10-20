<?php

require_once 'src/organizer.php';
require_once 'tests/fixtures/actions/AddsOneAction.php';

class ReduceUntilOrganizer {
    use LightServicePHP\Organizer;

    public static function call($number) {
        return self::with(['number' => $number])->reduce(
            AddsOneAction::class,
            self::reduce_until(function($context) { return 3 < $context->number; }, [
                AddsOneAction::class,
            ])
        );
    }
}
