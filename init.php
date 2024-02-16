<?php

use Controllers\AlertRestController;
use Controllers\CodeAdeRestController;
use Controllers\InformationRestController;
use Controllers\ProfileRestController;
use Controllers\ScheduleRestController;

include_once 'vendor/R34ICS/R34ICS.php';
include 'widgets/WidgetAlert.php';
include 'widgets/WidgetWeather.php';
include 'widgets/WidgetInformation.php';

// Login for viewer
require 'config-sample.php';

/**
 * Create all direcstory
 * (For ICS file and media)
 */

if (!file_exists(PATH . TV_UPLOAD_PATH)) {
    mkdir(PATH . TV_UPLOAD_PATH);
}

if (!file_exists(PATH . TV_ICSFILE_PATH)) {
    mkdir(PATH . TV_ICSFILE_PATH, 0777);
}

if (!file_exists(PATH . TV_ICSFILE_PATH . 'file0')) {
    mkdir(PATH . TV_ICSFILE_PATH . 'file0', 0777);
}

if (!file_exists(PATH . TV_ICSFILE_PATH . 'file1')) {
    mkdir(PATH . TV_ICSFILE_PATH . 'file1', 0777);
}

if (!file_exists(PATH . TV_ICSFILE_PATH . 'file2')) {
    mkdir(PATH . TV_ICSFILE_PATH . 'file2', 0777);
}

if (!file_exists(PATH . TV_ICSFILE_PATH . 'file3')) {
    mkdir(PATH . TV_ICSFILE_PATH . 'file3', 0777);
}

/**
 * Include all scripts
 * (CSS, JS)
 */

