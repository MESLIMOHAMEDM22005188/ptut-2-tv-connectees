<?php

namespace Controllers;

use Models\CodeAde;
use Models\DailySchedule;
use Models\Room;
use Models\RoomRepository;
use Models\User;
use Models\WeeklySchedule;
use Views\SecretaryView;
use Views\TeacherView;
use Models\RoomDetails;

class RoomController extends UserController {

    private $view;

    public function __construct()
    {
        parent::__construct();
        $this->view = new SecretaryView();
    }

    /**
     * @return mixed|string
     */
    public function displayContent() {
        return $this->displayRoomWeeklySchedule();
    }

    /** Renvois l'emploi du temps d'une salle
     * @param $roomName
     * @return array
     */
    public function getRoomDailyScheduleList($roomName){
        $codeAde = (new CodeAde())->getAllAdeCode();
        $roomDailyScheduleList = array_fill(0, sizeof((new WeeklySchedule($codeAde[0]))->getDailySchedules()), null); // Assurez-vous que cela reflète le nombre correct de jours

        foreach($codeAde as $code){
            $weeklySchedule = new WeeklySchedule($code);
            for($i = 0; $i < sizeof($weeklySchedule->getDailySchedules()); ++$i){
                if($roomDailyScheduleList[$i] === null){
                    $roomDailyScheduleList[$i] = new DailySchedule($weeklySchedule->getDailySchedules()[$i]->getDate());
                }
                $dailySchedule = $weeklySchedule->getDailySchedules()[$i];
                foreach($dailySchedule->getCourseList() as $course){
                    if($course === null){
                        continue;
                    }
                    if(strpos($course->getLocation(), $roomName) !== false){ // Cours dans la salle recherchée
                        if(!in_array($course, $roomDailyScheduleList[$i]->getCourseList())) {
                            $roomDailyScheduleList[$i]->addExistingCourse($course);
                        }
                    }
                }
            }
        }
        return $roomDailyScheduleList;
    }


    public function displayRoomWeeklySchedule(){
        if(isset($_POST['roomName'])){
            $roomName = $_POST['roomName'];
            $_SESSION['roomName'] = $roomName;
        }

        if(!isset($_SESSION['roomName'])){
            return $this->displayRoomChoicePage();
        }

        $roomName = $_SESSION['roomName'];
        $dailyScheduleList = $this->getRoomDailyScheduleList($roomName);
        return (new SecretaryView())->displayComputerRoomSchedule($dailyScheduleList);
    }

    public function displayRoomChoicePage() : string{
        $model = new RoomRepository();
        return (new SecretaryView())->displayRoomChoice($model->getAllRoom());
    }

    public function displayRoomLockForm(){
        if(!isset($_POST['roomName'])) return;
        $roomName = $_POST['roomName'];
        $view = new SecretaryView();
        return $view->displayRoomLock($roomName);
    }

    public function lockRoom(){
        if(!isset($_POST['roomName'])) return;
        $roomName = $_POST['roomName'];
        $endDate = str_replace('T',' ',$_POST['endDate']);
        $motif = $_POST['motif'];
        $model = new RoomRepository();
        $model->lockRoom($roomName, $motif, $endDate);
        return "<script>location.href = '". home_url('/secretary/computer-rooms') . "'</script>";
    }

    public function unlockRoom(){
        if(!isset($_POST['roomName'])) return;
        $roomName = $_POST['roomName'];
        $model = new RoomRepository();
        $model->unlockRoom($roomName);
        return "<script>location.href = '". home_url('/secretary/computer-rooms') . "'</script>";
    }

    public function displayComputerRoomConfig() {
        if (isset($_POST['check'])) {
            $this->updateComputerRooms();
        }

        // Récupère la liste des salles machines
        $roomList = (new RoomRepository())->getAllComputerRooms();

        // Filtrer les salles pour exclure celles sans nom
        $roomList = array_filter($roomList, function($room) {
            return !empty($room->getName());
        });

        // Éliminer les doublons basé sur le nom de la salle
        $roomList = array_reduce($roomList, function($carry, $room) {
            if (!isset($carry[$room->getName()])) {
                $carry[$room->getName()] = $room;
            }
            return $carry;
        }, []);

        // Convertir le tableau associatif en tableau indexé
        $roomList = array_values($roomList);

        // Tri par nom
        usort($roomList, fn($a, $b) => strcmp($a->getName(), $b->getName()));

        return (new TeacherView())->displaySalleMachineConfig($roomList);
    }



    /** Met a jours les salles machines
     * @return void
     */
    public function updateComputerRooms(){
        $model = new RoomRepository();
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['check']) && is_array($_POST['check']) && isset($_POST['hidden']) && is_array($_POST['hidden'])) {
            $model->resetComputerRoomCheck();
            foreach ($_POST['check'] as $index => $checkValue) {
                $hiddenName = isset($_POST['hidden'][$index]) ? $_POST['hidden'][$index] : '';
                $model->updateComputerRoom($hiddenName, 1);
            }
        }
    }


    public function addComputerRooms() {
        if (!empty($_POST['computerRooms'])) {
            $roomRepository = new RoomRepository();
            foreach ($_POST['computerRooms'] as $roomName) {
                $roomRepository->updateComputerRoom($roomName, 1);
            }
        }
    }

    public function getRoomDetails($roomName) {
        $filePath = __DIR__ . '../../data/ROOM-DETAILS.xlsx';
        $roomDetails = RoomDetails::getFromExcel($roomName, $filePath);

        header('Content-Type: application/json');
        if ($roomDetails) {
            echo json_encode($roomDetails);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Room not found']);
        }
    }
}
