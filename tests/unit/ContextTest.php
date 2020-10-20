<?php

require_once 'src/Context.php';
require_once 'src/exceptions/NextActionException.php';
require_once 'src/exceptions/KeyAliasException.php';

it('is instantiated with an empty context when given no state', function() {
    $context = new Context();

    expect($context->to_array())->toBeEmpty();
});

it('is instantiated with a context based on its given state', function() {
    $state   = ['a' => 1, 'b' => 2];
    $context = new Context($state);

    expect($context->to_array())->toEqual(['a' => 1, 'b' => 2]);
});

it('is instantiated with a context failure flag of false', function() {
    $context = new Context();

    expect($context->failure())->toBeFalse();
});

it('is instantiated with a context failure flag of true', function() {
    $context = new Context();

    expect($context->success())->toBeTrue();
});

it('can convert itself to an array', function() {
    $state   = ['a' => 1, 'b' => 2];
    $context = new Context($state);

    expect($context->to_array())->toEqual(['a' => 1, 'b' => 2]);
});

it('can convert itself to an array including its metadata', function() {
    $state   = ['a' => 1, 'b' => 2];
    $context = new Context($state);

    expect($context->to_array(true))->toEqual([
        'a'         => 1,
        'b'         => 2,
        '_metadata' => [
            'failure'           => false,
            'success'           => true,
            'message'           => '',
            'error_code'        => '',
            'skip_remaining'    => false,
            'current_action'    => '',
            'current_organizer' => '',
            'key_aliases'       => [],
            'rollback'          => false,
            'executed_actions'  => []
        ]
    ]);
});

it('fetches values from the context like an object', function() {
    $context = new Context(['a' => 1]);

    expect($context->a)->toEqual(1);
});

it('can set values like properties of an object', function() {
    $context = new Context();

    $context->a[] = 1;
    $context->b = 0;
    $context->b += 1;
    $context->c = 'foo';
    $context->c .= 'bar';

    expect($context->a)->toEqual([1]);
    expect($context->b)->toEqual(1);
    expect($context->c)->toEqual('foobar');
});

it('returns a value of null when the property being fetched does not exist in the context', function() {
    $context = new Context(['a' => 1]);

    expect($context->b)->toBeNull();
});

it('can merge a set of key/value pairs into the context using the merge function', function() {
    $context = new Context(['a' => 1]);

    $context->merge(['b' => 2, 'c' => 3]);

    expect($context->to_array())->toEqual(['a' => 1, 'b' => 2, 'c' => 3]);
});

it('can merge a set of key/value pairs into the context using the array merge function', function() {
    $context = new Context(['a' => 1]);

    $context->array_merge(['b' => 2, 'c' => 3]);

    expect($context->to_array())->toEqual(['a' => 1, 'b' => 2, 'c' => 3]);
});

it('can retrieve the keys inside the context with the keys function', function () {
    $state   = ['a' => 1, 'b' => 2];
    $context = new Context($state);

    expect($context->keys())->toEqual(['a', 'b']);
});

it('can retrieve the values inside the context with the values function', function() {
    $state   = ['a' => 1, 'b' => 2];
    $context = new Context($state);

    expect($context->values())->toEqual([1, 2]);
});

it('can retrieve multiple key/value pairs using the fetch function', function() {
    $state   = ['a' => 1, 'b' => 2, 'c' => 3];
    $context = new Context($state);

    expect($context->fetch(['a', 'c']))->toEqual(['a' => 1, 'c' => 3]);
});

it('marks the failure flag as true and the success flag as false when the context is explicitly failed', function() {
    $state   = ['a' => 1, 'b' => 2, 'c' => 3];
    $context = new Context($state);

    $context->fail();

    expect($context->failure())->toBeTrue();
});

it('can add an additional failure message when the context is explicitly failed', function() {
    $state   = ['a' => 1, 'b' => 2, 'c' => 3];
    $context = new Context($state);

    $context->fail('foo');

    expect($context->message())->toEqual('foo');
});

it('can mark the failure flag as true and throw a NextActionException when the fail_and_return function is called', function() {
    $context = new Context();
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
    $context = new Context();

    $context->skip_remaining();

    expect($context->must_skip_all_remaining_actions())->toBeTrue();
});

it('can set and get the current action', function() {
    $context = new Context();

    $context->set_current_action('SomeAction');

    expect($context->current_action())->toEqual('SomeAction');
});

it('can set and get the current organizer', function() {
    $context = new Context();

    $context->set_current_organizer('SomeOrganizer');

    expect($context->current_organizer())->toEqual('SomeOrganizer');
});

it('use a set of key aliases to change the context', function() {
    $context = new Context(['a' => 'value']);
    $context->use_aliases(['a' => 'an_alias_for_a']);

    expect($context->an_alias_for_a)->toEqual('value');
    expect($context->to_array())->toEqual([
        'an_alias_for_a' => 'value'
    ]);
});

it('throws an exception when it attempts to use and set a key alias which already exists inside the context', function() {
    $context = new Context(['a' => 'value', 'an_alias_for_a' => 'some other value']);
    $context->use_aliases(['a' => 'an_alias_for_a']);
})->throws(KeyAliasException::class);

it('can fail a context with an error code', function() {
    $context = new Context();

    $context->fail('Something went wrong', 4001);

    expect($context->message())->toEqual('Something went wrong');
    expect($context->error_code())->toEqual(4001);
});

it('can fail a context and skip to the next action with an error code', function() {
    $context = new Context();
    $correct_exception_thrown = false;

    try {
        $context->fail_and_return('Something went wrong', 4001);
    } catch (NextActionException $e) {
        $correct_exception_thrown = true;
    }

    expect($correct_exception_thrown)->toBeTrue();
    expect($context->failure())->toBeTrue();
    expect($context->message())->toEqual('Something went wrong');
    expect($context->error_code())->toEqual(4001);
});
