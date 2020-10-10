<?php

namespace LightServicePHP;

require 'classes/ActionContext.php';

require 'exceptions/NotImplementedException.php';
require 'exceptions/ExpectedKeysNotInContextException.php';
require 'exceptions/PromisedKeysNotInContextException.php';

use ActionContext;

use NotImplementedException;
use ExpectedKeysNotInContextException;
use PromisedKeysNotInContextException;

trait Action {
  private $context;

  public function __construct($context) {
    $this->context = is_a($context, ActionContext::class) ? $context : new ActionContext($context);
  }

  public function run() {
    $this->validate_expected_keys();
    $this->executed();
    $this->validate_promised_keys();

    return $this->context;
  }

  private function executed() {
    throw new NotImplementedException();
  }

  private function fail($message = '') {
    $this->context->fail($message);
  }

  // test what happens when the context given is a ActionContext (could change to a organizer context later)
  // test what happens when the context given is an array

  public static function execute($context) {
    return (new self($context))->run();
  }

  // test what happens when expects is empty and empty
  // test what happens when expects is not an array
  // test what happens when expects partially has accepted keys
  private function validate_expected_keys() {
    $expected_keys        = isset($this->expects) ? $this->expects : [];
    $expected_keys_length = count($expected_keys);
    $matched_keys         = array_keys($this->context->fetch($expected_keys));

    if ($expected_keys_length != count($matched_keys))
      throw new ExpectedKeysNotInContextException(join(', ', array_diff($expected_keys, $matched_keys)));
  }

  // test what happens when promises is empty and empty
  // test what happens when promises is not an array
  // test what happens when promises partially has accepted keys
  private function validate_promised_keys() {
    $promised_keys        = isset($this->promises) ? $this->promises : [];
    $promised_keys_length = count($promised_keys);
    $matched_keys         = array_keys($this->context->fetch($promised_keys));

    if ($promised_keys_length != count($matched_keys))
      throw new PromisedKeysNotInContextException(join(', ', array_diff($promised_keys, $matched_keys)));
  }

  public function context() {
    return $this->context;
  }
}
