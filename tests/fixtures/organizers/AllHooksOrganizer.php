<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\DoesNothingAction;

class AllHooksOrganizer {
    use \LightService\Organizer;

    public function around_each() {
        $this->context->hooks_called[] = 'around';
    }

    public function before_each() {
        $this->context->hooks_called[] = 'before';
    }

    public function after_each() {
        $this->context->hooks_called[] = 'after';
    }

    public static function call() {
        return self::with(['hooks_called' => []])->reduce(
            DoesNothingAction::class,
            DoesNothingAction::class
        );
    }
}
