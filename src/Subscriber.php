<?php

namespace EventDispatcher;

use EventDispatcher\Contracts\SubscriberInterface;

/**
 * Base Event Subscriber
 * 
 * Provides a base implementation for event subscribers
 */
abstract class Subscriber implements SubscriberInterface
{
    /**
     * Get the events to subscribe to
     * 
     * This method should be implemented by child classes
     * 
     * @return array
     */
    abstract public function getSubscribedEvents(): array;
}

