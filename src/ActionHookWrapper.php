<?php

namespace LightServicePHP;

use LightServicePHP\ActionHookDecorator;

class ActionHookWrapper {
    public static function wrap($action, $organizer) {
        $hooked_action = self::wrap_before_after_hooks($action, $organizer);
        $hooked_action = self::wrap_around_hooks($hooked_action, $organizer);

        return $hooked_action;
    }

    private static function wrap_before_after_hooks($action, $organizer) {
        $context = $organizer->context;

        return ActionHookDecorator::decorate(
            function() use ($organizer, $context) { $organizer->before_each($context); },
            function() use ($action, $context) { return $action::execute($context); },
            function() use ($organizer, $context) { $organizer->after_each($context); }
        );
    }

    private static function wrap_around_hooks($hooked_action, $organizer) {
        $context = $organizer->context;

        return ActionHookDecorator::decorate(
            function() use ($organizer, $context) { $organizer->around_each($context); },
            $hooked_action,
            function() use ($organizer, $context) { $organizer->around_each($context); }
        );
    }
}