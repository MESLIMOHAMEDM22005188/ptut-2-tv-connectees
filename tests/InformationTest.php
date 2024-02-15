<?php

use Models\Information;
use Models\User;

use PHPUnit\Framework\TestCase;

class InformationTest extends TestCase {

    private $information;
    private $user;

    public function setUp() : void {
        $this->information = new Information();
        $this->user = new User();
        $this->information->setId(1);
        $this->information->setAdminId(25);
        $this->information->setTitle('test info');
        $this->information->setAuthor($this->user);
        $this->information->setCreationDate('2024-01-22');
        $this->information->setExpirationDate('2024-0-27');
        $this->information->setType('Text');
    }

    public function testGetId() : void {
        $this->assertSame(1, $this->information->getId());
    }

    public function testGetAdminId() : void {
        $this->assertSame(25, $this->information->getAdminId());
    }

    public function testGetTitle() : void {
        $this->assertSame('test info', $this->information->getTitle());
    }

    public function testGetAuthor() : void {
        $this->assertSame($this->user, $this->information->getAuthor());
    }

    public function testGetCreationDate() : void {
        $this->assertSame('2024-01-22', $this->information->getCreationDate());
    }

    public function testGetExpirationDate() : void {
        $this->assertSame('2024-0-27', $this->information->getExpirationDate());
    }

    public function testGetType() : void {
        $this->assertSame('Text', $this->information->getType());
    }

    public function testSetId() : void {
        $this->information->setId(2);
        $this->assertSame(2, $this->information->getId());
    }

    public function testSetAdminId() : void {
        $this->information->setAdminId(26);
        $this->assertSame(26, $this->information->getAdminId());
    }

    public function testSetTitle() : void {
        $this->information->setTitle('nouveau titre');
        $this->assertSame('nouveau titre', $this->information->getTitle());
    }

    public function testSetAuthor() : void {
        $newUser = new User();
        $this->information->setAuthor($newUser);
        $this->assertSame($newUser, $this->information->getAuthor());
    }

    public function testSetCreationDate() : void {
        $this->information->setCreationDate('2024-02-01');
        $this->assertSame('2024-02-01', $this->information->getCreationDate());
    }

    public function testSetExpirationDate() : void {
        $this->information->setExpirationDate('2024-03-01');
        $this->assertSame('2024-03-01', $this->information->getExpirationDate());
    }

    public function testSetType() : void {
        $this->information->setType('PDF');
        $this->assertSame('PDF', $this->information->getType());
    }

}