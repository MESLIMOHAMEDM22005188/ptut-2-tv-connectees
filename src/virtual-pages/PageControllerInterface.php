<?php namespace EC\VirtualPages;

interface PageControllerInterface {

    /**
     * Init the controller, fires the hook that allows consumer to add pages
     */
    function init();

    /**
     * Register a page object in the controller
     *
     * @param  \EC\VirtualPages\PageInterface $page
     * @return \EC\VirtualPages\PageInterface
     */
    function addPage( PageInterface $page );

    /**
     * Run on 'do_parse_request' and if the request is for one of the registered pages
     * setup global variables, fire core hooks, requires page template and exit.
     *
     * @param boolean $bool The boolean flag value passed by 'do_parse_request'
     * @param \WP $wp       The global wp object passed by 'do_parse_request'
     */
    function dispatch( $bool, \WP $wp );
}
