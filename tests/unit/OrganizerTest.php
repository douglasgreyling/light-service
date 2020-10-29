<?php

use PHPUnit\Framework\TestCase;

use LightService\Exception\NotImplementedException;

use LightService\Fixtures\Organizers\AddToContextOrganizer;
use LightService\Fixtures\Organizers\AllHooksOrganizer;
use LightService\Fixtures\Organizers\AroundEachOrganizer;
use LightService\Fixtures\Organizers\BeforeAfterEachOrganizer;
use LightService\Fixtures\Organizers\DoesNothingOrganizer;
use LightService\Fixtures\Organizers\ExecuteOrganizer;
use LightService\Fixtures\Organizers\FailingOrchestratorLogicOrganizer;
use LightService\Fixtures\Organizers\FailingOrganizer;
use LightService\Fixtures\Organizers\IterateOrganizer;
use LightService\Fixtures\Organizers\KeyAliasesOrganizer;
use LightService\Fixtures\Organizers\NoCallFunctionOrganizer;
use LightService\Fixtures\Organizers\OneSkipOrganizer;
use LightService\Fixtures\Organizers\ReduceIfOrganizer;
use LightService\Fixtures\Organizers\ReduceUntilOrganizer;
use LightService\Fixtures\Organizers\RollbackOrchestratorLogicOrganizer;
use LightService\Fixtures\Organizers\RollbackOrganizer;
use LightService\Fixtures\Organizers\SkipRemainingOrchestratorLogicOrganizer;
use LightService\Fixtures\Organizers\SkipRemainingOrganizer;
use LightService\Fixtures\Organizers\SuccessfulOrganizer;

final class OrganizerTest extends TestCase {
    public function test_it_throws_an_error_when_the_call_function_is_not_implemented() {
        $this->expectException(NotImplementedException::class);

        NoCallFunctionOrganizer::call();
    }

    public function test_it_instantiates_an_organizer_with_the_given_context_when_using_the_with_function() {
        $result = DoesNothingOrganizer::call(['a' => 1]);

        $this->assertEquals(['a' => 1], $result->to_array());
    }

    public function test_it_instantiates_the_organizer_context_with_the_class_of_the_organizer() {
        $result = DoesNothingOrganizer::call(['a' => 1]);

        $this->assertEquals(DoesNothingOrganizer::class, $result->current_organizer());
    }

    public function test_it_executes_all_of_the_actions_provided_to_it_where_they_are_applicable() {
        $result = SuccessfulOrganizer::call(0);

        $this->assertEquals(['number' => 3], $result->to_array());
    }

    public function test_it_will_skip_actions_which_call_the_next_context_function() {
        $result = OneSkipOrganizer::call(0);

        $this->assertEquals(['number' => 2], $result->to_array());
    }

    public function test_it_marks_the_context_as_a_success_when_nothing_goes_wrong() {
        $result = SuccessfulOrganizer::call(0);

        $this->assertTrue($result->success());
        $this->assertFalse($result->failure());
    }

    public function test_it_marks_the_context_as_a_failure_when_an_action_fails_the_context() {
        $result = FailingOrganizer::call(0);

        $this->assertFalse($result->success());
        $this->assertTrue($result->failure());
        $this->assertEquals('foo', $result->message());
    }

    public function test_it_stops_executing_remaining_actions_when_an_actions_fails_the_context() {
        $result = FailingOrganizer::call(0);

        $this->assertFalse($result->success());
        $this->assertTrue($result->failure());
        $this->assertEquals(['number' => 1], $result->to_array());
    }

    public function test_it_shows_the_failure_message_when_an_action_fails_the_context() {
        $result = FailingOrganizer::call(0);

        $this->assertEquals('foo', $result->message());
    }

    public function test_it_can_skip_remaining_action_by_using_the_skip_remaining_on_the_context() {
        $result = SkipRemainingOrganizer::call(0);

        $this->assertEquals(['number' => 1], $result->to_array());
    }

