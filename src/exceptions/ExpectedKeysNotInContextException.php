<?php

class ExpectedKeysNotInContextException extends Exception {
  public function errorMessage($expected_keys) {
    return 'Expected the following keys to be in context: ' . $expected_keys;
  }
}
