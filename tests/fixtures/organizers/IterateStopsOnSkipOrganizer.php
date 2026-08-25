<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\SkipsOnSecondItemAction;

class IterateStopsOnSkipOrganizer
{
    use \LightService\Organizer;

    public static function call()
    {
        return self::with(['items' => [1, 2, 3], 'processed' => []])->reduce(
            self::iterate('items', [SkipsOnSecondItemAction::class])
        );
    }
}
