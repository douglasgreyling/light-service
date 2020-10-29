<?php

use PHPUnit\Framework\TestCase;

use LightService\Fixtures\Organizers\AllHooksOrganizer;
use LightService\Fixtures\Organizers\AroundHooksOrganizer;
use LightService\Fixtures\Organizers\BeforeAfterHooksOrganizer;

final class ActionHookWrapperTest extends TestCase {
    public function test_it_wraps_an_organizers_before_and_after_hooks_around_an_action() {
        $context = BeforeAfterHooksOrganizer::call()->to_array();

        $this->assertEquals(['a' => ['before', 'action', 'after']], $context);
    }

    public function test_it_wraps_an_organizers_around_hooks_around_an_action() {
        $context = AroundHooksOrganizer::call()->to_array();

        $this->assertEquals(['a' => ['around', 'action', 'around']], $context);
    }

    public function test_it_wraps_all_of_an_organizers_hooks_around_an_action() {
        $context = AllHooksOrganizer::call()->to_array();

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
