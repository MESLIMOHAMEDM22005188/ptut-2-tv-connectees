<?php

namespace Models;

use Models\User;

class Teacher extends User
{

    public function exist($name) : bool {
        $sql = "SELECT * FROM teacher WHERE name LIKE :name";
        $stmt = self::getConnection()->prepare($sql);
        // Utilisatioxn d'un placeholder pour sécuriser la requête
        $stmt->execute(['name' => "%" . $name . "%"]);
        if($stmt->fetch()){
            return true;
        }
        return false;
    }



    public function add($name) : void {
        if (!$this->exist($name)) {
            $sql = "INSERT INTO teacher(name) VALUES (:name)";
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute(['name' => $name]);
        }
    }

    public function getTeacherList(){
        $teacherList = [];
        $sql = "SELECT DISTINCT name FROM teacher";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute();
        while($row = $stmt->fetch()){
            $teacherList[] = $row['name'];
        }

        return array_unique($teacherList);

    }

    public function getTeacherSchedule($teacherName){
        $codeAde = ['8382','8380','8383','8381','8396','8397','8398','42523','42524','42525'];
        $teacherDailySchedule = new DailySchedule(date('Ymd'));

        foreach($codeAde as $code){
            $weeklySchedule = new WeeklySchedule($code);
            foreach ($weeklySchedule->getDailySchedules() as $dailySchedule){
                if($dailySchedule->getDate() != date('Ymd')) continue;
                foreach ($dailySchedule->getCourseList() as $course){
                    if($course != null && strpos($course->getTeacher(), $teacherName) !== false && !in_array($course,$teacherDailySchedule->getCourseList())){
                        $teacherDailySchedule->addExistingCourse($course);
                    }
                }
            }
        }
        return $teacherDailySchedule;

    }
}