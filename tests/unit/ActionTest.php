<?php

require 'tests/fixtures/NoMissingExpectsAction.php';
require 'tests/fixtures/NoMissingPromisesAction.php';
require 'tests/fixtures/MissingPromisesAction.php';
require 'tests/fixtures/SuccessfulAction.php';

it('returns no context validation errors with empty expected keys', function() {
    $action = NoMissingExpectsAction::call(['a' => 1, 'b' => 2]);

    expect($action->success())->toBeTrue();
});

it('throws an exception when the expected keys are not in the context', function() {
    SuccessfulAction::call(['a' => 1]);
})->throws(ExpectedKeysNotInContextException::class);

it('returns no context validation exceptions with empty promised keys', function() {
    $action = NoMissingPromisesAction::call(['a' => 1, 'b' => 2]);

    expect($action->success())->toBeTrue();
});

it('throws an exception when the promised keys are not in the context', function() {
    MissingPromisesAction::call(['a' => 1, 'b' => 2]);
})->throws(PromisedKeysNotInContextException::class);