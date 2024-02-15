<?php

namespace Controllers;

use Models\CourseRepository;
use Views\SecretaryView;

class CourseController extends Controller{

    public function modifyColors()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['color']) && is_array($_POST['color']) && isset($_POST['hidden']) && is_array($_POST['hidden'])) {
            foreach ($_POST['color'] as $index => $colorValue) {
                $hiddenName = isset($_POST['hidden'][$index]) ? $_POST['hidden'][$index] : '';
                $this->updateCourseColor($hiddenName, $colorValue);
            }
        }
    }

    public function updateCourseColor($courseName, $color){
        $model = new CourseRepository();
        $model->modifyColor($courseName, $color);
    }
}
