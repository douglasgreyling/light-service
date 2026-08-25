<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\ChargesItemAction;

class IterateRollbackOrganizer
{
    use \LightService\Organizer;

    public static function call()
    {
        return self::with(['items' => [1, 2, 3], 'charged' => [], 'refunded' => []])->reduce(
            self::iterate('items', [ChargesItemAction::class])
        );
    }
}
