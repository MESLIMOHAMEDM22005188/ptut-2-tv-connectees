<?php

namespace Controllers;

use Models\Alert;
use Models\CodeAde;
use Models\User;
use Views\AlertView;

/**
 * Class AlertController
 *
 * Manage alerts (create, update, delete, display)
 *
 * @package Controllers
 */
class AlertController extends Controller
{

    /**
     * @var Alert
     */
    private $model;

    /**
     * @var AlertView
     */
    private $view;

    /**
     * AlertController constructor
     */
    public function __construct() {
        $this->model = new Alert();
        $this->view = new AlertView();
    }

    /**
     * Insert an alert in the database
     */
    public function insert() {
        $codeAde = new CodeAde();
        $action = filter_input(INPUT_POST, 'submit');
        if (isset($action)) {
            $codes = $_POST['selectAlert'];
            $content = filter_input(INPUT_POST, 'content');
            $endDate = filter_input(INPUT_POST, 'expirationDate');

            $creationDate = date('Y-m-d');
            $endDateString = strtotime($endDate);
            $creationDateString = strtotime(date('Y-m-d', time()));

            $this->model->setForEveryone(0);

            $codesAde = array();
            foreach ($codes as $code) {
                if ($code != 'all' && $code != 0) {
                    if (is_null($codeAde->getByCode($code)->getId())) {
                        $this->view->errorMessageInvalidForm();
                        return;
                    } else {
                        $codesAde[] = $codeAde->getByCode($code);
                    }
                } else if ($code == 'all') {
                    $this->model->setForEveryone(1);
                }
            }

            if (is_string($content) && strlen($content) >= 4 && strlen($content) <= 280 && $this->isRealDate($endDate) && $creationDateString < $endDateString) {
                $current_user = wp_get_current_user();

                // Set the alert
                $this->model->setAuthor($current_user->ID);
                $this->model->setContent($content);
                $this->model->setCreationDate($creationDate);
                $this->model->setExpirationDate($endDate);
                $this->model->setCodes($codesAde);

                // Insert
                if ($id = $this->model->insert()) {
                    $this->view->displayAddValidate();
                } else {
                    $this->view->errorMessageCantAdd();
                }
            } else {
                $this->view->errorMessageInvalidForm();
            }
        }

        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return
          $this->view->renderContainer(
            $this->view->creationForm($years, $groups, $halfGroups), 'Créer une alerte'
          ) . $this->view->renderContainerDivider() .
          $this->view->renderContainer($this->view->contextCreateAlert());
    }

    /**
     * Modify an alert
     */
    public function modify() {
        $id = $_GET['id'];

        if (!is_numeric($id) || !$this->model->get($id)) {
            return $this->view->noAlert();
        }
        $current_user = wp_get_current_user();
        $alert = $this->model->get($id);
        
        if (!members_current_user_has_role('administrator') && !members_current_user_has_role('secretaire') && $alert->getAuthor()->getId() != $current_user->ID)
            return $this->view->alertNotAllowed();

        if ($alert->getAdminId()) {
            return $this->view->alertNotAllowed();
        }

        $codeAde = new CodeAde();

        $submit = filter_input(INPUT_POST, 'submit');
        if (isset($submit)) {
            // Get value
            $content = filter_input(INPUT_POST, 'content');
            $expirationDate = filter_input(INPUT_POST, 'expirationDate');
            $codes = $_POST['selectAlert'];

            $alert->setForEveryone(0);

            $codesAde = array();
            foreach ($codes as $code) {
                if ($code != 'all' && $code != 0) {
                    if (is_null($codeAde->getByCode($code)->getId())) {
                        $this->view->errorMessageInvalidForm();
                        return;
                    } else {
                        $codesAde[] = $codeAde->getByCode($code);
                    }
                } else if ($code == 'all') {
                    $alert->setForEveryone(1);
                }
            }

            // Set the alert
            $alert->setContent($content);
            $alert->setExpirationDate($expirationDate);
            $alert->setCodes($codesAde);

            if ($alert->update()) {
                $this->view->displayModifyValidate();
            } else {
                $this->view->errorMessageCantAdd();
            }
        }

        $delete = filter_input(INPUT_POST, 'delete');
        if (isset($delete)) {
            $alert->delete();
            $this->view->displayModifyValidate();
        }

        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->modifyForm($alert, $years, $groups, $halfGroups);
    }


