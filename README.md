# Event Dispatcher

A flexible and powerful event dispatcher package for PHP that allows your application components to communicate with each other by dispatching events and listening to them. Supports both **Laravel** and **core PHP**.

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## Features

- 🚀 **Simple and intuitive API**
- 🎯 **Priority-based event listeners**
- 📦 **Event subscribers for grouping related listeners**
- 🔄 **Event propagation control**
- 🎨 **Works with both Laravel and core PHP**
- 💪 **Type-safe with interfaces and contracts**
- 🧪 **Easy to test**
- 📝 **Well documented with examples**

## Installation

Install the package via Composer:

```bash
composer require tweekersnut/event-dispatcher
```

## Quick Start

### Core PHP

```php
use EventDispatcher\EventDispatcher;
use EventDispatcher\Event;

// Create dispatcher instance
$dispatcher = new EventDispatcher();

// Listen to an event
$dispatcher->listen('user.created', function ($event) {
    echo "User created: " . $event->getData('name');
});

// Dispatch an event
$event = new Event('user.created', ['name' => 'John Doe']);
$dispatcher->dispatch($event);
```

### Laravel

The service provider is automatically registered via Laravel's package discovery.

```php
use EventDispatcher\Laravel\Facades\EventDispatcher;
use EventDispatcher\Event;

// Listen to an event
EventDispatcher::listen('user.created', function ($event) {
    Log::info('User created: ' . $event->getData('name'));
});

// Dispatch an event
$event = new Event('user.created', ['name' => 'John Doe']);
EventDispatcher::dispatch($event);
```

## Usage

### Basic Event Dispatching

```php
use EventDispatcher\EventDispatcher;

$dispatcher = new EventDispatcher();

// Register a listener
$dispatcher->listen('order.placed', function ($event) {
    // Handle the event
    echo "Order placed: " . $event->getData('order_id');
});

// Dispatch the event
$dispatcher->dispatch('order.placed', ['order_id' => 123]);
```

### Using Event Objects

```php
use EventDispatcher\Event;

// Create an event with data
$event = new Event('user.registered', [
    'name' => 'Jane Doe',
    'email' => 'jane@example.com'
]);

// Dispatch the event
$dispatcher->dispatch($event);
```

### Custom Event Classes

```php
use EventDispatcher\Event;

class UserRegisteredEvent extends Event
{
    private $user;

    public function __construct(array $user)
    {
        parent::__construct('user.registered');
        $this->user = $user;
    }

    public function getUser(): array
    {
        return $this->user;
    }
}

// Dispatch custom event
$dispatcher->dispatch(new UserRegisteredEvent([
    'name' => 'John',
    'email' => 'john@example.com'
]));
```

### Event Listeners

#### Closure Listeners

```php
$dispatcher->listen('user.created', function ($event) {
    // Handle event
});
```

#### Class-based Listeners

```php
use EventDispatcher\Contracts\ListenerInterface;

class SendWelcomeEmail implements ListenerInterface
{
    public function handle($event): void
    {
        $user = $event->getData('user');
        // Send email logic
    }
}

// Register the listener
$dispatcher->listen('user.created', SendWelcomeEmail::class);
// or
$dispatcher->listen('user.created', new SendWelcomeEmail());
```

### Priority-based Listeners

Listeners with higher priority execute first:

```php
$dispatcher->listen('order.placed', function ($event) {
    echo "3. Send confirmation";
}, 0);

$dispatcher->listen('order.placed', function ($event) {
    echo "1. Validate order";
}, 100);

$dispatcher->listen('order.placed', function ($event) {
    echo "2. Process payment";
}, 50);
```

### Event Subscribers

Group related event listeners together:

```php
use EventDispatcher\Subscriber;

class UserEventSubscriber extends Subscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            'user.created' => 'onUserCreated',
            'user.updated' => ['method' => 'onUserUpdated', 'priority' => 10],
            'user.deleted' => [
                ['method' => 'onUserDeleted', 'priority' => 5],
                'logUserDeletion'
            ]
        ];
    }

    public function onUserCreated($event): void
    {
        // Handle user created
    }

    public function onUserUpdated($event): void
    {
        // Handle user updated
    }

    public function onUserDeleted($event): void
    {
        // Handle user deleted
    }

    public function logUserDeletion($event): void
    {
        // Log deletion
    }
}

// Register the subscriber
$dispatcher->subscribe(new UserEventSubscriber());
```

### Stopping Event Propagation

```php
$dispatcher->listen('user.login', function ($event) {
    if ($event->getData('user')->isBanned()) {
        return false; // Stop propagation
    }
}, 100);

$dispatcher->listen('user.login', function ($event) {
    // This won't execute if user is banned
}, 50);
```

### Managing Listeners

```php
// Check if event has listeners
if ($dispatcher->hasListeners('user.created')) {
    // Event has listeners
}

// Get all listeners for an event
$listeners = $dispatcher->getListeners('user.created');

// Remove all listeners for an event
$dispatcher->forget('user.created');

// Remove specific listener
$listener = function ($event) { /* ... */ };
$dispatcher->listen('user.created', $listener);
$dispatcher->forget('user.created', $listener);

// Get all registered events
$events = $dispatcher->getEvents();

// Clear all listeners
$dispatcher->clear();
```

## Laravel Integration

### Publishing Configuration

```bash
php artisan vendor:publish --tag=event-dispatcher-config
```

### Using Dependency Injection

```php
use EventDispatcher\Contracts\DispatcherInterface;

class UserController extends Controller
{
    protected $dispatcher;

    public function __construct(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function store(Request $request)
    {
        $user = User::create($request->all());
        $this->dispatcher->dispatch('user.created', $user);
        
        return response()->json($user);
    }
}
```

### Auto-registering Subscribers

In `config/event-dispatcher.php`:

```php
return [
    'subscribers' => [
        \App\Subscribers\UserEventSubscriber::class,
        \App\Subscribers\OrderEventSubscriber::class,
    ],
];
```

## Examples

Check the `examples` directory for more detailed examples:

- **Core PHP Examples:**
  - `examples/core-php/basic-usage.php` - Basic event dispatching
  - `examples/core-php/listener-classes.php` - Using listener classes
  - `examples/core-php/subscriber-example.php` - Event subscribers

- **Laravel Examples:**
  - `examples/laravel/README.md` - Comprehensive Laravel integration guide

## Testing

```php
use EventDispatcher\EventDispatcher;

$dispatcher = new EventDispatcher();

$eventDispatched = false;
$dispatcher->listen('test.event', function () use (&$eventDispatched) {
    $eventDispatched = true;
});

$dispatcher->dispatch('test.event');

assert($eventDispatched === true);
```

## API Reference

### EventDispatcher

- `listen(string $eventName, $listener, int $priority = 0): void` - Register an event listener
- `subscribe($subscriber): void` - Register an event subscriber
- `dispatch($event, $payload = null)` - Dispatch an event
- `forget(string $eventName, $listener = null): void` - Remove listeners
- `hasListeners(string $eventName): bool` - Check if event has listeners
- `getListeners(string $eventName): array` - Get all listeners for an event
- `getEvents(): array` - Get all registered events
- `clear(): void` - Clear all listeners

## Requirements

- PHP 7.4 or higher
- Laravel 8.x, 9.x, 10.x, or 11.x (for Laravel integration)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## Credits

- **Tweekersnut Network** - [admin@tweekersnut.com](mailto:admin@tweekersnut.com)

## Support

For issues, questions, or contributions, please visit the [GitHub repository](https://github.com/TaranpreetSinghRayat/event-dispatcher).

