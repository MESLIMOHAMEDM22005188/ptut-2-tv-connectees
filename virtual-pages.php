<?php
/**
 * Ce code gère la création de pages virtuelles dans WordPress en utilisant le plugin EC Virtual Pages.
 * Il initialise le contrôleur de page, configure les actions et les filtres nécessaires pour gérer les pages virtuelles,
 * et ajoute des commentaires pour expliquer certaines parties du code.
 */

// Importe les classes nécessaires depuis le namespace EC\VirtualPages.
use EC\VirtualPages\PageControllerInterface;
use EC\VirtualPages\PageController;
use EC\VirtualPages\TemplateLoaderInterface;
use EC\VirtualPages\TemplateLoader;
use EC\VirtualPages\PageInterface;

// Crée une instance du contrôleur de page en utilisant le TemplateLoader.
$controller = new PageController(new TemplateLoader());

// Initialise le contrôleur de page lors de l'initialisation de WordPress.
add_action('init', array( $controller, 'init' ));

// Filtre la demande de parse_request pour dispatcher les pages virtuelles.
add_filter('do_parse_request', array( $controller, 'dispatch' ), PHP_INT_MAX, 2);

// Ajoute une action pour réinitialiser la variable virtual_page après la fin de la boucle WordPress.
add_action('loop_end', function (\WP_Query $query) {
    if (isset($query->virtual_page) && ! empty($query->virtual_page)) {
        $query->virtual_page = null;
    }
});

// Filtre la génération de permalien pour les pages virtuelles.
add_filter('the_permalink', function ($plink) {
    global $post, $wp_query;
    if (
        $wp_query->is_page && isset($wp_query->virtual_page)
        && $wp_query->virtual_page instanceof PageInterface
        && isset($post->is_virtual) && $post->is_virtual
    ) {
        $plink = home_url($wp_query->virtual_page->getUrl());
    }
    return $plink;
});

