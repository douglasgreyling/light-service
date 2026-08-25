<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\DoesNothingAction;

class BeforeAfterEachOrganizer
{
    use \LightService\Organizer;

    public static function call()
    {
        return self::with(['hooks_called' => []])->reduce(
            DoesNothingAction::class,
            DoesNothingAction::class
        );
    }

    public function beforeEach()
    {
        $hooks = $this->context->hooks_called;
        $hooks[] = 'before';
        $this->context->hooks_called = $hooks;
    }

    public function afterEach()
    {
        $hooks = $this->context->hooks_called;
        $hooks[] = 'after';
        $this->context->hooks_called = $hooks;
    }
}
