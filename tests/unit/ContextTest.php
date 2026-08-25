<?php

namespace LightService\Tests\Unit;

use PHPUnit\Framework\TestCase;
use LightService\Context;
use LightService\Exception\NextActionException;
use LightService\Exception\KeyAliasException;

final class ContextTest extends TestCase
{
    public function testItIsInstantiatedWithAnEmptyContextWhenGivenNoState()
    {
        $context = new Context();

        $this->assertEmpty($context->toArray());
    }

    public function testItIsInstantiatedWithAContextBasedOnItsGivenState()
    {
        $state   = ['a' => 1, 'b' => 2];
        $context = new Context($state);

        $this->assertEquals(['a' => 1, 'b' => 2], $context->toArray());
    }

    public function testItIsInstantiatedWithAContextFailureFlagOfFalse()
    {
        $context = new Context();

        $this->assertFalse($context->failure());
    }

    public function testItIsInstantiatedWithAContextSuccessFlagOfTrue()
    {
        $context = new Context();

        $this->assertTrue($context->success());
    }

    public function testItCanConvertItselfToAnArray()
    {
        $state   = ['a' => 1, 'b' => 2];
        $context = new Context($state);

        $this->assertEquals(['a' => 1, 'b' => 2], $context->toArray());
    }

    public function testCanConvertItselfToAnArrayIncludingItsMetadata()
    {
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
                    'rollback'          => false,
                    'executed_actions'  => []
                ]
            ],
            $context->toArray(true)
        );
    }

    public function testItFetchesValuesFromTheContextLikeAnObject()
    {
        $context = new Context(['a' => 1]);

        $this->assertEquals(1, $context->a);
    }

    public function testItCanSetValuesLikePropertiesOfAnObject()
    {
        $context = new Context();

        $context->a = [];
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
            $context->toArray()
        );
    }

    public function testItReturnsAValueOfNullWhenThePropertyBeingFetchedDoesNotExistInTheContext()
    {
        $context = new Context(['a' => 1]);

        $this->assertNull($context->b);
    }

    public function testItCanMergeASetOfKeyValuePairsIntoTheContextUsingTheMergeFunction()
    {
        $context = new Context(['a' => 1]);

        $context->merge(['b' => 2, 'c' => 3]);

        $this->assertEquals(['a' => 1, 'b' => 2, 'c' => 3], $context->toArray());
    }

    public function testItCanRetrieveKeysInsideTheContextWithTheKeysFunction()
    {
        $state   = ['a' => 1, 'b' => 2];
        $context = new Context($state);

        $this->assertEquals(['a', 'b'], $context->keys());
    }

    public function testItCanRetrieveTheValuesInsideTheContextWithTheValuesFunction()
    {
        $state   = ['a' => 1, 'b' => 2];
        $context = new Context($state);

        $this->assertEquals([1, 2], $context->values());
    }

    public function testItCanRetrieveMultipleKeyValuePairsUsingTheFetchFunction()
    {
        $state   = ['a' => 1, 'b' => 2, 'c' => 3];
        $context = new Context($state);

        $this->assertEquals(['a' => 1, 'c' => 3], $context->fetch(['a', 'c']));
    }

    public function testItMarksTheFailureFlagAsTrueAndTheSuccessFlagAsFalseWhenTheContextIsExplicitlyFailed()
    {
        $state   = ['a' => 1, 'b' => 2, 'c' => 3];
        $context = new Context($state);

        $context->fail();

        $this->assertTrue($context->failure());
    }

    public function testItCanAddAnAdditionalFailureMessageWhenTheContextIsExplicitlyFailed()
    {
        $state   = ['a' => 1, 'b' => 2, 'c' => 3];
        $context = new Context($state);

        $context->fail('foo');

        $this->assertEquals('foo', $context->message());
    }

    public function testItCanMarkTheFailureFlagAsTrueAndThrowANextActionExceptionWhenTheFailAndReturnFunctionIsCalled()
    {
        $context                  = new Context();
        $correct_exception_thrown = false;

        try {
            $context->failAndReturn('foo');
        } catch (NextActionException $e) {
            $correct_exception_thrown = true;
        }

        $this->assertTrue($correct_exception_thrown);
        $this->assertTrue($context->failure());
    }

    public function testItCanMarkTheSkipRemainingFlagWhenTheSkipRemainingFunctionIsCalled()
    {
        $context = new Context();

        $context->skipRemaining();

        $this->assertTrue($context->mustSkipAllRemainingActions());
    }

    public function testItCanSetAndGetTheCurrentAction()
    {
        $context = new Context();

        $context->setCurrentAction('SomeAction');

        $this->assertEquals('SomeAction', $context->currentAction());
    }

    public function testItCanSetAndGetTheCurrentOrganizer()
    {
        $context = new Context();

        $context->setCurrentOrganizer('SomeOrganizer');

        $this->assertEquals('SomeOrganizer', $context->currentOrganizer());
    }

    public function testItCanUseASetOfKeyAliasesToChangeTheContext()
    {
        $context = new Context(['a' => 'value']);
        $context->useAliases(['a' => 'an_alias_for_a']);

        $this->assertEquals('value', $context->an_alias_for_a);
        $this->assertEquals(['an_alias_for_a' => 'value'], $context->toArray());
    }

    public function testItThrowsAnExceptionWhenItAttemptsToUseAndSetAKeyAliasWhichAlreadyExistsInsideTheContext()
    {
        $this->expectException(KeyAliasException::class);

        $context = new Context(['a' => 'value', 'an_alias_for_a' => 'some other value']);
        $context->useAliases(['a' => 'an_alias_for_a']);
    }

    public function testTheKeyAliasClashExceptionNamesTheClashingKey()
    {
        $context = new Context(['a' => 'value', 'an_alias_for_a' => 'some other value']);

        try {
            $context->useAliases(['a' => 'an_alias_for_a']);
            $this->fail('Expected a KeyAliasException');
        } catch (KeyAliasException $e) {
            // It used to report the key's position in keys() rather than its name.
            $this->assertStringContainsString('an_alias_for_a', $e->getMessage());
        }
    }

    public function testItCanFailAContextWithAnErrorCode()
    {
        $context = new Context();

        $context->fail('Something went wrong', 4001);

        $this->assertEquals('Something went wrong', $context->message());
        $this->assertEquals($context->errorCode(), 4001);
    }

    public function testItCanFailAContextAndSkipToTheNextActionWithAnErrorCode()
    {
        $context                  = new Context();
        $correct_exception_thrown = false;

        try {
            $context->failAndReturn('Something went wrong', 4001);
        } catch (NextActionException $e) {
            $correct_exception_thrown = true;
        }

        $this->assertTrue($correct_exception_thrown);
        $this->assertTrue($context->failure());
        $this->assertEquals('Something went wrong', $context->message());
        $this->assertEquals(4001, $context->errorCode());
    }

    public function testItReturnsNullForAnUndefinedKeyWithoutAddingItToTheContext()
    {
        $context = new Context();

        $result = $context->foo;

        $this->assertNull($result);
        $this->assertEquals([], $context->toArray());
        $this->assertEquals([], $context->keys());
    }

    public function testItDoesNotAddAKeyToTheContextWhenAnUndefinedKeyIsOnlyChecked()
    {
        $context = new Context(['a' => 1]);

        if ($context->some_flag) {
            // reading it is enough to have created it before
        }

        $this->assertEquals(['a' => 1], $context->toArray());
    }
}
