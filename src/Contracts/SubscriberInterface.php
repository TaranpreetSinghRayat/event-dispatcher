<?php

namespace EventDispatcher\Contracts;

/**
 * Event Subscriber Interface
 * 
 * Event subscribers can listen to multiple events
 */
interface SubscriberInterface
{
    /**
     * Get the events to subscribe to
     * 
     * Returns an array where keys are event names and values are:
     * - method name (string)
     * - array with 'method' and optional 'priority' keys
     * - array of method names or arrays
     * 
     * Examples:
     * [
     *     'user.created' => 'onUserCreated',
     *     'user.updated' => ['method' => 'onUserUpdated', 'priority' => 10],
     *     'user.deleted' => [
     *         ['method' => 'onUserDeleted', 'priority' => 5],
     *         'logUserDeletion'
     *     ]
     * ]
     * 
     * @return array
     */
    public function getSubscribedEvents(): array;
}

