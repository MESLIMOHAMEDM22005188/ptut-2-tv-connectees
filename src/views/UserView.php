<?php

namespace Views;


use Models\CodeAde;
use Models\User;

class UserView extends View
{

    /**
     * Display a creation form
     *
     * @param $name     string
     *
     * @return string
     */
    protected function displayBaseForm($name) {
        return '
            <form method="post" class="cadre">
            	<div class="form-group">
                	<label for="login' . $name . '">Login</label>
                	<input class="form-control" minlength="4" type="text" name="login' . $name . '" placeholder="Login" required="">
                	<small id="passwordHelpBlock" class="form-text text-muted">Votre login doit contenir entre 4 et 25 caractère</small>
                </div>
                <div class="form-group">
                	<label for="email' . $name . '">Email</label>
                	<input class="form-control" type="email" name="email' . $name . '" placeholder="Email" required="">
                </div>
                <div class="form-group">
                	<label for="pwd' . $name . '">Mot de passe</label>
                	<input class="form-control" minlength="8" maxlength="25" type="password" id="pwd' . $name . '" name="pwd' . $name . '" placeholder="Mot de passe" minlength="8" maxlength="25" required="" onkeyup=checkPwd("' . $name . '")>
                    <input class="form-control" minlength="8" maxlength="25" type="password" id="pwdConf' . $name . '" name="pwdConfirm' . $name . '" placeholder="Confirmer le Mot de passe" minlength="8" maxlength="25" required="" onkeyup=checkPwd("' . $name . '")>
                	<small id="passwordHelpBlock" class="form-text text-muted">Votre mot de passe doit contenir entre 8 et 25 caractère</small>
                </div>
                <button type="submit" class="btn btn-primary" id="valid' . $name . '" name="create' . $name . '">Créer</button>
            </form>';
    }

    /**
     * Form for modify the password
     *
     * @return string
     */
    public function displayModifyPassword() {
        return '<div class="container-sm px-5">
            <form id="check" method="post">
                <h2 class="display-6">Modifier le mot de passe</h2>

                <input type="hidden" name="action" value="modify_password" />

                <div class="mb-3">
                  <label for="oldPwd" class="form-label">Votre ancien mot de passe</label>
                  <input type="password" class="form-control text-center" name="old_password" placeholder="Mot de passe" minlength="8" maxlength="25" required="" onkeyup=checkPwd("modify")>
                </div>
                <div class="mb-3">
                  <label for="newPwd" class="form-label">Votre nouveau mot de passe</label>
                  <input type="password" class="form-control text-center" name="new_password" placeholder="Mot de passe" minlength="8" maxlength="25" required="" onkeyup=checkPwd("modify")>
                </div>
                <div class="d-grid">
                  <input class="btn btn-outline-warning" type="submit" value="Modifier" />
                </div>
            </form>
          </div>';
    }

    /**
     * Form to generate a code to delete the account
     *
     * @return string
     */
    public function displayEnterCode() {
      return '<div class="container-sm px-5">
          <form method="post" action="' . admin_url('admin-post.php') . '">
              <h2 class="display-6">Générer un code de suppression</h2>
              <p class="lead">
                Avant de supprimer votre compte, vous devez générer un code de suppression. Il vous suffit
                d\'entrer votre mot de passe actuel pour envoyer le code à l\'adresse email que vous avez indiqué lors de votre inscription.
              </p>

              <input type="hidden" name="action" value="generate_deletion_codes">
              <div class="form-group mb-3">
                <label for="password">Votre mot de passe actuel</label>
                <input type="password" class="form-control text-center" id="password" name="password" placeholder="Mot de passe" required="">
              </div>
              <div class="d-grid">
                <input class="btn btn-outline-danger" type="submit" value="Générer le code" />
              </div>
          </form>
        </div>';
    }

    /**
     * Form to delete the account
     *
     * @return string
     */
    public function displayDeleteAccount() {
      return '<div class="container-sm px-5"
          <form method="post" action="' . admin_url('admin-post.php') . '">
              <h2 class="display-6">Vous partez déjà?</h2>
              <p class="lead">
                Pour supprimer votre compte, vous devez avoir généré un code de suppression via l\'onglet éponyme.
              </p>

              <input type="hidden" name="action" value="delete_me" />

              <div class="form-group mb-3">
                <input type="text" class="form-control text-center" id="deletionCodes" name="deletionCodes" placeholder="Code à rentrer" required />
              </div>
              <div class="d-grid">
                <input class="btn btn-outline-danger" type="submit" value="Supprimer le compte" />
              </div>
          </form></div>';
    }

    /**
     * Display a form to change our own codes
     *
     * @param $codes        CodeAde[]
     * @param $years        CodeAde[]
     * @param $groups       CodeAde[]
     * @param $halfGroups   CodeAde[]
     *
     * @return string
     */

