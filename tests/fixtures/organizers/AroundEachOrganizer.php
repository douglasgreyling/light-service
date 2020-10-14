<?php

require_once 'src/organizer.php';
require_once 'tests/fixtures/actions/DoesNothingAction.php';

class AroundEachOrganizer {
    use LightServicePHP\Organizer;

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