function loadScriptsEcran()
{
    /* SCRIPTS */
    wp_enqueue_script('global_script', TV_PLUG_PATH . 'public/js/global.js', array(), VERSION, true);

    /**
    * Chargement conditionnel
    * Cette technique permet de charger les scripts et styles nécessaires à une page spéciale UNIQUEMENT à cette page,
    * pour améliorer les performances
    * @author Thomas Cardon
    */
    if (is_page('tablet-view')) {
        wp_enqueue_script('tablet-search', TV_PLUG_PATH . 'public/js/tablet-view/search.js', array(), VERSION, true);
        return;
    }
    
    /**
     * Désactivation complète de jQuery
     * hors tableau spécial administrateur
     * pour gain de performances
     * (endpoint /wp-admin)
     * @author Thomas Cardon
     */
     if (!is_admin()) wp_deregister_script('jquery');
     
     wp_enqueue_style('style_ecran', TV_PLUG_PATH . 'public/css/style.css', array(), VERSION);
     wp_enqueue_style('style_notif', TV_PLUG_PATH . 'public/css/secretary-notification.css', array(), VERSION);

    if(is_page('secretary-welcome')){
        wp_enqueue_style('welcome_secretary', TV_PLUG_PATH . 'public/css/secretaryAccueil.css', array(), VERSION);
    }

    if(is_page('teacher-search-schedule')){
        wp_enqueue_style('teacher_search_schedule', TV_PLUG_PATH . 'public/css/teacherSearchSchedule.css', array(), VERSION);
    }

    if(is_page('teacher-schedule') || is_page('student-group') || is_page('room-schedule')){
        wp_enqueue_style('teacher_schedule', TV_PLUG_PATH . 'public/css/teacherSchedule.css', array(), VERSION);
    }

    if(is_page('year-student-schedule') || is_page('all-years') || members_current_user_has_role("secretarytv") ){
        wp_enqueue_style('teacher_schedule', TV_PLUG_PATH . 'public/css/yearStudentSchedule.css', array(), VERSION);
        wp_enqueue_style('year-student', TV_PLUG_PATH . 'public/css/teacherSchedule.css', array(), VERSION);
    }

    if(is_page('computer-rooms') || is_page('lock-room')){
        wp_enqueue_style('teacher_search_schedule', TV_PLUG_PATH . 'public/css/availableComputerRooms.css', array(), VERSION);
    }
    if (is_page('teacher-view')) {
        wp_enqueue_style('teacher_view', TV_PLUG_PATH . 'public/css/teacherview.css', array(), VERSION);
    }
    if (is_page('rooms-available')) {
        wp_enqueue_style('rooms_available', TV_PLUG_PATH . 'public/css/rooms_available.css', array(), VERSION);
    }

    if(is_page('computer-room-schedule')){
        wp_enqueue_style('computer_room_schedule', TV_PLUG_PATH . 'public/css/computerRoomSchedule.css', array(), VERSION);
    }
    if (is_page('homepage')) {
        wp_enqueue_style('homepage', TV_PLUG_PATH . 'public/css/homepage.css', array(), VERSION);
    }

    if (is_page('secretary-config')) {
        wp_enqueue_style('secretary-config', TV_PLUG_PATH . 'public/css/secretaryConfig.css', array(), VERSION);
    }

    if (is_page('config-ade')) {
        wp_enqueue_style('secretary-config', TV_PLUG_PATH . 'public/css/secretary-ade-config.css', array(), VERSION);
    }
    if (is_page('config-computer-room')) {
        wp_enqueue_style('secretary-room-config', TV_PLUG_PATH . 'public/css/computerRoomConfig.css', array(), VERSION);
    }

    if(is_page('config-schedule')){
        wp_enqueue_style('room-schedule', TV_PLUG_PATH . 'public/css/secretaryConfigSchedule.css', array(), VERSION);
        wp_enqueue_script('color_update_script', TV_PLUG_PATH . 'public/js/updateConfigCourseColor.js', array(), VERSION, true);
    }

    if(is_page('all-years') || is_page('weekly-computer-room-schedule/')){
        wp_enqueue_script('refreshAuto', TV_PLUG_PATH . 'public/js/refreshAtAnHour.js', array(), VERSION);
    }

    if (is_page('tv-mode')) {
        /* STYLESHEETS */
        wp_enqueue_style('alert_ecran', TV_PLUG_PATH . 'public/css/alert.css', array(), VERSION);
        wp_enqueue_style('info_ecran', TV_PLUG_PATH . 'public/css/information.css', array(), VERSION);
        wp_enqueue_style('schedule_ecran', TV_PLUG_PATH . 'public/css/schedule.css', array(), VERSION);
        wp_enqueue_style('weather_ecran', TV_PLUG_PATH . 'public/css/weather.css', array(), VERSION);

        /* SCRIPTS */
        wp_enqueue_script('weather_script_ecran', TV_PLUG_PATH . 'public/js/weather.js', array(), VERSION, true);
        wp_enqueue_script('time_script_ecran', TV_PLUG_PATH . 'public/js/time.js', array(), VERSION, true);
        wp_enqueue_script('scroll_script_ecran', TV_PLUG_PATH . 'public/js/scroll.js', array(), VERSION, true);
        
        wp_enqueue_script('news_script_ecran', TV_PLUG_PATH . 'public/js/news.js', array('pdfJs_tv_script', 'pdfJs_core_tv_script'), VERSION, true);

        wp_enqueue_script('pdfJs_tv_script', TV_PLUG_PATH . 'public/vendor/pdf-js/pdf.js', array(), VERSION, true);
        wp_enqueue_script('pdfJs_core_tv_script', TV_PLUG_PATH . 'public/vendor/pdf-js/pdf.worker.js', array(), VERSION, true);

        if (TV_REFRESH)
          wp_enqueue_script('refresh_script_ecran', TV_PLUG_PATH . 'public/js/refresh.js', array(), VERSION, true);
    }
    else {
      /**
       * Sortable: a tiny, vanilla JS table sorter
       * @author tofsjonas
       * @see https://github.com/tofsjonas/sortable
       */
       
      wp_enqueue_script('sortable_script', TV_PLUG_PATH . 'public/vendor/sortable/sortable.min.js', array(), VERSION, true);
      wp_enqueue_style('sortable_style', TV_PLUG_PATH . 'public/vendor/sortable/sortable.min.css', array(), VERSION);

      wp_enqueue_script('jquery', 'https://code.jquery.com/jquery-3.7.1.js', array(), 3.7, true);
      wp_enqueue_script('add_delete_tv_code_script', TV_PLUG_PATH . 'public/js/addOrDeleteTvCode.js', array('jquery'), VERSION, true);
      wp_enqueue_script('addAllCheckBox_tv_script', TV_PLUG_PATH . 'public/js/addAllCheckBox.js', array(), VERSION, true);
      wp_enqueue_script('deleteRow_tv_script', TV_PLUG_PATH . 'public/js/deleteRow.js', array(), VERSION, true);
      wp_enqueue_script('confPass_script_ecran', TV_PLUG_PATH . 'public/js/confirmPass.js', array(), VERSION, true);
      wp_enqueue_script('search_script_ecran', TV_PLUG_PATH . 'public/js/search.js', array(), VERSION, true);
    }
}

