<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\SetsAAction;

class BeforeAfterHooksOrganizer
{
    use \LightService\Organizer;

    public function beforeEach($context)
    {
        $context->a[] = 'before';
    }

    public function afterEach($context)
    {
        $context->a[] = 'after';
    }

    public static function call()
    {
        return self::with(['a' => []])->reduce(
            SetsAAction::class
        );
    }
}
