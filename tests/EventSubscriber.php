<?php

use Aw\EventSubscriberInterface;

class EventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'acme.foo.action' => 'OnZshLaughter'
        );
//        return array(
//            'KernelEvents::RESPONSE' => array(
//                array('onKernelResponsePre', 10),
//                array('onKernelResponsePost', -10),
//            ),
//            'OrderPlacedEvent::NAME' => 'onStoreOrder',
//        );
    }

    public function OnZshLaughter(OrderPlacedEvent $event, $event_name, \Aw\EventDispatcher $dispatcher)
    {
        $dispatcher->removeListener($event_name, array($this, 'OnZshLaughter'));
        print $event_name . '::lol,i got ' . $event->getOrder()->dollar . " dollars，and i only laughter once.\n";
    }
}