add_action('wp_enqueue_scripts', 'loadScriptsEcran');

/**
 * Create tables in the database (Alert & Information)
 */
function installDatabaseEcran()
{
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $table_name = 'ecran_information';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id INT(10) NOT NULL AUTO_INCREMENT,
			title VARCHAR (40),
			content VARCHAR(280) NOT NULL,
			creation_date datetime DEFAULT NOW() NOT NULL,
			expiration_date datetime NOT NULL,
			author BIGINT(20) UNSIGNED NOT NULL,
			type VARCHAR (10) DEFAULT 'text' NOT NULL,
			administration_id INT(10) DEFAULT NULL,
			PRIMARY KEY (id),
			FOREIGN KEY (author) REFERENCES wp_users(ID) ON DELETE CASCADE
		) $charset_collate;";

    dbDelta($sql);

    $table_name = 'ecran_alert';

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id INT(10) NOT NULL AUTO_INCREMENT,
			content VARCHAR(280) NOT NULL,
			creation_date datetime DEFAULT NOW() NOT NULL,
			expiration_date datetime NOT NULL,
			author BIGINT(20) UNSIGNED NOT NULL,
			for_everyone INT(1) DEFAULT '1' NOT NULL,
			administration_id INT(10) DEFAULT NULL,
			PRIMARY KEY (id),
			FOREIGN KEY (author) REFERENCES wp_users(ID) ON DELETE CASCADE
		) $charset_collate;";

    dbDelta($sql);

    $table_name = 'ecran_code_ade';

    $query = "CREATE TABLE IF NOT EXISTS $table_name (
			id INT(10) NOT NULL AUTO_INCREMENT,
			type VARCHAR(15) NOT NULL,
			title VARCHAR (60) NOT NULL,
			code VARCHAR (20) NOT NULL,
			PRIMARY KEY (id)
			) $charset_collate;";

    dbDelta($query);

    // With wordpress id = 1 can't be access if we do : /page/1
    $sql = "ALTER TABLE $table_name AUTO_INCREMENT = 2;";
    dbDelta($sql);

    $table_name = 'ecran_code_alert';

    $query = "CREATE TABLE IF NOT EXISTS $table_name (
			alert_id INT(10) NOT NULL ,
			code_ade_id INT(10) NOT NULL ,
			PRIMARY KEY (alert_id, code_ade_id),
			FOREIGN KEY (alert_id) REFERENCES ecran_alert(id) ON DELETE CASCADE,
			FOREIGN KEY (code_ade_id) REFERENCES ecran_code_ade(id) ON DELETE CASCADE
			) $charset_collate;";

    dbDelta($query);

    $table_name = 'ecran_code_user';

    $query = "CREATE TABLE IF NOT EXISTS $table_name (
			user_id BIGINT(20) UNSIGNED NOT NULL,
			code_ade_id INT(10) NOT NULL ,
			PRIMARY KEY (user_id, code_ade_id),
			FOREIGN KEY (user_id) REFERENCES wp_users(ID) ON DELETE CASCADE,
			FOREIGN KEY (code_ade_id) REFERENCES ecran_code_ade(id) ON DELETE CASCADE
			) $charset_collate;";

    dbDelta($query);

    $table_name = 'ecran_code_delete_account';

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id INT(10) NOT NULL AUTO_INCREMENT,
			user_id BIGINT(20) UNSIGNED NOT NULL,
			code VARCHAR(40) NOT NULL,
			PRIMARY KEY (id),
			FOREIGN KEY (user_id) REFERENCES wp_users(ID) ON DELETE CASCADE
		) $charset_collate;";

    dbDelta($sql);

    $table_name = 'teacher';

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id int(11) NOT NULL,
            name varchar(50) NOT NULL)";

    dbDelta($sql);

    $table_name = 'ecran_rooms';

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            ID int(11) NOT NULL,
            name varchar(20) NOT NULL,
            isComputerRoom tinyint(1) NOT NULL DEFAULT 0
            )";

    dbDelta($sql);

    $table_name = "secretary_courses";

    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
            id int(11) NOT NULL,
            name varchar(80) NOT NULL,
            color varchar(7) NOT NULL)";

    dbDelta($sql);

    $table_name = "ecran_ade_years";

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            code int(11) NOT NULL,
            year int(11) NOT NULL)";

    dbDelta($sql);

    $table_name = "secretary_lock_room";

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
              roomName varchar(20) NOT NULL,
              motif varchar(300) NOT NULL,
              lockEndDate datetime NOT NULL)";

    dbDelta($sql);



}

