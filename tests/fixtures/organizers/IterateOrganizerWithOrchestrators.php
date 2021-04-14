<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\IterateAction;

class IterateOrganizerWithOrchestrators {
    use \LightService\Organizer;

    public static function call($context) {
        return self::with($context)->reduce(
            self::iterate('numbers', [
                IterateAction::class,
                self::execute(function($context) { $context->sum += 1; })
            ])
        );
    }
}
