<?php

namespace Controllers;

use Models\CodeAde;
use Models\User;
use Views\TelevisionView;

/**
 * Class TelevisionController
 *
 * Manage televisions (Create, update, delete, display, display schedules)
 *
 * @package Controllers
 */
class TelevisionController extends UserController implements Schedule
{
    /**
     * @var User
     */
    private $model;

    /**
     * @var TelevisionView
     */
    private $view;

    /**
     * Constructor of TelevisionController
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new User();
        $this->view = new TelevisionView();
    }

    /**
     * Redirects from / to /tv-mode if user is a TV
     * @author Thomas Cardon
     * @return mixed|string
     */
    public function displayContent()
    {
        return "<script>location.href = '". home_url('/tv-mode') . "'</script>";
    }

    /**
     * Displays the TV schedule
     * @author Thomas Cardon
     * @return mixed|string
     */
    public function displayTVInterface()
    {
        $current_user = wp_get_current_user();
        $user = $this->model->get($current_user->ID);
        $user = $this->model->getMycodes([$user])[0];

        $string = '
        <div class="row">
          <div id="scheduleList" class="col-11 schedule-table">';

        if (count($user->getCodes()) > 0) {
            foreach ($user->getCodes() as $code) {
                $string .= $this->displaySchedule($code->getCode());
            }
        } else {
            $string .= $this->view->displayNoSchedule();
        }

        $string .= '
          </div>
          <div class="col-auto">
          </div>
        </div>';

        return $string;
    }
    
    /**
     * Modify a television
     *
     * @param $user User
     *
     * @return string
     */
    public function modify($user)
    {
        $page = get_page_by_title('Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);

        $codeAde = new CodeAde();

        $action = filter_input(INPUT_POST, 'modifValidate');

        if (isset($action)) {
            $codes = $_POST['selectTv'];

            $codesAde = array();
            foreach ($codes as $code) {
                if (is_null($codeAde->getByCode($code)->getId())) {
                    return 'error';
                } else {
                    $codesAde[] = $codeAde->getByCode($code);
                }
            }

            $user->setCodes($codesAde);

            if ($user->update()) {
                $this->view->displayModificationValidate($linkManageUser);
            }
        }

        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->modifyForm($user, $years, $groups, $halfGroups);
    }

    /**
     * Display all televisions in a table
     *
     * @return string
     */
    public function displayTableTv()
    {
        $users = $this->model->getUsersByRole('television');
        return $this->view->displayTableTv($users);
    }
}
