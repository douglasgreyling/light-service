<?php

namespace LightService\Tests\Unit;

use PHPUnit\Framework\TestCase;
use LightService\Exception\NotImplementedException;
use LightService\Exception\KeyIsNotIterableException;
use RuntimeException;
use LightService\Fixtures\Organizers\{
    AddToContextOrganizer,
    AllHooksOrganizer,
    BaseNameOrganizer,
    AroundEachOrganizer,
    BeforeAfterEachOrganizer,
    DoesNothingOrganizer,
    ExecuteOrganizer,
    FailingOrchestratorLogicOrganizer,
    FailingOrganizer,
    IterateOrganizer,
    IterateOrganizerWithOrchestrators,
    IterateOverAMissingKeyOrganizer,
    IterateOverANonIterableOrganizer,
    IterateRollbackOrganizer,
    IterateStopsOnFailureOrganizer,
    IterateStopsOnSkipOrganizer,
    IterateWithExistingSingularKeyOrganizer,
    IterateWithHooksOrganizer,
    KeyAliasesOrganizer,
    NoCallFunctionOrganizer,
    OneSkipOrganizer,
    ReduceIfOrganizer,
    ReduceIfOrganizerWithFalseActions,
    ReduceUntilOrganizer,
    ReduceUntilStopsOnFailureOrganizer,
    ReduceTwiceAfterRollbackOrganizer,
    ReduceUntilStopsOnSkipOrganizer,
    RepeatedActionRollbackOrganizer,
    RollbackAtEndOfChainOrganizer,
    RollbackOrchestratorLogicOrganizer,
    RollbackOrganizer,
    SkipRemainingOrchestratorLogicOrganizer,
    SkipRemainingOrganizer,
    SpecialisedNameOrganizer,
    SuccessfulOrganizer,
    ThrowingActionWithAliasesOrganizer,
    ThrowingActionWithHooksOrganizer
};

final class OrganizerTest extends TestCase
{
    public function testItThrowsAnErrorWhenTheCallFunctionIsNotImplemented()
    {
        $this->expectException(NotImplementedException::class);

        NoCallFunctionOrganizer::call();
    }

    public function testItInstantiatesAnOrganizerWithTheGivenContextWhenUsingTheWithFunction()
    {
        $result = DoesNothingOrganizer::call(['a' => 1]);

        $this->assertEquals(['a' => 1], $result->toArray());
    }

    public function testItInstantiatesTheOrganizerContextWithTheClassOfTheOrganizer()
    {
        $result = DoesNothingOrganizer::call(['a' => 1]);

        $this->assertEquals(DoesNothingOrganizer::class, $result->currentOrganizer());
    }

    public function testItExecutesAllOfTheActionsProvidedToItWhereTheyAreApplicable()
    {
        $result = SuccessfulOrganizer::call(0);

        $this->assertEquals(['number' => 3], $result->toArray());
    }

    public function testItWillSkipActionsWhichCallTheNextContextFunction()
    {
        $result = OneSkipOrganizer::call(0);

        $this->assertEquals(['number' => 2], $result->toArray());
    }

    public function testItMarksTheContextAsASuccessWhenNothingGoesWrong()
    {
        $result = SuccessfulOrganizer::call(0);

        $this->assertTrue($result->success());
        $this->assertFalse($result->failure());
    }

    public function testItMarksTheContextAsAFailureWhenAnActionFailsTheContext()
    {
        $result = FailingOrganizer::call(0);

        $this->assertFalse($result->success());
        $this->assertTrue($result->failure());
        $this->assertEquals('foo', $result->message());
    }

    public function testItStopsExecutingRemainingActionsWhenAnActionsFailsTheContext()
    {
        $result = FailingOrganizer::call(0);

        $this->assertFalse($result->success());
        $this->assertTrue($result->failure());
        $this->assertEquals(['number' => 1], $result->toArray());
    }

    public function testItShowsTheFailureMessageWhenAnActionFailsTheContext()
    {
        $result = FailingOrganizer::call(0);

        $this->assertEquals('foo', $result->message());
    }

    public function testItCanSkipRemainingActionByUsingTheSkipRemainingOnTheContext()
    {
        $result = SkipRemainingOrganizer::call(0);

        $this->assertEquals(['number' => 1], $result->toArray());
    }

    public function testItCanExecuteBeforeAndAfterActions()
    {
        $result = BeforeAfterEachOrganizer::call();

        $this->assertEquals(['hooks_called' => ['before', 'after', 'before', 'after']], $result->toArray());
    }

    public function testItCanExecuteAroundEachActions()
    {
        $result = AroundEachOrganizer::call();

        $this->assertEquals(['hook_count' => 4], $result->toArray());
    }

