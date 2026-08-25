<?php

namespace LightService;

use LightService\Context;
use LightService\Exception\ExpectedKeysNotInContextException;
use LightService\Exception\PromisedKeysNotInContextException;
use LightService\Exception\NextActionException;
use LightService\Exception\RollbackException;
use LightService\Exception\NotImplementedException;

trait Action
{
    private $context;

    // Final so that new static() in execute() and rollback() is safe: a
    // subclass cannot change the signature these call it with.
    final public function __construct($context = [])
    {
        $this->context = is_a($context, Context::class) ? $context : new Context($context);
        $this->context->setCurrentAction(static::class);
    }

    public function run()
    {
        try {
            $this->validateKeys($this->expectedKeys(), ExpectedKeysNotInContextException::class);
            $this->executed($this->context);
            $this->validateKeys($this->promisedKeys(), PromisedKeysNotInContextException::class);
        } catch (NextActionException $e) {
            // no op
        } catch (RollbackException $e) {
            $this->rolledBack($this->context);
        }

        return $this->context;
    }

    public static function execute($context = [])
    {
        return (new static($context))->run();
    }

    public static function rollback($context = [])
    {
        $action = new static($context);
        $action->rolledBack($action->context());

        return $action->context();
    }

    public function context()
    {
        return $this->context;
    }

    public function expectedKeys()
    {
        return $this->declaredKeys('expects');
    }

    public function promisedKeys()
    {
        return $this->declaredKeys('promises');
    }

    // $expects and $promises may each be a single key or a list of them.
    private function declaredKeys($property)
    {
        if (!isset($this->$property)) {
            return [];
        }

        $keys = is_array($this->$property) ? $this->$property : [$this->$property];

        return array_values(array_unique($keys));
    }

    protected function executed($context)
    {
        throw new NotImplementedException();
    }

    protected function rolledBack($context)
    {
        // no op
    }

    private function validateKeys($keys, $exception_class)
    {
        $missing_keys = [];

        foreach ($keys as $key) {
            if (!$this->context->has($key)) {
                $missing_keys[] = $key;
            }
        }

        if ($missing_keys) {
            throw new $exception_class(join(', ', $missing_keys));
        }
    }

    protected function nextContext()
    {
        throw new NextActionException();
    }
}
