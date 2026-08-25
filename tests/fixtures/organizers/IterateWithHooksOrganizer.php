<?php

namespace LightService\Fixtures\Organizers;

use LightService\Fixtures\Actions\RecordsItemAction;

class IterateWithHooksOrganizer
{
    use \LightService\Organizer;

    public function beforeEach($context)
    {
        $context->hooks_called[] = 'before';
    }

    public function afterEach($context)
    {
        $context->hooks_called[] = 'after';
    }

    public static function call()
    {
        return self::with(['items' => [1, 2], 'processed' => [], 'hooks_called' => []])->reduce(
            self::iterate('items', [RecordsItemAction::class])
        );
    }
}
