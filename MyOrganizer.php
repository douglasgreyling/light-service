<?php

require 'src/organizer.php';
require_once 'src/action.php';

class CalculatesTax {
    use LightServicePHP\Organizer;

    public static function call($number) {
        return self::with(['num' => $number])->reduce(
            AddsOneAction::class,
            AddsTwoAction::class
        );
    }
}

class AddsOneAction {
    use LightServicePHP\Action;

    private $expects  = ['num'];
    private $promises = ['num'];

    private function executed() {
        $this->context->num += 1;
    }
}

class AddsTwoAction {
    use LightServicePHP\Action;

    private $expects  = ['num'];
    private $promises = ['num'];

    private function executed() {
        $this->context->num += 2;
    }
}
