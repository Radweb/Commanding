[<img src="http://i.imgur.com/Qslhr5z.png" align="right" height="40">](http://radweb.co.uk)

# Commanding

## Commands

A Command represents a single Use-Case in your system. They contain the data required to execute that Use-Case. For example, you might have a `ChangeSubscriptionPlanCommand` may look like this:

```php
class ChangeSubscriptonPlanCommand implements Command {

    /**
     * The current user account
     * @var Account
     */
    public $account;

    /**
     * The new plan to change to
     * @var string
     */
    public $plan;

    public function __construct(Account $account, $plan)
    {
        $this->account = $account;
        $this->plan = $plan;
    }

}
```

Executing this command from your controller may look like:

```php
class SubscriptionController {

    public function __construct(CommandBus $bus, Auth $auth, Request $request)
    {
        $this->bus = $bus;
        $this->auth = $auth;
        $this->request = $request;
    }

    public function changePlan()
    {
        $account = $this->auth->getCurrentUser()->account;
        $newPlan = $this->request->param('plan');

        $this->bus->execute(new ChangeSubscriptionPlanCommand($account, $newPlan));

        return 'Plan Change Successful!';
    }
}
```

So how does the command get processed? With a Command Handler:

```php
class ChangeSubscriptionPlan implements CommandHandler {

    public function __construct(/* dependencies here */)
    {
        // ...
    }

    public function handle(ChangeSubscriptionPlanCommand $command)
    {
        // do something...
    }

}
```

You may not want to crowd your Command Handler with validation logic. Instead, you can use a CommandValidator (ensure you're running the `ValidatingCommandBus` below).

```php
// write a Rule-Based validator:
class ChangeSubscriptionPlanValidator extends RuleBasedCommandValidator {

    public function rules()
    {
        return [
            'account' => ['required'],
            'plan' => ['required', 'in:Small,Medium,Large'],
        ];
    }

}

// a custom one:
class ChangeSubscriptionPlanValidator extends CommandValidator {

    /**
     * @return MessageProviderInterface|void
     */
    public function validate(Command $command)
    {
        // to return a general error:
        return $this->genericError('Something message');

        // to return many errors:
        return $this->addError('account', 'Something message')
                    ->addError('plan', 'Another message');

        // return nothing (void|null) for OK
    }

}

// or, you can write an entirely custom one by implementing `Radweb\Commanding\CommandValidator`
```

You may also want to separate Authentication logic: (ensure you're running the `AuthenticatingCommandBus` below)

```php
class ChangeSubscriptionPlanAuthenticator implements CommandAuthenticator {

    public function __construct(Auth $auth)
    {
        $this->currentUser = $auth->getCurrentUser();
    }

    /**
     * @return boolean
     */
    public function authenticate(Command $command)
    {
        // return true/false
        return $this->currentUser->isAccountAdministrator();
    }

}
```

## Command Translating

Given the Command `ChangeSubscriptionPlanCommand`, the associated Command Handler would be `ChangeSubscriptionPlan` (remove "Command"). This should implement `Radweb\Commanding\CommandHandler`.

The associated Command Validator would be `ChangeSubscriptionPlanValidator` (swap "Command" for "Validator"). This should implement `Radweb\Commanding\CommandValidator`.

The associated Command Authenticator would be `ChangeSubscriptionPlanAuthenticator` (swap "Command" for "Authenticator"). This should implement `Radweb\Commanding\CommandAuthenticator`.

## Buses

`BasicCommandBus` simply translates the given Command into a Handler class, and executes it via the Container.

Additional Command Buses are included which decorate the `BasicCommandBus` to provide additional behaviour.

`ValidatingCommandBus` will execute a `CommandValidator` first.

`AuthenticatingCommandBus` will execute a `CommandAuthenticator` first.

`LoggingCommandBus` will write to a log before executing a command, and log whenever a `CommandBusException` is thrown.

## Decorating

Each additional Command Bus decorates the `BasicCommandBus`. Below is an example of decorating the Command Bus with each decorator in Laravel's IoC Container:

```php
// Bind the base command bus first
$container->bind('Radweb\Commanding\CommandBus', 'Radweb\Commanding\Buses\BasicCommandBus');

// Decorate with the validating command bus, providing its dependencies
$container->extend('Radweb\Commanding\CommandBus', function(CommandBus $b, Container $c) {
    // $b is now "BasicCommandBus"
    return new ValidatingCommandBus($b, $c, $c->make('Radweb\Commanding\CommandTranslator');
});

// Decorate with the authenticating command bus, providing its dependencies
$container->extend('Radweb\Commanding\CommandBus', function(CommandBus $b, Container $c) {
    // $b is now "ValidatingCommandBus"
    return new AuthenticatingCommandBus($b, $c, $c->make('Radweb\Commanding\CommandTranslator');
});

// Decorate with the logging command bus, providing its dependencies
$container->extend('Radweb\Commanding\CommandBus', function(CommandBus $b, Container $c) {
    // $b is now "AuthenticatingCommandBus"
    return new LoggingCommandBus($b, $c->make('Psr\Log\LoggerInterface');
});

// 'Radweb\Commanding\CommandBus' is now 'LoggerInterface', which wraps 'AuthenticatingCommandBus', which wraps 'ValidatingCommandBus', which wraps 'BasicCommandBus'
```

You can write your own Command Bus, or a decorator for it, simply by implementing the `Radweb\Commanding\CommandBus` interface. If you're decorating an existing bus, you should accept a `CommandBus` as a dependency and forward calls onto it.

## Logging

The `LoggingCommandBus` expects an implementation of `Psr\Log\LoggerInterface`.

