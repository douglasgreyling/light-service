# Changelog

All notable changes to this project are documented in this file.

## [1.0.0] - 2026-08-25

The first stable release. Every public method has been renamed, so this is a
breaking change for everyone on 0.x.

### Changed

- **Adopted PSR-12 across the library.** Method names are now `camelCase`
  rather than `snake_case`, and the source is formatted to the standard.
  Enforced in CI by PHP_CodeSniffer.
- README code samples reformatted to PSR-12, so the documentation matches
  the standard the library is now held to.
- **Actions inside `iterate()` now run through the orchestrator**, which is what
  makes the flags and rollback apply. Two visible consequences: the organizer's
  `beforeEach` / `afterEach` / `aroundEach` hooks now fire for them, where before
  they did not, and they are recorded in `executed_actions`.
- `src/exceptions` renamed to `src/Exception` so the directory matches its
  PSR-4 namespace, which lets the `LightService\` root mapping cover it and
  removes a second autoload rule. Namespaces and class names are unchanged, so
  this is invisible to consumers.
- `promisedKeys()` is now public, matching `expectedKeys()`. The two were an
  accidental pair, one public and one private.
- `ActionHookWrapper::wrap()` now takes a constructed action rather than an
  action class name, since the orchestrator already has the instance. This is
  internal plumbing rather than something you would normally call.
- Test classes moved into the `LightService\Tests\Unit` namespace, and
  `tests/unit/exceptions` was renamed to `tests/unit/Exceptions` so the
  directory matches its PSR-4 namespace.

### Added

- PHPStan (level 5) static analysis, enforced in CI.
- `composer lint`, `composer lint:fix`, `composer analyse` and `composer check`.
- `KeyIsNotIterableException`, raised by `iterate()` when the key it was given
  holds something that cannot be looped over.
- `Context::has()`, a membership test that does not copy the context the way
  `fetch()` and `keys()` do. A key explicitly set to `null` counts as present.

### Removed

- `Context::arrayMerge()`. It was a second name for `merge()` and did nothing
  else; call `merge()` instead.

### Performance

- **Each action is built once per run rather than twice.** The orchestrator used
  to construct an action purely to read its expected keys, throw it away, and
  construct it again to execute it. Each throwaway also allocated a `Context`
  and a `ContextMetadata` that were immediately discarded.
- The organizer's key aliases are resolved once per run instead of once per
  action, and are not consulted at all when the organizer declares none.
- `reduceUntil()` reuses a single orchestrator across passes instead of building
  one per pass.
- `iterate()` builds its Doctrine inflector once per process rather than on every
  call, and singularises the key when the chain is declared rather than on every
  run.
- Key validation asks the context about one key at a time instead of copying the
  whole context twice per action.

Measured on PHP 8.5, same workload before and after: a 5-action chain about 40%
faster, a 2000-pass `reduceUntil` about 38% faster, and 400 `iterate()`
declarations about 87% faster (47ms down to 6ms).

### Fixed

- **Rollback now runs when the rolling-back action is last in the chain.** The
  rollback flag was only inspected at the top of the next loop pass, so if
  nothing followed the failing action the loop simply ended and no preceding
  action was rolled back. The failing action still undid itself, which made the
  bug look like partial success.
- **Rollback no longer aborts when the failing action's class appears earlier in
  the chain.** The failure point was located with `array_search()` over a list
  of class names, which returns the *first* match, so reusing an action class
  truncated or emptied the set of actions to roll back. The position is now
  recorded as the chain runs.
- **Reading an undefined context key no longer creates it.** `Context::__get()`
  returned `$this->$key` by reference, which brought the key into existence as
  a side effect of reading it. Beyond polluting `toArray()`, this let an action
  satisfy its own `$promises` by merely reading the key it was supposed to set.
- **`reduceUntil()` no longer loops forever when an action fails or skips.** The
  orchestrator short-circuits every pass once the context has failed or been told
  to skip, so the context could never change again and the predicate could never
  be satisfied. It now stops, leaving the failure on the context for the caller
  to inspect. This was a hang, not a wrong answer.
- **`iterate()` now honours failure, skip-remaining and rollback.** It called
  each action directly rather than going through an orchestrator, so none of the
  flags were consulted: an action failing on the second of three items left the
  remaining work running, and any item already processed could not be rolled
  back. Charging a list of items and having the third card declined used to leave
  the first two charges in place.
- **`iterate()` no longer destroys a context key matching the singular of the key
  it iterates.** Iterating `items` overwrote and then deleted any existing `item`
  key. Whatever was there is now restored afterwards.
- **`iterate()` says so when the key does not hold something it can loop over.**
  A missing key is still treated as nothing to do, but any other non-iterable
  value produced a PHP warning and then silently behaved as though the list were
  empty. It now raises `KeyIsNotIterableException` naming the key.
- **The closing half of a hook runs even when an action throws.** `beforeEach`
  and the opening `aroundEach` ran, then an unexpected exception skipped
  `afterEach` and the closing `aroundEach` entirely, so a timing or logging hook
  could open and never close. They now run in a `finally`.
- **Key aliases are unwound even when an action throws.** The context was left
  under its aliased names, so a caller catching the exception found keys it did
  not recognise. Also now a `finally`.
- **The key-alias clash exception names the clashing key.** It reported the key's
  position in `keys()` instead — a clash on `the_alias` read `(1)`.
- **An action class whose name matches a PHP function is no longer dispatched as
  orchestrator logic.** The orchestrator told actions and orchestrator logic
  apart with `is_callable($action)`, which is true for a class-string that
  shares a name with a global function, so an un-namespaced action class called
  `Touch` was invoked as `touch($organizer)`. `iterate()` had the same test
  inside its loop. Namespaced action classes were never affected.
- **Actions and organizers can now be subclassed.** `execute()`, `rollback()`
  and `with()` used `new self`, which in a trait binds to the class that
  composed it, so a subclass silently ran its parent's code.
- Exception constructors now pass `$code` and `$previous` through to
  `parent::__construct()`. Both were previously accepted and silently
  discarded, so `getCode()` always returned `0` and `getPrevious()` always
  returned `null`.
- `NotImplementedException::__construct()` no longer declares parameters.
  Its message has always been fixed and the parameters were ignored.
- Corrected broken code samples in the README: a method declared without
  parentheses, an unbalanced parenthesis in an `if`, a missing semicolon,
  and `$expects` / `$promises` written without their `$`.

### Migration

Context keys, the `$expects` and `$promises` properties, and the metadata keys
returned by `toArray(true)` are unchanged. Only method names moved:

| Before | After |
| --- | --- |
| `add_to_context()` | `addToContext()` |
| `after_each()` | `afterEach()` |
| `around_each()` | `aroundEach()` |
| `array_merge()` | removed — use `merge()` |
| `before_each()` | `beforeEach()` |
| `current_action()` | `currentAction()` |
| `current_organizer()` | `currentOrganizer()` |
| `error_code()` | `errorCode()` |
| `expected_keys()` | `expectedKeys()` |
| `fail_and_return()` | `failAndReturn()` |
| `fail_with_rollback()` | `failWithRollback()` |
| `key_aliases()` | `keyAliases()` |
| `must_skip_all_remaining_actions()` | `mustSkipAllRemainingActions()` |
| `next_context()` | `nextContext()` |
| `reduce_if()` | `reduceIf()` |
| `reduce_until()` | `reduceUntil()` |
| `rolled_back()` | `rolledBack()` |
| `set_current_action()` | `setCurrentAction()` |
| `set_current_organizer()` | `setCurrentOrganizer()` |
| `skip_remaining()` | `skipRemaining()` |
| `to_array()` | `toArray()` |
| `use_aliases()` | `useAliases()` |

Four behavioural changes also need attention:

1. **Declare `executed()` and `rolledBack()` as `protected`, not `private`.**
   Private methods are not polymorphic, so a `private` hook cannot be
   overridden by a subclass. `private` still works if you never subclass the
   action, and the README now shows `protected` throughout.
2. **Initialise a context key before appending to it.** `$context->items[] = 1`
   used to work on a key that did not exist yet, because reading created it.
   Set `$context->items = []` first; appending to an unset key now raises
   PHP's "indirect modification" notice instead of silently working.
3. **Action and organizer constructors are `final`.** This is what makes
   `new static()` safe. Composing the trait and defining your own
   `__construct` still works; extending a class that composed it and
   overriding the constructor does not.
4. **`executed_actions` is cleared once a rollback has been replayed**, so a
   nested orchestrator's rollback is not repeated by an enclosing one.

The two you are most likely to have written yourself are `rolled_back()` on
your actions and `before_each()` / `after_each()` / `around_each()` on your
organizers. Renaming those is required — a hook left under its old name is
simply never called, and nothing will warn you.

## [0.13.1] and earlier

See the commit history.
