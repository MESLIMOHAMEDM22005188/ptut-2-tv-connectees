<?php

namespace Controllers;

use Models\Alert;
use Models\CodeAde;
use Models\Information;
use Models\User;

use Views\ICSView;
use Views\UserView;

use R34ICS;

/**
 * Class UserController
 *
 * Manage all users (Create, update, delete)
 *
 * @package Controllers
 */
class UserController extends Controller
{

    /**
     * @var User
     */
    private $model;

    /**
     * @var UserView
     */
    private $view;

    /**
     * UserController constructor.
     */
    public function __construct() {
        $this->model = new User();
        $this->view = new UserView();
    }

    /**
     * Displays user view content
     * @author Thomas Cardon
     * @return mixed|string
     */
    public function displayContent() {
        return $this->view->displayContent();
    }

    /**
     * Delete an user
     *
     * @param $id   int
     */
    public function delete($id) {
        $user = $this->model->get($id);
        $userData = get_userdata($id);
        $user->delete();
        if (in_array("enseignant", $userData->roles) || in_array("secretaire", $userData->roles) ||
            in_array("administrator", $userData->roles) || in_array("directeuretude", $userData->roles)) {
            $modelAlert = new Alert();
            $alerts = $modelAlert->getAuthorListAlert($user->getLogin());
            foreach ($alerts as $alert) {
                $alert->delete();
            }
        }

        if (in_array("secretaire", $userData->roles) || in_array("administrator", $userData->roles) ||
            in_array("directeuretude", $userData->roles)) {
            $modelInfo = new Information();
            $infos = $modelInfo->getAuthorListInformation($user->getId());
            foreach ($infos as $info) {
                $goodType = ['img', 'pdf', 'tab', 'event'];
                if (in_array($info->getType(), $goodType)) {
                    $infoController = new InformationController();
                    $infoController->deleteFile($info->getId());
                }
                $modelInfo->delete();
            }
        }
    }

    /**
     * Modifies user's password, delete their account or modify their groups
     * @author Thomas Cardon
     * @return mixed|string
     */
    public function edit() {
        $current_user = wp_get_current_user();

        return $this->view->renderHeroHeader('Vos réglages', 'Changez votre mot de passe, vos groupes ou supprimez votre compte.', URL_PATH . TV_PLUG_PATH . 'public/img/settings.png')
        . $this->view->renderContainerDivider()
        . $this->view->renderContainer(
           (isset($_GET['message']) ? '<div class="alert alert-' . $_GET['message'] . '">' . $_GET['message_content'] . '</div>' : '') .
           $this->view->displayStartMultiSelect()
        .  $this->view->displayTitleSelect('pass', 'Modifier mon mot de passe', true)
        .  $this->view->displayTitleSelect('generate', 'Générer un code de suppression')
        .  $this->view->displayTitleSelect('delete', 'Supprimer mon compte')
        .  $this->view->displayTitleSelect('groups', 'Modifier mes groupes')
        .  $this->view->displayEndOfTitle()
        .  $this->view->displayContentSelect('pass', $this->view->displayModifyPassword(), true)
        .  $this->view->displayContentSelect('generate', $this->view->displayEnterCode())
        .  $this->view->displayContentSelect('delete', $this->view->displayDeleteAccount())
        .  $this->view->displayContentSelect('groups', $this->modifyCodes())
        .  $this->view->displayEndDiv()
        , '', 'container-sm px-4 pb-3 my-3 text-center');
    }

    /**
     * Display schedule
     *
     * @param $code     int Code ADE of the schedule
     * @param $allDay   bool
     *
     * @return string|bool
     */
    public function displaySchedule($code, $allDay = false) {
        global $R34ICS;
        $R34ICS = new R34ICS();

        $url = $this->getFilePath($code);
        $args = array(
            'count' => 10,
            'description' => null,
            'eventdesc' => null,
            'format' => null,
            'hidetimes' => null,
            'showendtimes' => null,
            'title' => null,
            'view' => 'list',
        );
        $datas = $R34ICS->display_calendar($url, $code, $allDay, $args, true);
        $icsView = new ICSView();
        return $icsView->displaySchedule($datas[0], $datas[1], $datas[2]);
    }

    /**
     * Display the schedule link to the code in the url
     *
     * @return string
     */
    function displayYearSchedule() {
        $id = $this->getMyIdUrl();

        $codeAde = new CodeAde();

        if (is_numeric($id)) {
            $codeAde = $codeAde->get($id);
            if (!is_null($codeAde->getTitle()) && $codeAde->getType() === 'year') {
                return $this->displaySchedule($codeAde->getCode(), true);
            }
        }

        return $this->view->displaySelectSchedule();
    }


    /**
     * Check if a code Ade already exists with the same title or code
     *
     * @param User $newUser
     *
     * @return bool
     */
    public function checkDuplicateUser(User $newUser) {
        $codesAde = $this->model->checkUser($newUser->getLogin(), $newUser->getEmail());

        if (sizeof($codesAde) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Modify codes ade for the student
     *
     * @return string
     */
    public function modifyCodes() {
        return $this->view->displayModifyMyCodesView(CodeAde::find());
    }

    public function displayUsers() {
        return $this->view->displayUsers(User::find());
    }
}
