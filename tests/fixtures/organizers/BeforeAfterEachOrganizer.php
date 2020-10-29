<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\DoesNothingAction;

class BeforeAfterEachOrganizer {
    use \LightService\Organizer;

    public static function call() {
        return self::with(['hooks_called' => []])->reduce(
            DoesNothingAction::class,
            DoesNothingAction::class
        );
    }

    public function before_each() {
        $hooks = $this->context->hooks_called;
        $hooks[] = 'before';
        $this->context->hooks_called = $hooks;
    }

    public function after_each() {
        $hooks = $this->context->hooks_called;
        $hooks[] = 'after';
        $this->context->hooks_called = $hooks;
    }
}
