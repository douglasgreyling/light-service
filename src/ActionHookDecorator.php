<?php

namespace LightService;

class ActionHookDecorator
{
    public static function decorate($before_callback, $action_callback, $after_callback)
    {
        return function () use ($before_callback, $action_callback, $after_callback) {
            $before_callback();

            // finally, so that an action throwing does not leave the closing
            // half of an around hook unrun.
            try {
                return $action_callback();
            } finally {
                $after_callback();
            }
        };
    }
}
