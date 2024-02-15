<?php

namespace Models;

use ICal\Event;

class DailySchedule{

    /**
     * @var Course[]
     */
    private array $courseList;
    private string $date;
    private string $group;

    public function __construct($date,$group = '')
    {
        $this->date = date('Ymd',$date);
        $this->courseList = array();
        $this->group = $group;
    }



    public function addExistingCourse($course){
        $this->courseList[] = $course;
    }

    public function addCourse($event){
        $professeur = '';
        $location = '';
        $duration = str_replace(':', 'h', date("H:i", strtotime($event['deb']))) . ' - ' . str_replace(':', 'h', date("H:i", strtotime($event['fin'])));
        $group = '';
        $label = str_replace(array(' (INFO)', ' G1', ' G2', ' G3', ' G4', ' 4h', ' 2h', '*'), '', $event['label']);

        /** Améliore les strings pour l'affichage */
        if(isset($event['description'])){
            $professeur = substr($event['description'], 0, -30);
            $professeur = preg_split('/(Groupe [1-9].?)|G[1-9].? |.?[0-9](ère|ème) (A|a)nnée.?|an[1-3]|[A-B].?-[1-3]/',$professeur);
            $professeur = $professeur[sizeof($professeur)-1];

            $group = preg_split('/' . $professeur . '/',$event['description']);
            $group = preg_replace(array('/G1/','/G2/','/G3/','/G4/'), array('Groupe 1', 'Groupe 2', 'Groupe 3', 'Groupe 4'),$group[0]);
            $group = str_replace(array('an1','an2','an3'),'',$group);
            $group = $this->initGroupName($group);
        }

        if(isset($event['location'])){
            $location = $event['location'];
        }

        $this->courseList[] = new Course($label, $professeur, $location, $duration, $group);
    }

    public function initGroupName($profName) : string{
        $description = preg_replace('/\s+/', ' ', $profName);
        $descriptionSplit = explode(' ', $description);
        $description = '';

        /* Enèeve le problème des suites de chiffre dans la chaine de caractère */
        foreach ($descriptionSplit as $descriptionPart){
            if(is_numeric($descriptionPart) && intval($descriptionPart) > 1000) continue;
            $description .= $descriptionPart . ' ';
        }

        return $description;
    }

    /** Tri la liste de cours par horraire */
    public function orderCourse(){
        usort($this->courseList, fn($a,$b) =>  strtotime(str_replace('h',':',$a->getHeureDeb())) >
                                                      strtotime(str_replace('h',':',$b->getHeureDeb())));
    }

    /**
     * @return mixed
     */
    public function getCourseList()
    {
        $this->orderCourse();
        if(sizeof($this->courseList) == 0) return [];
        $courseList = $this->courseList;
        $dailyScheduleWithPause = [];
        $listeHorraireDebut = ["8:15","9:15","10:40","11:10","13:30","14:35","15:30","16:25"];
        $indexHorraire = 0;
        $indexCourse = 0;
        while($indexHorraire < sizeof($listeHorraireDebut) && $indexHorraire < 8){ // Tant que l'edt n'est pas fini
            if($indexCourse >= sizeof($courseList)){ // S'il n'y a pas de nouveau cours a ajouté
                $dailyScheduleWithPause[] = null;
                $indexHorraire++;
                continue;
            }

            $heureDebutCours = strtotime(str_replace('h',':',$courseList[$indexCourse]->getHeureDeb()));
            $heureFinCours = strtotime(str_replace('h',':', $courseList[$indexCourse]->getHeureFin()));

            if($heureDebutCours >= strtotime("12:15") && $heureFinCours <= strtotime("13:30")){
                $indexCourse++;
                continue;
            }
            if($heureDebutCours <= strtotime($listeHorraireDebut[$indexHorraire])){ // Le cours commence a cet horaire
                if($indexCourse != sizeof($courseList) - 1 ){

                    /* Verification si le cours est en demi groupe */
                    if($courseList[$indexCourse]->getHeureDeb() == $courseList[$indexCourse + 1]->getHeureDeb()){
                        $courseList[$indexCourse]->setIsDemiGroupe(true);
                        $courseList[$indexCourse + 1]->setIsDemiGroupe(true);
                    }
                }

                if(!$courseList[$indexCourse]->isDemiGroupe() || $courseList[$indexCourse + 1] == null){
                    $indexHorraire += $courseList[$indexCourse]->getDuration();
                }
                else if($courseList[$indexCourse] != null && $courseList[$indexCourse + 1]  != null && $courseList[$indexCourse]->getHeureDeb() != $courseList[$indexCourse + 1]->getHeureDeb()){
                    $indexHorraire += $courseList[$indexCourse]->getDuration();
                }

                /* Ajout du cours dans la liste des cours */
                $dailyScheduleWithPause[] = $courseList[$indexCourse];
                $indexCourse++;

            }else{ // Le cours n'a pas encore commencé on ajoute un cours vide
                $dailyScheduleWithPause[] = null;
                $indexHorraire++;
            }
        }


        return $dailyScheduleWithPause;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    public function getTest(){
        return $this->courseList;
    }




}
