<?php

namespace LightService;

use LightService\Context;

use LightService\Exception\ExpectedKeysNotInContextException;
use LightService\Exception\PromisedKeysNotInContextException;
use LightService\Exception\NextActionException;
use LightService\Exception\RollbackException;
use LightService\Exception\NotImplementedException;

trait Action {
    private $context;

    public function __construct($context = []) {
        $this->context = is_a($context, Context::class) ? $context : new Context($context);
        $this->context->set_current_action(self::class);
    }

    public function run() {
        try {
            $this->validate_expected_keys();
            $this->executed($this->context);
            $this->validate_promised_keys();
        } catch (NextActionException $e) {
            // no op
        }
        catch (RollbackException $e) {
            $this->rolled_back($this->context);
        }

        return $this->context;
    }

    public static function execute($context = []) {
        return (new self($context))->run();
    }

    public static function rollback($context = []) {
        $action = new self($context);
        $action->rolled_back($action->context());

        return $action->context();
    }

    public function context() {
        return $this->context;
    }

    public function expected_keys() {
        $expected_keys = [];

        if (isset($this->expects))
            $expected_keys = is_array($this->expects) ? $this->expects : [$this->expects];

        $expected_keys = array_unique($expected_keys);

        return $expected_keys;
    }

    private function executed($context) {
        throw new NotImplementedException();
    }

    private function rolled_back($context) {
        // no op
    }

    private function validate_expected_keys() {
        $expected_keys = $this->expected_keys();

        $expected_keys_length = count($expected_keys);
        $matched_keys         = array_keys($this->context->fetch($expected_keys));

        if ($expected_keys_length != count($matched_keys))
            throw new ExpectedKeysNotInContextException(join(', ', array_diff($expected_keys, $matched_keys)));
    }

    private function validate_promised_keys() {
        $promised_keys = $this->promised_keys();

        $promised_keys_length = count($promised_keys);
        $matched_keys         = array_keys($this->context->fetch($promised_keys));

        if ($promised_keys_length != count($matched_keys))
            throw new PromisedKeysNotInContextException(join(', ', array_diff($promised_keys, $matched_keys)));
    }

    private function promised_keys() {
        $promised_keys = [];

        if (isset($this->promises))
            $promised_keys = is_array($this->promises) ? $this->promises : [$this->promises];

        $promised_keys = array_unique($promised_keys);

        return $promised_keys;
    }

    private function next_context() {
        throw new NextActionException;
    }
}
