<?php

namespace Views;


use Models\User;

/**
 * Class TechnicianView
 *
 * Contain all view for technician (Forms, tables)
 *
 * @package Views
 */
class TechnicianView extends UserView
{

    /**
     * Display a creation form
     *
     * @return string
     */
    public function displayFormTechnician() {
        return '
        <h2>Compte technicien</h2>
        <p class="lead">Pour créer des techniciens, remplissez ce formulaire avec les valeurs demandées.</p>
        ' . $this->displayBaseForm('Tech');
    }

    /**
     * Display all technicians in a table
     *
     * @param $users    User[]
     *
     * @return string
     */
    public function displayTableTechnicians($users) {
        $title = '<b>Rôle affiché: </b> Technicien';
        $header = ['Identifiant'];

        $row = array();
        $count = 0;
        foreach ($users as $user) {
            ++$count;
            $row[] = [$count, $this->buildCheckbox('Technician', $user->getId()), $user->getLogin()];
        }

        return $this->displayTable('Technician', $title, $header, $row, 'Technician', '<a type="submit" class="btn btn-primary" role="button" aria-disabled="true" href="' . home_url('/users/create') . '">Créer</a>');
    }
}
