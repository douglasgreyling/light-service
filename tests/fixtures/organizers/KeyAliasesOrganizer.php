<?php

require_once 'src/organizer.php';
require_once 'tests/fixtures/actions/KeyAliasesAction.php';
require_once 'tests/fixtures/actions/AddsOneAction.php';

class KeyAliasesOrganizer {
    use LightServicePHP\Organizer;

    private $aliases = ['number' => 'num_alias'];

    public static function call($number) {
        return self::with(['number' => $number])->reduce(
            AddsOneAction::class,
            KeyAliasesAction::class,
            AddsOneAction::class
        );
    }
}
