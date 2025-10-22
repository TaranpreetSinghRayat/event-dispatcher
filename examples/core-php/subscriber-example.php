<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use EventDispatcher\EventDispatcher;
use EventDispatcher\Event;
use EventDispatcher\Subscriber;

// Example subscriber class
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
        echo "UserEventSubscriber: User created - " . $event->getData('name') . "\n";
    }

    public function onUserUpdated($event): void
    {
        echo "UserEventSubscriber: User updated - " . $event->getData('name') . "\n";
    }

    public function onUserDeleted($event): void
    {
        echo "UserEventSubscriber: User deleted - " . $event->getData('name') . "\n";
    }

    public function logUserDeletion($event): void
    {
        echo "UserEventSubscriber: Logging user deletion to audit log\n";
    }
}

class OrderEventSubscriber extends Subscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            'order.placed' => 'onOrderPlaced',
            'order.shipped' => 'onOrderShipped',
            'order.delivered' => 'onOrderDelivered',
        ];
    }

    public function onOrderPlaced($event): void
    {
        echo "OrderEventSubscriber: Order placed - Order #" . $event->getData('order_id') . "\n";
    }

    public function onOrderShipped($event): void
    {
        echo "OrderEventSubscriber: Order shipped - Order #" . $event->getData('order_id') . "\n";
    }

    public function onOrderDelivered($event): void
    {
        echo "OrderEventSubscriber: Order delivered - Order #" . $event->getData('order_id') . "\n";
    }
}

// Create dispatcher
$dispatcher = new EventDispatcher();

echo "=== Using Event Subscribers ===\n\n";

// Register subscribers
$dispatcher->subscribe(new UserEventSubscriber());
$dispatcher->subscribe(new OrderEventSubscriber());

// Dispatch user events
echo "--- User Events ---\n";
$dispatcher->dispatch(new Event('user.created', ['name' => 'Bob Wilson']));
$dispatcher->dispatch(new Event('user.updated', ['name' => 'Bob Wilson Jr.']));
$dispatcher->dispatch(new Event('user.deleted', ['name' => 'Bob Wilson Jr.']));

echo "\n--- Order Events ---\n";
$dispatcher->dispatch(new Event('order.placed', ['order_id' => 1001]));
$dispatcher->dispatch(new Event('order.shipped', ['order_id' => 1001]));
$dispatcher->dispatch(new Event('order.delivered', ['order_id' => 1001]));

echo "\nDone!\n";

