<?php

namespace Views;


use WP_User;

/**
 * Class ICSView
 *
 * Display the schedule
 *
 * @package Views
 */
class ICSView extends View
{
    /**
     * ADisplay the schedule
     *
     * @param $ics_data     array
     * @param $title        string
     *
     * @return bool
     */
    public function displaySchedule($ics_data, $title, $allDay) {
        $current_user = wp_get_current_user();
        if (isset($ics_data['events'])) {
            $string = '<h1 class="group-title">' . $title . '</h1>';
            $current_study = 0;
            foreach (array_keys((array)$ics_data['events']) as $year) {
                for ($m = 1; $m <= 12; $m++) {
                    $month = $m < 10 ? '0' . $m : '' . $m;
                    if (array_key_exists($month, (array)$ics_data['events'][$year])) {
                        foreach ((array)$ics_data['events'][$year][$month] as $day => $day_events) {
                            // HEADER
                            if ($current_study > 9) {
                                break;
                            }
                            if ($allDay) {
                                if ($day == date('j')) {
                                    $string .= $this->displayStartSchedule($current_user);
                                }
                            } else if (in_array('television', $current_user->roles) || in_array('technicien', $current_user->roles)) {
                                if ($day == date('j')) {
                                    $string .= $this->displayStartSchedule($current_user);
                                }
                            } else {
                                $string .= $this->giveDate($day, $month, $year);
                                $string .= $this->displayStartSchedule($current_user);
                            }
                            foreach ($day_events as $day_event => $events) {
                                foreach ($events as $event) {
                                    // CONTENT
                                    if ($allDay) {
                                        if ($day == date('j')) {
                                            if ($current_study > 9) {
                                                break;
                                            }
                                            if ($this->getContent($event)) {
                                                ++$current_study;
                                                $string .= $this->getContent($event);
                                            }
                                        }
                                    } else {
                                        if (in_array('television', $current_user->roles) || in_array('technicien', $current_user->roles)) {
                                            if ($day == date('j')) {
                                                if ($current_study > 9) {
                                                    break;
                                                }
                                                if ($this->getContent($event)) {
                                                    ++$current_study;
                                                    $string .= $this->getContent($event);
                                                }
                                            }
                                        } elseif (in_array('enseignant', $current_user->roles) || in_array('directeuretude', $current_user->roles)
                                            || in_array('etudiant', $current_user->roles)) {
                                            if ($current_study > 9) {
                                                break;
                                            }
                                            if ($this->getContent($event)) {
                                                ++$current_study;
                                                $string .= $this->getContent($event, $day);
                                            }
                                        } else {
                                            if ($current_study > 9) {
                                                break;
                                            }
                                            if ($day == date('j')) {
                                                if ($current_study > 9) {
                                                    break;
                                                }
                                                if ($this->getContent($event)) {
                                                    ++$current_study;
                                                    $string .= $this->getContent($event);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            // FOOTER
                            if (in_array('television', $current_user->roles) || in_array('technicien', $current_user->roles)) {
                                if ($day == date('j')) {
                                    $string .= $this->displayEndSchedule();
                                }
                            } else {
                                $string .= $this->displayEndSchedule();
                            }
                        }
                    }

                }
            }
            // IF NO SCHEDULE
            if ($current_study === 0) return '';
        } else return '';

        return $string;
    }

    /**
     * Display the header
     *
     * @param $current_user     WP_User
     *
     * @return string
     */
    public function displayStartSchedule($current_user) {
        $string = '<div class="table-responsive">
                   	<table class="table tabSchedule schedule">
                    	<thead class="headerTab">
                        	<tr>
                            	<th scope="col" class="text-center">Horaire</th>';
        if (!in_array("technicien", $current_user->roles)) {
            $string .= '<th scope="col" class="text-center">Cours</th>
                        <th scope="col" class="text-center">Groupe</th>
                        <th scope="col" class="text-center">Enseignant</th>';
        }
        $string .= '<th scope="col" class="text-center">Salle</th>
                 </tr>
              </thead>
           <tbody>';

        return $string;
    }

    /**
     * Give the date of the schedule
     *
     * @param $day
     * @param $month
     * @param $year
     *
     * @return string
     */
    public function giveDate($day, $month, $year) {
        $day_of_week = $day + 1;

        return '<h2>' . date_i18n('l j F', mktime(0, 0, 0, $month, $day_of_week, $year)) . '</h2>';
    }

    /**
     * Give the content of an event
     *
     * @param $event
     * @param int $day
     *
     * @return bool|string
     */
    public function getContent($event, $day = 0) {
        if ($day == 0) {
            $day = date('j');
        }

        $time = date("H:i");
        $duration = str_replace(':', 'h', date("H:i", strtotime($event['deb']))) . '<br />' . str_replace(':', 'h', date("H:i", strtotime($event['fin'])));
        if ($day == date('j')) {
            if (date("H:i", strtotime($event['deb'])) <= $time && $time < date("H:i", strtotime($event['fin']))) {
                $active = true;
            } else {
                $active = false;
            }
        }

        if (substr($event['label'], -3) == "alt") {
            $label = substr($event['label'], 0, -3);
        } else {
            $label = $event['label'];
        }
        $description = substr($event['description'], 0, -30);
        $description = preg_replace('/\s+/', ' ', $description);
        $descriptionSplit = explode(' ', $description);
        $description = '';

        /* Remove occasional number problem in the professor name */
        foreach ($descriptionSplit as $descriptionPart){
            if(is_numeric($descriptionPart) && intval($descriptionPart) > 1000) continue;
            $description .= $descriptionPart . ' ';
        }



        if (!(date("H:i", strtotime($event['fin'])) <= $time) || $day != date('j')) {
            $current_user = wp_get_current_user();
            if (in_array("technicien", $current_user->roles)) {
                return $this->displayLineSchedule([$duration, $event['location']], $active);
            } else {
                return $this->displayLineSchedule([$duration, $label,$description, $event['location']], $active);
            }
        }

        return false;
    }

    /**
     * Create a line for the schedule
     *
     * @param $datas
     * @param bool $active
     *
     * @return string
     */
    public function displayLineSchedule($datas, $active = false) {
        if ($active) {
            $string = '<tr class="table-success">';
        } else {
            $string = '<tr>';
        }
        
         /* Nettoyage données ADE */
        foreach ($datas as $key => $data) {
            if ($key === 1) {
              $data = str_replace(array(' (INFO)', ' G1', ' G2', ' G3', ' G4', ' 4h', ' 2h', '*'), '', $data);
              $string .= '<td class="text-center">' . $data . '</td>';

            }
            elseif ($key === 2) {
              $professeur = preg_split('/(Groupe [1-9].?)|G[1-9].? |.?[0-9](ère|ème) (A|a)nnée.?|an[1-3]|[A-B].?-[1-3]/',$data);
              $professeur = $professeur[sizeof($professeur)-1];
              $group = preg_split('/' . $professeur . '/',$data);
              $group = preg_replace(array('/G1/','/G2/','/G3/','/G4/'), array('Groupe 1', 'Groupe 2', 'Groupe 3', 'Groupe 4'),$group[0]);
              $group = str_replace(array('an1','an2','an3'),'',$group);

              $string .= '<td class="text-center">' . $group . '</td>';
              $string .= '<td class="text-center">' . $professeur . '</td>';
            }
            else $string .= '<td class="text-center">' . $data . '</td>';
        }

        return $string . '</tr>';
    }

    /**
     * Display the footer of the schedule
     *
     * @return string
     */
    public function displayEndSchedule() {
        return '</tbody>
             </table>
          </div>';
    }

}
