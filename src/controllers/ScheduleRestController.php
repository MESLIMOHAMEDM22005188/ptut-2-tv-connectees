<?php

namespace Controllers;

use Models\Information;

use Views\ICSView;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

use R34ICS;

class ScheduleRestController extends WP_REST_Controller
{
    /**
     * Constructor for the REST controller
     */
    public function __construct() {
        $this->namespace = 'amu-ecran-connectee/v1';
        $this->rest_base = 'schedule';
    }

    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<code>\d+)',
            array(
                array(
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => array($this, 'get_items'),
                    'args' => $this->get_collection_params(),
                ),
                'schema' => array($this, 'get_public_item_schema'),
            )
        );
    }

        /**
     * Get the path of a code
     *
     * @param $code     int
     *
     * @return string
     */
    public function getFilePath($code) {
        $base_path = ABSPATH . TV_ICSFILE_PATH;

        // Check if local file exists
        for ($i = 0; $i <= 3; ++$i) {
            $file_path = $base_path . 'file' . $i . '/' . $code . '.ics';
            // TODO: Demander a propos du filesize
            if (file_exists($file_path) && filesize($file_path) > 100)
                return $file_path;
        }

        // No local version, let's download one
        $this->addFile($code);
        return $base_path . "file0/" . $code . '.ics';
    }

    /**
     * Upload a ics file
     *
     * @param $code     int Code ADE
     */
    public function addFile($code) {
        try {
            $path = ABSPATH . TV_ICSFILE_PATH . "file0/" . $code . '.ics';
            $url = $this->getUrl($code);
            //file_put_contents($path, fopen($url, 'r'));
            $contents = '';
            if (($handler = @fopen($url, "r")) !== FALSE) {
                while (!feof($handler)) {
                    $contents .= fread($handler, 8192);
                }
                fclose($handler);
            } else {
                throw new Exception('File open failed.');
            }
            if ($handle = fopen($path, "w")) {
                fwrite($handle, $contents);
                fclose($handle);
            } else {
                throw new Exception('File open failed.');
            }
        } catch (Exception $e) {
            $this->addLogEvent($e);
        }
    }

    /**
     * Display schedule
     *
     * @param $code     int Code ADE of the schedule
     * @param $allDay   bool
     *
     * @return string|bool
     */
    public function displaySchedule($code, $allDay = false) {
        global $R34ICS;
        $R34ICS = new R34ICS();

        $url = $this->getFilePath($code);
        $args = array(
            'disable_sorting' => true
        );
        $datas = $R34ICS->display_calendar($url, $code, $allDay, $args, true);
        $icsView = new ICSView();
        return $icsView->displaySchedule($datas[0], $datas[1], $datas[2]);
    }

    /**
     * Get a collection of items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_items($request) {        
        $s = $this->displaySchedule($request['code']);
        ob_clean();

        $data = array(
            "success" => $s === '' ? false : true,
            "errorCode" => $s === '' ? 501 : 200,
            "code" => "8402",
            "data" => $s
        );


        return new WP_REST_Response($data, 200);
    }


    /**
     * Check if a given request has access to get items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_items_permissions_check($request) {
        return true;
    }

    /**
     * Checks if a given request has access to create an information.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has access to create items, WP_Error object otherwise.
     */
    public function create_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Retrieves the query params for collections.
     *
     * @return array Collection parameters.
     */
    public function get_collection_params() {
        $query_params = [];

        $query_params['limit'] = array(
            'description' => __('Maximum number of schedule to fetch'),
            'type' => 'integer',
            'default' => 25,
        );

        $query_params['offset'] = array(
            'description' => __('Offset of the schedule to fetch'),
            'type' => 'integer',
            'default' => 0,
        );

        return apply_filters('rest_user_collection_params', $query_params);
    }
}