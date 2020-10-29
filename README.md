# LightService:

[![build Actions Status](https://github.com/douglasgreyling/light-service/workflows/build/badge.svg)](https://github.com/douglasgreyling/light-service/actions)
[![codecov](https://codecov.io/gh/douglasgreyling/light-service/branch/main/graph/badge.svg?token=6KV6XMO36Z)](undefined)

A service object framework heavily, heavily, heavily inspired by the [LightService](https://github.com/adomokos/light-service) Ruby gem.

This package ports over most of the awesome ideas in [LightService](https://github.com/adomokos/light-service) so that one can use it in PHP. If you're familiar with the Ruby version, then you should feel mostly at home with this package.

Be sure to check out the original [LightService](https://github.com/adomokos/light-service) if you ever find yourself in Ruby-land!

## Table of Content

- [Why LightService?](#why-lightservice)
- [How LightService works in 60 seconds](#how-lightservice-works-in-60-seconds)
- [Getting started](#getting-started)
  - [Requirements](#requirements)
  - [Installation](#installation)
  - [Your first action](#your-first-action)
  - [Your first organizer](#your-first-organizer)
- [Simplifying our first tax example](#simplifying-our-first-tax-example)
  - [The organizer](#the-organizer)
  - [Looking up the tax percentage](#looking-up-the-tax-percentage)
  - [Calculating the order tax](#calculating-the-order-tax)
  - [Providing free shipping (where applicable)](<#providing-free-shipping-(where-applicable)>)
  - [And finally, the controller](#and-finally-the-controller)
- [Tips & Tricks](#tips-&-tricks)
  - [Stopping a series of actions](#stopping-a-series-of-actions)
  - [Hooks](#hooks)
  - [Expects and promises](#expects-and-promises)
  - [Context metadata](#context-metadata)
  - [Key aliases](#key-aliases)
  - [Logging](#logging)
  - [Error codes](#error-codes)
  - [Action rollback](#action-rollback)
  - [Orchestrator logic](#orchestrator-logic)
  - [Context factory for faster action testing](#context-factory-for-faster-action-testing)
- [Contributing](#contributing)
- [License](#license)

## Why LightService?

What do you think of this code?

```php
class TaxController extends SomeController {
  public function update {
    $order = Order::find(request('id'));
    $tax_ranges = TaxRange::for_region($order->region);

    if (is_null($tax_ranges)) {
      return ...; // render some view
    }

    $tax_percentage = $tax_ranges->for_total($order->total);

    if (is_null($tax_percentage)) {
      return ...; // render some other view
    }

    $order->tax = round(($order->total * ($tax_percentage/100)), 2);

    if ($order->total_with_tax > 200) {
      $order->provide_free_shipping;
    }

    return ...; // Redirect to some view with a flash message
  }
}
```

This controller violates the [SRP](http://en.wikipedia.org/wiki/Single_responsibility_principle). Can you imagine testing something like this?

In this instance we have a fairly simple controller, but one shudders to think what controllers could look like in more complex codebases out there in the wild.

You could argue that you could clean up this controller by moving the `$tax_percentage` logic and calculations into a tax model, but then you'll be relying on heavy model logic.

If you've ever done debugging (haha, who hasn't?) you might find it difficult to determine what's going on and where you need to start. This is especially difficult when you have a high level overview of what the code does and what needs to happen to resolve your bug.

Wouldn't it be nice if your code was broken up into smaller pieces which tell you exactly what they do?

In the case with our controller above, it would be great if our code dispelled any confusion by telling us that it was doing 3 simple things in a specific sequence whenever an order is updated:

1. Looking up the tax percentage based on order total.
2. Calculating the order tax.
3. Providing free shipping if the total with tax is greater than \$200.

If you've ever felt the headache of fat controllers, difficult code to reason about, or seemingly endless rabbit holes, then this is where LightService comes in.

## How LightService works in 60 seconds:

There are 2 key things to know about when working with LightService:

1. Actions.
2. Organizers.

**Actions** are the building blocks of getting stuff done in LightService. Actions focus on doing one thing really well. They can be executed on their own, but you'll often seem them bundled together with other actions inside Organizers.

**Organizers** group multiple actions together to complete some task. Organizers consist of at least one action. Organizers execute actions in a set order, one at a time. Organizers use actions to tell you the 'story' of what will happen.

Here's a diagram to understand the relationship between organizers and actions:

![LightService](resources/lightservice-interaction.png)

## Getting started:

### Requirements:

PHP 7.3+ is required 😅

### Installation:

TODO

### Your first action:

Let's make a simple greeting action.

```php
class GreetsSomeoneAction {
  use LightService\Action;

  private $expects  = ['name'];
  private $promises = ['greeting'];

  private function executed($context) {
    $context->greeting = "Hello, {$context->name}. Solved any fun mysteries lately?";
  }
}

$result = GreetsSomeoneAction::execute(['name' => 'Scooby']);
```

Actions take an optional list of expected inputs and can return an optional list of promised outputs. In this case we've told our action that it expects to receive an input called `name`.

The `executed` function is the function which gets called whenever we execute/run our action. We can access the inputs available to this action through the `$context` variable. Likewise, we can add/set any outputs through the context as well.

Once an action is run we can access the finished context, and the status of the action.

```php
$result = GreetsSomeoneAction::execute(['name' => 'Scooby']);

if ($result->success()) {
  echo $result->greeting;
}

> "Hello, Scooby. Solved any fun mysteries lately?"
```

Actions try to promote simplicity. They either succeed, or they fail, and they have very clear inputs and outputs. They generally focus on doing one thing, and because of that they can be a dream to test!

### Your first organizer

Most times a simple action isn't enough. LightService lets you compose a bunch of actions into a single organizer. By bundling your simple actions into an organizer you can stitch very complicated business logic together in a manner that's very easy to reason about. Good organizers tell you a clear story!

Before we create out organizer, let's create one more action:

```php
class FeedsSomeoneAction {
  use LightService\Action;

  private $expects = ['name'];

  private function executed($context) {
    $snack = Fridge::fetch('Grapes');

    Person::find($context->name)->feed($snack);
  }
}
```

Now let's create our organizer like this:

```php
class GreetsAndFeedsSomeone {
  use LightService\Organizer;

  public static function call($name) {
    return self::with(['name' => $name])->reduce(
      GreetsSomeoneAction::class,
      FeedSomeoneAction::class
    );
  }
}

$result = GreetsAndFeedsSomeone::call(['name' => 'Shaggy']);
```

And that's your first organizer! It ties two actions together through a static function `call`. The organizer call function takes any name and uses it to setup an initial context (this is what the `with` function does). The organizer then executes each of the actions on after another with the `reduce` function.

As your actions are executed they will add/remove to the context you initially set up.

Just like actions, organizers return the final context as their return value.

```php
$result = GreetsAndFeedsSomeone::call(['name' => 'Shaggy']);

if ($result->success()) {
  echo "Time to stock up on snacks!";
}

> "Time to stock up on snacks!"
```

Because organizers generally run through complex business logic, and every action has the potential to cause a failure, testing an organizer is functionally equivalent to an integration test.

## Simplifying our first tax example:

Let's clean up the controller we started with by using LightService.

We'll begin by looking at the controller. We want to look for distinct steps which we can separate whenever we need to update the tax on an order. By doing this we notice 3 clear processes:

1. Look up the tax percentage based on order total.
2. Calculate the order tax.
3. Provide free shipping if the total with tax is greater than \$200.

#### The organizer:

```php
class CalculatesTax {
  use LightService\Organizer;

  public static function call($order) {
    return self::with(['order' => $order])->reduce(
      LooksUpTaxPercentageAction::class,
      CalculatesOrderTaxAction::class,
      ProvidesFreeShippingAction::class
    );
  }
}
```

#### Looking up the tax percentage:

```php
class LooksUpTaxPercentageAction {
  use LightService\Action;

  private $expects  = ['order'];
  private $promises = ['tax_percentage'];

  private function executed($context) {
    $order      = $context->order;
    $tax_ranges = TaxRange::for_region($order->region);

    $context->tax_percentage = 0;

    if (is_null($tax_ranges)) {
      $context->fail('The tax ranges were not found');
      $this->next_context();
    }

    $tax_percentage = $tax_ranges->for_total($order->total);

    if (is_null($tax_percentage)) {
      $context->fail('The tax percentage were not found');
      $this->next_context();
    }

    $context->tax_percentage = $tax_percentage
  }
}
```

#### Calculating the order tax:

```php
class CalculatesOrderTaxAction {
  use LightService\Action;

  private $expects = ['order', 'tax_percentage'];

  private function executed($context) {
    $context
      ->order
      ->tax = round($order->total * ($tax_percentage/100), 2);
  }
}
```

#### Providing free shipping (where applicable):

```php
class ProvidesFreeShippingAction {
  use LightService\Action;

  private $expects = ['order'];

  private function executed($context) {
    $total_with_tax = $context->order->total_with_tax;

    if ($total_with_tax > 200)) {
      $context->order->provide_free_shipping;
    }
  }
}
```

#### And finally, the controller:

```php
class TaxController extends Controller {
  public function update {
    $order = Order::find(request('id'));

    $service_result = CalculatesTax::call($order);

    if ($service_result->failure()) {
      return ...; // render some view
    } else {
      return ...; // Redirect to some view with a flash message
    }
  }
}
```

## Tips & Tricks:

### Stopping a series of actions

When nothing unexpected happens during the organizer's call, the returned context will be successful. Here is how you can check for this:

However, sometimes not everything will play out as you expect it. An external API call might not be available or some complex business logic will need to stop the processing of a series of actions. You have two options to stop the call chain:

1. Failing the context
2. Skipping the rest of the actions

#### Failing the context:

When something goes wrong in an action and you want to halt the chain, you need to call `fail()` on the context object. This will push the context in a failure state (`$context->failure()` will evalute to true). The context's `fail` function can take an optional message argument, this message might help describe what went wrong. In case you need to return immediately from the point of failure, you have to do that by calling next context.

In case you want to fail the context and stop the execution of the executed block, use the `fail_and_return('something went wrong')` function. This will immediately fail the context and cause the execute function to return.

Here's an example:

```php
class SubmitsOrderAction {
  use LightService\Action;

  private function executed($context) {
    if (!$context->order->submit_order_successful()) {
      $context->fail_and_return('Failed to submit the order');
    }

    // This won't be executed
    $context->mailer->send_order_notification();
  }
}
```

Let's imagine that in the example above the organizer could have called 4 actions. The first 2 actions were executed until the 3rd action failed, and pushed the context into a failed state and so the 4th action was skipped.

![LightService](resources/failing-the-context.png)

#### Skipping the rest of the actions

You can skip the rest of the actions by calling `skip_remaining()` on the context. This behaves very similarly to the above-mentioned fail mechanism, except this will not push the context into a failure state. A good use case for this is executing the first couple of actions and based on a check you might not need to execute the rest. Here is an example of how you do it:

```php
class ChecksOrderStatusAction {
  use LightService\Action;

  private function executed($context) {
    if ($context->order->must_send_notification()) {
      $context->skip_remaining("Everything is good, no need to execute the rest of the actions");
    }
  }
}
```

Let's imagine that in the example above the organizer called 4 actions. The first 2 actions got executed successfully. The 3rd decided to skip the rest, the 4th action was not invoked. The context was successful.

![LightService](resources/skip-remaining.png)

### Hooks

In case you need to inject code right before, after or even around actions, then hooks could be the droid you're looking for. This addition to LightService is a great way to decouple instrumentation from business logic.

Consider this code:

```php
class SomeOrganizer {
  use LightService\Organizer;

  public static function call($context) {
    return self::with($context)->reduce(...self::actions());
  }

  public static function actions() {
    return [
      OneAction::class,
      TwoAction::class,
      ThreeAction::class
    ];
  }
}

class TwoAction {
  use LightService\Action;

  private function executed($context) {
    if ($context->user->role == 'admin')
      $context->logger->info('admin is doing something');

    $context->user->do_something();
  }
}
```

The logging logic makes `TwoAction` more complex, there is more code for logging than for business logic.

You have two options to decouple instrumentation from real logic with `before_each` and `after_each` hooks:

1. Declare your hooks in the Organizer
2. Attach hooks to the Organizer from the outside

This is how you can declaratively add before and after hooks to the organizer:

```php
class SomeOrganizer {
  use LightService\Organizer;

  public function before_each($context) {
    if ($context->current_action() == TwoAction::class) {
      if ($context->user->role != 'admin')
        return;

      $context->logger->info('admin is doing something');
    }
  }

  public function after_each($context) {
    if ($context->current_action() == TwoAction::class) {
      if ($context->user->role != 'admin')
        return;

      $context->logger->info('admin is doing something');
    }
  }

  public static function call($context) {
    return self::with($context)->reduce(...self::actions());
  }

  public static function actions() {
    return [
      OneAction::class,
      TwoAction::class,
      ThreeAction::class
    ];
  }
}

class TwoAction {
  use LightService\Action;

  private function executed($context) {
    $context->user->do_something();
  }
}
```

Note how the action has no logging logic after this change. Also, you can target before and after action logic for specific actions, as the `$context->current_action()` will have the class name of the currently processed action. In the example above, logging will occur only for `TwoAction` and not for `OneAction` or `ThreeAction`.

### Expects and promises

The expects and promises functions are rules for the inputs/outputs of an action. `expects` describes what keys it needs to exist inside the context for the action to execute and finish successfully. `promises` makes sure the keys are in the context after the action has been executed. If either of them are violated, a custom exception is thrown.

This is how it's used:

```php
class FooAction {
  use LightService\Action;

  private expects  = ['a', 'b'];
  private promises = ['c'];

  private function executed($context) {
    $context->c = $context->a + $context->b;
  }
}
```

For those who are utterly slothful, you can also set the `expects` and `promises` to a single string value if you're only dealing with one key.

```php
class FooAction {
  use LightService\Action;

  private expects  = 'a';
  private promises = 'b';

  private function executed($context) {
    $context->b = $context->a + 1;
  }
}
```

### Context metadata

The context will track some handy metadata.

They include:

1. The current action (`$context->current_action();`)
2. The current organizer (`$context->current_organizer();`)
3. The failure status of the context (`$context->failure();`)
4. The success status of the context (`$context->success();`)
5. The failure message if it exists (`$context->message();`)

### Key aliases

The `aliases` property allows you to create an alias for a key found inside the organizers context. Actions can then access the context using the aliases.

This allows you to put together existing actions from different sources and have them work together without having to modify their code. Aliases will work with, or without, action expects.

If a key alias is set for a key which already exists inside the context, then an exception is raised.

Say for example you have actions `AnAction` and `AnotherAction` that you've used in previous projects. `AnAction` provides `my_key` but `AnotherAction` needs to use that key but expects it to be called `key_alias` instead. You can use them together in an organizer like so:

```php
class AnOrganizer {
  use LightService\Organizer;

  private $aliases = ['my_key' => 'key_alias'];

  public static function call($order) {
    return self::with(['order' => $order])->reduce(
      AnAction::class,
      AnotherAction::class,
    );
  }
}

class AnAction {
  use LightService\Action;

  private $promises = 'my_key';

  private function executed($context) {
    $context->my_key = "value";
  }
}

class AnotherAction {
  use LightService\Action;

  private $expects = 'key_alias';

  private function executed($context) {
    $context->key_alias;
  }
}
```

### Error codes

You can add some more structure to your error handling by taking advantage of error codes in the context. Normally, when something goes wrong in your actions, you fail the process by setting the context to failure:

```php
class SomeAction {
  use LightService\Action;

  private function executed($context) {
    $context->fail("I don't like what happened here.");
  }
}
```

However, you might need to handle the errors coming from your action pipeline differently. Using an error code can help you check what type of expected error occurred in the organizer, or in the actions.

```php
class SomeAction {
  use LightService\Action;

  private function executed($context) {
    if (95 < $context->teapot->heat())
      $context->fail("The teapot is not hot enough", 1234);

    # Make some tea

    if (2 < $context->sugar->amount())
      $context->fail("There is not enough sugar for the tea", 5678);
  }
}
```

If this action were executed, then you can pull the error message like you would normally, but you can also retrieve the error code.

```php
$result = SomeAction::execute();

echo $result->message();
> "The teapost is not hot enough"

echo $result->error_code();
> 1234
```

### Action rollback

Sometimes your action has to undo what it did when an error occurs. Think about a chain of actions where you need to persist records in your data store in one action and you have to call an external service in the next. What happens if there is an error when you call the external service? You want to remove the records you previously saved. You can do it now with the `rolled_back` function.

```php
class SaveEntities {
  use LightService\Action;

  private $expects = 'user';

  private function executed($context) {
    $context->user->save();
  }

  private function rolled_back($executed) {
    $context->user->destroy();
  }
}
```

You need to call the `fail_with_rollback` function to initiate a rollback for actions starting with the action where the failure was triggered.

```php
class CallSomeExternalAPI {
  use LightService\Action;

  private function executed($context) {
    $api_call_result = SomeAPI::save_user($context->user);

    if ($api_call_result->failure)
      $context->fail_with_rollback("Error when calling external API");
  }
}
```

Using the `rolled_back` function is optional for the actions in the chain. You shouldn't care about undoing non-persisted changes.

The actions are rolled back in reversed order from the point of failure starting with the action that triggered it.

### Orchestrator logic

The Organizer - Action combination works really well for simple use cases. However, as business logic gets more complex, or when LightService is used in an ETL workflow, the code that routes the different organizers becomes very complex and imperative.

Let's look at a piece of code that does basic data transformations:

```php
class ExtractsTransformsLoadsData {
  public static function run($connection) {
    $context = RetrievesConnectionInfo::call($connection);
    $context = PullsDataFromRemoteApi::call($context);

    $retrieved_items = $context->retrieved_items;

    if ($retrieved_items->empty)
      NotifiesEngineeringTeamAction::execute($context);

    foreach($retrieved_items as $item) {
      $context->item = $item;
      TransformsData::call($context);
    }

    $context = LoadsData::call($context);

    return SendsNotifications::call($context);
  }
}
```

The LightService::Context is initialized with the first action, that context is passed around among organizers and actions. This code is still simpler than many out there, but it feels very imperative: it has conditionals and iterators in it.

Let's see how we could make it a bit more simpler with a declarative style:

```php
class ExtractsTransformsLoadsData {
  use LightService\Organizer;

  public static function call($connection) {
    return self::with(['connection' => $connection])->reduce(...self::actions());
  }

  public static function actions() {
    return [
      RetrievesConnectionInfo::class,
      PullsDataFromRemoteApi::class,
      self::reduce_if(
        function($context) {
          return array_empty($context->retrieved_items);
        },
        [ NotifiesEngineeringTeamAction::class ]
      ),
      self::iterate('retrieved_items', [ TransformsData::class ]),
      LoadsData::class,
      SendsNotifications::class
    ];
  }
}
```

This code is much easier to reason about, it's less noisy and it captures the goal of LightService well: simple, declarative code that's easy to understand.

The 5 different orchestrator constructs an organizer can have:

#### 1. `reduce_until`

`reduce_until` behaves like a while loop in imperative languages, it iterates until the provided predicate in the callback function evaluates to true.

```php
class ReduceUntilOrganizer {
  use LightService\Organizer;

  public static function call($number) {
    return self::with(['number' => $number])->reduce(
      AddsOneAction::class,
      self::reduce_until(
        function($context) {
          return 3 < $context->number;
        },
        [ AddsOneAction::class ]
      )
    );
  }
}
```

In this case the organizer above takes a number, executes a couple of actions before reducing an array of actions (in this case only containing the `AddsOneAction`) until the number in the context is greater than 3.

#### 2. `reduce_if`

`reduce_if` will reduce the included actions if the predicate in the callback function evaluates to true.

```php
class ReduceIfOrganizer {
  use LightService\Organizer;

  public static function call($number) {
    return self::with(['number' => $number])->reduce(
      AddsOneAction::class,
      self::reduce_if(
        function($context) {
          return 1 < $context->number;
        },
        [ AddsOneAction::class ]
      ),
      AddsOneAction::class
    );
  }
}
```

In this case the organizer above takes a number, executes a couple of actions before reducing an array of actions (in this case only containing the `AddsOneAction`) if the number in the context is greater than 1.

#### 3. `iterate`

`iterate` gives you iteration logic based on a string which exists as a key inside the context otherwise it will fail.

The organizer will singularize the key name and will put the actual item into the context under that name. Each element will be accessible by the singlular itme name for the actions in the iterate actions.

```php
class IterateOrganizer {
  use LightService\Organizer;

  public static function call($context) {
    return self::with($context)->reduce(
      self::iterate('numbers', [
        IterateAction::class,
      ])
    );
  }
}

class IterateAction {
  use LightService\Action;

  private $expects  = ['number'];
  private $promises = ['number'];

  private function executed($context) {
    $context->sum += $context->number;
  }
}
```

In this case the organizer above takes a collection of numbers and sums all the numbers together by iterating through them all.

#### 4. `execute`

To take advantage of another organizer or action, you might need to tweak the context a bit. Let's say you have an array, and you need to iterate over its values in a series of actions. To alter the context and have the values assigned into a variable, you need to create a new action with 1 line of code in it.

That seems a lot of ceremony for a simple change. You can do that in an `execute` function like this:

```php
class ExecuteOrganizer {
  use LightService\Organizer;

  public static function call($number) {
    return self::with(['number' => $number])->reduce(
      AddsOneAction::class,
      self::execute(function($context) { $context->number += 1; })
    );
  }
}
```

In this case the organizer above simply changes the context in some way defined within the `execute` functions callback.

#### 5. `add_to_context`

`add_to_context` can add key-value pairs on the fly to the context. This functionality is useful when you need a value injected into the context under a specific key right before the subsequent actions are executed.

```php
class AddToContextOrganizer {
  use LightService\Organizer;

  public static function call() {
    return self::with([])->reduce(
      self::add_to_context(['number' => 0]),
      AddsOneAction::class
    );
  }
}
```

In this case the organizer above adds some kv's into the context which the `AddsOneAction` needs in order to function correctly.

### Context factory for faster action testing

TODO - This will come one day.

### Logging

TODO - This will come one day.

## Contributing

1. Fork it
2. Try keep your commits semantic [like this](https://seesparkbox.com/foundry/semantic_commit_messages).
3. Create your feature branch (git checkout -b my-new-feature)
4. Commit your changes (git commit -am 'Added some feature')
5. Push to the branch (git push origin my-new-feature)
6. Create new Pull Request

## License

LightService is released under the MIT License.
