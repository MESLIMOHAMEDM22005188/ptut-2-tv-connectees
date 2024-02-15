<?php

use Models\DailySchedule;
use Models\Course;

use PHPUnit\Framework\TestCase;

class DailyScheduleTest extends TestCase {

    private $dailyschedule;

    public function setUp() : void {
        $this->dailyschedule = new DailySchedule(strtotime('2024-01-22'), '');
    }

    public function testGetDate()
    {
        $this->assertEquals(date('Ymd', strtotime('2024-01-22')), $this->dailyschedule->getDate());
    }

    public function testAddCourse()
    {
        $event = [
            'deb' => '08:15',
            'fin' => '12:15',
            'duration' => '2h',
            'label' => 'Cours de C++ (INFO) G1 4h',
            'description' => 'Gauthier Paul Groupe 1 2ème Année an2 A-1',
            'location' => 'I-004'
        ];

        $this->dailyschedule->addCourse($event);
        $this->assertCount(1, $this->dailyschedule->getTest());

        $courses = $this->dailyschedule->getTest();
        $this->assertEquals('Cours de C++', $courses[0]->getSubject());
        $this->assertEquals('Gauthier Paul', $courses[0]->getTeacher());
        $this->assertEquals('I-004', $courses[0]->getLocation());
        $this->assertEquals(4, $courses[0]->getDuration());
    }

    public function testInitGroupName() : void {
        // Test classique
        $this->assertEquals("Legrand Arnaud ", $this->dailyschedule->initGroupName("Legrand Arnaud"));

        // Test avec un nom contenant des numéros
        $this->assertEquals("Legrand Arnaud ", $this->dailyschedule->initGroupName("166524 Legrand Arnaud"));
    }

    public function testGetTest() : void {
        $premierCours = new Course("Mathématiques", "ANNI Samuel", "I-002", "8h15 - 10h15", "G2");
        $this->dailyschedule->addExistingCourse($premierCours);
        $this->assertCount(1, $this->dailyschedule->getTest());
    }

    public function testAddExistingCourse() : void {
        $premierCours = new Course("Mathématiques", "ANNI Samuel", "I-002", "8h15 - 10h15", "G2");
        $deuxiemeCours = new Course("Java", "Lotfi Lakhal", "I-004", "10h30 - 12h15", "G1");
        $troisiemecours = new Course("Crypto", "Lotfi Lakhal", "I-004", "13h30 - 15h15", "G3");
        $this->dailyschedule->addExistingCourse($premierCours);
        $this->dailyschedule->addExistingCourse($deuxiemeCours);
        $this->dailyschedule->addExistingCourse($troisiemecours);
        $this->assertCount(3, $this->dailyschedule->getTest());
    }

}