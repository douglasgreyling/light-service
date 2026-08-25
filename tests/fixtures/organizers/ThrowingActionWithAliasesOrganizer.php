<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\ThrowsWithAnAliasedKeyAction;

class ThrowingActionWithAliasesOrganizer
{
    use \LightService\Organizer;

    protected $aliases = ['number' => 'aliased_number'];

    public static function call()
    {
        return self::with(['number' => 1])->reduce(ThrowsWithAnAliasedKeyAction::class);
    }
}
