<?php

namespace LightService\Tests\Unit;

use PHPUnit\Framework\TestCase;
use LightService\Fixtures\Organizers\AllHooksOrganizer;
use LightService\Fixtures\Organizers\AroundHooksOrganizer;
use LightService\Fixtures\Organizers\BeforeAfterHooksOrganizer;

final class ActionHookWrapperTest extends TestCase
{
    public function testItWrapsAnOrganizersBeforeAndAfterHooksAroundAnAction()
    {
        $context = BeforeAfterHooksOrganizer::call()->toArray();

        $this->assertEquals(['a' => ['before', 'action', 'after']], $context);
    }

    public function testItWrapsAnOrganizersAroundHooksAroundAnAction()
    {
        $context = AroundHooksOrganizer::call()->toArray();

        $this->assertEquals(['a' => ['around', 'action', 'around']], $context);
    }

    public function testItWrapsAllOfAnOrganizersHooksAroundAnAction()
    {
        $context = AllHooksOrganizer::call()->toArray();

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
            $context
        );
    }
}
