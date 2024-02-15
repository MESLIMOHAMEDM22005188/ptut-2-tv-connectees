<?php

namespace Models;
use Controllers\UserController;

class WeeklySchedule
{

    /**
     * @var DailySchedule[]
     */
    private array $dailySchedules;

    public function __construct($code)
    {
        $this->init_schedule($code);
    }

    private function init_schedule($code)
    {
        for ($i = 0; $i < 5; $i++) {
            $this->dailySchedules[] = new DailySchedule(strtotime("monday this week +" . $i . " day"), $code);
        }

        global $R34ICS;
        $R34ICS = new \R34ICS();
        $url = (new UserController())->getFilePath($code);

        $datas = $R34ICS->display_calendar($url, $code, true, array(), true);
        $ics_data = $datas[0];
        $dayOfTheWeek = 0;

        /** Parcours le fichier ICS pour recuperer tous les events */
        if (isset($ics_data['events'])) {
            foreach (array_keys((array)$ics_data['events']) as $year) {
                for ($m = 1; $m <= 12; $m++) {
                    $month = $m < 10 ? '0' . $m : '' . $m;
                    if (array_key_exists($month, (array)$ics_data['events'][$year])) {
                        foreach ((array)$ics_data['events'][$year][$month] as $day => $day_events) {
                            $findDay = false;
                            while(!$findDay && $dayOfTheWeek < 5){
                                if(date("d",strtotime("monday this week +" . $dayOfTheWeek . 'day')) != $day){
                                    $dayOfTheWeek++;
                                }else{
                                    $findDay = true;
                                }
                            }
                            foreach ($day_events as $day_event => $events) {
                                foreach ($events as $event) {
                                    if($this->dailySchedules[$dayOfTheWeek] == null){
                                        continue; // SAMEDI (A implémenter)
                                    }
                                    $this->dailySchedules[$dayOfTheWeek]->addCourse($event);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getDailySchedules(): array
    {
        return $this->dailySchedules;
    }


}