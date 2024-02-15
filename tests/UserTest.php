<?php

use Models\User;

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase {

    private $user;
    private $otheruser;

    public function setUp() : void {
        $this->user = new User();
        $this->user->setId(1);
        $this->user->setLogin("test1");
        $this->user->setPassword("test123");
        $this->user->setEmail("test123@gmail.com");
        $this->user->setRole("Student");
        $this->user->setCodes([10, 20, 30]);
        $this->otheruser = new User();
        $this->otheruser->setId(5);
        $this->otheruser->setLogin("otheruser");
        $this->otheruser->setPassword("otheruser123");
        $this->otheruser->setEmail("otheruser123@gmail.com");
        $this->otheruser->setRole("Secretary");
        $this->otheruser->setCodes([100, 200, 300]);
    }

    public function testGetId() : void {
        $this->assertSame(1, $this->user->getId());
        $this->assertNotSame(1, $this->otheruser->getId());
    }

    public function testGetLogin() : void {
        $this->assertEquals("test1", $this->user->getLogin());
        $this->assertNotEquals("test1", $this->otheruser->getLogin());
    }

    public function testGetPassword() : void {
        $this->assertEquals("test123", $this->user->getPassword());
        $this->assertNotEquals("test123", $this->otheruser->getPassword());
    }

    public function testGetEmail() : void {
        $this->assertEquals("test123@gmail.com", $this->user->getEmail());
        $this->assertNotEquals("test123@gmail.com", $this->otheruser->getEmail());
    }

    public function testGetRole() : void {
        $this->assertEquals("Student", $this->user->getRole());
        $this->assertNotEquals("Student", $this->otheruser->getRole());
    }

    public function testGetCodes() : void {
        $this->assertSame([10, 20, 30], $this->user->getCodes());
        $this->assertNotSame([10, 20, 30], $this->otheruser->getCodes());
    }

    public function testSetId() : void {
        $expected = 2;
        $this->user->setId($expected);
        $this->assertSame($expected, $this->user->getId());
    }

    public function testSetLogin() : void {
        $expected = "test2";
        $this->user->setLogin($expected);
        $this->assertSame($expected, $this->user->getLogin());
    }

    public function testSetPassword() : void {
        $expected = "newPassword123";
        $this->user->setPassword($expected);
        $this->assertSame($expected, $this->user->getPassword());
    }

    public function testSetEmail() : void {
        $expected = "test1234@gmail.com";
        $this->user->setEmail($expected);
        $this->assertSame($expected, $this->user->getEmail());
    }

    public function testSetRole() : void {
        $expected = "Teacher";
        $this->user->setRole($expected);
        $this->assertSame($expected, $this->user->getRole());
    }

    public function testSetCodes() : void {
        $expected = [40, 50, 60];
        $this->user->setCodes($expected);
        $this->assertSame($expected, $this->user->getCodes());
    }

    public function testJsonSerialize() : void {
        $this->assertEquals(['id' => 1, 'name' => 'test1'], $this->user->jsonSerialize());
    }

}