<?php

use PHPUnit\Framework\TestCase;

use LightServicePHP\Context;

use LightServicePHP\Exception\NextActionException;
use LightServicePHP\Exception\KeyAliasException;

final class ContextTest extends TestCase {
    public function test_it_is_instantiated_with_an_empty_context_when_given_no_state() {
        $context = new Context();

        $this->assertEmpty($context->to_array());
    }

    public function test_it_is_instantiated_with_a_context_based_on_its_given_state() {
        $state   = ['a' => 1, 'b' => 2];
        $context = new Context($state);

        $this->assertEquals(['a' => 1, 'b' => 2], $context->to_array());
    }

    public function test_it_is_instantiated_with_a_context_failure_flag_of_false() {
        $context = new Context();

        $this->assertFalse($context->failure());
    }

    public function test_it_is_instantiated_with_a_context_success_flag_of_true() {
        $context = new Context();

        $this->assertTrue($context->success());
    }

    public function test_it_can_convert_itself_to_an_array() {
        $state   = ['a' => 1, 'b' => 2];
        $context = new Context($state);

        $this->assertEquals(['a' => 1, 'b' => 2], $context->to_array());
    }

    public function test_can_convert_itself_to_an_array_including_its_metadata() {
        $state   = ['a' => 1, 'b' => 2];
        $context = new Context($state);

        $this->assertEquals(
            [
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
            ],
            $context->to_array(true));
    }

    public function test_it_fetches_values_from_the_context_like_an_object() {
        $context = new Context(['a' => 1]);

        $this->assertEquals(1, $context->a);
    }

    public function test_it_can_set_values_like_properties_of_an_object() {
        $context = new Context();

        $context->a[] = 1;
        $context->b = 0;
        $context->b += 1;
        $context->c = 'foo';
        $context->c .= 'bar';

        $this->assertEquals(
            [
                'a' => [1],
                'b' => 1,
                'c' => 'foobar'
            ],
            $context->to_array()
        );
    }

    public function test_it_returns_a_value_of_null_when_the_property_being_fetched_does_not_exist_in_the_context() {
        $context = new Context(['a' => 1]);

        $this->assertNull($context->b);
    }

    public function test_it_can_merge_a_set_of_key_value_pairs_into_the_context_using_the_merge_function() {
        $context = new Context(['a' => 1]);

        $context->merge(['b' => 2, 'c' => 3]);

        $this->assertEquals(['a' => 1, 'b' => 2, 'c' => 3], $context->to_array());
    }

    public function test_it_can_retrieve_keys_inside_the_context_with_the_keys_function() {
        $state   = ['a' => 1, 'b' => 2];
        $context = new Context($state);

        $this->assertEquals(['a', 'b'], $context->keys());
    }

    public function test_it_can_retrieve_the_values_inside_the_context_with_the_values_function() {
        $state   = ['a' => 1, 'b' => 2];
        $context = new Context($state);

        $this->assertEquals([1, 2], $context->values());
    }

    public function test_it_can_retrieve_multiple_key_value_pairs_using_the_fetch_function() {
        $state   = ['a' => 1, 'b' => 2, 'c' => 3];
        $context = new Context($state);

        $this->assertEquals(['a' => 1, 'c' => 3], $context->fetch(['a', 'c']));
    }

    public function test_it_marks_the_failure_flag_as_true_and_the_success_flag_as_false_when_the_context_is_explicitly_failed() {
        $state   = ['a' => 1, 'b' => 2, 'c' => 3];
        $context = new Context($state);

        $context->fail();

        $this->assertTrue($context->failure());
    }

    public function test_it_can_add_an_additional_failure_message_when_the_context_is_explicitly_failed() {
        $state   = ['a' => 1, 'b' => 2, 'c' => 3];
        $context = new Context($state);

        $context->fail('foo');

        $this->assertEquals('foo', $context->message());
    }

    public function test_it_can_mark_the_failure_flag_as_true_and_throw_a_NextActionException_when_the_fail_and_return_function_is_called() {
        $context                  = new Context();
        $correct_exception_thrown = false;

        try {
            $context->fail_and_return('foo');
        } catch (NextActionException $e) {
            $correct_exception_thrown = true;
        }

        $this->assertTrue($correct_exception_thrown);
        $this->assertTrue($context->failure());
    }

    public function test_it_can_mark_the_skip_remaining_flag_when_the_skip_remaining_function_is_called() {
        $context = new Context();

        $context->skip_remaining();

        $this->assertTrue($context->must_skip_all_remaining_actions());
    }

    public function test_it_can_set_and_get_the_current_action() {
        $context = new Context();

        $context->set_current_action('SomeAction');

        $this->assertEquals('SomeAction', $context->current_action());
    }

    public function test_it_can_set_and_get_the_current_organizer() {
        $context = new Context();

        $context->set_current_organizer('SomeOrganizer');

        $this->assertEquals('SomeOrganizer', $context->current_organizer());
    }

    public function test_it_can_use_a_set_of_key_aliases_to_change_the_context() {
        $context = new Context(['a' => 'value']);
        $context->use_aliases(['a' => 'an_alias_for_a']);

        $this->assertEquals('value', $context->an_alias_for_a);
        $this->assertEquals(['an_alias_for_a' => 'value'], $context->to_array());
    }

    public function test_it_throws_an_exception_when_it_attempts_to_use_and_set_a_key_alias_which_already_exists_inside_the_context() {
        $this->expectException(KeyAliasException::class);

        $context = new Context(['a' => 'value', 'an_alias_for_a' => 'some other value']);
        $context->use_aliases(['a' => 'an_alias_for_a']);
    }

    public function test_it_can_fail_a_context_with_an_error_code() {
        $context = new Context();

        $context->fail('Something went wrong', 4001);

        $this->assertEquals('Something went wrong', $context->message());
        $this->assertEquals($context->error_code(), 4001);
    }

    public function test_it_can_fail_a_context_and_skip_to_the_next_action_with_an_error_code() {
        $context                  = new Context();
        $correct_exception_thrown = false;

        try {
            $context->fail_and_return('Something went wrong', 4001);
        } catch (NextActionException $e) {
            $correct_exception_thrown = true;
        }

        $this->assertTrue($correct_exception_thrown);
        $this->assertTrue($context->failure());
        $this->assertEquals('Something went wrong', $context->message());
        $this->assertEquals(4001, $context->error_code());
    }
}
