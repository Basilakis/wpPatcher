<?php

/*
    Plugin Name: wpPatcher - Remote Updates
    Plugin URI: http://www.creativeg.gr
    Description: Send Remote Updates to your WordPress Projects
    Author: Basilis Kanonidis
    Version: 1.0
    Author URI: http://www.creativeg,gr
    */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once 'includes/definitions.php';

require_once WP_PATCHER_INCLUDES . '/functions.php';
require_once WP_PATCHER_INCLUDES . '/code-patch-cpt.php';

/**
 * Need for WP version < 4.4.0.
 */
if( ! defined( 'REST_API_VERSION' ) ) {
    require_once WP_PATCHER_INCLUDES . '/api.php';
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/classes/class-wp-patcher-activator.php
 */
function activate_wp_patcher() {
	require_once WP_PATCHER_INCLUDES . '/classes/class-wp-patcher-activator.php';
	WP_Patcher_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/classes/class-wp-patcher-deactivator.php
 */
function deactivate_wp_patcher() {
	require_once WP_PATCHER_INCLUDES . '/classes/class-wp-patcher-deactivator.php';
	WP_Patcher_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_patcher' );
register_deactivation_hook( __FILE__, 'deactivate_wp_patcher' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require WP_PATCHER_INCLUDES . '/classes/class-wp-patcher.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_wp_patcher() {

	$plugin = new WP_Patcher();
	$plugin->run();

}
run_wp_patcher();
