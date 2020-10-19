<?php

require_once 'src/ContextMetadata.php';

it('is instantiated with the default metadata values', function() {
  $context_metadata = new ContextMetadata();

  foreach(ContextMetadata::DEFAULT_METADATA as $metadata => $default)
    expect($context_metadata->$metadata)->toEqual($default);
});

it('can be converted to an array', function() {
    $context_metadata = new ContextMetadata();

    expect($context_metadata->to_array())->toEqual(ContextMetadata::DEFAULT_METADATA);
});

it('can mark the failure flag as true', function() {
    $context_metadata = new ContextMetadata();

    $context_metadata->fail();

    expect($context_metadata->failure)->toBeTrue();
});

it('can mark the failure flag as true with a message', function() {
    $context_metadata = new ContextMetadata();

    $context_metadata->fail('Foo');

    expect($context_metadata->message)->toEqual('Foo');
});

it('can mark the failure flag as true with a message and an error code', function() {
    $context_metadata = new ContextMetadata();

    $context_metadata->fail('Foo', 100);

    expect($context_metadata->error_code)->toEqual(100);
});

it('returns the inverse key for a given alias key when it exists', function() {
    $context_metadata = new ContextMetadata();

    $context_metadata->key_aliases = ['a' => 'an_alias_for_a'];

    expect($context_metadata->invert_alias('an_alias_for_a'))->toEqual('a');
});

it('returns null for a given alias key when it does not exist', function() {
    $context_metadata = new ContextMetadata();

    expect($context_metadata->invert_alias('an_alias_for_a'))->toBeNull();
});

it('can determine if a given key alias exists', function() {
    $context_metadata = new ContextMetadata();

    $context_metadata->key_aliases = ['a' => 'an_alias_for_a'];

    expect($context_metadata->alias_exists('an_alias_for_a'))->toBeTrue();
});