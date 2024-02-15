<?php

namespace Views;

/**
 * Class View
 *
 * Main class View,
 * got basics functions for all views
 *
 * @package Views
 */
class View
{

    /**
     * Displays the divider - allows containers to be separated conveniently
     * @author Thomas Cardon
     * @param $data
     * @param $classes
     * @return string - Container HTML data
     */
    public function renderContainerDivider() {
      return '<div class="container-divider"></div>';
    }

    /**
     * Displays a container in an elegant way
     * @author Thomas Cardon
     * @param $data
     * @param $classes
     * @return string - Container HTML data
     */
    public function renderContainer($data, $title = '', $classes = 'container-sm px-4 py-5 my-5 text-center') {
      return '<div class="' . $classes . '">' . ($title == '' ? '' : '<h1 class="display-5 fw-bold" style="color: var(--color-primary-200) !important;">' . $title . '</h1>') . $data . '</div>';
    }

    /**
     * Displays a hero header for a page
     * @author Thomas Cardon
     * @param $title
     * @param $p - The descriptions
     * @param $url - The side image URL
     * @return string - Container HTML data
     */
    public function renderHeroHeader($title = 'Page sans titre', $p, $url = URL_PATH . TV_PLUG_PATH . 'public/img/alert.png') {
      return '
      <section class="container col-xxl-10 py-5">
          <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
            <div class="col-10 col-sm-8 col-lg-6">
              <img draggable="false" src="' . $url . '" class="d-block mx-lg-auto img-fluid" loading="lazy" width="256">
            </div>
            <div class="col-lg-6">
              <h1 class="display-5 fw-bold title-bold">' . $title . '</h1>
              <p class="lead">
                ' . $p . '
              </p>
            </div>
          </div>
        </section>';
    }

    /**
     * Displays the view's header
     * @author Thomas Cardon
    */
    public function getHeader($title = '', $p = '', $icon = URL_PATH . TV_PLUG_PATH . 'public/img/alert.png') {
      return $this->renderHeroHeader($title, $p, $icon);
    }

    /**
     * Display a table, showing all element from a database
     *
     * @param $name
     * @param $title
     * @param $dataHeader
     * @param $dataList
     * @param string $idTable
     *
     * @return string
     */
    public function displayTable($name, $title, $dataHeader, $dataList, $idTable = '', $create = '<a type="submit" class="btn btn-primary disabled" role="button" aria-disabled="true">Créer</a>') {
        $name = '\'' . $name . '\'';
        $table = ($title ? '<h2>' . $title . '</h2>' : '') . '
    		<form method="post">
    			<div class="table-responsive">
    				<table class="table table-hover sortable" id="table' . $idTable . '">
    					<thead>
    						<tr class="text-center">
    							<th width="5%" class="text-center">#</th>
    		          <th scope="col" width="5%" class="text-center no-sort">
                    <input type="checkbox" onClick="toggle(this, ' . $name . ')" />
                  </th>';

        foreach ($dataHeader as $data) {
            if (is_array($data) && $data[0] === true) {
              $table .= '<th scope="col" class="text-center no-sort">' . $data . '</th>';
              continue;
            }
            
            $table .= '<th scope="col" class="text-center">' . $data . '</th>';
        }

        $table .= '</tr></thead><tbody>';

        foreach ($dataList as $data) {
            $table .= '<tr>';
            foreach ($data as $column) {
                $table .= '<td class="text-center">' . $column . '</td>';
            }
            $table .= '</tr>';
        }

        $table .= '</tbody></table></div>';

        $table .= '
        <div class="row d-flex justify-content-center">
          <div class="col-auto my-auto">
            ' . $create . '
            <button type="submit" class="btn btn-danger" name="delete" onclick="return confirm(\' Voulez-vous supprimer le(s) élément(s) sélectionné(s) ?\');">Suprimer</button>
          </div>
          <div class="col-auto my-auto">
            <span id="passwordHelpInline" class="form-text">
              Supprime les éléments sélectionnés.
            </span>
          </div>
          <div class="col-auto my-auto">
            <label for="key' . $idTable . '" class="col-form-label">Rechercher</label>
          </div>
          <div class="col-auto my-auto">
            <input class="form-control form-control-sm" type="text" id="key' . $idTable . '" name="key" onkeyup="search(\'' . $idTable . '\')" placeholder="Entrez un mot-clé...">
          </div>
        </div>
	    </form>';

      return $table;
    }

