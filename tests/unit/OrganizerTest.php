<?php

require_once 'tests/fixtures/NoCallFunctionOrganizer.php';
require_once 'tests/fixtures/DoesNothingOrganizer.php';
require_once 'tests/fixtures/SuccessfulOrganizer.php';
require_once 'tests/fixtures/OneSkipOrganizer.php';
require_once 'tests/fixtures/FailingOrganizer.php';

it('throws an error when the call function is not implemented', function() {
    NoCallFunctionOrganizer::call();
})->throws(NotImplementedException::class);

it('instantiates an organizer with the given context when using the with function', function() {
    $result = DoesNothingOrganizer::call(['a' => 1]);

    expect($result->to_array())->toEqual(['a' => 1]);
});

it('executes all of the actions provided to it where they are applicable', function() {
    $result = SuccessfulOrganizer::call(0);

    expect($result->to_array())->toEqual(['number' => 3]);
});

it('will skip actions which call the next_context function', function() {
    $result = OneSkipOrganizer::call(0);

    expect($result->to_array())->toEqual(['number' => 2]);
});

it('marks the context as a success when nothing goes wrong', function() {
    $result = SuccessfulOrganizer::call(0);

    expect($result->success())->toBeTrue();
    expect($result->failure())->toBeFalse();
});

it('marks the context as a failure when an action fails the context', function() {
    $result = FailingOrganizer::call(0);

    expect($result->to_array())->toEqual(['number' => 2]);
    expect($result->success())->toBeFalse();
    expect($result->failure())->toBeTrue();
    expect($result->message())->toEqual('foo');
});

it('shows the failure message when an action fails the context', function() {
    $result = FailingOrganizer::call(0);

    expect($result->message())->toEqual('foo');
});