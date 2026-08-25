<?php

namespace LightService;

use LightService\Orchestrator;
use LightService\Exception\KeyIsNotIterableException;
use Doctrine\Inflector\InflectorFactory;
use Doctrine\Inflector\Language;

trait OrchestratorLogic
{
    public static function reduceIf($predicate_fn, $true_actions, $false_actions = [])
    {
        return function ($organizer) use ($predicate_fn, $true_actions, $false_actions) {
            $org = new Orchestrator($organizer);

            return $org->run($predicate_fn($organizer->context) ? $true_actions : $false_actions);
        };
    }

    public static function reduceUntil($predicate_fn, $actions)
    {
        return function ($organizer) use ($predicate_fn, $actions) {
            $context = $organizer->context;
            $org     = new Orchestrator($organizer);

            while (!$predicate_fn($context)) {
                $context = $org->run($actions);

                // Nothing can change the context once it has failed or been
                // told to skip, so the predicate would never be satisfied.
                if ($context->failure() || $context->mustSkipAllRemainingActions()) {
                    break;
                }
            }

            return $context;
        };
    }

    public static function iterate($key, $actions)
    {
        $singularized_key = self::inflector()->singularize($key);

        return function ($organizer) use ($key, $actions, $singularized_key) {
            $context  = $organizer->context;
            $iterable = is_null($context->$key) ? [] : $context->$key;
            $org      = new Orchestrator($organizer);

            // A missing key is treated as nothing to do, but a key holding
            // something that cannot be looped over is a mistake worth saying
            // out loud rather than quietly skipping.
            if (!is_iterable($iterable)) {
                throw new KeyIsNotIterableException($key);
            }

            // The loop borrows the singularised key. Anything already stored
            // under that name is put back afterwards rather than destroyed.
            $had_singular_key      = $context->has($singularized_key);
            $previous_singular_key = $had_singular_key ? $context->$singularized_key : null;

            foreach ($iterable as $item) {
                if ($context->failure() || $context->mustSkipAllRemainingActions()) {
                    break;
                }

                $context->merge([$singularized_key => $item]);

                // Running through the orchestrator rather than calling the
                // actions directly is what makes failure, skip-remaining,
                // rollback and the organizer's hooks apply inside iterate.
                $context = $org->run($actions);
            }

            if ($had_singular_key) {
                $context->$singularized_key = $previous_singular_key;
            } else {
                unset($context->$singularized_key);
            }

            return $context;
        };
    }

    public static function execute($callback)
    {
        return function ($organizer) use ($callback) {
            $context = $organizer->context;

            $callback($context);

            return $context;
        };
    }

    // Building an inflector walks a sizeable ruleset. The result is stateless
    // and reusable, so it is built once rather than on every iterate() call.
    private static function inflector()
    {
        static $inflector = null;

        if (is_null($inflector)) {
            $inflector = InflectorFactory::createForLanguage(Language::ENGLISH)->build();
        }

        return $inflector;
    }

    public static function addToContext($kvs)
    {
        return function ($organizer) use ($kvs) {
            $context = $organizer->context;

            $context->merge($kvs);

            return $context;
        };
    }
}
