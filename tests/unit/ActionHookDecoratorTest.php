<?php

require_once 'src/ActionHookDecorator.php';

it('wraps before and after callbacks around a function', function() {
  $a = [];

  ActionHookDecorator::decorate(
    function() use(&$a) { $a[] = 'before'; },
    function() use(&$a) { return $a[] = 'actual'; },
    function() use(&$a) { $a[] = 'after'; }
  )();

  expect($a)->toEqual(['before', 'actual', 'after']);
});

it('returns the return value of the actual callback function', function() {
  $a = [];

  $result = ActionHookDecorator::decorate(
    function() use(&$a) { $a[] = 'before'; },
    function() use(&$a) { return $a[] = 'actual'; },
    function() use(&$a) { $a[] = 'after'; }
  )();

  expect($result)->toEqual('actual');
});