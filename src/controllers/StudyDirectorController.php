<?php

namespace Controllers;

use Models\User;
use Views\StudyDirectorView;

/**
 * Class StudyDirectorController
 *
 * Manage study director (Create, update, delete, display)
 *
 * @package Controllers
 */
class StudyDirectorController extends UserController implements Schedule
{

    /**
     * @var User
     */
    private $model;

    /**
     * @var StudyDirectorView
     */
    private $view;

    /**
     * Constructor of StudyDirectorController
     */
    public function __construct() {
        parent::__construct();
        $this->model = new User();
        $this->view = new StudyDirectorView();
    }

    /**
     * Displays the study director's schedule
     * @author Thomas Cardon
     * @return mixed|string
     */
    public function displayContent() {
        $current_user = wp_get_current_user();
        $user = $this->model->get($current_user->ID);
        if (sizeof($user->getCodes()) > 0) {
            return $this->displaySchedule($user->getCodes()[0]->getCode());
        } else {
            return $this->view->errorMessageNoCodeRegister();
        }
    }

    /**
     * Modify the study director
     *
     * @param $user   User
     *
     * @return string
     */
    public function modify($user) {
        $page = get_page_by_title('Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);

        $action = filter_input(INPUT_POST, 'modifValidate');

        if ($action === 'Valider') {
            $code = filter_input(INPUT_POST, 'modifCode');
            if (is_numeric($code)) {
                $user->setRole('directeuretude');
                $user->getCodes()[0]->setCode($code);

                if ($user->update()) {
                    $this->view->displayModificationValidate($linkManageUser);
                }
            }
        }
        return $this->view->displayModifyStudyDirector($user);
    }

    /**
     * Display all study directors
     */
    public function displayTableStudyDirector() {
        $users = $this->model->getUsersByRole('directeuretude');
        $users = $this->model->getMyCodes($users);
        return $this->view->displayTableStudyDirector($users);
    }
}
