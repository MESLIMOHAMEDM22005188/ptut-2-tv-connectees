<?php

/**
 * Plugin Name:       Ecran connecté AMU
 * Plugin URI:        https://github.com/thomas-cardon/plugin-ecran-connecte
 * Description:       Plugin écrans connectés de l'AMU, ce plugin permet de générer des fichiers ICS. Ces fichiers sont ensuite lus pour pouvoir afficher l'emploi du temps de la personne connectée. Ce plugin permet aussi d'afficher la météo, des informations, des alertes. Tant en ayant une gestion des utilisateurs et des informations.
 * Version:           1.2.9
 * License:           GNU General Public License v2
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ecran-connecte
 * GitHub Plugin URI: https://github.com/thomas-cardon/ptut-2-tv-connectees
 */

use Controllers\AlertController;
use Controllers\CodeAdeController;
use Controllers\InformationController;
use Models\CodeAde;
use Models\User;

if (!defined('ABSPATH')) {
	exit(1);
}

define('TV_PLUG_PATH', '/wp-content/plugins/ptut-2-tv-connectees/');
define('TV_UPLOAD_PATH', '/wp-content/uploads/media/');
define('TV_ICSFILE_PATH', '/wp-content/uploads/fileICS/');

require __DIR__ . '/autoload.php';
require_once __DIR__ . '/vendor/autoload.php';

require 'init.php';
require 'virtual-pages.php';
require 'blocks.php';

// Upload schedules
$dl1 = filter_input(INPUT_POST, 'updatePluginEcranConnecte');
$dl2 = filter_input(INPUT_POST, 'dlEDT');

if(isset($dl1) || isset($dl2)) {
    include_once(ABSPATH . 'wp-includes/pluggable.php');

    if(members_current_user_has_role('administrator') || members_current_user_has_role('secretaire'))
	    downloadFileICS_func();
}

function add_cors_http_header(){
    header("Access-Control-Allow-Origin: *");
}
add_action('init','add_cors_http_header');


/**
 * Function for WPCron
 * Upload schedules
 */
function downloadFileICS_func()
{
    move_fileICS_schedule();

	$controllerAde = new CodeAdeController();
    $model = new CodeAde();

    $codesAde = $model->getList();
    foreach ($codesAde as $codeAde) {
        $controllerAde->addFile($codeAde->getCode());
    }

    updateTeacherRoomDB();

	/*
    $information = new InformationController();
    $information->registerNewInformation();

    $alert = new AlertController();
    $alert->registerNewAlert();
	*/
}

function updateTeacherRoomDB(){
    $codeAde = ['8382','8380','8383','8381','8396','8397','8398','42523','42524','42525'];
    $teacherModel = new \Models\Teacher();
    $roomModel = new \Models\RoomRepository();
    $courseModel = new \Models\CourseRepository();
    foreach ($codeAde as $code){
        $schedule = new \Models\WeeklySchedule($code);
        foreach ($schedule->getDailySchedules() as $dailySchedule){
            foreach ($dailySchedule->getCourseList() as $course){
                if($course == null) continue;
                $teacherName = preg_split('/\n/', $course->getTeacher())[1];

                if(!$roomModel->exist($course->getLocation())){
                    if(strlen($course->getLocation()) < 10){
                        $roomModel->add($course->getLocation());
                    }
                }

                if(!$teacherModel->exist($teacherName)){
                    if(strlen($teacherName) > 6){
                        $teacherModel->add($teacherName);
                    }
                }

                $course = preg_replace('/(TD)|(TP)|(G[0-9].?)|(\*)|(|(A$|B$)|)|(G..$)|(G.-.)|(G..-.$)|(G$)/','',$course->getSubject());
                $course = rtrim($course);
                if(!$courseModel->exist(str_replace("'","''",$course))){
                    $courseModel->add($course,'#666666');
                }
            }
        }
    }
}

add_action('downloadFileICS', 'downloadFileICS_func');

/**
 * Upload the schedule of users
 *
 * @param $users    User[]
 */
function downloadSchedule($users)
{
    $controllerAde = new CodeAdeController();
    foreach ($users as $user) {
        foreach ($user->getCodes() as $code) {
            $controllerAde->addFile($code->getCode());
        }
    }
}

/**
 * Change place of file
 */
function move_fileICS_schedule()
{
    if ($myFiles = scandir(PATH . TV_ICSFILE_PATH . 'file3')) {
        foreach ($myFiles as $myFile) {
            if (is_file(PATH . TV_ICSFILE_PATH . 'file3/' . $myFile)) {
                wp_delete_file(PATH . TV_ICSFILE_PATH . 'file3/' . $myFile);
            }
        }
    }
    if ($myFiles = scandir(PATH . TV_ICSFILE_PATH . 'file2')) {
        foreach ($myFiles as $myFile) {
            if (is_file(PATH . TV_ICSFILE_PATH . 'file2/' . $myFile)) {
                copy(PATH . TV_ICSFILE_PATH . 'file2/' . $myFile, PATH . TV_ICSFILE_PATH . 'file3/' . $myFile);
                wp_delete_file(PATH . TV_ICSFILE_PATH . 'file2/' . $myFile);
            }
        }
    }

    if ($myFiles = scandir(PATH . TV_ICSFILE_PATH . 'file1')) {
        foreach ($myFiles as $myFile) {
            if (is_file(PATH . TV_ICSFILE_PATH . 'file1/' . $myFile)) {
                copy(PATH . TV_ICSFILE_PATH . 'file1/' . $myFile, PATH . TV_ICSFILE_PATH . 'file2/' . $myFile);
                wp_delete_file(PATH . TV_ICSFILE_PATH . 'file1/' . $myFile);
            }
        }
    }

    if ($myFiles = scandir(PATH . TV_ICSFILE_PATH . 'file0')) {
        foreach ($myFiles as $myFile) {
            if (is_file(PATH . TV_ICSFILE_PATH . 'file0/' . $myFile)) {
                copy(PATH . TV_ICSFILE_PATH . 'file0/' . $myFile, PATH . TV_ICSFILE_PATH . 'file1/' . $myFile);
                wp_delete_file(PATH . TV_ICSFILE_PATH . 'file0/' . $myFile);
            }
        }
    }
}

require_once 'register-dashboard-forms.php';