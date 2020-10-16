<?php

namespace LightServicePHP;

require_once 'classes/ActionContext.php';

require_once 'exceptions/ExpectedKeysNotInContextException.php';
require_once 'exceptions/NextActionException.php';
require_once 'exceptions/NotImplementedException.php';
require_once 'exceptions/PromisedKeysNotInContextException.php';

use ActionContext;

use ExpectedKeysNotInContextException;
use NextActionException;
use NotImplementedException;
use PromisedKeysNotInContextException;

trait Action {
    private $context;

    public function __construct($context) {
        $this->context = is_a($context, ActionContext::class) ? $context : new ActionContext($context);
        $this->context->set_current_action(self::class);
    }

    public function run() {
        try {
            $this->validate_expected_keys();
            $this->executed($this->context);
            $this->validate_promised_keys();
        } catch (NextActionException $e) {}

        return $this->context;
    }

    private function executed($context) {
        throw new NotImplementedException();
    }

    public static function execute($context = []) {
        return (new self($context))->run();
    }

    private function validate_expected_keys() {
        $expected_keys = [];

        if (isset($this->expects))
            $expected_keys = is_array($this->expects) ? $this->expects : [$this->expects];

        $expected_keys_length = count($expected_keys);
        $matched_keys         = array_keys($this->context->fetch($expected_keys));

        if ($expected_keys_length != count($matched_keys))
        throw new ExpectedKeysNotInContextException(join(', ', array_diff($expected_keys, $matched_keys)));
    }

    private function validate_promised_keys() {
        $promised_keys = [];

        if (isset($this->promises))
            $promised_keys = is_array($this->promises) ? $this->promises : [$this->promises];

        $promised_keys_length = count($promised_keys);
        $matched_keys         = array_keys($this->context->fetch($promised_keys));

        if ($promised_keys_length != count($matched_keys))
        throw new PromisedKeysNotInContextException(join(', ', array_diff($promised_keys, $matched_keys)));
    }

    public function context() {
        return $this->context;
    }

    private function next_context() {
        throw new NextActionException;
    }
}
