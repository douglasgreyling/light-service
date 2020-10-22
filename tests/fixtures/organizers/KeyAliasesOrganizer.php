<?php

namespace LightServicePHP\Fixtures\Organizers;

use LightServicePHP\Fixtures\Actions\AddsOneAction;
use LightServicePHP\Fixtures\Actions\KeyAliasesAction;

class KeyAliasesOrganizer {
    use \LightServicePHP\Organizer;

    private $aliases = ['number' => 'num_alias'];

    public static function call($number) {
        return self::with(['number' => $number])->reduce(
            AddsOneAction::class,
            KeyAliasesAction::class,
            AddsOneAction::class
        );
    }
}
