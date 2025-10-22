<?php

namespace EventDispatcher\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Event Dispatcher Facade
 * 
 * @method static void listen(string $eventName, $listener, int $priority = 0)
 * @method static void subscribe($subscriber)
 * @method static mixed dispatch($event, $payload = null)
 * @method static void forget(string $eventName, $listener = null)
 * @method static bool hasListeners(string $eventName)
 * @method static array getListeners(string $eventName)
 * @method static array getEvents()
 * @method static void clear()
 * 
 * @see \EventDispatcher\EventDispatcher
 */
class EventDispatcher extends Facade
{
    /**
     * Get the registered name of the component
     * 
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'event.dispatcher';
    }
}

