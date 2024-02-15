<?php
namespace Models;

class Room{

    private string $name;
    private string $motifLock;
    private string $endLockDate;
    private bool $isSalleMachine;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->isSalleMachine = false;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isAvailableAt($time){
        $isAvailable = true;
        $codeAde = ['8382','8380','8383','8381','8396','8397','8398','42523','42524','42525'];
        foreach($codeAde as $code){
            $weeklySchedule = new WeeklySchedule($code);
            foreach ($weeklySchedule->getDailySchedules() as $dailySchedule){
                if($dailySchedule->getDate() != date('Ymd')) continue;
                foreach ($dailySchedule->getCourseList() as $course){
                    if($course == null) continue;
                    if(strpos($course->getLocation(), $this->getName()) !== false){
                        $heureDebutCours = strtotime(str_replace('h',':',$course->getHeureDeb()));
                        $heureFinCours = strtotime(str_replace('h',':',$course->getHeureFin()));
                        if($heureDebutCours < $time && $heureFinCours > $time){
                            $isAvailable = false;
                        }
                    }
                }
            }
        }
        return $isAvailable;
    }

    public function isAvailable(){
        return $this->isAvailableAt(strtotime(date('G:i')));
    }

    public function isLocked(){
        $roomRepository = new RoomRepository();
        if($roomRepository->isRoomLocked($this->getName())){
            $motif = $roomRepository->getMotifLock($this->getName());
            $this->setMotifLock($motif[0]);
            $this->setEndLockDate($motif[1]);
            return true;
        }
        return false;
    }

    public function getAllCourseBetween($startTime, $endTime){
        $courseList = [];
        $codeAde = ['8382','8380','8383','8381','8396','8397','8398','42523','42524','42525'];

        foreach($codeAde as $code){
            $weeklySchedule = new WeeklySchedule($code);
            foreach ($weeklySchedule->getDailySchedules() as $dailySchedule){
                foreach ($dailySchedule->getCourseList() as $course){
                    // TODO
                }
            }
        }
    }

    public function getMotifLock(): string
    {
        return $this->motifLock;
    }

    public function setMotifLock(string $motifLock): void
    {
        $this->motifLock = $motifLock;
    }

    public function getEndLockDate(): string
    {
        return $this->endLockDate;
    }

    public function setEndLockDate(string $endLockDate): void
    {
        $this->endLockDate = $endLockDate;
    }

    public function isSalleMachine(): bool
    {
        return $this->isSalleMachine = in_array($this, (new RoomRepository())->getAllComputerRooms());
    }




}
