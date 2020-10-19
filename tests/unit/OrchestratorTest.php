<?php

it('can run through a list of actions belonging to an organizer', function() {
    $action_orchestrator = new Orchestrator(
        [
            AddsOneAction::class,
            AddsOneAction::class,
            AddsOneAction::class
        ],
        new SuccessfulOrganizer(['number' => 1])
    );

    $result = $action_orchestrator->run();

    expect($result->to_array())->toEqual(['number' => 4]);
});

it('can skip running the remaining actions when the context has been marked as a failure', function() {
    $action_orchestrator = new Orchestrator(
        [
            AddsOneAction::class,
            FailingAction::class,
            AddsOneAction::class
        ],
        new FailingOrganizer(['number' => 1])
    );

    $result = $action_orchestrator->run();

    expect($result->to_array())->toEqual(['number' => 2]);
});

it('can skip remaining actions when marked to skip remaining actions', function() {
    $action_orchestrator = new Orchestrator(
        [
            AddsOneAction::class,
            SkipRemainingAction::class,
            AddsOneAction::class
        ],
        new SkipRemainingOrganizer(['number' => 1])
    );

    $result = $action_orchestrator->run();

    expect($result->to_array())->toEqual(['number' => 2]);
});

it('can rollback the remaining actions when marked to rollback previously run actions', function() {
    $action_orchestrator = new Orchestrator(
        [
            AddsOneAction::class,
            AddsOneAction::class,
            AddsOneAction::class,
            AddsOneAction::class,
            RollbackAction::class,
            AddsOneAction::class,
        ],
        new RollbackOrganizer(['number' => 1])
    );

    $result = $action_orchestrator->run();

    expect($result->to_array())->toEqual(['number' => 0]);
});

it('can switch key aliases for a context when an action uses keys marked as key aliases', function() {
    $action_orchestrator = new Orchestrator(
        [
            AddsOneAction::class,
            KeyAliasesAction::class,
            AddsOneAction::class
        ],
        new KeyAliasesOrganizer(['number' => 1])
    );

    $result = $action_orchestrator->run();

    expect($result->to_array())->toEqual(['number' => 4]);
});
