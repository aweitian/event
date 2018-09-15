<?php
use Aw\EventDispatcher;

class DispatcherTest extends PHPUnit_Framework_TestCase
{
    public function testDispatcher()
    {
        $this->load();
        $subscriber = new EventSubscriber();
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener('acme.foo.action', function (OrderPlacedEvent $event) {
            print "\$" . $event->getOrder()->dollar . "\n";
        });

        $dispatcher->addSubscriber($subscriber);

        $dispatcher->dispatch('acme.foo.action', new OrderPlacedEvent(new Order(123)));
        $dispatcher->dispatch('acme.foo.action', new OrderPlacedEvent(new Order(456)));
        print "---------------------------------------------\n\n";
        //var_export($dispatcher->getListeners());
    }

    public function testRemove()
    {
        $this->load();
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener('acme.foo.action', function (OrderPlacedEvent $event) {
            print "\$" . $event->getOrder()->dollar . "\n";
        });

        $re = $dispatcher->addListener('acme.foo.action', function (OrderPlacedEvent $event) {
            print "\$" . $event->getOrder()->dollar . " --\n";
        });

        $dispatcher->dispatch('acme.foo.action', new OrderPlacedEvent(new Order(123)));
        $dispatcher->removeListener('acme.foo.action', $re);
        $dispatcher->dispatch('acme.foo.action', new OrderPlacedEvent(new Order(456)));
        print "---------------------------------------------\n\n";

        //var_export($dispatcher->getListeners());
    }

    public function testPriority()
    {
        $this->load();
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener('acme.foo.action', function () {
            print "my priority is 0\n";
        });

        $dispatcher->addListener('acme.foo.action', function () {
            print "my priority is -1\n";
        },-1);

        $dispatcher->addListener('acme.foo.action', function () {
            print "my priority is 2\n";
        },2);

        $dispatcher->dispatch('acme.foo.action', new OrderPlacedEvent(new Order(123)));
        print "---------------------------------------------\n\n";

        //var_export($dispatcher->getListeners());
    }


    public function testStop()
    {
        $this->load();
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener('acme.foo.action', function () {
            print "my priority is 0\n";
        });

        $dispatcher->addListener('acme.foo.action', function () {
            print "my priority is -1\n";
        },-1);

        $dispatcher->addListener('acme.foo.action', function (\Aw\Event $event) {
            print "my priority is 2,and i decide to stop Propagation\n";
            $event->stopPropagation();
        },2);

        $dispatcher->dispatch('acme.foo.action', new OrderPlacedEvent(new Order(123)));
        print "---------------------------------------------\n\n";

        //var_export($dispatcher->getListeners());
    }
//
//    public function testDispatchOnResponse()
//    {
//        require_once "response_dispatcher_test/ResponseEvent.php";
//        require_once "response_dispatcher_test/GoogleListener.php";
//        require_once "response_dispatcher_test/ContentLengthListener.php";
//
//        $dispatcher = new EventDispatcher();
//        $dispatcher->addListener('response', function (ResponseEvent $event) {
//            print $event->response;
//        });
//
//        $dispatcher->addListener('response', function (ResponseEvent $event) {
//            print $event->request . $event->response;
//        }, -255);
//
////        $dispatcher->addListener('response', array(new ContentLengthListener(), 'onResponse'), -255);
////        $dispatcher->addListener('response', array(new GoogleListener(), 'onResponse'));
//
//        $dispatcher->addSubscriber(new ContentLengthListener());
//        $dispatcher->addSubscriber(new GoogleListener());
//
//        $dispatcher->dispatch('response', new ResponseEvent('response', '$request'));
//
//    }

    private function load()
    {
        require_once "Order.php";
        require_once "OrderPlacedEvent.php";
        require_once "EventSubscriber.php";
    }
}

