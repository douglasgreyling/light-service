<?php

require_once 'src/organizer.php';
require_once 'tests/fixtures/actions/DoesNothingAction.php';

class AllHooksOrganizer {
    use LightServicePHP\Organizer;

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
