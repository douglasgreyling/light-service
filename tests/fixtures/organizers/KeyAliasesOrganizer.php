<?php

require_once 'src/organizer.php';
require_once 'tests/fixtures/actions/KeyAliasesAction.php';

class KeyAliasesOrganizer {
    use LightServicePHP\Organizer;

    private $aliases = ['a' => 'an_alias_for_a'];

    public static function call($context) {
        return self::with($context)->reduce(
            KeyAliasesAction::class,
        );
    }
}