add_action('plugins_loaded', 'installDatabaseEcran');


/*
 * CREATE ROLES
 */

$result = add_role(
    'secretaire',
    __('Secretaire'),
    array(
        'read' => true,  // true allows this capability
        'edit_posts' => true,
        'delete_posts' => false, // Use false to explicitly deny
    )
);

$result = add_role(
    'television',
    __('Television'),
    array(
        'read' => true,  // true allows this capability
        'edit_posts' => true,
        'delete_posts' => false, // Use false to explicitly deny
    )
);

$result = add_role(
    'etudiant',
    __('Etudiant'),
    array(
        'read' => true,  // true allows this capability
        'edit_posts' => true,
        'delete_posts' => false, // Use false to explicitly deny
    )
);

$result = add_role(
    'enseignant',
    __('Enseignant'),
    array(
        'read' => true,  // true allows this capability
        'edit_posts' => true,
        'delete_posts' => false, // Use false to explicitly deny
    )
);

$result = add_role(
    'technicien',
    __('Technicien'),
    array(
        'read' => true,  // true allows this capability
        'edit_posts' => true,
        'delete_posts' => false, // Use false to explicitly deny
    )
);

$result = add_role(
    'directeuretude',
    __('Directeur etude'),
    array(
        'read' => true,  // true allows this capability
        'edit_posts' => true,
        'delete_posts' => false, // Use false to explicitly deny
    )
);
$result = add_role(
    'informationposter',
    __('informationPoster'),
    array(
        'read' => true,  // true allows this capability
    )
);

$result = add_role(
    'computerroom',
    __('ComputerRoom'),
    array(
        'read' => true,  // true allows this capability
    )
);

$result = add_role(
    'secretarytv',
    __('secretaryTV'),
    array(
        'read' => true,  // true allows this capability
    )
);

/*
 * CREATE REST API ENDPOINTS
 */

add_action('rest_api_init', function () {
    error_reporting(0);
    
    $controller = new InformationRestController();
    $controller->register_routes();

    $controller = new CodeAdeRestController();
    $controller->register_routes();

    $controller = new AlertRestController();
    $controller->register_routes();

    $controller = new ProfileRestController();
    $controller->register_routes();

    $controller = new ScheduleRestController();
    $controller->register_routes();
});
