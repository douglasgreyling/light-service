<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\DoesNothingAction;

class AroundEachOrganizer {
    use \LightService\Organizer;

    public static function call() {
        return self::with(['hook_count' => 0])->reduce(
            DoesNothingAction::class,
            DoesNothingAction::class
        );
    }

    public function around_each() {
        $this->context->hook_count += 1;
    }
}
