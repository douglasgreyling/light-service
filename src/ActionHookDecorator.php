<?php

class ActionHookDecorator {
    public static function new($beforeCallback, $function, $afterCallback) {
        return function() use ($beforeCallback, $function, $afterCallback) {
            $beforeCallback();
            $result = $function();
            $afterCallback();

            return $result;
        };
    }
}
