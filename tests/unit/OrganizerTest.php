<?php

require_once 'tests/fixtures/organizers/NoCallFunctionOrganizer.php';
require_once 'tests/fixtures/organizers/DoesNothingOrganizer.php';
require_once 'tests/fixtures/organizers/SuccessfulOrganizer.php';
require_once 'tests/fixtures/organizers/OneSkipOrganizer.php';
require_once 'tests/fixtures/organizers/FailingOrganizer.php';
require_once 'tests/fixtures/organizers/SkipRemainingOrganizer.php';
require_once 'tests/fixtures/organizers/BeforeAfterEachOrganizer.php';
require_once 'tests/fixtures/organizers/AroundEachOrganizer.php';
require_once 'tests/fixtures/organizers/AllHooksOrganizer.php';

it('throws an error when the call function is not implemented', function() {
    NoCallFunctionOrganizer::call();
})->throws(NotImplementedException::class);

it('instantiates an organizer with the given context when using the with function', function() {
    $result = DoesNothingOrganizer::call(['a' => 1]);

    expect($result->to_array())->toEqual(['a' => 1]);
});

it('instantiates the organizer context with the class of the organizer', function() {
    $result = DoesNothingOrganizer::call(['a' => 1]);

    expect($result->current_organizer())->toEqual(DoesNothingOrganizer::class);
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

    expect($result->success())->toBeFalse();
    expect($result->failure())->toBeTrue();
    expect($result->message())->toEqual('foo');
});

it('stops executing remaining actions when an actions fails the context', function() {
    $result = FailingOrganizer::call(0);

    expect($result->success())->toBeFalse();
    expect($result->failure())->toBeTrue();
    expect($result->to_array())->toEqual(['number' => 1]);
});

it('shows the failure message when an action fails the context', function() {
    $result = FailingOrganizer::call(0);

    expect($result->message())->toEqual('foo');
});

it('can skip remaining action by using the skip_remaining on the context', function() {
    $result = SkipRemainingOrganizer::call(0);

    expect($result->to_array())->toEqual(['number' => 1]);
});

it('can execute before and after actions', function() {
    $result = BeforeAfterEachOrganizer::call();

    expect($result->to_array())->toEqual(['hooks_called' => ['before', 'after', 'before', 'after']]);
});

it('can execute around each actions', function() {
    $result = AroundEachOrganizer::call();

    expect($result->to_array())->toEqual(['hook_count' => 4]);
});

it('can execute around, before and each actions in the correct order', function() {
    $result = AllHooksOrganizer::call();

    expect($result->to_array())->toEqual(['hooks_called' => ['around', 'before', 'after', 'around', 'around', 'before', 'after', 'around']]);
});
