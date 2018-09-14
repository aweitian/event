<?php

use Aw\GenericEvent;

class GenericEventTest extends PHPUnit_Framework_TestCase
{

    public function testGetSubject()
    {
        $test = new GenericEvent('subject-concrete', array(
            'a' => 1,
            'b' => 2
        ));
        $this->assertEquals("subject-concrete", $test->getSubject());
    }

    public function testGetArgument()
    {
        $test = new GenericEvent('subject-concrete', array(
            'a' => 1,
            'b' => 2,
            'c'
        ));
//        var_dump($test->getArguments());
        $this->assertEquals(2, $test->getArgument('b'));
        $this->assertEquals(1, $test->getArgument('a'));
        $this->assertEquals('c', $test->getArgument(0));
    }

    public function testForeachArgument()
    {
        $test = new GenericEvent('subject-concrete', array(
            'a' => 1,
            'b' => 2,
            'c'
        ));
        $i = 0;
        foreach ($test as $key => $value) {
            switch ($i) {
                case 0:
                    $this->assertEquals('a', $key);
                    $this->assertEquals(1, $value);
                    break;
                case 1:
                    $this->assertEquals('b', $key);
                    $this->assertEquals(2, $value);
                    break;
                case 2:
                    $this->assertEquals(0, $key);
                    $this->assertEquals('c', $value);
                    break;
            }

            $i++;
        }
    }


    public function testArrayAccessArgument()
    {
        $test = new GenericEvent('subject-concrete', array(
            'a' => 1,
            'b' => 2,
            'c'
        ));
        $this->assertEquals('c', $test[0]);
    }
}

