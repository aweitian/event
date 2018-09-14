<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/14
 * Time: 16:08
 */

namespace Aw;


class EventDispatcher
{
    private $listeners = array();
    private $sorted = array();

    /**
     * Dispatches an event to all registered listeners.
     *
     * @param string $eventName The name of the event to dispatch. The name of
     *                          the event is the name of the method that is
     *                          invoked on listeners.
     * @param Event  $event     The event to pass to the event handlers/listeners
     *                          If not supplied, an empty Event instance is created
     *
     * @return Event
     */
    public function dispatch($eventName, Event $event = null)
    {
        if (null === $event) {
            $event = new Event();
        }

        if ($listeners = $this->getListeners($eventName)) {
            $this->doDispatch($listeners, $eventName, $event);
        }

        return $event;
    }

    /**
     * Gets the listeners of a specific event or all listeners sorted by descending priority.
     *
     * @param string $eventName The name of the event
     *
     * @return array The event listeners for the specified event, or all event listeners by event name
     */
    public function getListeners($eventName = null)
    {
        if (null !== $eventName) {
            if (empty($this->listeners[$eventName])) {
                return array();
            }

            if (!isset($this->sorted[$eventName])) {
                $this->sortListeners($eventName);
            }

            return $this->sorted[$eventName];
        }

        foreach ($this->listeners as $eventName => $eventListeners) {
            if (!isset($this->sorted[$eventName])) {
                $this->sortListeners($eventName);
            }
        }

        return array_filter($this->sorted);
    }

    /**
     * Gets the listener priority for a specific event.
     *
     * Returns null if the event or the listener does not exist.
     *
     * @param string   $eventName The name of the event
     * @param callable $listener  The listener
     *
     * @return int|null The event listener priority
     */
    public function getListenerPriority($eventName, $listener)
    {
        if (empty($this->listeners[$eventName])) {
            return null;
        }

        if (\is_array($listener) && isset($listener[0]) && $listener[0] instanceof \Closure) {
            $listener[0] = $listener[0]();
        }

        foreach ($this->listeners[$eventName] as $priority => $listeners) {
            foreach ($listeners as $k => $v) {
                if ($v !== $listener && \is_array($v) && isset($v[0]) && $v[0] instanceof \Closure) {
                    $v[0] = $v[0]();
                    $this->listeners[$eventName][$priority][$k] = $v;
                }
                if ($v === $listener) {
                    return $priority;
                }
            }
        }
        return null;
    }

    /**
     * Checks whether an event has any registered listeners.
     *
     * @param string $eventName The name of the event
     *
     * @return bool true if the specified event has any listeners, false otherwise
     */
    public function hasListeners($eventName = null)
    {
        if (null !== $eventName) {
            return !empty($this->listeners[$eventName]);
        }

        foreach ($this->listeners as $eventListeners) {
            if ($eventListeners) {
                return true;
            }
        }

        return false;
    }

    /**
     * Adds an event listener that listens on the specified events.
     *
     * @param string   $eventName The event to listen on
     * @param callable $listener  The listener
     * @param int      $priority  The higher this value, the earlier an event
     *                            listener will be triggered in the chain (defaults to 0)
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->listeners[$eventName][$priority][] = $listener;
        unset($this->sorted[$eventName]);
    }

    /**
     * Removes an event listener from the specified events.
     *
     * @param string   $eventName The event to remove a listener from
     * @param callable $listener  The listener to remove
     */
    public function removeListener($eventName, $listener)
    {
        if (empty($this->listeners[$eventName])) {
            return;
        }

        if (\is_array($listener) && isset($listener[0]) && $listener[0] instanceof \Closure) {
            $listener[0] = $listener[0]();
        }

        foreach ($this->listeners[$eventName] as $priority => $listeners) {
            foreach ($listeners as $k => $v) {
                if ($v !== $listener && \is_array($v) && isset($v[0]) && $v[0] instanceof \Closure) {
                    $v[0] = $v[0]();
                }
                if ($v === $listener) {
                    unset($listeners[$k], $this->sorted[$eventName]);
                } else {
                    $listeners[$k] = $v;
                }
            }

            if ($listeners) {
                $this->listeners[$eventName][$priority] = $listeners;
            } else {
                unset($this->listeners[$eventName][$priority]);
            }
        }
    }

    /**
     * Adds an event subscriber.
     *
     * The subscriber is asked for all the events he is
     * interested in and added as a listener for these events.
     * @param EventSubscriberInterface $subscriber
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (\is_string($params)) {
                $this->addListener($eventName, array($subscriber, $params));
            } elseif (\is_string($params[0])) {
                $this->addListener($eventName, array($subscriber, $params[0]), isset($params[1]) ? $params[1] : 0);
            } else {
                foreach ($params as $listener) {
                    $this->addListener($eventName, array($subscriber, $listener[0]), isset($listener[1]) ? $listener[1] : 0);
                }
            }
        }
    }

    /**
     * @param EventSubscriberInterface $subscriber
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (\is_array($params) && \is_array($params[0])) {
                foreach ($params as $listener) {
                    $this->removeListener($eventName, array($subscriber, $listener[0]));
                }
            } else {
                $this->removeListener($eventName, array($subscriber, \is_string($params) ? $params : $params[0]));
            }
        }
    }

    /**
     * Triggers the listeners of an event.
     *
     * This method can be overridden to add functionality that is executed
     * for each listener.
     *
     * @param callable[] $listeners The event listeners
     * @param string     $eventName The name of the event to dispatch
     * @param Event      $event     The event object to pass to the event handlers/listeners
     */
    protected function doDispatch($listeners, $eventName, Event $event)
    {
        foreach ($listeners as $listener) {
            if ($event->isPropagationStopped()) {
                break;
            }
            \call_user_func($listener, $event, $eventName, $this);
        }
    }

    /**
     * Sorts the internal list of listeners for the given event by priority.
     *
     * @param string $eventName The name of the event
     */
    private function sortListeners($eventName)
    {
        krsort($this->listeners[$eventName]);
        $this->sorted[$eventName] = array();

        foreach ($this->listeners[$eventName] as $priority => $listeners) {
            foreach ($listeners as $k => $listener) {
                if (\is_array($listener) && isset($listener[0]) && $listener[0] instanceof \Closure) {
                    $listener[0] = $listener[0]();
                    $this->listeners[$eventName][$priority][$k] = $listener;
                }
                $this->sorted[$eventName][] = $listener;
            }
        }
    }
}