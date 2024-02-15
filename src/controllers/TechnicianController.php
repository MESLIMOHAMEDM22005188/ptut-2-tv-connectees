<?php

namespace Controllers;

use Models\CodeAde;
use Models\User;
use Views\TechnicianView;

/**
 * Class TechnicianController
 *
 * Manage Technician (Create, update, delete, display, display schedule)
 *
 * @package Controllers
 */
class TechnicianController extends UserController implements Schedule
{

    /**
     * @var User
     */
    private $model;

    /**
     * @var TechnicianView
     */
    private $view;

    /**
     * Constructor of SecretaryController.
     */
    public function __construct() {
        parent::__construct();
        $this->model = new User();
        $this->view = new TechnicianView();
    }

    /**
     * Displays the schedule of all students
     * @author Thomas Cardon
     * @return mixed|string
     */
    public function displayContent() {
        $codeAde = new CodeAde();

        $years = $codeAde->getAllFromType('year');
        $string = "";
        foreach ($years as $year) {
            $string .= $this->displaySchedule($year->getCode());
        }
        return $string;
    }

    /**
     * Display all technicians in a table
     *
     * @return string
     */
    public function displayTableTechnician() {
        $users = $this->model->getUsersByRole('technicien');
        return $this->view->displayTableTechnicians($users);
    }
}
