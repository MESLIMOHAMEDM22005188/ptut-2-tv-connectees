<?php

namespace Models;

class CourseRepository extends Model{

    public function add($courseName, $defaultColor){
        $sql = "INSERT INTO secretary_courses(name,color) VALUES (?,?)";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute([$courseName,$defaultColor]);
    }

    public function exist($courseName){
        $sql = "SELECT * FROM secretary_courses WHERE name LIKE '%" . $courseName . "%'";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute([$courseName]);
        if($stmt->fetch()){
            return true;
        }
        return false;
    }

    public function getCourseList(){
        $courseList = [];
        $sql = "SELECT * FROM secretary_courses";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute([]);
        while($row = $stmt->fetch()){
            $course = new Course($row['name']);
            $course->setColor($row['color']);
            $courseList[] = $course;
        }
        return $courseList;
    }

    public function modifyColor($courseName, $courseColor){
        $sql = "UPDATE secretary_courses SET color=? WHERE name LIKE '%" . $courseName . "%'";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute([$courseColor]);
    }

    public function getCourseColor($courseName) : string{
        $sql = "SELECT * FROM secretary_courses WHERE name LIKE '%" . $courseName . "%'";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute([$courseName]);
        if($row = $stmt->fetch()){
            return $row['color'];
        }
        return '#000000';
    }
}