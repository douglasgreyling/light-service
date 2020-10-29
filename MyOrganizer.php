<?php

class CalculatesTax {
    use LightService\Organizer;

    public static function call($number) {
        return self::with(['num' => $number])->reduce(
            AddsOneAction::class,
            AddsTwoAction::class
        );
    }
}

class AddsOneAction {
    use LightService\Action;

    private $expects  = ['num'];
    private $promises = ['num'];

    private function executed($context) {
        $context->num += 1;
    }
}

class AddsTwoAction {
    use LightService\Action;

    private $expects  = ['num'];
    private $promises = ['num'];

    private function executed($context) {
        $context->num += 2;
    }
}
