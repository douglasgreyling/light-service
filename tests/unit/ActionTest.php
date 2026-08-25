<?php

namespace LightService\Tests\Unit;

use PHPUnit\Framework\TestCase;
use LightService\Context;
use LightService\Exception\ExpectedKeysNotInContextException;
use LightService\Exception\PromisedKeysNotInContextException;
use LightService\Exception\NotImplementedException;
use LightService\Fixtures\Actions\FailAndReturnAction;
use LightService\Fixtures\Actions\FailingAction;
use LightService\Fixtures\Actions\MissingAllPromisesAction;
use LightService\Fixtures\Actions\MissingSomePromisesAction;
use LightService\Fixtures\Actions\NoExecutedFunctionAction;
use LightService\Fixtures\Actions\NextActionAction;
use LightService\Fixtures\Actions\NoMissingExpectsAction;
use LightService\Fixtures\Actions\NoMissingPromisesAction;
use LightService\Fixtures\Actions\RollbackAction;
use LightService\Fixtures\Actions\SingleExpectsAndPromisesAction;
use LightService\Fixtures\Actions\SuccessfulAction;
use LightService\Fixtures\Actions\ReadsAPromisedKeyAction;
use LightService\Fixtures\Actions\BaseNameAction;
use LightService\Fixtures\Actions\SpecialisedNameAction;
use LightService\Fixtures\Actions\DuplicateExpectsAction;
use LightService\Fixtures\Actions\DuplicatePromisesAction;

final class ActionTest extends TestCase
{
    public function testItCanBeInstantiatedWithAnAssociatedArrayAsContext()
    {
        $action = new SuccessfulAction(['a' => 1, 'b' => 2]);

        $this->assertEquals(['a' => 1, 'b' => 2], $action->context()->toArray());
    }

    public function testItCanBeInstantiatedWithAGivenContextAsActionContext()
    {
        $action_context = new Context(['a' => 1, 'b' => 2]);
        $action         = new SuccessfulAction($action_context);

        $this->assertEquals(['a' => 1, 'b' => 2], $action->context()->toArray());
    }

    public function testItInstantiatesTheActionContextWithTheClassOfTheAction()
    {
        $action_context = new Context();
        $action         = new SuccessfulAction($action_context);

        $this->assertEquals(SuccessfulAction::class, $action->context()->currentAction());
    }

    public function testItReturnsNoContextValidationErrorsWithEmptyExpectedKeys()
    {
        $result = NoMissingExpectsAction::execute(['a' => 1, 'b' => 2]);

        $this->assertTrue($result->success());
    }

    public function testItCanAcceptASingleStringForTheExpectedKeyInTheContext()
    {
        $result = SingleExpectsAndPromisesAction::execute(['a' => 1]);

        $this->assertEquals(2, $result->b);
    }

    public function testItThrowsAnExceptionWhenAllOfTheExpectedKeysAreNotInTheContext()
    {
        $this->expectException(ExpectedKeysNotInContextException::class);

        SuccessfulAction::execute([]);
    }

    public function testItThrowsAnExceptionSomeOfTheExpectedKeysAreNotInTheContext()
    {
        $this->expectException(ExpectedKeysNotInContextException::class);

        SuccessfulAction::execute(['a' => 1]);
    }

    public function testItReturnsNoContextValidationExceptionsWithEmptyPromisedKeys()
    {
        $result = NoMissingPromisesAction::execute(['a' => 1, 'b' => 2]);

        $this->assertTrue($result->success());
    }

    public function testItThrowsAnExceptionWhenAllOfTheThePromisedKeysAreNotInTheContext()
    {
        $this->expectException(PromisedKeysNotInContextException::class);

        MissingAllPromisesAction::execute(['a' => 1, 'b' => 2]);
    }

    public function testItThrowsAnExceptionSomeOfTheThePromisedKeysAreNotInTheContext()
    {
        $this->expectException(PromisedKeysNotInContextException::class);

        MissingSomePromisesAction::execute(['a' => 1, 'b' => 2]);
    }

    public function testItThrowsAnExceptionWhenTheExecutedFunctionIsNotImplemented()
    {
        $this->expectException(NotImplementedException::class);

        NoExecutedFunctionAction::execute(['a' => 1, 'b' => 2]);
    }

    public function testItCanSkipToTheNextActionUsingTheNextContextFunction()
    {
        $result = NextActionAction::execute(['a' => 1, 'b' => 2]);

        $this->assertFalse($result->failure());
        $this->assertTrue($result->success());
        $this->assertArrayNotHasKey('d', $result->keys());
    }

    public function testItCanMarkTheCurrentContextAsFailedWithAMessageUsingTheFailFunction()
    {
        $result = FailingAction::execute(['a' => 1, 'b' => 2]);

        $this->assertTrue($result->failure());
        $this->assertFalse($result->success());
    }

    public function testItCanMarkTheCurrentContextAsFailedAndMoveOntoTheNextContextUsingTheFailAndReturnFunction()
    {
        $result = FailAndReturnAction::execute();

        $this->assertTrue($result->failure());
        $this->assertArrayNotHasKey('one', $result->keys());
    }

    public function testItCanGetTheCurrentContext()
    {
        $action = new SuccessfulAction(['a' => 1]);

        $this->assertEquals(['a' => 1], $action->context()->toArray());
    }

    public function testItCanGetTheExpectedKeys()
    {
        $action = new SuccessfulAction(['a' => 1]);

        $this->assertEquals(['a', 'b'], $action->expectedKeys());
    }

    public function testItCanGetThePromisedKeys()
    {
        $action = new SuccessfulAction(['a' => 1]);

        $this->assertEquals(['c'], $action->promisedKeys());
    }

    public function testItCanFailTheContextAndRollback()
    {
        $result = RollbackAction::execute(['number' => 1]);

        $this->assertEquals(['number' => 0], $result->toArray());
        $this->assertEquals('I want to roll back!', $result->message());
    }

    public function testItCanFailTheContextAndRollbackStaticallyWithAGivenContext()
    {
        $result = RollbackAction::rollback(['number' => 1]);

        $this->assertEquals(['number' => 0], $result->toArray());
    }

    public function testItDoesNothingWhenNoRollbackFunctionIsDefined()
    {
        $result = SuccessfulAction::rollback(['a' => 1]);

        $this->assertEquals(['a' => 1], $result->toArray());
    }

    public function testItIgnoresDuplicateExpectsKeys()
    {
        $result = DuplicateExpectsAction::execute(['number' => 0]);

        $this->assertEquals(['number' => 1], $result->toArray());
    }

    public function testItIgnoresDuplicatePromisesKeys()
    {
        $result = DuplicatePromisesAction::execute(['number' => 0]);

        $this->assertEquals(['number' => 1], $result->toArray());
    }

    public function testItDoesNotLetReadingAPromisedKeySatisfyThePromise()
    {
        $this->expectException(PromisedKeysNotInContextException::class);

        ReadsAPromisedKeyAction::execute(['a' => 1]);
    }

    public function testItRunsTheSubclassWhenAnActionIsExtended()
    {
        $this->assertEquals('SpecialisedNameAction', SpecialisedNameAction::execute()->who);
        $this->assertEquals('BaseNameAction', BaseNameAction::execute()->who);
    }

    public function testItRecordsTheSubclassAsTheCurrentAction()
    {
        $this->assertEquals(
            SpecialisedNameAction::class,
            SpecialisedNameAction::execute()->currentAction()
        );
    }
}
