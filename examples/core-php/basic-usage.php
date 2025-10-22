<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use EventDispatcher\EventDispatcher;
use EventDispatcher\Event;

// Create dispatcher instance
$dispatcher = new EventDispatcher();

// Example 1: Simple event with closure listener
echo "=== Example 1: Simple Event with Closure ===\n";

$dispatcher->listen('user.created', function ($event) {
    echo "User created: " . $event->getData('name') . "\n";
});

$event = new Event('user.created', ['name' => 'John Doe', 'email' => 'john@example.com']);
$dispatcher->dispatch($event);

echo "\n";

// Example 2: Multiple listeners with priorities
echo "=== Example 2: Multiple Listeners with Priorities ===\n";

$dispatcher->listen('order.placed', function ($event) {
    echo "3. Send confirmation email\n";
}, 0);

$dispatcher->listen('order.placed', function ($event) {
    echo "1. Validate order (priority: 10)\n";
}, 10);

$dispatcher->listen('order.placed', function ($event) {
    echo "2. Process payment (priority: 5)\n";
}, 5);

$orderEvent = new Event('order.placed', ['order_id' => 123, 'total' => 99.99]);
$dispatcher->dispatch($orderEvent);

echo "\n";

// Example 3: Stopping event propagation
echo "=== Example 3: Stopping Event Propagation ===\n";

$dispatcher->listen('user.login', function ($event) {
    echo "Checking credentials...\n";
    if ($event->getData('username') === 'banned_user') {
        echo "User is banned! Stopping propagation.\n";
        return false; // Stop propagation
    }
}, 10);

$dispatcher->listen('user.login', function ($event) {
    echo "This should not execute for banned users\n";
}, 0);

$loginEvent = new Event('user.login', ['username' => 'banned_user']);
$dispatcher->dispatch($loginEvent);

echo "\n";

// Example 4: Modifying event data through listeners
echo "=== Example 4: Modifying Event Data ===\n";

$dispatcher->listen('data.process', function ($data) {
    echo "Step 1: Uppercase - ";
    return strtoupper($data);
}, 10);

$dispatcher->listen('data.process', function ($data) {
    echo "Step 2: Add prefix - ";
    return "PROCESSED: " . $data;
}, 5);

$result = $dispatcher->dispatch('data.process', 'hello world');
echo "Final result: " . $result . "\n";

echo "\n";

// Example 5: Using event objects
echo "=== Example 5: Custom Event Class ===\n";

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

    public function getEmail(): string
    {
        return $this->user['email'];
    }
}

$dispatcher->listen('user.registered', function (UserRegisteredEvent $event) {
    echo "Welcome email sent to: " . $event->getEmail() . "\n";
});

$dispatcher->listen('user.registered', function (UserRegisteredEvent $event) {
    echo "User added to newsletter: " . $event->getUser()['name'] . "\n";
});

$userEvent = new UserRegisteredEvent([
    'name' => 'Jane Smith',
    'email' => 'jane@example.com'
]);

$dispatcher->dispatch($userEvent);

echo "\n";

// Example 6: Check if event has listeners
echo "=== Example 6: Checking for Listeners ===\n";

if ($dispatcher->hasListeners('user.registered')) {
    echo "Event 'user.registered' has " . count($dispatcher->getListeners('user.registered')) . " listener(s)\n";
}

if (!$dispatcher->hasListeners('non.existent.event')) {
    echo "Event 'non.existent.event' has no listeners\n";
}

echo "\n";

// Example 7: Removing listeners
echo "=== Example 7: Removing Listeners ===\n";

echo "Before removal: " . count($dispatcher->getListeners('user.registered')) . " listeners\n";
$dispatcher->forget('user.registered');
echo "After removal: " . count($dispatcher->getListeners('user.registered')) . " listeners\n";

echo "\nAll examples completed!\n";