    public function pageNumber($pageNumber, $currentPage, $url, $numberElement = null) {
        $pagination = '
        <nav aria-label="Naviguer">
            <ul class="pagination">
              <li class="page-item' . ($currentPage == 1 ? ' disabled' : '') . '">
                <a class="page-link" href="' . $url . '/' . ($currentPage - 1) . '/?number=' . $numberElement . '" aria-label="Précédent">
                  <span aria-hidden="true">&laquo;</span>
                </a>
              </li>';

        if ($currentPage > 3) {
            $pagination .= '<li class="page-item page-link disabled">...</li>';
        }
        for ($i = $currentPage - 3; $i < $currentPage; ++$i) {
            if ($i > 1) {
                $pagination .= '<li class="page-item"><a class="page-link" href="' . $url . $i . '/?number=' . $numberElement . '">' . $i . '</a></li>';
            }
        }

        $pagination .= '
        <li class="page-item active" aria-current="page">
          <a class="page-link">' . $currentPage . '</a>
        </li>';
        for ($i = $currentPage + 1; $i < $currentPage + 3; ++$i) {
            if ($i < $pageNumber) {
                $pagination .= '<li class="page-item"><a class="page-link" href="' . $url . '/' . $i . '/?number=' . $numberElement . '">' . $i . '</a></li>';
            }
        }

        $pagination .= '
            <li class="page-item">
              <a class="page-link' . ($currentPage < $pageNumber ? '' : ' disabled') . '" href="' . $url . '/' . ($currentPage + 1) . '/?number=' . $numberElement . '" aria-label="Suivant">
                <span aria-hidden="true">&raquo;</span>
              </a>
            </li>
          </ul>
        </nav>';
        return $pagination;
    }

    /**
     * Creates a link
     * @param string $url
     * @param string $text
     * @param string $class
     * @param string $id
     * @return string
     * 
     * @author : Thomas Cardon
     */
    public function link($url, $text, $class = '', $id = '') {
        return '<a id="' . $id . '" class="' . $class . '" href="' . $url . '">' . $text . '</a>';
    }

    /**
     * Create a checkbox
     *
     * @param $name
     * @param $id
     *
     * @return string
     */
    public function buildCheckbox($name, $id) {
        return '<input type="checkbox" name="checkboxStatus' . $name . '[]" value="' . $id . '"/>';
    }

    /**
     * Create the begin of a multi select
     *
     * @return string
     */
    public function displayStartMultiSelect() {
        return '<ul class="nav nav-pills mb-3" id="nav-tab" role="tablist">';
    }

    /**
     * Create one tab for the multi select
     *
     * @param $id           string id de l'onglet
     * @param $title        string titre de l'onglet
     * @param $active       bool affiche l'onglet (si c'est à true) lors du chargement de la page
     * @return string
     */
    public function displayTitleSelect($id, $title, $active = false) {
        $string = '<li class="nav-item' . ($active ? ' active' : '') . '" role="presentation">';

        $string .= '
          <button class="nav-link' . ($active ? ' active' : '') . '" id="nav-' . $id . '-tab" href="#nav-' . $id . '" role="tab" data-bs-toggle="pill" data-bs-target="#"nav-' . $id . '" aria-controls="nav-' . $id . '" aria-selected="false">' . $title . '</button>
        ';

        $string .= '</li>';
        return $string;
    }

    /**
     * Close the creation of new tab
     *
     * @return string
     */
    public function displayEndOfTitle() {
        return '</ul>
        <br />
        <div class="tab-content" id="nav-tabContent">';
    }

