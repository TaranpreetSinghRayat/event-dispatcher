<?php

namespace EventDispatcher;

use EventDispatcher\Contracts\DispatcherInterface;
use EventDispatcher\Contracts\EventInterface;
use EventDispatcher\Contracts\ListenerInterface;
use EventDispatcher\Contracts\SubscriberInterface;

/**
 * Event Dispatcher
 * 
 * Core event dispatcher implementation that works with plain PHP
 */
class EventDispatcher implements DispatcherInterface
{
    /**
     * Registered event listeners
     * 
     * @var array
     */
    protected $listeners = [];

    /**
     * Sorted listeners cache
     * 
     * @var array
     */
    protected $sorted = [];

    /**
     * Register an event listener
     * 
     * @param string $eventName The event name
     * @param callable|ListenerInterface|string $listener The listener
     * @param int $priority Priority (higher = earlier execution)
     * @return void
     */
    public function listen(string $eventName, $listener, int $priority = 0): void
    {
        $this->listeners[$eventName][$priority][] = $listener;
        unset($this->sorted[$eventName]);
    }

    /**
     * Register an event subscriber
     * 
     * @param SubscriberInterface|string $subscriber The subscriber instance or class name
     * @return void
     */
    public function subscribe($subscriber): void
    {
        if (is_string($subscriber)) {
            $subscriber = new $subscriber();
        }

        if (!$subscriber instanceof SubscriberInterface) {
            throw new \InvalidArgumentException('Subscriber must implement SubscriberInterface');
        }

        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (is_string($params)) {
                // Simple method name
                $this->listen($eventName, [$subscriber, $params]);
            } elseif (is_array($params)) {
                if (isset($params['method'])) {
                    // Single listener with priority
                    $priority = $params['priority'] ?? 0;
                    $this->listen($eventName, [$subscriber, $params['method']], $priority);
                } else {
                    // Multiple listeners
                    foreach ($params as $listener) {
                        if (is_string($listener)) {
                            $this->listen($eventName, [$subscriber, $listener]);
                        } elseif (is_array($listener) && isset($listener['method'])) {
                            $priority = $listener['priority'] ?? 0;
                            $this->listen($eventName, [$subscriber, $listener['method']], $priority);
                        }
                    }
                }
            }
        }
    }

    /**
     * Dispatch an event
     * 
     * @param string|object $event Event name or event object
     * @param mixed $payload Event payload (if event name is string)
     * @return mixed
     */
    public function dispatch($event, $payload = null)
    {
        // Determine event name and payload
        if (is_object($event)) {
            $eventName = $event instanceof EventInterface ? $event->getName() : get_class($event);
            $payload = $event;
        } else {
            $eventName = $event;
        }

        $listeners = $this->getListeners($eventName);

        if (empty($listeners)) {
            return $payload;
        }

        foreach ($listeners as $listener) {
            $result = $this->callListener($listener, $payload);
            
            // If listener returns false, stop propagation
            if ($result === false) {
                break;
            }
            
            // If listener returns a value, update payload
            if ($result !== null) {
                $payload = $result;
            }
        }

        return $payload;
    }

    /**
     * Call a listener
     * 
     * @param callable|ListenerInterface|string $listener
     * @param mixed $event
     * @return mixed
     */
    protected function callListener($listener, $event)
    {
        if ($listener instanceof ListenerInterface) {
            $listener->handle($event);
            return null;
        }

        if (is_string($listener) && class_exists($listener)) {
            $instance = new $listener();
            if ($instance instanceof ListenerInterface) {
                $instance->handle($event);
                return null;
            }
            $listener = $instance;
        }

        if (is_callable($listener)) {
            return call_user_func($listener, $event);
        }

        throw new \InvalidArgumentException('Listener must be callable or implement ListenerInterface');
    }

    /**
     * Remove a listener from an event
     * 
     * @param string $eventName The event name
     * @param callable|ListenerInterface|string|null $listener The listener to remove, or null to remove all
     * @return void
     */
    public function forget(string $eventName, $listener = null): void
    {
        if ($listener === null) {
            unset($this->listeners[$eventName], $this->sorted[$eventName]);
            return;
        }

        if (!isset($this->listeners[$eventName])) {
            return;
        }

        foreach ($this->listeners[$eventName] as $priority => $listeners) {
            foreach ($listeners as $key => $registeredListener) {
                if ($registeredListener === $listener) {
                    unset($this->listeners[$eventName][$priority][$key]);
                }
            }

            if (empty($this->listeners[$eventName][$priority])) {
                unset($this->listeners[$eventName][$priority]);
            }
        }

        unset($this->sorted[$eventName]);
    }

    /**
     * Check if an event has listeners
     * 
     * @param string $eventName The event name
     * @return bool
     */
    public function hasListeners(string $eventName): bool
    {
        return !empty($this->listeners[$eventName]);
    }

    /**
     * Get all listeners for an event
     * 
     * @param string $eventName The event name
     * @return array
     */
    public function getListeners(string $eventName): array
    {
        if (!isset($this->listeners[$eventName])) {
            return [];
        }

        if (!isset($this->sorted[$eventName])) {
            $this->sortListeners($eventName);
        }

        return $this->sorted[$eventName];
    }

    /**
     * Sort listeners by priority
     * 
     * @param string $eventName
     * @return void
     */
    protected function sortListeners(string $eventName): void
    {
        $this->sorted[$eventName] = [];

        if (isset($this->listeners[$eventName])) {
            krsort($this->listeners[$eventName]);
            $this->sorted[$eventName] = array_merge(...$this->listeners[$eventName]);
        }
    }

    /**
     * Get all registered events
     * 
     * @return array
     */
    public function getEvents(): array
    {
        return array_keys($this->listeners);
    }

    /**
     * Clear all listeners
     * 
     * @return void
     */
    public function clear(): void
    {
        $this->listeners = [];
        $this->sorted = [];
    }
}

