<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\RecordsItemAction;

class IterateOverANonIterableOrganizer
{
    use \LightService\Organizer;

    public static function call()
    {
        return self::with(['items' => 42, 'processed' => []])->reduce(
            self::iterate('items', [RecordsItemAction::class])
        );
    }
}
