# Laravel Integration Examples

This directory contains examples of how to use the Event Dispatcher package in Laravel applications.

## Installation

1. Install the package via Composer:
```bash
composer require your-vendor/event-dispatcher
```

2. The service provider will be automatically registered via Laravel's package discovery.

3. (Optional) Publish the configuration file:
```bash
php artisan vendor:publish --tag=event-dispatcher-config
```

## Usage Examples

### 1. Using the Facade

```php
use EventDispatcher\Laravel\Facades\EventDispatcher;
use EventDispatcher\Event;

// Listen to an event
EventDispatcher::listen('user.created', function ($event) {
    // Handle the event
    Log::info('User created: ' . $event->getData('name'));
});

// Dispatch an event
$event = new Event('user.created', ['name' => 'John Doe']);
EventDispatcher::dispatch($event);
```

### 2. Using Dependency Injection

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
        
        // Dispatch event
        $this->dispatcher->dispatch('user.created', $user);
        
        return response()->json($user);
    }
}
```

### 3. Creating Event Listeners

Create a listener class in `app/Listeners`:

```php
namespace App\Listeners;

use EventDispatcher\Contracts\ListenerInterface;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail implements ListenerInterface
{
    public function handle($event): void
    {
        $user = $event->getData('user');
        
        Mail::to($user->email)->send(new WelcomeEmail($user));
    }
}
```

Register the listener in a service provider:

```php
use EventDispatcher\Contracts\DispatcherInterface;
use App\Listeners\SendWelcomeEmail;

public function boot(DispatcherInterface $dispatcher)
{
    $dispatcher->listen('user.registered', SendWelcomeEmail::class);
}
```

### 4. Creating Event Subscribers

Create a subscriber class in `app/Subscribers`:

```php
namespace App\Subscribers;

use EventDispatcher\Subscriber;
use Illuminate\Support\Facades\Log;

class UserEventSubscriber extends Subscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            'user.created' => 'onUserCreated',
            'user.updated' => 'onUserUpdated',
            'user.deleted' => 'onUserDeleted',
        ];
    }

    public function onUserCreated($event): void
    {
        Log::info('User created', $event->getData());
    }

    public function onUserUpdated($event): void
    {
        Log::info('User updated', $event->getData());
    }

    public function onUserDeleted($event): void
    {
        Log::info('User deleted', $event->getData());
    }
}
```

Register the subscriber in `config/event-dispatcher.php`:

```php
return [
    'subscribers' => [
        \App\Subscribers\UserEventSubscriber::class,
    ],
];
```

Or register it manually in a service provider:

```php
use EventDispatcher\Contracts\DispatcherInterface;
use App\Subscribers\UserEventSubscriber;

public function boot(DispatcherInterface $dispatcher)
{
    $dispatcher->subscribe(new UserEventSubscriber());
}
```

### 5. Using in Eloquent Models

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use EventDispatcher\Laravel\Facades\EventDispatcher;
use EventDispatcher\Event;

class User extends Model
{
    protected static function booted()
    {
        static::created(function ($user) {
            EventDispatcher::dispatch(new Event('user.created', [
                'user' => $user,
                'timestamp' => now()
            ]));
        });

        static::updated(function ($user) {
            EventDispatcher::dispatch(new Event('user.updated', [
                'user' => $user,
                'timestamp' => now()
            ]));
        });
    }
}
```

### 6. Custom Event Classes

```php
namespace App\Events;

use EventDispatcher\Event;

class UserRegistered extends Event
{
    protected $user;

    public function __construct($user)
    {
        parent::__construct('user.registered');
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}
```

Dispatch the custom event:

```php
use App\Events\UserRegistered;
use EventDispatcher\Laravel\Facades\EventDispatcher;

$user = User::create($data);
EventDispatcher::dispatch(new UserRegistered($user));
```

## Advanced Features

### Priority-based Listeners

```php
// Higher priority executes first
EventDispatcher::listen('order.placed', ValidateOrder::class, 100);
EventDispatcher::listen('order.placed', ProcessPayment::class, 50);
EventDispatcher::listen('order.placed', SendConfirmation::class, 10);
```

### Stopping Event Propagation

```php
EventDispatcher::listen('user.login', function ($event) {
    if ($event->getData('user')->isBanned()) {
        // Stop propagation
        return false;
    }
}, 100);

EventDispatcher::listen('user.login', function ($event) {
    // This won't execute if user is banned
    Log::info('User logged in');
}, 50);
```

### Removing Listeners

```php
// Remove all listeners for an event
EventDispatcher::forget('user.created');

// Remove specific listener
$listener = function ($event) { /* ... */ };
EventDispatcher::listen('user.created', $listener);
EventDispatcher::forget('user.created', $listener);
```

## Testing

```php
use EventDispatcher\Contracts\DispatcherInterface;

class UserControllerTest extends TestCase
{
    public function test_user_creation_dispatches_event()
    {
        $dispatcher = $this->app->make(DispatcherInterface::class);
        
        $eventDispatched = false;
        $dispatcher->listen('user.created', function () use (&$eventDispatched) {
            $eventDispatched = true;
        });

        $this->post('/users', ['name' => 'Test User']);

        $this->assertTrue($eventDispatched);
    }
}
```

