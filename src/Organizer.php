<?php

namespace LightServicePHP;

require_once 'classes/ActionContext.php';

require_once 'exceptions/NotImplementedException.php';

use ActionContext;

use NotImplementedException;

trait Organizer {
    private $context;

    public function __construct($context) {
        $this->context = new ActionContext($context);
    }

    public static function call() {
        throw new NotImplementedException;
    }

    public static function with($context) {
        return new self($context);
    }

    public function reduce(...$actions) {
        foreach ($actions as $action) {
            $this->context = $action::execute($this->context);
        }

        return $this->context;
    }
}
