<?php

namespace LightService\Tests\Unit;

use PHPUnit\Framework\TestCase;
use LightService\Orchestrator;
use LightService\Fixtures\Actions\AddsOneAction;
use LightService\Fixtures\Actions\CountsConstructionsAction;
use LightService\Fixtures\Actions\FailingAction;
use LightService\Fixtures\Actions\KeyAliasesAction;
use LightService\Fixtures\Actions\RollbackAction;
use LightService\Fixtures\Actions\SkipRemainingAction;
use LightService\Fixtures\Organizers\CountsConstructionsOrganizer;
use LightService\Fixtures\Organizers\FailingOrganizer;
use LightService\Fixtures\Organizers\KeyAliasesOrganizer;
use LightService\Fixtures\Organizers\RollbackOrganizer;
use LightService\Fixtures\Organizers\SkipRemainingOrganizer;
use LightService\Fixtures\Organizers\SuccessfulOrganizer;

final class OrchestratorTest extends TestCase
{
    public function testItCanRunThroughAListOfActionsBelongingToAnOrganizer()
    {
        $action_orchestrator = new Orchestrator(
            new SuccessfulOrganizer(['number' => 1])
        );

        $result = $action_orchestrator->run([
            AddsOneAction::class,
            AddsOneAction::class,
            AddsOneAction::class
        ]);

        $this->assertEquals(['number' => 4], $result->toArray());
    }

    public function testItCanSkipRunningTheRemainingActionsWhenTheContextHasBeenMarkedAsAFailure()
    {
        $action_orchestrator = new Orchestrator(
            new FailingOrganizer(['number' => 1])
        );

        $result = $action_orchestrator->run([
            AddsOneAction::class,
            FailingAction::class,
            AddsOneAction::class
        ]);

        $this->assertEquals(['number' => 2], $result->toArray());
    }

    public function testItCanSkipRemainingActionsWhenMarkedToSkipRemainingActions()
    {
        $action_orchestrator = new Orchestrator(
            new SkipRemainingOrganizer(['number' => 1])
        );

        $result = $action_orchestrator->run([
            AddsOneAction::class,
            SkipRemainingAction::class,
            AddsOneAction::class
        ]);

        $this->assertEquals(['number' => 2], $result->toArray());
    }

    public function testItCanRollbackTheRemainingActionsWhenMarkedToRollbackPreviouslyRunActions()
    {
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

        $this->assertEquals(['number' => 0], $result->toArray());
    }

    public function testItCanSwitchKeyAliasesForAContextWhenAnActionUsesKeysMarkedAsKeyAliases()
    {
        $action_orchestrator = new Orchestrator(
            new KeyAliasesOrganizer(['number' => 1])
        );

        $result = $action_orchestrator->run([
            AddsOneAction::class,
            KeyAliasesAction::class,
            AddsOneAction::class
        ]);

        $this->assertEquals(['number' => 4], $result->toArray());
    }
    public function testItBuildsEachActionOnlyOnce()
    {
        CountsConstructionsAction::$constructions = 0;

        $result = CountsConstructionsOrganizer::call();

        $this->assertEquals(3, $result->number);
        // One build per action. It used to be two: one to read the expected
        // keys and a second to actually run it.
        $this->assertEquals(3, CountsConstructionsAction::$constructions);
    }
    public function testItTreatsAClassNameAsAnActionEvenWhenAPhpFunctionSharesItsName()
    {
        // is_callable('touch') is true because touch() exists, which used to
        // make an un-namespaced action class called Touch get invoked as a
        // function instead of run as an action.
        $this->assertFalse(Orchestrator::isOrchestratorLogic('touch'));
        $this->assertFalse(Orchestrator::isOrchestratorLogic(AddsOneAction::class));
        $this->assertTrue(Orchestrator::isOrchestratorLogic(function () {
            return null;
        }));
    }
}
