<?php

if( ! class_exists('WP_REST_Controller') ){
	require_once WP_PATCHER_INCLUDES . '/api-endpoints/class-wp-rest-controller.php';
}

require_once WP_PATCHER_INCLUDES . '/api-endpoints/class-wp-rest-patches-controller.php';
