<?php

use PHPUnit\Framework\TestCase;

use LightService\Orchestrator;

use LightService\Fixtures\Actions\AddsOneAction;
use LightService\Fixtures\Actions\FailingAction;
use LightService\Fixtures\Actions\KeyAliasesAction;
use LightService\Fixtures\Actions\RollbackAction;
use LightService\Fixtures\Actions\SkipRemainingAction;

use LightService\Fixtures\Organizers\FailingOrganizer;
use LightService\Fixtures\Organizers\KeyAliasesOrganizer;
use LightService\Fixtures\Organizers\RollbackOrganizer;
use LightService\Fixtures\Organizers\SkipRemainingOrganizer;
use LightService\Fixtures\Organizers\SuccessfulOrganizer;

final class OrchestratorTest extends TestCase {
    public function test_it_can_run_through_a_list_of_actions_belonging_to_an_organizer() {
        $action_orchestrator = new Orchestrator(
            new SuccessfulOrganizer(['number' => 1])
        );

        $result = $action_orchestrator->run([
            AddsOneAction::class,
            AddsOneAction::class,
            AddsOneAction::class
        ]);

        $this->assertEquals(['number' => 4], $result->to_array());
    }

    public function test_it_can_skip_running_the_remaining_actions_when_the_context_has_been_marked_as_a_failure() {
        $action_orchestrator = new Orchestrator(
            new FailingOrganizer(['number' => 1])
        );

        $result = $action_orchestrator->run([
            AddsOneAction::class,
            FailingAction::class,
            AddsOneAction::class
        ]);

        $this->assertEquals(['number' => 2], $result->to_array());
    }

    public function test_it_can_skip_remaining_actions_when_marked_to_skip_remaining_actions() {
        $action_orchestrator = new Orchestrator(
            new SkipRemainingOrganizer(['number' => 1])
        );

        $result = $action_orchestrator->run([
            AddsOneAction::class,
            SkipRemainingAction::class,
            AddsOneAction::class
        ]);

        $this->assertEquals(['number' => 2], $result->to_array());
    }

    public function test_it_can_rollback_the_remaining_actions_when_marked_to_rollback_previously_run_actions() {
        $action_orchestrator = new Orchestrator(
            new RollbackOrganizer(['number' => 1])
        );

        $result = $action_orchestrator->run([
            AddsOneAction::class,
            AddsOneAction::class,
            AddsOneAction::class,
            AddsOneAction::class,
            RollbackAction::class,
            AddsOneAction::class,
        ]);

        $this->assertEquals(['number' => 0], $result->to_array());
    }

    public function test_it_can_switch_key_aliases_for_a_context_when_an_action_uses_keys_marked_as_key_aliases() {
        $action_orchestrator = new Orchestrator(
            new KeyAliasesOrganizer(['number' => 1])
        );

        $result = $action_orchestrator->run([
            AddsOneAction::class,
            KeyAliasesAction::class,
            AddsOneAction::class
        ]);

        $this->assertEquals(['number' => 4], $result->to_array());
    }
}
