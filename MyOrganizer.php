<?php

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

    private function executed($context) {
        $context->num += 1;
    }
}

class AddsTwoAction {
    use LightServicePHP\Action;

    private $expects  = ['num'];
    private $promises = ['num'];

    private function executed($context) {
        $context->num += 2;
    }
}
