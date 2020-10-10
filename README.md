Action Context:

The thought was to make the context friendly. It will quack the way you might want it to.
Whether that's like an associative array, object or through some helpers.

Things were made static since I wanted to track the context failures etc whenever an exception was thrown. This wasn't really possible in a static context
If you need fine grained control over the actions, then use the less friendler run approach. This will allow you to dig into exceptions etc

We can use next_action() to skip the current action
