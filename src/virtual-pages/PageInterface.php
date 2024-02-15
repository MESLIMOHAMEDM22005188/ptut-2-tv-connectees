<?php namespace EC\VirtualPages;

interface PageInterface {

    function getUrl();

    function getTemplate();

    function getTitle();

    function setTitle( $title );

    function setContent( $content );

    function setTemplate( $template );

    function asWpPost();

}
