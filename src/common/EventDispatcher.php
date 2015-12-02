<?php

namespace jamwork\common;

class EventDispatcher
{

	protected $handlers = array();
	protected $iterator = 0; //zu testzwecken siehe EventDispatcher Test

	public function addHandler($eventName, EventHandler $handler)
	{
		if (!isset($this->handlers[$eventName]))
		{
			$this->handlers[$eventName] = array();
		}
		$this->handlers[$eventName][] = $handler;
	}

	public function triggerEvent($event, $context = null, $info = null)
	{
		if (!$event instanceof Event)
		{
			$event = new Event($event, $context, $info);
		}
		$eventName = $event->getName();
		if (!isset($this->handlers[$eventName]))
		{
			return $event;
		}

		foreach ($this->handlers[$eventName] as $handler)
		{
			$handler->handle($event);
			if ($event->isCanceled())
			{
				break;
			}
			$this->iterator++;
		}

		return $event;
	}

    /**
     * Liefert alle Modulenamen in einem Array die sich auf ein Event gehängt haben
     *
     * @param string $eventName
     * @return array of EventHandler
     */
    public function getEventListeners($eventName) {

        $listenerModules = array();

        if(!isset($this->handlers[$eventName]))
        {
            return $listenerModules;
        }

        $listeners = $this->handlers[$eventName];
        foreach($listeners as $key => $eventHandlerObject)
        {
            $listenerModules[] = $eventHandlerObject;
        }

        return $listenerModules;
    }
}