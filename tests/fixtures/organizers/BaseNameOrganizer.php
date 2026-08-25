<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\BaseNameAction;

class BaseNameOrganizer
{
    use \LightService\Organizer;

    public static function call()
    {
        return self::with([])->reduce(BaseNameAction::class);
    }
}