    public function displayTable() {
        $numberAllEntity = $this->model->countAll();
        $url = $this->getPartOfUrl();
        $number = filter_input(INPUT_GET, 'number');
        $pageNumber = 1;
        if (sizeof($url) >= 2 && is_numeric($url[1])) {
            $pageNumber = $url[1];
        }
        if (isset($number) && !is_numeric($number) || empty($number)) {
            $number = 25;
        }
        $begin = ($pageNumber - 1) * $number;
        $maxPage = ceil($numberAllEntity / $number);
        if ($maxPage <= $pageNumber && $maxPage >= 1) {
            $pageNumber = $maxPage;
        }
        $current_user = wp_get_current_user();
        if (members_current_user_has_role('administrator') || members_current_user_has_role('secretaire'))
            $alertList = $this->model->getList($begin, $number);
         else
           $alertList = $this->model->getAuthorListAlert($current_user->ID, $begin, $number);

        $name = 'Alert';
        $header = ['Contenu', 'Date de création', 'Date d\'expiration', 'Auteur', ''];
        $dataList = [];
        $row = $begin;
        
        foreach ($alertList as $alert) {
            ++$row;
            $dataList[] = [$row, $this->view->buildCheckbox($name, $alert->getId()), $alert->getContent(), $alert->getCreationDate(), $alert->getExpirationDate(), $alert->getAuthor()->getLogin(), /*$this->view->buildLinkForModify(esc_url(get_permalink(get_page_by_title('Modifier une alerte'))) . '?id=' . $alert->getId()) TODO Modify link*/ ];
        }

        $submit = filter_input(INPUT_POST, 'delete');
        if (isset($submit)) {
            if (isset($_REQUEST['checkboxStatusAlert'])) {
                $checked_values = $_REQUEST['checkboxStatusAlert'];
                foreach ($checked_values as $id) {
                    $entity = $this->model->get($id);
                    $entity->delete();
                }
                $this->view->refreshPage();
            }
        }

        return ($pageNumber == 1 ? $this->view->getHeader() : '') .
        $this->view->renderContainerDivider() .
        $this->view->renderContainer(
          $this->view->displayTable($name, 'Alertes', $header, $dataList, '', '<a type="submit" class="btn btn-primary" role="button" aria-disabled="true" href="' . home_url('/creer-une-alerte') . '">Créer</a>') . $this->view->pageNumber($maxPage, $pageNumber, home_url('/gerer-les-alertes'), $number)
        , '', 'container-xl py-5 my-5 text-center');
    }
    
    /**
     * displayAlerts()
     * Displays all alerts in an horizontal list
     * @author Thomas Cardon
     */
    public function displayAlerts() {
      $alerts = $this->model->getForEveryone();
      $userAlerts = $this->model->getForUser(wp_get_current_user()->ID);
            
      foreach ($alerts as $i)
        $this->view->carousel->add($i->getAuthor(), $i->getContent());
        
      foreach ($userAlerts as $i)
        $this->view->carousel->add($i->getAuthor(), $i->getContent());

      echo $this->view->carousel->build();
    }

    public function registerNewAlert() {
        $alertList = $this->model->getFromAdminWebsite();
        $myAlertList = $this->model->getAdminWebsiteAlert();
        foreach ($myAlertList as $alert) {
            if ($adminInfo = $this->model->getAlertFromAdminSite($alert->getId())) {
                if ($alert->getContent() != $adminInfo->getContent()) {
                    $alert->setContent($adminInfo->getContent());
                }
                if ($alert->getExpirationDate() != $adminInfo->getExpirationDate()) {
                    $alert->setExpirationDate($adminInfo->getExpirationDate());
                }
                $alert->setCodes([]);
                $alert->setForEveryone(1);
                $alert->update();
            } else {
                $alert->delete();
            }
        }
        foreach ($alertList as $alert) {
            $exist = 0;
            foreach ($myAlertList as $myAlert) {
                if ($alert->getId() == $myAlert->getAdminId()) {
                    ++$exist;
                }
            }
            if ($exist == 0) {
                $alert->setAdminId($alert->getId());
                $alert->setCodes([]);
                $alert->insert();
            }
        }
    }

    /**
     * Check the end date of the alert
     *
     * @param $id
     * @param $endDate
     */
    public function endDateCheckAlert($id, $endDate) {
        if ($endDate <= date("Y-m-d")) {
            $alert = $this->model->get($id);
            $alert->delete();
        }
    } //endDateCheckAlert()
}
