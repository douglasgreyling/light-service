<?php

use PHPUnit\Framework\TestCase;

use LightServicePHP\ContextMetadata;

final class ContextMetadataTest extends TestCase {
    public function test_it_is_instantiated_with_the_default_metadata_values() {
        $context_metadata = new ContextMetadata();

        foreach(ContextMetadata::DEFAULT_METADATA as $metadata => $default)
            $this->assertEquals($default, $context_metadata->$metadata);
    }

    public function test_it_can_be_convert_to_an_array() {
        $context_metadata = new ContextMetadata();

        $this->assertEquals(ContextMetadata::DEFAULT_METADATA, $context_metadata->to_array());
    }

    public function test_it_can_mark_the_failure_flag_as_true() {
        $context_metadata = new ContextMetadata();

        $context_metadata->fail();

        $this->assertTrue($context_metadata->failure);
    }

    public function test_it_can_mark_the_failure_flag_as_true_with_a_message() {
        $context_metadata = new ContextMetadata();

        $context_metadata->fail('Foo');

        $this->assertEquals('Foo', $context_metadata->message);
    }

    public function test_it_can_mark_the_failure_flag_as_true_with_a_message_and_an_error_code() {
        $context_metadata = new ContextMetadata();

        $context_metadata->fail('Foo', 100);

        $this->assertEquals('Foo', $context_metadata->message);
        $this->assertEquals(100, $context_metadata->error_code);
    }

    public function test_it_returns_the_inverse_key_for_a_given_alias_key_when_it_exists() {
        $context_metadata = new ContextMetadata();

        $context_metadata->key_aliases = ['a' => 'an_alias_for_a'];

        $this->assertEquals('a', $context_metadata->invert_alias('an_alias_for_a'));
    }

    public function test_it_returns_null_for_a_given_alias_key_when_it_does_not_exist() {
        $context_metadata = new ContextMetadata();

        $this->assertNull($context_metadata->invert_alias('an_alias_for_a'));
    }

    public function test_it_can_determine_if_a_given_key_alias_exists() {
        $context_metadata = new ContextMetadata();

        $context_metadata->key_aliases = ['a' => 'an_alias_for_a'];

        $this->assertTrue($context_metadata->alias_exists('an_alias_for_a'));
    }
}