    /**
     * Create the content for one tab
     *
     * @param $id           string
     * @param $content      string
     * @param $active       bool
     *
     * @return string
     */
    public function displayContentSelect($id, $content, $active = false) {
        $string = '<div class="tab-pane fade' . ($active ? ' show active' : '');
        $string .= '" id="nav-' . $id . '" role="tabpanel" aria-labelledby="nav-' . $id . '-tab">';
        $string .= $content;
        $string .= '</div>';

        return $string;
    }

    /**
     * Refresh the page
     */
    public function refreshPage() {
        echo '<meta http-equiv="refresh" content="0">';
    }

    /**
     * @param $title
     * @param $content
     * @param null $redirect
     */
    public function buildModal($title, $content, $redirect = null) {
        $unique_id = 'modal_'.uniqid();
        $modal = '
    <!-- MODAL -->
    <div class="modal" id="' . $unique_id . '" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">' . $title . '</h5>
                </div>
                <div class="modal-body">
                    ' . $content . '
                </div>
                <div class="modal-footer">';

        $modal .= '<button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fermer</button>';
        $modal .= '</div>
            </div>
        </div>
    </div>

    <script>
        const docReady = fn => {
            if (document.readyState === "complete" || document.readyState === "interactive") setTimeout(fn, 1);
            else document.addEventListener("DOMContentLoaded", fn);
        }

        docReady(() =>
            bootstrap.Modal
            .getOrCreateInstance(document.querySelector("#' . $unique_id . '"))
            .show()
        );
    </script>';

        echo $modal;
    }

    /**
     * Close a div
     *
     * @return string
     */
    public function displayEndDiv() {
        return '</div>';
    }

    /**
     * Display a message if the two password are different
     */
    public function displayBadPassword() {
        $this->buildModal('Mauvais mot de passe', '<p class=\'alert alert-danger\'>Les deux mots de passe ne sont pas correctes </p>');
    }

    /**
     * Display a message if an user already exist
     *
     * @param $doubles  array de login
     */
    public function displayErrorDouble($doubles) {
        $content = "";
        foreach ($doubles as $double) {
            $content .= '<p class="alert alert-danger">' . $double . ' a rencontré un problème lors de l\'enregistrement, vérifié son login et son email !</p>';
        }
        $this->buildModal('Erreur durant l\'inscription', $content);
    }

    /**
     * Display a message if the inscription is a success
     */
    public function displayInsertValidate() {
        $this->buildModal('Inscription validée', '<p class=\'alert alert-success\'>Votre inscription a été validée.</p>');
    }

    /**
     * Display a message if the extension of the file is wrong
     */
    public function displayWrongExtension() {
        $this->buildModal('Mauvais fichier !', '<p class="alert alert-danger"> Mauvaise extension de fichier !</p>');
    }

    /**
     * Display a message if the file isn't a good file
     */
    public function displayWrongFile() {
        $this->buildModal('Mauvais fichier !', '<p class="alert alert-danger"> Vous utilisez un mauvais fichier excel / ou vous avez changé le nom des colonnes</p>');
    }

    /**
     * Display a message if the modification is a success
     */
    public function displayModificationValidate($redirect = null) {
        $this->buildModal('Modification réussie', '<p class="alert alert-success"> La modification a été appliquée</p>', $redirect);
    }

    /**
     * Display a message if the creation of an user has failed
     */
    public function displayErrorInsertion() {
        $this->buildModal('Erreur lors de l\'inscription', '<p class="alert alert-danger"> Le login ou l\'adresse mail est déjà utilisé(e) </p>');
    }

    public function errorMessageInvalidForm() {
        $this->buildModal('Le formulaire n\'a pas été correctement remplie', '<p class="alert alert-danger">Le formulaire a été mal remplie, veuillez revoir les données rentrées et réessayez.</p>');
    }

    public function errorMessageCantAdd() {
        $this->buildModal('L\'ajout a échoué', '<p class="alert alert-danger">Une erreur s\'est produite lors de l\'envoie du formulaire, veuillez réessayer après avoir vérifié vos informations.</p>');
    }

    /**
     * Displays an error page for the users that doesn't have access to that page
     * @return string
     */
    public function displayForbiddenAccess() {
        return "<p>Vous n'avez pas les permissions requises pour accéder à cette page.</p>";
    }
}
