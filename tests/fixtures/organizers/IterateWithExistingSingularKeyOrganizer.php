<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\RecordsItemAction;

class IterateWithExistingSingularKeyOrganizer
{
    use \LightService\Organizer;

    // 'item' is already in use before iterating 'items'.
    public static function call()
    {
        return self::with([
            'items'     => [1, 2],
            'item'      => 'something the caller put there',
            'processed' => []
        ])->reduce(
            self::iterate('items', [RecordsItemAction::class])
        );
    }
}
