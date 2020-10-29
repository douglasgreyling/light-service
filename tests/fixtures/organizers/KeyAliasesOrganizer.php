<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\AddsOneAction;
use LightService\Fixtures\Actions\KeyAliasesAction;

class KeyAliasesOrganizer {
    use \LightService\Organizer;

    private $aliases = ['number' => 'num_alias'];

    public static function call($number) {
        return self::with(['number' => $number])->reduce(
            AddsOneAction::class,
            KeyAliasesAction::class,
            AddsOneAction::class
        );
    }
}