    public function testItCanExecuteAroundBeforeAndEachActionsInTheCorrectOrder()
    {
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
            $result->toArray()
        );
    }

    public function testItCanUseSetKeyAliasesForKeysInTheContext()
    {
        $result = KeyAliasesOrganizer::call(1);

        $this->assertEquals(['number' => 4], $result->toArray());
    }

    public function testItCanRollbackASetOfActions()
    {
        $result = RollbackOrganizer::call(1);

        $this->assertEquals(['number' => 0], $result->toArray());
        $this->assertEquals('I want to roll back!', $result->message());
    }

    public function testItWillReduceAnActionIfThePredicateReturnsTrueInTheReduceIfOrchestratorLogicFunction()
    {
        $result = ReduceIfOrganizer::call(1);

        $this->assertEquals(['number' => 4], $result->toArray());
    }

    public function testItWillNotReduceAnActionIfThePredicateReturnsFalseInTheReduceIfOrchestratorLogicFunction()
    {
        $result = ReduceIfOrganizer::call(-3);

        $this->assertEquals(['number' => -1], $result->toArray());
    }

    public function testItWillReduceTheFirstSetOfProvidedActionsIfThePredicateIsTrue()
    {
        $result = ReduceIfOrganizerWithFalseActions::call(2);

        $this->assertEquals(['number' => 5], $result->toArray());
    }

    public function testItWillReduceTheFirstSetOfProvidedActionsIfThePredicateIsFalse()
    {
        $result = ReduceIfOrganizerWithFalseActions::call(0);

        $this->assertEquals(['number' => 4], $result->toArray());
    }

    public function testItWillReduceActionsUntilThePredicateReturnsTrueInTheReduceUntilOrchestratorLogicFunction()
    {
        $result = ReduceUntilOrganizer::call(0);

        $this->assertEquals(['number' => 4], $result->toArray());
    }

    public function testItWillReduceActionsWhenThePredicateReturnsFalseInTheReduceUntilOrchestratorLogicFunction()
    {
        $result = ReduceUntilOrganizer::call(5);

        $this->assertEquals(['number' => 6], $result->toArray());
    }

    public function testItWillExecuteAGivenCallbackActionWhenTheExecuteOrchestratorLogicFunctionIsUsed()
    {
        $result = ExecuteOrganizer::call(0);

        $this->assertEquals(['number' => 2], $result->toArray());
    }

    public function testItWillAddKvsToTheContextWithTheAddToContextOrchestratorLogicFunction()
    {
        $result = AddToContextOrganizer::call(0);

        $this->assertEquals(['number' => 1], $result->toArray());
    }

    public function testItIteratesOverAGivenKeyAndExecutesActionsViaTheIterateFunction()
    {
        $result = IterateOrganizer::call(['numbers' => [1, 2, 3], 'sum' => 0]);

        $this->assertEquals(['numbers' => [1, 2, 3], 'sum' => 6], $result->toArray());
    }

    public function testItWillIterateUsingOrchestrators()
    {
        $result = IterateOrganizerWithOrchestrators::call(['numbers' => [1, 2, 3], 'sum' => 0]);

        $this->assertEquals(['numbers' => [1, 2, 3], 'sum' => 9], $result->toArray());
    }

    public function testItWillRollbackAllTheActionsWhenOrchestratorLogicFunctionsAreUsed()
    {
        $result = RollbackOrchestratorLogicOrganizer::call(0);

        $this->assertEquals(['number' => -1], $result->toArray());
    }

    public function testItStopsRunningActionsWhenTheContextFailsAndOrchestratorLogicIsUsed()
    {
        $result = FailingOrchestratorLogicOrganizer::call(0);

        $this->assertEquals(['number' => 5], $result->toArray());
    }

    public function testItSkipsRemainingActionsWhenMarkedToSkipAndOrchestratorLogicIsUsed()
    {
        $result = SkipRemainingOrchestratorLogicOrganizer::call(0);

        $this->assertEquals(['number' => 5], $result->toArray());
    }
    public function testItRollsBackPrecedingActionsWhenTheRollingBackActionIsLastInTheChain()
    {
        // Two AddsOne then a rollback: +2, the rolling-back action undoes 1 of
        // its own, and the two AddsOne actions are then replayed backwards.
        $result = RollbackAtEndOfChainOrganizer::call(1);

        $this->assertEquals(0, $result->number);
        $this->assertTrue($result->failure());
    }

    public function testItRollsBackPrecedingActionsWhenTheRollingBackActionClassAppearsEarlierInTheChain()
    {
        $result = RepeatedActionRollbackOrganizer::call(0);

        $this->assertEquals(0, $result->number);
        // One from the failing action undoing itself, one from replaying the
        // earlier occurrence of the same class.
        $this->assertCount(2, $result->rolled_back_actions);
    }

    public function testItRunsTheSubclassWhenAnOrganizerIsExtended()
    {
        $result = SpecialisedNameOrganizer::call();

        $this->assertEquals(SpecialisedNameOrganizer::class, $result->currentOrganizer());
        $this->assertEquals(BaseNameOrganizer::class, BaseNameOrganizer::call()->currentOrganizer());
    }
    public function testReduceUntilStopsWhenAnActionFailsTheContext()
    {
        $result = ReduceUntilStopsOnFailureOrganizer::call();

        $this->assertTrue($result->failure());
        // A failed context can never satisfy the predicate, so the loop must
        // give up rather than spin. One evaluation is all it should take.
        $this->assertLessThanOrEqual(2, ReduceUntilStopsOnFailureOrganizer::$predicate_calls);
    }

    public function testReduceUntilStopsWhenAnActionSkipsTheRemainingActions()
    {
        $result = ReduceUntilStopsOnSkipOrganizer::call();

        $this->assertTrue($result->mustSkipAllRemainingActions());
        $this->assertLessThanOrEqual(2, ReduceUntilStopsOnSkipOrganizer::$predicate_calls);
    }

    public function testIterateStopsOnceAnActionFailsTheContext()
    {
        $result = IterateStopsOnFailureOrganizer::call();

        $this->assertTrue($result->failure());
        $this->assertEquals([1], $result->processed);
    }

    public function testIterateStopsOnceAnActionSkipsTheRemainingActions()
    {
        $result = IterateStopsOnSkipOrganizer::call();

        $this->assertTrue($result->mustSkipAllRemainingActions());
        $this->assertEquals([1], $result->processed);
    }

    public function testIterateRollsBackTheItemsItAlreadyProcessed()
    {
        $result = IterateRollbackOrganizer::call();

        $this->assertTrue($result->failure());
        $this->assertEquals([], $result->charged);
        $this->assertEquals([2, 1], $result->refunded);
    }

    public function testIterateLeavesAnExistingKeyMatchingTheSingularisedNameAlone()
    {
        $result = IterateWithExistingSingularKeyOrganizer::call();

        $this->assertEquals([1, 2], $result->processed);
        $this->assertEquals('something the caller put there', $result->item);
    }

    public function testIterateRunsTheOrganizersHooksAroundEachAction()
    {
        $result = IterateWithHooksOrganizer::call();

        $this->assertEquals([1, 2], $result->processed);
        $this->assertEquals(['before', 'after', 'before', 'after'], $result->hooks_called);
    }
    public function testIterateRaisesWhenTheKeyDoesNotHoldSomethingIterable()
    {
        $this->expectException(KeyIsNotIterableException::class);
        $this->expectExceptionMessage('items');

        IterateOverANonIterableOrganizer::call();
    }

    public function testIterateTreatsAMissingKeyAsNothingToDo()
    {
        $result = IterateOverAMissingKeyOrganizer::call();

        $this->assertEquals([], $result->processed);
        $this->assertFalse($result->failure());
    }

    public function testTheClosingHooksStillRunWhenAnActionThrows()
    {
        $organizer = ThrowingActionWithHooksOrganizer::with(['hooks_called' => []]);

        try {
            $organizer->reduce(\LightService\Fixtures\Actions\ThrowsUnexpectedlyAction::class);
            $this->fail('Expected the action to throw');
        } catch (RuntimeException $e) {
            // The around hook has to close, or a timing or logging hook would
            // silently never finish.
            $this->assertEquals(
                ['around', 'before', 'after', 'around'],
                $organizer->context->hooks_called
            );
        }
    }

    public function testKeyAliasesAreUnwoundWhenAnActionThrows()
    {
        $organizer = ThrowingActionWithAliasesOrganizer::with(['number' => 1]);

        try {
            $organizer->reduce(\LightService\Fixtures\Actions\ThrowsWithAnAliasedKeyAction::class);
            $this->fail('Expected the action to throw');
        } catch (RuntimeException $e) {
            $this->assertEquals(['number'], $organizer->context->keys());
        }
    }

    public function testReducingAgainAfterARollbackDoesNothing()
    {
        $result = ReduceTwiceAfterRollbackOrganizer::call();

        // The rolling-back action already undid itself, taking 1 to 0. The
        // second reduce must not run its action or replay anything.
        $this->assertEquals(0, $result->number);
        $this->assertTrue($result->failure());
    }
}