add_action('ec_virtual_pages', function ($controller) {
    /**
     *  Page: /
     */
    $controller->addPage(new \EC\VirtualPages\Page("/"))
    ->setTitle('Accueil')
    ->setContent('
    <!-- wp:tvconnecteeamu/schedule -->
    test
    <!-- /wp:tvconnecteeamu/schedule -->
    ')
    ->setTemplate('page.php');

    $controller->addPage(new \EC\VirtualPages\Page("/tv-mode"))
    ->setTitle('tv-mode')
    ->setContent('
    <!-- wp:tvconnecteeamu/tv-mode -->
    test
    <!-- /wp:tvconnecteeamu/tv-mode -->
    ')
    ->setTemplate('page-tv.php');

    /**
     *  Page: /creer-utilisateur
     */
    $controller->addPage(new \EC\VirtualPages\Page("/creer-utilisateur"))
    ->setTitle('Créer un utilisateur')
    ->setContent('
    <!-- wp:tvconnecteeamu/creation-user -->
    test
    <!-- /wp:tvconnecteeamu/creation-user -->
    ')
    ->setTemplate('page.php');
    
    /**
     *  Page: /users/edit/{id}
     *  Work in progress
     */
    $controller->addPage(new \EC\VirtualPages\Page("/users/edit"))
    ->setTitle('Modifier un utilisateur')
    ->setContent('
    <!-- wp:tvconnecteeamu/modify-user -->
    test
    <!-- /wp:tvconnecteeamu/modify-user -->
    ')
    ->setTemplate('page.php');


    /**
     *  Page: /liste-utilisateur
     */
    $controller->addPage(new \EC\VirtualPages\Page("/users/list"))
    ->setTitle('Liste des utilisateurs')
    ->setContent('
    <!-- wp:tvconnecteeamu/management-user -->
    test
    <!-- /wp:tvconnecteeamu/management-user -->
    ')
    ->setTemplate('page.php');

    /**
     *  Page: /creer-information
     */
    $controller->addPage(new \EC\VirtualPages\Page("/creer-information"))
    ->setTitle('Créer une information')
    ->setContent('
    <!-- wp:tvconnecteeamu/add-information -->
    test
    <!-- /wp:tvconnecteeamu/add-information -->
    ')
    ->setTemplate('page.php');

    /**
     *  Page: /creer-une-alerte
     */
    $controller->addPage(new \EC\VirtualPages\Page("/creer-une-alerte"))
    ->setTitle('Créer une alerte')
    ->setContent('
    <!-- wp:tvconnecteeamu/add-alert -->
    test
    <!-- /wp:tvconnecteeamu/add-alert -->

    <!-- wp:html -->
    <a href="/gerer-les-alertes/">Gérer les alertes</a>
    <!-- /wp:html -->
    ')
    ->setTemplate('page.php');

    /**
     *  Page: /emploi-du-temps
     */
    $controller->addPage(new \EC\VirtualPages\Page("/gerer-les-informations"))
    ->setTitle('Gérer les informations')
    ->setContent('
    <!-- wp:tvconnecteeamu/manage-information -->
    test
    <!-- /wp:tvconnecteeamu/manage-information -->
    ')
    ->setTemplate('page.php');

    /**
     *  Page: /gerer-les-informations/modification-information
     */
    $controller->addPage(new \EC\VirtualPages\Page("/gerer-les-informations/modification-information"))
    ->setTitle('Modifier une information')
    ->setContent('
    <!-- wp:heading {"level":1} -->
    <h1>Modifier une information</h1>
    <!-- /wp:heading -->

    <!-- wp:tvconnecteeamu/modify-information -->
    test
    <!-- /wp:tvconnecteeamu/modify-information -->
    ')
    ->setTemplate('page.php');

    /**
     *  Page: /emploi-du-temps
     */
    $controller->addPage(new \EC\VirtualPages\Page("/emploi-du-temps"))
    ->setTitle('Emploi du temps')
    ->setContent('
    <!-- wp:tvconnecteeamu/schedules -->
    test
    <!-- /wp:tvconnecteeamu/schedules -->
    ')
    ->setTemplate('page.php');

    /**
     *  Page: /me
     */
    $controller->addPage(new \EC\VirtualPages\Page("/me"))
    ->setTitle('Mon compte')
    ->setContent('
    <!-- wp:tvconnecteeamu/choose-account -->
    <!-- /wp:tvconnecteeamu/choose-account -->
    ')
    ->setTemplate('page.php');

    /**
     *  Page: /gestion-codes-ade/modification-code-ade
     */
    $controller->addPage(new \EC\VirtualPages\Page("/gestion-codes-ade/modification-code-ade"))
    ->setTitle('Modifier un code ADE')
    ->setContent('
    <!-- wp:heading {"level":1} -->
    <h1>Modifier code ADE</h1>
    <!-- /wp:heading -->

    <!-- wp:tvconnecteeamu/modify-code -->
    test
    <!-- /wp:tvconnecteeamu/modify-code -->
    ')
    ->setTemplate('page.php');
    /**
     *  Page: /gestion-codes-ade
     */
    $controller->addPage(new \EC\VirtualPages\Page("/gestion-codes-ade"))
    ->setTitle('Gestion des codes ADE')
    ->setContent('
    <!-- wp:tvconnecteeamu/manage-codes -->
      <!-- wp:tvconnecteeamu/add-code -->
      <!-- /wp:tvconnecteeamu/add-code -->
    <!-- /wp:tvconnecteeamu/manage-codes -->
    ')
    ->setTemplate('page.php');


    /**
     *  Page: /gerer-les-alertes
     */
    $controller->addPage(new \EC\VirtualPages\Page("/gerer-les-alertes"))
    ->setTitle('Gérer les alertes')
    ->setContent('
    <!-- wp:tvconnecteeamu/manage-alert -->
    <!-- /wp:tvconnecteeamu/manage-alert -->
    ')
    ->setTemplate('page.php');


    /**
     *  Page: /tablet-view
     */
    $controller->addPage(new \EC\VirtualPages\Page("/tablet-view"))
    ->setTitle('tablet-view')
    ->setContent('
    <!-- wp:tvconnecteeamu/tablet-select-year -->
    test
    <!-- /wp:tvconnecteeamu/tablet-select-year -->
    ')
    ->setTemplate('page-tablet.php');

    /**
     *  Page: /tablet-view/schedule
     */
    $controller->addPage(new \EC\VirtualPages\Page("/tablet-view/emploi-du-temps"))
    ->setTitle('tablet-view')
    ->setContent('
    <!-- wp:tvconnecteeamu/tablet-schedule -->
    test
    <!-- /wp:tvconnecteeamu/tablet-schedule -->
    ')
    ->setTemplate('page-tablet.php');
    
    /**
     *  Page: /tablet-view/schedule
     */
    $controller->addPage(new \EC\VirtualPages\Page("/cgu"))
    ->setTitle('Conditions générales d\'utilisation')
    ->setContent('
    <!-- wp:tvconnecteeamu/cgu -->
    test
    <!-- /wp:tvconnecteeamu/cgu -->
    ')
    ->setTemplate('page-tablet.php');

    /**
     *  Page: /secretary/welcome
     */
    $controller->addPage(new \EC\VirtualPages\Page("/secretary/welcome"))
        ->setTitle('secretary-welcome')
        ->setContent('
    <!-- wp:tvconnecteeamu/secretary-welcome -->
    test
    <!-- /wp:tvconnecteeamu/secretary-welcome -->
    ')
        ->setTemplate('header-blue.php');

    /**
     *  Page: /secretary/computer-rooms
     */
    $controller->addPage(new \EC\VirtualPages\Page("/secretary/computer-rooms"))
        ->setTitle('computer-rooms')
        ->setContent('
    <!-- wp:tvconnecteeamu/computer-rooms -->
    test
    <!-- /wp:tvconnecteeamu/computer-rooms -->
    ')
        ->setTemplate('header-orange.php');


    /**
     *  Page: /secretary/teacher-schedule
     */
    $controller->addPage(new \EC\VirtualPages\Page("/secretary/teacher-schedule"))
        ->setTitle('teacher-schedule')
        ->setContent('
    <!-- wp:tvconnecteeamu/teacher-schedule -->
    test
    <!-- /wp:tvconnecteeamu/teacher-schedule -->
    ')
        ->setTemplate('header-orange.php');


    /**
     *  Page: /secretary/main-menu
     */
    $controller->addPage(new \EC\VirtualPages\Page("/secretary/main-menu"))
        ->setTitle('Menu secretaire')
        ->setContent('
    <!-- wp:tvconnecteeamu/main-menu -->
    test
    <!-- /wp:tvconnecteeamu/main-menu -->
    ')
        ->setTemplate('header-blue.php');


    /**
     *  Page: /secretary/room-schedule
     */
    $controller->addPage(new \EC\VirtualPages\Page("/secretary/room-schedule"))
        ->setTitle('room-schedule')
        ->setContent('
    <!-- wp:tvconnecteeamu/room-schedule -->
    test
    <!-- /wp:tvconnecteeamu/room-schedule -->
    ')
        ->setTemplate('header-orange.php');


    /**
     *  Page: /secretary/year-student-schedule
     */
    $controller->addPage(new \EC\VirtualPages\Page("/secretary/year-student-schedule"))
        ->setTitle('year-student-schedule')
        ->setContent('
    <!-- wp:tvconnecteeamu/year-student-schedule -->
    test
    <!-- /wp:tvconnecteeamu/year-student-schedule -->
    ')
        ->setTemplate('header-blue.php');


    /**
     *  Page: /secretary/group-student-schedule
     */
    $controller->addPage(new \EC\VirtualPages\Page("/secretary/group-student-schedule"))
        ->setTitle('student-group')
        ->setContent('
    <!-- wp:tvconnecteeamu/group-student-schedule -->
    test
    <!-- /wp:tvconnecteeamu/group-student-schedule -->
    ')
        ->setTemplate('header-blue.php');


    /**
     *  Page: /secretary/all-years-schedule
     */
    $controller->addPage(new \EC\VirtualPages\Page("/secretary/all-years-schedule"))
        ->setTitle('Emplois du temps')
        ->setContent('
    <!-- wp:tvconnecteeamu/all-years-schedule -->
    test
    <!-- /wp:tvconnecteeamu/all-years-schedule -->
    ')
        ->setTemplate('header-blue.php');


    /**
     *  Page: /secretary/teacher-search-schedule
     */
    $controller->addPage(new \EC\VirtualPages\Page("/secretary/teacher-search-schedule"))
        ->setTitle('teacher-search-schedule')
        ->setContent('
    <!-- wp:tvconnecteeamu/teacher-search-schedule -->
    test
    <!-- /wp:tvconnecteeamu/teacher-search-schedule -->
    ')
        ->setTemplate('header-orange.php');


    /**
     *  Page: /secretary/weekly-computer-room-schedule
     */
    $controller->addPage(new \EC\VirtualPages\Page("/secretary/weekly-computer-room-schedule"))
        ->setTitle('computer-room-schedule')
        ->setContent('
    <!-- wp:tvconnecteeamu/weekly-computer-room-schedule -->
    test
    <!-- /wp:tvconnecteeamu/weekly-computer-room-schedule -->
    ')
        ->setTemplate('header-orange.php');

    $controller->addPage(new \EC\VirtualPages\Page("secretary/teacher-view"))
        ->setTitle('Teacher View')
        ->setContent('
         <!-- wp:tvconnecteeamu/teacher-view -->
    test
    <!-- /wp:tvconnecteeamu/teacher-view -->
    ')
        ->setTemplate('page.php');

    
    $controller->addPage(new \EC\VirtualPages\Page("secretary/rooms-available"))
        ->setTitle('rooms-available')
        ->setContent('
         <!-- wp:tvconnecteeamu/rooms-available -->
    test
    <!-- /wp:tvconnecteeamu/rooms-available -->
    ')
        ->setTemplate('header-orange.php');

    $controller->addPage(new \EC\VirtualPages\Page("secretary/homepage"))
        ->setTitle('homepage')
        ->setContent('
         <!-- wp:tvconnecteeamu/homepage -->
    test
    <!-- /wp:tvconnecteeamu/homepage -->
    ');

    $controller->addPage(new \EC\VirtualPages\Page("secretary/config-schedule"))
        ->setTitle('config-schedule')
        ->setContent('
         <!-- wp:tvconnecteeamu/config-schedule -->
    test
    <!-- /wp:tvconnecteeamu/config-schedule -->
    ')
        ->setTemplate('header-blue.php');


    $controller->addPage(new \EC\VirtualPages\Page("/blocks/course-color/modify"))
        ->setTitle('modify-course-color')
        ->setContent('

    <!-- wp:tvconnecteeamu/modify-course-color -->
    <!-- /wp:tvconnecteeamu/modify-course-color -->
    ')
        ;


    $controller->addPage(new \EC\VirtualPages\Page("/secretary/config"))
        ->setTitle('secretary-config')
        ->setContent('

    <!-- wp:tvconnecteeamu/secretary-config -->
    test
    <!-- /wp:tvconnecteeamu/secretary-config -->
    ')
        ->setTemplate('header-blue.php');


    $controller->addPage(new \EC\VirtualPages\Page("/secretary/lock-room"))
        ->setTitle('lock-room')
        ->setContent('

    <!-- wp:tvconnecteeamu/lock-room -->
    test
    <!-- /wp:tvconnecteeamu/lock-room -->
    ')
        ->setTemplate('header-orange.php');


    $controller->addPage(new \EC\VirtualPages\Page("/secretary/room/lock"))
        ->setTitle('room-lock-action')
        ->setContent('

    <!-- wp:tvconnecteeamu/room-lock-action -->
    test
    <!-- /wp:tvconnecteeamu/room-lock-action -->
    ')
        ->setTemplate('header-orange.php');


    $controller->addPage(new \EC\VirtualPages\Page("/secretary/room/unlock"))
        ->setTitle('room-unlock-action')
        ->setContent('

    <!-- wp:tvconnecteeamu/room-unlock-action -->
    test
    <!-- /wp:tvconnecteeamu/room-unlock-action -->
    ')
        ->setTemplate('header-orange.php');

    $controller->addPage(new \EC\VirtualPages\Page("/secretary/all-years"))
        ->setTitle('all-years')
        ->setContent('

    <!-- wp:tvconnecteeamu/all-years -->
    test
    <!-- /wp:tvconnecteeamu/all-years -->
    ')
        ->setTemplate('secretary-tv.php');


    $controller->addPage(new \EC\VirtualPages\Page("/secretary/config-ade"))
        ->setTitle('config-ade')
        ->setContent('

    <!-- wp:tvconnecteeamu/config-ade -->
    
    <!-- /wp:tvconnecteeamu/config-ade -->
    ')
        ->setTemplate('header-orange.php');

    $controller->addPage(new \EC\VirtualPages\Page("/secretary/config-computer-room"))
        ->setTitle('config-computer-room')
        ->setContent('

    <!-- wp:tvconnecteeamu/config-computer-room -->
    
    <!-- /wp:tvconnecteeamu/config-computer-room -->
    ')
        ->setTemplate('header-orange.php');
});
