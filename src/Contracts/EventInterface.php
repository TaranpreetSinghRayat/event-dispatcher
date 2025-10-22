<?php

namespace EventDispatcher\Contracts;

/**
 * Event Interface
 * 
 * Marker interface for events. Events can implement this interface
 * or be plain objects/arrays.
 */
interface EventInterface
{
    /**
     * Get the event name
     * 
     * @return string
     */
    public function getName(): string;
}

