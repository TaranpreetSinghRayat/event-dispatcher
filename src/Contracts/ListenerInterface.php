<?php

namespace EventDispatcher\Contracts;

/**
 * Listener Interface
 * 
 * Event listeners should implement this interface
 */
interface ListenerInterface
{
    /**
     * Handle the event
     * 
     * @param mixed $event The event object or data
     * @return void
     */
    public function handle($event): void;
}

