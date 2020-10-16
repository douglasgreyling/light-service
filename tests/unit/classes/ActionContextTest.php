<?php

require_once 'src/classes/ActionContext.php';
require_once 'src/exceptions/NextActionException.php';
require_once 'src/exceptions/KeyAliasException.php';

it('is instantiated with an empty context when given no state', function() {
    $context = new ActionContext();

    expect($context->to_array())->toBeEmpty();
});

it('is instantiated with a context based on its given state', function() {
    $state   = ['a' => 1, 'b' => 2];
    $context = new ActionContext($state);

    expect($context->to_array())->toEqual(['a' => 1, 'b' => 2]);
});

it('is instantiated with a context failure flag of false', function() {
    $context = new ActionContext();

    expect($context->failure())->toBeFalse();
});

it('is instantiated with a context failure flag of true', function() {
    $context = new ActionContext();

    expect($context->success())->toBeTrue();
});

it('can retrieve values like an array', function() {
    $context = new ActionContext(['a' => 1]);

    expect($context['a'])->toEqual(1);
});

it('can set values like an array', function() {
    $context = new ActionContext();

    $context['a'] = 1;

    expect($context['a'])->toEqual(1);
});

it('returns a value of null when the key being fetched does not exist in the context', function() {
    $context = new ActionContext(['a' => 1]);

    expect($context->get('b'))->toBeNull();
});

it('can retrieve values using the get function', function() {
    $context = new ActionContext(['a' => 1]);

    expect($context->get('a'))->toEqual(1);
});

it('returns a value of null when the key passed to the get function does not exist in the context', function() {
    $context = new ActionContext(['a' => 1]);

    expect($context->get('b'))->toBeNull();
});

it('can set values using the set function', function() {
    $context = new ActionContext();

    $context->set('a', 1);

    expect($context->to_array())->toEqual(['a' => 1]);
});

it('can get values like properties of an object', function() {
    $context = new ActionContext(['a' => 1]);

    expect($context->a)->toEqual(1);
});

it('can set values like properties of an object', function() {
    $context = new ActionContext();

    $context->a = 1;

    expect($context->a)->toEqual(1);
});

it('returns a value of null when the property being fetched does not exist in the context', function() {
    $context = new ActionContext(['a' => 1]);

    expect($context->b)->toBeNull();
});

it('can merge a set of key/value pairs into the context using the merge function', function() {
    $context = new ActionContext(['a' => 1]);

    $context->merge(['b' => 2, 'c' => 3]);

    expect($context->to_array())->toEqual(['a' => 1, 'b' => 2, 'c' => 3]);
});

it('can merge a set of key/value pairs into the context using the array merge function', function() {
    $context = new ActionContext(['a' => 1]);

    $context->array_merge(['b' => 2, 'c' => 3]);

    expect($context->to_array())->toEqual(['a' => 1, 'b' => 2, 'c' => 3]);
});

it('can retrieve the keys inside the context with the keys function', function () {
    $state   = ['a' => 1, 'b' => 2];
    $context = new ActionContext($state);

    expect($context->keys())->toEqual(['a', 'b']);
});

it('can retrieve the values inside the context with the values function', function() {
    $state   = ['a' => 1, 'b' => 2];
    $context = new ActionContext($state);

    expect($context->values())->toEqual([1, 2]);
});

it('can return the current context as an array', function() {
    $state   = ['a' => 1, 'b' => 2];
    $context = new ActionContext($state);

    expect($context->to_array())->toEqual(['a' => 1, 'b' => 2]);
});

it('can retrieve multiple key/value pairs using the fetch function', function() {
    $state   = ['a' => 1, 'b' => 2, 'c' => 3];
    $context = new ActionContext($state);

    expect($context->fetch(['a', 'c']))->toEqual(['a' => 1, 'c' => 3]);
});

it('marks the failure flag as true and the success flag as false when the context is explicitly failed', function() {
    $state   = ['a' => 1, 'b' => 2, 'c' => 3];
    $context = new ActionContext($state);

    $context->fail();

    expect($context->failure())->toBeTrue();
});

it('can add an additional failure message when the context is explicitly failed', function() {
    $state   = ['a' => 1, 'b' => 2, 'c' => 3];
    $context = new ActionContext($state);

    $context->fail('foo');

    expect($context->message())->toEqual('foo');
});

it('can mark the failure flag as true and throw a NextActionException when the fail_and_return function is called', function() {
    $context = new ActionContext();
    $correct_exception_thrown = false;

    try {
        $context->fail_and_return('foo');
    } catch (NextActionException $e) {
        $correct_exception_thrown = true;
    }

    expect($correct_exception_thrown)->toBeTrue();
    expect($context->failure())->toBeTrue();
});

it('can mark the skip_remaining flag when the skip_remaining function is called', function() {
    $context = new ActionContext();

    $context->skip_remaining();

    expect($context->must_skip_all_remaining_actions())->toBeTrue();
});

it('can set and get the current action', function() {
    $context = new ActionContext();

    $context->set_current_action('SomeAction');

    expect($context->current_action())->toEqual('SomeAction');
});

it('can set and get the current organizer', function() {
    $context = new ActionContext();

    $context->set_current_organizer('SomeOrganizer');

    expect($context->current_organizer())->toEqual('SomeOrganizer');
});

it('can fetch a key by an alias', function() {
    $context = new ActionContext(['a' => 'value']);
    $context->set_key_aliases(['a' => 'an_alias_for_a']);

    expect($context->an_alias_for_a)->toEqual('value');
});

it('throws an exception when a key alias is defined for a key which is already inside the context', function() {
    $context = new ActionContext(['a' => 'value', 'an_alias_for_a' => 'some other value']);
    $context->set_key_aliases(['a' => 'an_alias_for_a']);
})->throws(KeyAliasException::class);
