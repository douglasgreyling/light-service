<?php

require_once 'src/ActionHookWrapper.php';

require_once 'tests/fixtures/organizers/BeforeAfterHooksOrganizer.php';
require_once 'tests/fixtures/organizers/AroundHooksOrganizer.php';

it('wraps an organizers before and after hooks around an action', function() {
    $context = BeforeAfterHooksOrganizer::call()->to_array();

    expect($context)->toEqual(['a' => ['before', 'action', 'after']]);
});

it('wraps an organizers around hooks around an action', function() {
    $context = AroundHooksOrganizer::call()->to_array();

    expect($context)->toEqual(['a' => ['around', 'action', 'around']]);
});

it('wraps all of an organizers hooks around an action', function() {
    $context = AllHooksOrganizer::call()->to_array();

    expect($context)->toEqual(['hooks_called' => ['around', 'before', 'after', 'around', 'around', 'before', 'after', 'around']]);
});