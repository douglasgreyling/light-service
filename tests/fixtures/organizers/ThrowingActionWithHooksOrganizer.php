<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\ThrowsUnexpectedlyAction;

class ThrowingActionWithHooksOrganizer
{
    use \LightService\Organizer;

    public function beforeEach($context)
    {
        $context->hooks_called[] = 'before';
    }

    public function afterEach($context)
    {
        $context->hooks_called[] = 'after';
    }

    public function aroundEach($context)
    {
        $context->hooks_called[] = 'around';
    }

    public static function call()
    {
        return self::with(['hooks_called' => []])->reduce(ThrowsUnexpectedlyAction::class);
    }
}
