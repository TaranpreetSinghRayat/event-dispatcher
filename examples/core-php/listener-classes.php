<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use EventDispatcher\EventDispatcher;
use EventDispatcher\Event;
use EventDispatcher\Contracts\ListenerInterface;

// Example listener class
class SendWelcomeEmailListener implements ListenerInterface
{
    public function handle($event): void
    {
        $user = $event->getData('user');
        echo "Sending welcome email to: {$user['email']}\n";
    }
}

class LogUserActivityListener implements ListenerInterface
{
    public function handle($event): void
    {
        $user = $event->getData('user');
        echo "Logging activity for user: {$user['name']}\n";
    }
}

class UpdateUserStatisticsListener implements ListenerInterface
{
    public function handle($event): void
    {
        echo "Updating user statistics...\n";
    }
}

// Create dispatcher
$dispatcher = new EventDispatcher();

// Register listener classes
echo "=== Using Listener Classes ===\n\n";

// Method 1: Register instance
$dispatcher->listen('user.created', new SendWelcomeEmailListener());

// Method 2: Register class name (will be instantiated when event is dispatched)
$dispatcher->listen('user.created', SendWelcomeEmailListener::class);
$dispatcher->listen('user.created', LogUserActivityListener::class);
$dispatcher->listen('user.created', UpdateUserStatisticsListener::class);

// Dispatch event
$event = new Event('user.created', [
    'user' => [
        'name' => 'Alice Johnson',
        'email' => 'alice@example.com'
    ]
]);

$dispatcher->dispatch($event);

echo "\nDone!\n";

