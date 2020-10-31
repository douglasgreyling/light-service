<?php

namespace LightService;

class ActionHookDecorator {
    public static function decorate($before_callback, $action_callback, $after_callback) {
        return function() use ($before_callback, $action_callback, $after_callback) {
            call_user_func($before_callback);
            $result = call_user_func($action_callback);
            call_user_func($after_callback);

            return $result;
        };
    }
}
