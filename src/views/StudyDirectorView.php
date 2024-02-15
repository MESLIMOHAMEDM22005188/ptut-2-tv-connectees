<?php

namespace Views;

use Models\User;

/**
 * Class StudyDirectorView
 *
 * Contain all view for study director (Forms, tables)
 *
 * @package Views
 */
class StudyDirectorView extends UserView
{

    /**
     * Display a form for create a study director
     *
     * @return string
     */
    public function displayCreateDirector() {
        return '
        <h2>Compte directeur d\'études</h2>
        <p class="lead">Pour créer des directeurs d\'études, remplissez ce formulaire avec les valeurs demandées.</p>
        <p class="lead">Le code ADE demandé est son code provenant de l\'ADE, pour avoir ce code, suivez le ce trouvant dans la partie pour créer un enseignant.</p>
        <form class="cadre" method="post">
            <div class="form-group">
                <label for="loginDirec">Login</label>
                <input minlength="4" type="text" class="form-control" name="loginDirec" placeholder="Login" required="">
                <small id="passwordHelpBlock" class="form-text text-muted">Votre login doit contenir entre 4 et 25 caractère</small>
            </div>
            <div class="form-group">
                <label for="emailDirec">Email</label>
                <input type="email" class="form-control" name="emailDirec" placeholder="Email" required="">
            </div>
            <div class="form-group">
                <label for="pwdDirec">Mot de passe</label>
                <input type="password" class="form-control" id="pwdDirec" name="pwdDirec" minlength="8" maxlength="25" placeholder="Mot de passe" required="" onkeyup=checkPwd("Direc")>
                <input type="password" class="form-control" id="pwdConfDirec" name="pwdConfirmDirec" minlength="8" maxlength="25" placeholder="Confirmer le Mot de passe" required="" onkeyup=checkPwd("Direc")>
                <small id="passwordHelpBlock" class="form-text text-muted">Votre mot de passe doit contenir entre 8 et 25 caractère</small>
            </div>
            <div class="form-group">
                <label for="codeADEDirec"> Code ADE</label>
                <input type="text" class="form-control" placeholder="Code ADE" name="codeDirec" required="">
            </div>
            <button type="submit" class="btn btn-primary" id="validDirec" name="createDirec" value="Créer">Créer</button>
        </form>';
    }

    /**
     * Display all study directors in a table
     *
     * @param $users    User[]
     *
     * @return string
     */
    public function displayTableStudyDirector($users) {
        $title = '<b>Rôle affiché: </b> Directeur d\'études';
        $header = ['Numéro Ent', 'Code ADE', 'Modifier'];

        $row = array();
        $count = 0;
        foreach ($users as $user) {

            if (sizeof($user->getCodes()) == 0) {
                $code = 'Aucun code';
            } else {
                $code = $user->getCodes()[0]->getCode();
            }

            ++$count;
            $row[] = [$count, $this->buildCheckbox($name, $user->getId()), $user->getLogin(), $code, add_query_arg('id', $user->getId(), home_url('/users/edit'))];
        }

        return $this->displayTable('Direc', $title, $header, $row, 'director', '<a type="submit" class="btn btn-primary" role="button" aria-disabled="true" href="' . home_url('/users/create') . '">Créer</a>');
    }

    /**
     * Display a form to modify the study director
     *
     * @param $user   User
     *
     * @return string
     */
    public function displayModifyStudyDirector($user) {
        $code = 'Aucun code';

        if (sizeof($user->getCodes()) > 0) {
            $code = $user->getCodes()[0]->getCode();
        }

        return '
        <a href="' . home_url('/users/list') . '">< Retour</a>
        <h2>' . $user->getLogin() . '</h2>
        <form method="post">
            <div class="form-group">
                <label for="modifCode">Code ADE</label>
                <input type="text" class="form-control" id="modifCode" name="modifCode" placeholder="Entrer le Code ADE" value="' . $code . '" required="">
            </div>
            <button class="btn btn-primary" name="modifValidate" type="submit" value="Valider">Valider</button>
            <a class="btn btn-danger" href="' . home_url('/users/list') . '">Annuler</a>
        </form>';
    }
}
