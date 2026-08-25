<?php

namespace LightService;

use LightService\ActionHookDecorator;

class ActionHookWrapper
{
    public static function wrap($action, $organizer)
    {
        $hooked_action = self::wrapBeforeAfterHooks($action, $organizer);
        $hooked_action = self::wrapAroundHooks($hooked_action, $organizer);

        return $hooked_action;
    }

    private static function wrapBeforeAfterHooks($action, $organizer)
    {
        $context = $organizer->context;

        return ActionHookDecorator::decorate(
            function () use ($organizer, $context) {
                $organizer->beforeEach($context);
            },
            function () use ($action) {
                return $action->run();
            },
            function () use ($organizer, $context) {
                $organizer->afterEach($context);
            }
        );
    }

    private static function wrapAroundHooks($hooked_action, $organizer)
    {
        $context = $organizer->context;

        return ActionHookDecorator::decorate(
            function () use ($organizer, $context) {
                $organizer->aroundEach($context);
            },
            $hooked_action,
            function () use ($organizer, $context) {
                $organizer->aroundEach($context);
            }
        );
    }
}
