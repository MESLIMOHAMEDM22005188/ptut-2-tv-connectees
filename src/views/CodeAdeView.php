<?php

namespace Views;

use Models\CodeAde;

/**
 * Class CodeAdeView
 *
 * All view for code ade (Forms, table, messages)
 *
 * @package Views
 */
class CodeAdeView extends View
{
    private $typesDictionary = array(
        'year' => 'Année',
        'group' => 'Groupe',
        'halfGroup' => 'Demi-Groupe',
        'teacher' => 'Enseignant'
    );


    /**
     * Display form for create code ade
     *
     * @return string
     */
    public function createForm()
    {
        $radios = '';
        foreach ($this->typesDictionary as $value => $title) {
            $radios .= '
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="type" id="' . $value . '" value="' . $value . '">
            <label class="form-check-label" for="' . $value . '">' . $title . '</label>
        </div>
        ';
        }

        return '
        <form method="post">
            <div class="row justify-content-center gx-2 mb-2">
              <div class="form-floating col-4">
                  <input class="form-control" type="text" placeholder="Ex: Marc LAPORTE, Groupe 3, etc." id="title" name="title" required="" minlength="5" maxlength="29">
                  <label for="title">Titre (enseignant: Prénom NOM)</label>
              </div>
              <div class="form-floating col-4">
                  <input class="form-control" type="number" placeholder="Code à récupérer sur l\'interface ADE" id="code" name="code" required="" maxlength="19" pattern="\d+">
                  <label for="code">Code ADE</label>
              </div>
            </div>
            <div class="mb-3">
              ' . $radios . '
            </div>
          <button type="submit" class="btn btn-primary" name="submit">Ajouter</button>
        </form>';
    }

    /**
     * Display a form for modify a code ade
     *
     * @param $title    string
     * @param $type     string
     * @param $code     int
     *
     * @return string
     */
    public function displayModifyCode($title, $type, $code)
    {
        $page = get_page_by_title('Gestion des codes ADE');
        $linkManageCode = get_permalink($page->ID);

        return '
        <a href="' . esc_url(get_permalink(get_page_by_title('Gestion des codes ADE'))) . '">< Retour</a>
         <form method="post">
         	<div class="mb-3">
            	<label for="title">Titre</label>
            	<input class="form-control" type="text" id="title" name="title" placeholder="Titre" value="' . $title . '">
            </div>
            <div class="mb-3">
            	<label for="code">Code</label>
            	<input type="text" class="form-control" id="code" name="code" placeholder="Code" value="' . $code . '">
            </div>
            <div class="mb-3">
            	<label for="type">Selectionner un type</label>
             	<select class="form-control" id="type" name="type">
                    ' . $this->createTypeOption($type) . '
                </select>
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Modifier</button>
            <a href="' . $linkManageCode . '">Annuler</a>
         </form>';
    }

    /**
     * Display options for selecting a code type
     *
     * @param string $selectedType Currently selected type of the code
     *
     * @return string
     */
    private function createTypeOption($selectedType)
    {
        $result = '';

        // Build option list
        foreach ($this->typesDictionary as $value => $title) {
            $result .= '<option value="' . $value . '"';

            if ($selectedType === $value) {
                $result .= ' selected';
            }

            $result .= '>' . $title . '</option>' . PHP_EOL;
        }

        return $result;
    }

    /**
     * Displays all ADE code data
     *
     * @param ...$codes CodeAde[]
     * @return HTMLElement
     */
    public function displayTableCode(...$groups)
    {
        $header = ['Titre', 'Code', 'Type', 'Modifier'];

        $row = array();
        $count = 0;

        foreach ($groups as $codeAde) {
            foreach ($codeAde as $code) {
                ++$count;
                $row[] = [$count, $this->buildCheckbox($code->getTitle(), $code->getId()), $code->getTitle(), $code->getCode(), $code->getType(), $this->link(add_query_arg('id', $code->getId(), home_url('/gestion-codes-ade/modification-code-ade')), 'voir')];
            }
        }

        return $this->displayTable('Code', 'Codes ADE gérés par le système', $header, $row, 'code', '');
    }

    /**
     * Display a success message for the creation of a new code ADE
     */
    public function successCreation()
    {
        $this->buildModal('Ajout du code ADE', '<p>Le code ADE a bien été ajouté</p>');
    }

    /**
     * Display a success message for the modification of a code ADE
     */
    public function successModification()
    {
        $page = get_page_by_title('Gestion des codes ADE');
        $linkManageCode = get_permalink($page->ID);
        $this->buildModal('Modification du code ADE', '<p>Le code ADE a bien été modifié</p>', $linkManageCode);
    }

    /**
     * Display an error message for the creation of a code ADE
     */
    public function errorCreation()
    {
        $this->buildModal('Erreur lors de l\'ajout du code ADE', '<p>Le code ADE a rencontré une erreur lors de son ajout</p>');
    }

    /**
     * Display an error message for the modification of a code ADE
     */
    public function errorModification()
    {
        $this->buildModal('Erreur lors de la modification du code ADE', '<p>Le code ADE a rencontré une erreur lors de sa modification</p>');
    }

    /**
     * Error message if title or code exist
     */
    public function displayErrorDoubleCode()
    {
        echo '<p class="alert alert-danger"> Ce code ou ce titre existe déjà</p>';
    }

    /**
     * Display an message if there is nothing
     */
    public function errorNobody()
    {
        $page = get_page_by_title('Gestion des codes ADE');
        $linkManageCode = get_permalink($page->ID);
        echo '<p>Il n\'y a rien par ici</p><a href="' . $linkManageCode . '">Retour</a>';
    }
}
