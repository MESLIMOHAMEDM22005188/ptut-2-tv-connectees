<?php

namespace Views;

use Models\Alert;
use Models\CodeAde;

/**
 * Class AlertView
 *
 * All view for Alert (Forms, tables, messages)
 *
 * @package Views
 */
class AlertView extends View
{
    public $carousel;
    
    public function __construct() {
      $this->carousel = new AlertCarouselView();
    }


    /**
     * Display creation form
     *
     * @param $years        array
     * @param $groups       array
     * @param $halfGroups   array
     *
     * @return string
     */
    public function creationForm($years, $groups, $halfGroups) {
        $dateMin = date('Y-m-d', strtotime("+1 day")); // Fix the date min to the next day

        return '
        <form method="post" id="alert">
            <div class="form-floating mb-3">
              <input type="text" class="form-control" id="content" name="content" placeholder="Votre contenu" placeholder="280 caractères au maximum" minlength="4" maxlength="280" required>
              <label for="content">Contenu (280 caractères max.)</label>
            </div>
            
            <div class="row g-2 mb-4">
              <div class="col-md">
                <div class="form-floating">
                  <input type="date" class="form-control" id="expirationDate" name="expirationDate" placeholder="JJ/MM/AAAA" min="' . date('Y-m-d') . '">
                  <label for="expirationDate">Date d\'expiration</label>
                </div>
              </div>
              <div class="col-md">
                <div class="form-floating">
                  ' . $this->buildSelectCode($years, $groups, $halfGroups) . '
                  <label for="selectAlert">Année, groupe, demi-groupes concernés</label>
                </div>
              </div>
            </div>
            
            <button type="submit" class="btn btn-primary" name="submit">Valider</button>
            <a role="button" class="btn btn-secondary" href="' . home_url('/gerer-les-alertes') . '">Voir les alertes</a>
        </form>';
    }

    /**
     * Explain how the alert's display
     *
     * @return string
     */
    public function contextCreateAlert() {
      return '
        <div>
          <h2 style="color: var(--color-secondary) !important;">Les alertes</h2>
          <p class="lead mb-4">
            Lors de la création de votre alerte, celle-ci est postée directement sur tous les téléviseurs qui utilisent ce site.
            Les alertes sont affichées les une après les autres défilant à la chaîne en bas des téléviseurs.
          </p>
          <div class="text-center">
            <figure class="figure">
              <img src="' . URL_PATH . TV_PLUG_PATH . 'public/img/presentation.png" class="figure-img img-fluid rounded-3 shadow-lg" alt="Représentation d\'un téléviseur">
              <figcaption class="figure-caption">Représentation d\'un téléviseur</figcaption>
            </figure>
          </div>
        </div>';
    }

    /**
     * Display form for modify alert
     *
     * @param $alert       Alert
     * @param $years        array
     * @param $groups       array
     * @param $halfGroups   array
     *
     * @return string
     */
    public function modifyForm($alert, $years, $groups, $halfGroups) {
        $dateMin = date('Y-m-d', strtotime("+1 day"));
        $codes = $alert->getCodes();

        $form = '
        <a href="' . esc_url(get_permalink(get_page_by_title('Gestion des alertes'))) . '">< Retour</a>
        <form method="post" id="alert">
            <div class="mb-3">
                <label for="content">Contenu</label>
                <input type="text" class="form-control" id="content" name="content" value="' . $alert->getContent() . '" placeholder="280 caractères au maximum" minlength="4" maxlength="280" required>
            </div>
            <div class="mb-3">
                <label for="expirationDate">Date d\'expiration</label>
                <input type="date" class="form-control" id="expirationDate" name="expirationDate" min="' . $dateMin . '" value = "' . $alert->getExpirationDate() . '" required>
            </div>
            <div class="mb-3">
                <div class="form-floating">' .
                    $this->buildSelectCode($years, $groups, $halfGroups, $codes[0], 1, $alert->getForEveryone()) . '
                    <label for="selectAlert">Année, groupe, demi-groupes concernés</label>
                </div>
            </div>';

        if (!$alert->getForEveryone()) {
            $count = 2;
            foreach ($codes as $code) {
                $form .= '
				<div class="row">' .
                    $this->buildSelectCode($years, $groups, $halfGroups, $code, $count)
                    . '<input type="button" id="selectId' . $count . '" onclick="deleteRow(this.id)" class="selectbtn" value="Supprimer">
                  </div>';
                $count = $count + 1;
            }
        }

        $form .= '<input type="button" onclick="addButtonAlert()" value="+">
                  <button type="submit" class="btn btn-primary" name="submit">Valider</button>
                  <button type="submit" class="btn btn-danger" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette alerte ?\');">Supprimer</button>
                </form>' . $this->contextModify();

        return $form;
    }

