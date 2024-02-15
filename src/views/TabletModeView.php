<?php

namespace Views;

/**
 * Class TabletModeView
 *
 * @package Views
 */
class TabletModeView extends View
{

  public function displayYearSelector($years) {
    $data = '';
    
    foreach ($years as $year)
      $data .= $year->getTitle() . ': ' . $year->getId() . '<br />';

    return $data;
  }
}
