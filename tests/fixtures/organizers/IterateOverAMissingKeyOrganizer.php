<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\RecordsItemAction;

class IterateOverAMissingKeyOrganizer
{
    use \LightService\Organizer;

    public static function call()
    {
        return self::with(['processed' => []])->reduce(
            self::iterate('items', [RecordsItemAction::class])
        );
    }
}