    public function displayModifyMyCodesView($codeArray) {
      $selectedCodes = User::getById()->getCodes(true);
      $codes = '';

      foreach ($codeArray as $code) {
        $codes .= '<option value="' . $code->getId() . '" ' . (isset($selectedCodes, $selectedCodes[$code->getId()]) ? 'selected="true"' : '') . '>' . $code->getTitle() . '</option>';
      }

      $form = '<div class="container-sm px-5">
          <form method="post" action="' . admin_url('admin-post.php') . '">
              <h2 class="display-6">Modifier mon emploi du temps</h2>

              <p class="lead">
                Ce changement sera effectif que pour l\'affichage de l\'emploi du temps en mode TV.
                Pour changer votre emploi du temps sur la PWA, accédez à l\'onglet "Paramètres". Ils seront alors mis à jour automatiquement.
              </p>

              <input type="hidden" name="action" value="modify_my_codes" />
              
              <select class="form-select form-select-lg mb-3" multiple id="codes" name="codes[]" size="10">
                <option value="0">Aucun</option>
                ' . $codes . '
              </select>

              <p class="lead">
                Vous pouvez choisir plusieurs codes à la fois. Pour enlever un code, il suffit de le déselectionner.
                Pour enregistrer vos modifications, cliquez sur le bouton "Modifier".
              </p>

              <div class="d-grid">
                <input class="btn btn-outline-warning" type="submit" value="Modifier" />
              </div>
              
              </form>
            </div>';

      return $form;
    }

    /**
     * Display a message to select a schedule
     */
    public function displaySelectSchedule() {
        return '<p>Veuillez choisir un emploi du temps.</p>';
    }

    /**
     * Displays the welcome page
     *
     * @return string
     */
     public function displayContent() {
       return '<section class="container col-xxl-10 py-5">
         <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
           <div class="col-10 col-sm-8 col-lg-6">
             <img draggable="false" src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f8/Aix-Marseille_université_%28logo%29.png/1920px-Aix-Marseille_université_%28logo%29.png" class="d-block mx-lg-auto img-fluid" alt="Bootstrap Themes" loading="lazy" width="700" height="500">
           </div>
           <div class="col-lg-6">
             <h1 class="display-5 fw-bold title-bold">' . get_bloginfo("name") . '</h1>
             <p class="lead">
               Bienvenue sur la page d\'accueil des écrans connectés. Vous manquez de permissions pour accéder aux autres pages.
             </p>
           </div>
         </div>
       </section>';
     }

    /**
     * Display to user, no lesson today
     *
     * @return string
     */
    public function displayNoStudy() {
        return '<p>Vous n\'avez pas cours!</p>';
    }

    public function errorMessageNoCodeRegister() {
        $current_user = wp_get_current_user();
        return '
        <h2>' . $current_user->user_login . '</h2>
        <p>Vous êtes enregistré sans aucun emploi du temps, rendez-vous sur votre compte pour pouvoir vous attribuez un code afin d\'accèder à votre emploi du temps</p>';
    }

    public function successMesageChangeCode() {
        $this->buildModal('Modification validée', '<div class="alert alert-success"> Le changement de groupe a été pris en compte</div>');
    }

    public function errorMesageChangeCode() {
        $this->buildModal('Modification échouée', '<div class="alert alert-danger"> Le changement de groupe n\'a pas été pris en compte</div>');
    }

    private function getRoles($user) {
        $roles = array();
        foreach ($user->roles as $role) {
            $roles[] = $role;
        }
        return $roles;
    }

    /**
     * Displays all users
     *
     * @param $users    User[]
     * @return string
     */
    public function displayUsers($users)
    {
        $title = '<b>Rôle affiché: </b> tous';
        $id = 'all';

        $header = ['Identifiant', 'Nom', 'Email', 'Rôle', 'Modifier', 'Supprime'];

        $row = array();

        foreach ($users as $user) {

            $delete_url = wp_nonce_url(admin_url('admin-post.php?action=delete_user&id=' . $user->getId()), 'delete_user_' . $user->getId());

            $row[] = [
                $user->ID,
                $this->buildCheckbox('All', $user->getId()),
                $user->get('user_login'),
                $user->get('display_name'),
                $user->get('user_email'),
                implode("'", $this->getRoles($user)),
                $this->link(add_query_arg(['id' => $user->getId()], home_url('/users/edit')), 'Modifier'),
                $this->link($delete_url, 'Supprimer')
            ];
        }

        return $this->displayTable($id, $title, $header, $row, $id, '<a type="submit" class="btn btn-primary" role="button" aria-disabled="true" href="' . home_url('/users/create') . '">Créer</a>');
    }
}

