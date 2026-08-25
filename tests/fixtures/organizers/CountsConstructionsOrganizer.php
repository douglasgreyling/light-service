<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\CountsConstructionsAction;

class CountsConstructionsOrganizer
{
    use \LightService\Organizer;

    public static function call()
    {
        return self::with(['number' => 0])->reduce(
            CountsConstructionsAction::class,
            CountsConstructionsAction::class,
            CountsConstructionsAction::class
        );
    }
}
