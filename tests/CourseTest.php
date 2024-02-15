<?php

use Models\DailySchedule;
use Models\Course;

use PHPUnit\Framework\TestCase;

class CourseTest extends TestCase {

    private $course;
    private $autrecourse;
    private $troisiemecourse;

    public function setUp() : void {
        $this->course = new Course("Mathématiques", "FOURNIER Alex", "I-004", "08h15 - 10h15", "S3");
        $this->autrecourse = new Course("Java", "PAUL Gauthier", "I-106", "13h30 - 17h30", "S5");
    }

    public function testGetSubject() : void {
        // Cours de mathématiques
        $this->assertEquals("Mathématiques", $this->course->getSubject());

        // Même exemple mais avec un cours de java
        $this->assertEquals("Java", $this->autrecourse->getSubject());
    }

    public function testGetTeacher() : void {
        // Le prof est FOURNIER Alex
        $this->assertEquals("FOURNIER Alex", $this->course->getTeacher());

        // Même exemple mais Gauthier comme prof
        $this->assertEquals("PAUL Gauthier", $this->autrecourse->getTeacher());
    }

    public function testGetLocation() : void {
        // La location est I-004
        $this->assertEquals("I-004", $this->course->getLocation());

        // La location ici est I-106
        $this->assertEquals("I-106", $this->autrecourse->getLocation());

    }

    public function testGetHeureDeb() : void {
        // L'heure de début est 08h15
        $this->assertEquals("08h15", $this->course->getHeureDeb());

        // L'heure de début est 13h30'
        $this->assertEquals("13h30", $this->autrecourse->getHeureDeb());

    }

    public function testGetHeureFin() : void {
        // L'heure de fin est 10h15'
        $this->assertEquals("10h15", $this->course->getHeureFin());

        // L'heure de fin est 17h30'
        $this->assertEquals("17h30", $this->autrecourse->getHeureFin());

    }

    public function testGetGroup() : void {
        // Le groupe est 'S3'
        $this->assertEquals("S3", $this->course->getGroup());

        // Le groupe est 'S5'
        $this->assertEquals("S5", $this->autrecourse->getGroup());
    }

    public function testGetDuration() : void {
        // La durée est de 2h
        $this->assertEquals(2, $this->course->getDuration());

        // La durée est de 4h
        $this->assertEquals(4, $this->autrecourse->getDuration());

    }

    public function testCalcDuration() : void {
        // Cette fonction privée est appelé au constructeur de l'objet Course, de ce fait, getDuration() devrait renvoyer 2
        $this->assertEquals(2, $this->course->getDuration());
    }

}