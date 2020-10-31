<?php

namespace LightService;

use LightService\Orchestrator;

use \Doctrine\Common\Inflector\Inflector;

trait OrchestratorLogic {
    public static function reduce_if($predicate_fn, $actions) {
        return function($organizer) use($predicate_fn, $actions) {
            $context = $organizer->context;

            if ($predicate_fn($context)) {
                $org     = new Orchestrator($organizer);
                $context = $org->run($actions);
            }

            return $context;
        };
    }

    public static function reduce_until($predicate_fn, $actions) {
        return function($organizer) use($predicate_fn, $actions) {
            $context = $organizer->context;

            while(!$predicate_fn($context)) {
                $org     = new Orchestrator($organizer);
                $context = $org->run($actions);
            }

            return $context;
        };
    }

    public static function iterate($key, $actions) {
        return function($organizer) use($key, $actions) {
            $context          = $organizer->context;
            $iterable         = is_null($context->$key) ? [] : $context->$key;
            $singularized_key = Inflector::singularize($key);

            foreach($iterable as $i) {
                foreach($actions as $action) {
                    $action_context = $context->merge([$singularized_key => $i]);
                    $context        = $action::execute($action_context);
                }
            }

            unset($context->$singularized_key);

            return $context;
        };
    }

    public static function execute($callback) {
        return function($organizer) use($callback) {
            $context = $organizer->context;

            $callback($context);

            return $context;
        };
    }

    public static function add_to_context($kvs) {
        return function($organizer) use($kvs) {
            $context = $organizer->context;

            $context->merge($kvs);

            return $context;
        };
    }
}
