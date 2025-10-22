<?php

namespace EventDispatcher;

use EventDispatcher\Contracts\EventInterface;

/**
 * Base Event Class
 * 
 * Provides a base implementation for events
 */
class Event implements EventInterface
{
    /**
     * Event name
     * 
     * @var string
     */
    protected $name;

    /**
     * Event data
     * 
     * @var array
     */
    protected $data = [];

    /**
     * Whether propagation is stopped
     * 
     * @var bool
     */
    protected $propagationStopped = false;

    /**
     * Constructor
     * 
     * @param string|null $name Event name
     * @param array $data Event data
     */
    public function __construct(?string $name = null, array $data = [])
    {
        $this->name = $name ?? static::class;
        $this->data = $data;
    }

    /**
     * Get the event name
     * 
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set event data
     * 
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setData(string $key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Get event data
     * 
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function getData(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->data;
        }

        return $this->data[$key] ?? $default;
    }

    /**
     * Check if event has data key
     * 
     * @param string $key
     * @return bool
     */
    public function hasData(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Stop event propagation
     * 
     * @return self
     */
    public function stopPropagation(): self
    {
        $this->propagationStopped = true;
        return $this;
    }

    /**
     * Check if propagation is stopped
     * 
     * @return bool
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    /**
     * Magic getter
     * 
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->getData($key);
    }

    /**
     * Magic setter
     * 
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set(string $key, $value): void
    {
        $this->setData($key, $value);
    }

    /**
     * Magic isset
     * 
     * @param string $key
     * @return bool
     */
    public function __isset(string $key): bool
    {
        return $this->hasData($key);
    }
}

