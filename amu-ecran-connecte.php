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
use Models\RoomRepository;
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
/*
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
                    if(strlen($course->getLocation()) < 20){
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
}*/

function updateTeacherRoomDB(){

    //Nous avons décidé d'utiliser pour avoir toutes les salles une liste statique des salles manquantes qui peut être modifié dynamiquement si une nouvelle salle est rencontré

    $staticRoomList = [
        'Mobile/TD I-214', 'Mobile/TD I-110',
        'Audio I-206',
        'TD I-107', 'TD I-109', 'TD I-111','TD I-205', 'TD I-207', 'TD I-208', 'TD I-209',  'TD I-211', 'TD I-212',
        'TP I-102', 'TP I-106',  'TP I-002', 'TP I-004', 'TP I-009', 'TP I-010', 'TP I-104'
    ];

    $codeAde = ['8382', '8380', '8383', '8381', '8396', '8397', '8398', '42523', '42524', '42525'];
    $roomModel = new \Models\RoomRepository();
    $teacherModel = new \Models\Teacher();
    $courseModel = new \Models\CourseRepository();

    // Ajouter les salles de la liste statique à la base de données
    foreach ($staticRoomList as $roomName) {
        if (!$roomModel->exist($roomName)) {
            $roomModel->add($roomName);
        }
    }


    foreach ($codeAde as $code) {
        $schedule = new \Models\WeeklySchedule($code);
        foreach ($schedule->getDailySchedules() as $dailySchedule) {
            foreach ($dailySchedule->getCourseList() as $course) {
                if (is_null($course)) continue;

                processRoom($course->getLocation(), $roomModel);
                processTeacher($course->getTeacher(), $teacherModel);
                processCourse($course->getSubject(), $courseModel);
            }
        }
    }
}

function processRoom($location, $roomModel){
    // Exclusion de certains noms de salle et gestion des salles multiples
    if (strpos($location, 'Amphi') !== false || strpos($location, '/') !== false) return;
    if (strlen($location) > 20) return; // Limite de longueur ajustée

    if (!$roomModel->exist($location)) {
        $roomModel->add($location);
    }
}

function processTeacher($teacherName, $teacherModel){
    // Nettoyage et vérification supplémentaire ici si nécessaire
    if (strlen($teacherName) > 6 && !$teacherModel->exist($teacherName)) {
        $teacherModel->add($teacherName);
    }
}

function processCourse($courseName, $courseModel){
    // Nettoyage du nom du cours
    $cleanCourseName = preg_replace('/(TD)|(TP)|(G[0-9].?)|(\*)|(|(A$|B$)|)|(G..$)|(G.-.)|(G..-.$)|(G$)/', '', $courseName);
    $cleanCourseName = rtrim($cleanCourseName);

    if (!$courseModel->exist($cleanCourseName)) {
        $courseModel->add($cleanCourseName, '#666666');
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

function my_plugin_enqueue_scripts() {
    // Enregistrement et localisation de room-details.js
    wp_enqueue_script('room-details', plugins_url('public/js/room-details.js', __FILE__), array('jquery'), '1.0.0', true);
    wp_localize_script('room-details', 'roomDetailsAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'), // Notez l'utilisation de 'ajaxurl' tout en minuscules
    ));
}

add_action('wp_enqueue_scripts', 'my_plugin_enqueue_scripts');

add_action('wp_ajax_get_room_details', 'handle_ajax_get_room_details');
add_action('wp_ajax_nopriv_get_room_details', 'handle_ajax_get_room_details');



function handle_ajax_get_room_details() {
    $roomName = isset($_POST['roomName']) ? sanitize_text_field($_POST['roomName']) : '';
    $filePath = plugin_dir_path(__FILE__) . 'data/ROOM-DETAILS.xlsx';
    $roomDetails = Models\RoomDetails::getFromExcel($roomName, $filePath);

    wp_send_json($roomDetails);
}

function mon_plugin_enqueue_scripts() {
    wp_enqueue_script(
        'add-computer-rooms',
        plugins_url('public/js/add-computer-rooms.js', __FILE__),
        array('jquery'),
        '1.0.0',
        true
    );

    // Passer ajaxurl et un nonce à votre script JS pour les appels AJAX.
    wp_localize_script('add-computer-rooms', 'monPluginAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('manage_computer_rooms_nonce') // Crée et passe un nonce pour la vérification
    ));
}
add_action('wp_enqueue_scripts', 'mon_plugin_enqueue_scripts');


function handle_manage_computer_rooms_ajax() {
    // Vérification du nonce pour la sécurité
    check_ajax_referer('manage_computer_rooms_nonce', 'nonce');

    // Extraction du nom de la salle
    if (empty($_POST['roomName'])) {
        wp_send_json_error(['message' => 'La salle n\'est pas spécifiée.']);
        return;
    }
    $roomName = sanitize_text_field($_POST['roomName']);
    $isComputer = isset($_POST['isComputer']) ? (int)$_POST['isComputer'] : 0;


    // Initialisation du repository des salles
    $roomRepository = new Models\RoomRepository();


    // Vérifier si la salle existe avant de tenter de la mettre à jour
    if (!$roomRepository->exist($roomName)) {
        wp_send_json_error(['message' => 'La salle spécifiée n\'existe pas.']);
        return;
    }

    // Mise à jour du statut isComputer de la salle
    $success = $roomRepository->updateComputerRoom($roomName, $isComputer);

    if ($success) {
        $message = $isComputer ? 'La salle a été marquée comme salle informatique.' : 'La salle a été démarquée comme salle informatique.';
        wp_send_json_success(['message' => $message]);
    } else {
        wp_send_json_error(['message' => 'Une erreur est survenue lors de la mise à jour.']);
    }
}
add_action('wp_ajax_manage_computer_rooms_ajax', 'handle_manage_computer_rooms_ajax');





require_once 'register-dashboard-forms.php';