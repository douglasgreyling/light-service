<?php

require 'tests/fixtures/MissingPromisesAction.php';
require 'tests/fixtures/NextActionAction.php';
require 'tests/fixtures/NoExecutedFunctionAction.php';
require 'tests/fixtures/NoMissingExpectsAction.php';
require 'tests/fixtures/NoMissingPromisesAction.php';
require 'tests/fixtures/SuccessfulAction.php';
require 'tests/fixtures/UnexpectedErrorAction.php';

it('returns no context validation errors with empty expected keys', function() {
    $result = NoMissingExpectsAction::execute(['a' => 1, 'b' => 2]);

    expect($result->success())->toBeTrue();
});

it('throws an exception when the expected keys are not in the context', function() {
    SuccessfulAction::execute(['a' => 1]);
})->throws(ExpectedKeysNotInContextException::class);

it('returns no context validation exceptions with empty promised keys', function() {
    $result = NoMissingPromisesAction::execute(['a' => 1, 'b' => 2]);

    expect($result->success())->toBeTrue();
});

it('throws an exception when the promised keys are not in the context', function() {
    MissingPromisesAction::execute(['a' => 1, 'b' => 2]);
})->throws(PromisedKeysNotInContextException::class);

it('throws an exception when the executed function is not implemented', function() {
    NoExecutedFunctionAction::execute(['a' => 1, 'b' => 2]);
})->throws(NotImplementedException::class);

it('can mark the current context as failed with a message using the fail method', function() {
    $result = NextActionAction::execute(['a' => 1, 'b' => 2]);

    expect($result->failure())->toBeFalse();
    expect($result->success())->toBeTrue();
    expect($result->keys())->not()->toHaveKey('d');
});
