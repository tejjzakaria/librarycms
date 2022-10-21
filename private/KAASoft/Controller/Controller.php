<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller;

    /**
     * Interface Controller
     * @package KAASoft\Controller
     */
    interface Controller {
        const AJAX_PARAM_NAME_REDIRECT = "redirect";
        const AJAX_PARAM_NAME_ERROR    = "error";
        const AJAX_PARAM_NAME_WARNING  = "warning";
        const AJAX_PARAM_NAME_SUCCESS  = "success";
        const AJAX_PARAM_NAME_HTML     = "html";

        const CONTENT_TYPE_HTML = "text/html";
        const CONTENT_TYPE_XML  = "text/xml";
        const CONTENT_TYPE_JSON = "application/json";


        public function processRequest($args = null);
    }