<?php

namespace Views;


use Models\Teacher;
use Models\User;

/**
 * Class TeacherView
 *
 * Contain all view for teacher (Forms, tables)
 *
 * @package Views
 */
class TeacherView extends UserView
{

    /**
     * Display a creation form
     */
    public function displayInsertImportFileTeacher() {
        return '
        <h2>Compte enseignant</h2>
        <p class="lead">Pour créer des enseignants, commencer par télécharger le fichier Excel en cliquant sur le lien ci-dessous.</p>
        <p class="lead">Remplissez les colonnes par les valeurs demandées, une ligne est égale à un utilisateur.</p>
        <p class="lead">Le code demandé est son code provenant de l\'ADE, pour avoir ce code, suivez ce petit tutoriel :</p>
        <ul>
            <li><p class="lead">Connectez vous sur l\'ADE</p></li>
            <li><p class="lead">...</p></li>
        </ul>
        <p class="lead">Lorsque vous avez remplis le fichier Excel, enregistrez le et cliquez sur "Parcourir" et sélectionnez votre fichier.</p>
        <p class="lead">Pour finir, validez l\'envoie du formulaire en cliquant sur "Importer le fichier"</p>
        <p class="lead">Lorsqu\'un enseignant est inscrit, un email lui est envoyé contenant son login et son mot de passe avec un lien du site.</p>
        <a href="' . URL_PATH . TV_PLUG_PATH . 'public/files/Ajout Profs.xlsx" download="Ajout Prof.xlsx">Télécharger le fichier excel ! </a>
        <form id="Prof" method="post" enctype="multipart/form-data">
            <input type="file" name="excelProf" class="inpFil" required=""/>
            <button type="submit" class="btn btn-primary" name="importProf" value="Importer">Importer le fichier</button>
        </form>';
    }

    /**
     * Display form to modify a teacher
     *
     * @param $user   User
     *
     * @return string
     */
    public function modifyForm($user) {
        return '
        <a href="' . home_url('/users/list') . '">< Retour</a>
        <h2>' . $user->getLogin() . '</h2>
        <form method="post">
            <label for="modifCode">Code ADE</label>
            <input type="text" class="form-control" id="modifCode" name="modifCode" placeholder="Entrer le Code ADE" value="' . $user->getCodes()[0]->getCode() . '" required="">
            <button name="modifValidate" class="btn btn-primary" type="submit" value="Valider">Valider</button>
            <a href="' . $linkManageUser . '">Annuler</a>
        </form>';
    }

    /**
     * Display all teachers in a table
     *
     * @param $teachers    User[]
     *
     * @return string
     */
    public function displayTableTeachers($teachers) {
        $title = '<b>Rôle affiché: </b> Enseignant';
        $header = ['Numéro ENT', 'Code ADE', 'Modifier'];

        $row = array();
        $count = 0;
        foreach ($teachers as $teacher) {
            ++$count;
            $row[] = [$count, $this->buildCheckbox($name, $teacher->getId()), $teacher->getLogin(), $teacher->getCodes()[0]->getCode(), add_query_arg('id', $teacher->getId(), home_url('/users/edit'))];
        }

        return $this->displayTable('Teacher', $title, $header, $row, 'teacher', '<a type="submit" class="btn btn-primary" role="button" aria-disabled="true" href="' . home_url('/creer-utilisateur') . '">Créer</a>');
    }

    public function displayTeacherSearchSchedule() : string{
        $model = new Teacher();
        $str = '
        <section id="search-container">
            <img id="profil-picture" alt="profil image" src="https://cdn-icons-png.flaticon.com/512/9706/9706640.png">
            <h2>Recherchez votre emploi du temps</h2>
            <div id="search-bar">
            <form method="post" action="' . home_url('/secretary/teacher-schedule') . '">         
                <input type="text" placeholder="Nom..." list="teacher-name" name="teacherName">
                <datalist id="teacher-name">';

        foreach ($model->getTeacherList() as $teacherName){
            $str .= '<option>' . $teacherName . '</option>';
        }

        $str .= '</datalist>        
                 <button id="search-btn" type="submit">
                     <img id="loupe" src="https://cdn-icons-png.flaticon.com/512/694/694985.png" alt="loupe">
                 </button>
            </form>
            </div>
        </section>';
        return $str;
    }

    public function displayTeacherDailySchedule($dailySchedule){
        $view =
        '<div class="container-body">
            <div class="container-horaire">
                <h3 id="text-horaire">8h15 - 10h15</h3>
            </div>
            <div class="container-horaire">
                <h3 id="text-horaire">10h15 - 12h15</h3>
            </div>
            <div class="container-horaire">
                <h3 id="text-horaire">13h30 - 15h15</h3>
            </div>
            <div class="container-horaire">
                <h3 id="text-horaire">15h30 - 17h30</h3>
            </div>';

        $courseList = $dailySchedule->getCourseList();
        if($courseList == []){
            $view .= '<h3 style="grid-column: span 8; justify-self: center; font-size: 32px"> Pas de cours aujourd\'hui</h2>';
        }

        foreach ($courseList as $course) {
            if ($course != null) {
                $view .= '<div class="container-matiere green" style="grid-column: span ' . $course->getDuration() . '">
                            <p class="text-matiere">' . $course->getSubject() . '</p>
                            <p class="text-prof">' . $course->getTeacher() . '</p>
                            <p class="text-salle">' . $course->getLocation() . '</p>
                        </div>';
            }else{
                $view .= '<div></div>';
            }

        }

        $view .= '</div>';

        return $view;
    }

    public function displaySalleMachineConfig($roomList) : string{
        $view = '<form class=all-room-container method="post">';
        $index = 0;
        foreach ($roomList as $room){
            $view .= '<div class="room-container">
                        <p>' . $room->getName() . '</p>
                        <input name="hidden[' . $index . ']' . '" type="hidden" value="' . $room->getName() . '"' . '</input>
                        <input name="check[' . $index . ']' . '" type=checkbox ';
            if($room->isSalleMachine()){
                $view .= 'checked';
            }
            $view .= '      
                    ></div>';
            ++$index;
        }
        return $view . '<input type="submit"></form>';
    }

}