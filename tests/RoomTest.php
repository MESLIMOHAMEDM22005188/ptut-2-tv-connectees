<?php

use Models\Room;

use PHPUnit\Framework\TestCase;

class RoomTest extends TestCase {

    private $roomone;
    private $roomtwo;

    public function setUp() : void {
        $this->roomone = new Room("I-002");
        $this->roomtwo = new Room("I-004");
    }

    public function testGetName() : void {
        // Doit renvoyer I-002
        $this->assertEquals("I-002", $this->roomone->getName());
        // Doit renvoyer I-004
        $this->assertEquals("I-004", $this->roomtwo->getName());
    }

}