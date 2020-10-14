<?php

require_once 'src/organizer.php';
require_once 'tests/fixtures/actions/DoesNothingAction.php';

class AllHooksOrganizer {
    use LightServicePHP\Organizer;

    public static function call() {
        return self::with(['hooks_called' => []])->reduce(
            DoesNothingAction::class,
            DoesNothingAction::class
        );
    }

    public function around_each() {
        $hooks = $this->context->hooks_called;
        $hooks[] = 'around';
        $this->context->hooks_called = $hooks;
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
