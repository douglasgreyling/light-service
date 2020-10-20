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
require_once 'tests/fixtures/organizers/KeyAliasesOrganizer.php';
require_once 'tests/fixtures/organizers/RollbackOrganizer.php';
require_once 'tests/fixtures/organizers/ReduceIfOrganizer.php';
require_once 'tests/fixtures/organizers/ReduceUntilOrganizer.php';
require_once 'tests/fixtures/organizers/ExecuteOrganizer.php';
require_once 'tests/fixtures/organizers/AddToContextOrganizer.php';
require_once 'tests/fixtures/organizers/RollbackOrchestratorLogicOrganizer.php';
require_once 'tests/fixtures/organizers/FailingOrchestratorLogicOrganizer.php';
require_once 'tests/fixtures/organizers/SkipRemainingOrchestratorLogicOrganizer.php';
require_once 'tests/fixtures/organizers/IterateOrganizer.php';

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
    expect($result->message())->toEqual('Skipped remaining actions');
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

it('can use set key aliases for keys in the context', function() {
    $result = KeyAliasesOrganizer::call(1);

    expect($result->to_array())->toEqual(['number' => 4]);
});

it('can rollback a set of actions', function() {
    $result = RollbackOrganizer::call(1);

    expect($result->to_array())->toEqual(['number' => 0]);
    expect($result->message())->toEqual('I want to roll back!');
});

it('will reduce an action if the predicate returns true in the reduce if orchestrator logic function', function() {
    $result = ReduceIfOrganizer::call(1);

    expect($result->to_array())->toEqual(['number' => 4]);
});

it('will not reduce an action if the predicate returns false in the reduce if orchestrator logic function', function() {
    $result = ReduceIfOrganizer::call(-3);

    expect($result->to_array())->toEqual(['number' => -1]);
});

it('will reduce actions until the predicate returns true in the reduce until orchestrator logic function', function() {
    $result = ReduceUntilOrganizer::call(0);

    expect($result->to_array())->toEqual(['number' => 4]);
});

it('will reduce actions when the predicate returns false in the reduce until orchestrator logic function', function() {
    $result = ReduceUntilOrganizer::call(5);

    expect($result->to_array())->toEqual(['number' => 6]);
});

it('will execute a given callback action when the execute orchestrator logic function is used', function() {
    $result = ExecuteOrganizer::call(0);

    expect($result->to_array())->toEqual(['number' => 2]);
});

it('will add kvs to the context with the add to context orchestrator logic function', function() {
    $result = AddToContextOrganizer::call(0);

    expect($result->to_array())->toEqual(['number' => 1]);
});

it('will iterate over value for a given key and execute a set of actions with the iterate orchestrator logic function', function() {
    $result = IterateOrganizer::call(['numbers' => [1, 2, 3], 'sum' => 0]);

    expect($result->to_array())->toEqual(['numbers' => [1, 2, 3], 'sum' => 6]);
});

it('will rollback all the actions when orchestrator logic functions are used', function() {
    $result = RollbackOrchestratorLogicOrganizer::call(0);

    expect($result->to_array())->toEqual(['number' => -1]);
});

it('will stop running all the actions when the context is marked as a failure and orchestrator logic functions are used', function() {
    $result = FailingOrchestratorLogicOrganizer::call(0);

    expect($result->to_array())->toEqual(['number' => 5]);
});

it('will skip all remaining actions when marked to skip all reamining actions and orchestrator logic functions are used', function() {
    $result = SkipRemainingOrchestratorLogicOrganizer::call(0);

    expect($result->to_array())->toEqual(['number' => 5]);
});
