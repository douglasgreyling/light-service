<?php

class ActionHookDecorator {
    public static function decorate($before_callback, $action_callback, $after_callback) {
        return function() use ($before_callback, $action_callback, $after_callback) {
            $before_callback();
            $result = $action_callback();
            $after_callback();

            return $result;
        };
    }
}