    public function contextModify() {
        return '
		<hr class="half-rule">
		<div>
			<p class="lead">La modification d\'une alerte prend effet comme pour la création, le lendemain.</p>
			<p class="lead">Vous pouvez donc prolonger le temps d\'expiration ou bien modifier le contenu de votre alerte.</p>
		</div>';
    }

    public function getHeader($t = 'Gestion des alertes', $p = '
    Vous pouvez retrouver ici toutes les alertes qui ont été créées sur ce site.
    <br /> <br />
    Les alertes sont triées de la plus vieille à la plus récente.
    Vous pouvez modifier une alerte en cliquant sur "Modifier" à la ligne correspondante à l\'alerte.
    Vous souhaitez <b>supprimer une / plusieurs alerte(s)</b> ? Cochez les cases des alertes puis cliquez sur "Supprimer" en dessous du tableau.', $i = URL_PATH . TV_PLUG_PATH . 'public/img/alert.png') {
      return parent::getHeader($t, $p, $i);
    }


    /**
     * Build a select with all codes Ade
     *
     * @param $years        CodeAde[]
     * @param $groups       CodeAde[]
     * @param $halfGroups   CodeAde[]
     * @param $code         CodeAde
     * @param $count        int
     *
     * @return string
     */
    public function buildSelectCode($years, $groups, $halfGroups, $code = null, $count = 0, $forEveryone = 0) {
        $select = '<select class="form-select" id="selectAlert" name="selectAlert[]" required>';

        if ($forEveryone) {
            $select .= '<option value="all" selected>Tous</option>';
        } elseif (!is_null($code)) {
            $select .= '<option value="' . $code->getCode() . '" selected>' . $code->getTitle() . '</option>';
        }

        $select .= '<option value="all">Tous</option>
                    <option value="0">Aucun</option>
            		<optgroup label="Année">';

        foreach ($years as $year) {
            $select .= '<option value="' . $year->getCode() . '">' . $year->getTitle() . '</option >';
        }
        $select .= '</optgroup><optgroup label="Groupe">';

        foreach ($groups as $group) {
            $select .= '<option value="' . $group->getCode() . '">' . $group->getTitle() . '</option>';
        }
        $select .= '</optgroup><optgroup label="Demi groupe">';

        foreach ($halfGroups as $halfGroup) {
            $select .= '<option value="' . $halfGroup->getCode() . '">' . $halfGroup->getTitle() . '</option>';
        }
        
        $select .= '
          </optgroup>
			  </select>';

        return $select;
    }

    public function noAlert() {
        return '
		<a href="' . esc_url(get_permalink(get_page_by_title('Gestion des alertes'))) . '">< Retour</a>
		<div>
			<h3>Alerte non trouvée</h3>
			<p>Cette alerte n\'existe pas, veuillez bien vérifier d\'avoir bien cliqué sur une alerte.</p>
			<a href="' . esc_url(get_permalink(get_page_by_title('Créer une alerte'))) . '">Créer une alerte</a>
		</div>';
    }

    public function alertNotAllowed() {
        return '
		<a href="' . esc_url(get_permalink(get_page_by_title('Gestion des alertes'))) . '">< Retour</a>
		<div>
			<h3>Vous ne pouvez pas modifier cette alerte</h3>
			<p>Cette alerte appartient à quelqu\'un d\'autre, vous ne pouvez donc pas modifier cette alerte.</p>
			<a href="' . esc_url(get_permalink(get_page_by_title('Créer une alerte'))) . '">Créer une alerte</a>
		</div>';
    }

    /**
     * Display modal for validate the creation of an alert
     */
    public function displayAddValidate() {
        $this->buildModal('Ajout d\'alerte', '<div class="alert alert-success"> Votre alerte a été envoyée !</div>', esc_url(get_permalink(get_page_by_title('Gestion des alertes'))));
    }

    /**
     * Display modal for validate the modification of an alert
     */
    public function displayModifyValidate() {
        $page = get_page_by_title('Gestion des alertes');
        $linkManageAlert = get_permalink($page->ID);
        $this->buildModal('Ajout d\'alerte', '<div class="alert alert-success"> Votre alerte a été modifiée ! </div>', $linkManageAlert);
    }
}
