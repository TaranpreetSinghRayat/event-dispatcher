<?php

namespace EventDispatcher\Contracts;

/**
 * Event Dispatcher Interface
 */
interface DispatcherInterface
{
    /**
     * Register an event listener
     * 
     * @param string $eventName The event name
     * @param callable|ListenerInterface|string $listener The listener
     * @param int $priority Priority (higher = earlier execution)
     * @return void
     */
    public function listen(string $eventName, $listener, int $priority = 0): void;

    /**
     * Register an event subscriber
     * 
     * @param SubscriberInterface|string $subscriber The subscriber instance or class name
     * @return void
     */
    public function subscribe($subscriber): void;

    /**
     * Dispatch an event
     * 
     * @param string|object $event Event name or event object
     * @param mixed $payload Event payload (if event name is string)
     * @return mixed
     */
    public function dispatch($event, $payload = null);

    /**
     * Remove a listener from an event
     * 
     * @param string $eventName The event name
     * @param callable|ListenerInterface|string|null $listener The listener to remove, or null to remove all
     * @return void
     */
    public function forget(string $eventName, $listener = null): void;

    /**
     * Check if an event has listeners
     * 
     * @param string $eventName The event name
     * @return bool
     */
    public function hasListeners(string $eventName): bool;

    /**
     * Get all listeners for an event
     * 
     * @param string $eventName The event name
     * @return array
     */
    public function getListeners(string $eventName): array;
}

