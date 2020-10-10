# Light-Service-PHP:

A service object framework heavily, heavily, heavily inspired by the [LightService](https://github.com/adomokos/light-service) Ruby gem.

This package ports over most of the awesome ideas in [LightService](https://github.com/adomokos/light-service) so that one can use it in PHP. If you're familiar with the Ruby version, then you should feel mostly at home with this package.

Be sure to check out the original [LightService](https://github.com/adomokos/light-service) if you ever find yourself in Ruby-land!

## Table of Content

- [Why LightService?](#why-lightservice)
- [How it works](#how-it-works)
- [Let's build something!](#lets-build-something)
- [Getting Started](#getting-started)
  - [Requirements](#requirements)
  - [Installation](#installation)

## Why LightService?

What do you think of this code?

```php
class TaxController extends SomeController {
  public function update {
    $order = Order::find(request('id'));
    $tax_ranges = TaxRange->for_region($order->region);

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

You could move the `$tax_percentage` logic and calculations into a tax model,
but then you'll be relying on heavy model logic.

Essentially, this controller does 3 things in a specific order:

1. Looks up the tax percentage based on order total.
2. Calculates the order tax.
3. Provides free shipping if the total with tax is greater than \$200.

The order of these tasks matters since you can't calculate the order tax without the percentage.

Wouldn't it be nice to see something in your code like this instead?

```php
(
  LooksUpTaxPercentage,
  CalculatesOrderTax,
  ProvidesFreeShipping
)
```

This block of code simplifies what's going on in your code since it tells a story by describing the steps in a clear manner.

LightService allows you to write your code this way.

## How it works

### You start with an organizer:

An organizer essentially describes the set of actions which it will execute. It also describes the order in which the actions will be executed. The organizer is the bit of code which executes the actions one by one.

You can pass data to your organizer through something called 'context'. The context acts like a central store where your actions can add or retrieve bits of data they need to work. The context is a key way to have your actions interact with each other. More on this later!

### You fall in love with actions:

Once you have an organizer describing the set of actions which need to occur, then you actually need to make them! Actions are simple objects which perform one thing really well.

If you're a pictures then here's one that might help understand how organizers and actions interact with each other:

![LightService](resources/lightservice-interaction.png)

## Let's build something!

So let's try make an organizer and the necessary actions for the controller above:

### The organizer:

```php
class CalculatesTax {
  use LightServicePHP\Organizer;

  public static function call($order) {
    return self::with(['order' => $order])->reduce(
      LooksUpTaxPercentageAction::class,
      CalculatesOrderTaxAction::class,
      ProvidesFreeShippingAction::class
    );
  }
}
```

And just like that we've made an organizer which starts out with a basic context which stores the order the actions will need to work on.

### The actions:

Let's start with looking up the tax percentage:

```php
class LooksUpTaxPercentageAction {
  use LightServicePHP\Action;

  private $expects  = ['order'];
  private $promises = ['tax_percentage'];

  private function executed() {
    $order      = $this->context->order;
    $tax_ranges = TaxRange::for_region($order->region);

    $this->context->tax_percentage = 0;

    $tax_range_nil = $this->object_is_nil(
                       $tax_ranges,
                       $context,
                       'The tax ranges were not found'
                     );

    if ($tax_range_nil) {
      $this->next_context();
    }

    $this
      ->context
      ->tax_percentage = $tax_ranges->for_total($order->total);

    $tax_percentage_nil = $this->object_is_nil(
                            $this->context->tax_percentage,
                            $context,
                            'The tax percentage were not found'
                          );

    if ($tax_percentage_nil) {
      $this->next_context();
    }
  }

  private function object_is_nil($object, $context, $message) {
    if (is_null($object) {
      $this->context->fail($message);
      return true;
    }

    return false;
  }
}
```

Now let's build the action which calculates the tax for the order:

```php
class CalculatesOrderTaxAction {
  use LightServicePHP\Action;

  private $expects = ['order', 'tax_percentage'];

  private function executed() {
    $this
      ->context
      ->order
      ->tax = round($order->total * ($tax_percentage/100), 2);
  }
}
```

And finally, let's see if we need to provide free shipping:

```php
class ProvidesFreeShippingAction {
  use LightServicePHP\Action;

  private $expects = ['order'];

  private function executed() {
    $total_with_tax = $this->context->order->total_with_tax;

    if ($total_with_tax > 200)) {
      $this->context->order->provide_free_shipping;
    }
  }
}
```

And with all that, your controller should be super simple:

```php
class TaxController extends Contoller {
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

## Getting started

### Requirements

TODO

### Installation

TODO
