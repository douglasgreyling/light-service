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
  // test what happens when expects is present and empty
  public static function expects() {
    return [];
  }

  // test what happens when promises is present and empty
  public static function promises() {
    return [];
  }

  // test what happens when executed is not implemented
  public static function executed($context) {
    throw new NotImplementedException();
  }

  // test that the context is marked as failed with an exception
  // test that the error is raised after being caught
  // test what happens when the context given is a ActionContext (could change to a organizer context later)
  // test what happens when the context given is an array
  public static function call($context) {
    $context = is_a($context, 'ActionContext') ? $context : new ActionContext($context);

    try {
      self::validateExpectedKeys($context);
      self::executed($context);
      self::validatePromisedKeys($context);
      // Change this so it handle two key exceptions and doesnt catch all exceptions
    } catch (\Exception $e) {
      $context['failure'] = true;
      throw $e;
    }

    return $context;
  }

  // test what happens when expects is empty and empty
  // test what happens when expects is not an array
  // test what happens when expects partially has accepted keys
  private static function validateExpectedKeys($context) {
    $expected_keys_length = count(self::expects());
    $matched_keys         = array_keys($context->fetch(self::expects()));

    if ($expected_keys_length != count($matched_keys))
      throw new ExpectedKeysNotInContextException(join(', ', array_diff(self::expects(), $matched_keys)));
  }

  // test what happens when promises is empty and empty
  // test what happens when promises is not an array
  // test what happens when promises partially has accepted keys
  private static function validatePromisedKeys($context) {
    $promised_keys_length = count(self::promises());
    $matched_keys         = array_keys($context->fetch(self::promises()));

    if ($promised_keys_length != count($matched_keys))
      throw new PromisedKeysNotInContextException(join(', ', array_diff(self::promises(), $matched_keys)));
  }
}
