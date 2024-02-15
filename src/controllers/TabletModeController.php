<?php

namespace Controllers;

use Models\CodeAde;
use Views\TabletModeView;

/**
 * Class TabletModeController
 *
 * Manages tablet mode
 *
 * @package Controllers
 */
class TabletModeController extends Controller
{

    /**
     * @var CodeAde
     */
    private $model;

    /**
     * @var TabletModeView
     */
    private $view;

    /**
     * TabletModeController constructor
     */
    public function __construct() {
        $this->model = new CodeAde();
        $this->view = new TabletModeView();
    }

    public function displayYearSelector() {
      $years = $this->model->getAllFromType('year');
      return $this->view->displayYearSelector($years);
    }
}