    public function test_it_can_execute_before_and_after_actions() {
        $result = BeforeAfterEachOrganizer::call();

        $this->assertEquals(['hooks_called' => ['before', 'after', 'before', 'after']], $result->to_array());
    }

    public function test_it_can_execute_around_each_actions() {
        $result = AroundEachOrganizer::call();

        $this->assertEquals(['hook_count' => 4], $result->to_array());
    }

    public function test_it_can_execute_around_before_and_each_actions_in_the_correct_order() {
        $result = AllHooksOrganizer::call();

        $this->assertEquals(
            [
                'hooks_called' => [
                    'around',
                    'before',
                    'after',
                    'around',
                    'around',
                    'before',
                    'after',
                    'around'
                ]
            ],
            $result->to_array()
        );
    }

    public function test_it_can_use_set_key_aliases_for_keys_in_the_context() {
        $result = KeyAliasesOrganizer::call(1);

        $this->assertEquals(['number' => 4], $result->to_array());
    }

    public function test_it_can_rollback_a_set_of_actions() {
        $result = RollbackOrganizer::call(1);

        $this->assertEquals(['number' => 0], $result->to_array());
        $this->assertEquals('I want to roll back!', $result->message());
    }

    public function test_it_will_reduce_an_action_if_the_predicate_returns_true_in_the_reduce_if_orchestrator_logic_function() {
        $result = ReduceIfOrganizer::call(1);

        $this->assertEquals(['number' => 4], $result->to_array());
    }

    public function test_it_will_not_reduce_an_action_if_the_predicate_returns_false_in_the_reduce_if_orchestrator_logic_function() {
        $result = ReduceIfOrganizer::call(-3);

        $this->assertEquals(['number' => -1], $result->to_array());
    }

    public function test_it_will_reduce_actions_until_the_predicate_returns_true_in_the_reduce_until_orchestrator_logic_function() {
        $result = ReduceUntilOrganizer::call(0);

        $this->assertEquals(['number' => 4], $result->to_array());
    }

    public function test_it_will_reduce_actions_when_the_predicate_returns_false_in_the_reduce_until_orchestrator_logic_function() {
        $result = ReduceUntilOrganizer::call(5);

        $this->assertEquals(['number' => 6], $result->to_array());
    }

    public function test_it_will_execute_a_given_callback_action_when_the_execute_orchestrator_logic_function_is_used() {
        $result = ExecuteOrganizer::call(0);

        $this->assertEquals(['number' => 2], $result->to_array());
    }

    public function test_it_will_add_kvs_to_the_context_with_the_add_to_context_orchestrator_logic_function() {
        $result = AddToContextOrganizer::call(0);

        $this->assertEquals(['number' => 1], $result->to_array());
    }

    public function test_it_will_iterate_over_value_for_a_given_key_and_execute_a_set_of_actions_with_the_iterate_orchestrator_logic_function() {
        $result = IterateOrganizer::call(['numbers' => [1, 2, 3], 'sum' => 0]);

        $this->assertEquals(['numbers' => [1, 2, 3], 'sum' => 6], $result->to_array());
    }

    public function test_it_will_rollback_all_the_actions_when_orchestrator_logic_functions_are_used() {
        $result = RollbackOrchestratorLogicOrganizer::call(0);

        $this->assertEquals(['number' => -1], $result->to_array());
    }

    public function test_it_will_stop_running_all_the_actions_when_the_context_is_marked_as_a_failure_and_orchestrator_logic_functions_are_used() {
        $result = FailingOrchestratorLogicOrganizer::call(0);

        $this->assertEquals(['number' => 5], $result->to_array());
    }

    public function test_it_will_skip_all_remaining_actions_when_marked_to_skip_all_reamining_actions_and_orchestrator_logic_functions_are_used() {
        $result = SkipRemainingOrchestratorLogicOrganizer::call(0);

        $this->assertEquals(['number' => 5], $result->to_array());
    }
}
