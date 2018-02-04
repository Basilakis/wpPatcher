<?php
if ( ! defined( 'WP_PATCHER_VERSION' ) ) { define( 'WP_PATCHER_VERSION', '1.0.0' ); }
if ( ! defined( 'WP_PATCHER_SLUG' ) ) { define( 'WP_PATCHER_SLUG', 'wp-patcher' ); }
if ( ! defined( 'WP_PATCHER_ROOT' ) ) { define( 'WP_PATCHER_ROOT', substr( plugin_dir_path( __DIR__, 1 ), 0, -1 ) ); }
if ( ! defined( 'WP_PATCHER_INCLUDES' ) ) { define( 'WP_PATCHER_INCLUDES', WP_PATCHER_ROOT . '/includes' ); }
if ( ! defined( 'WP_PATCHER_REST_API' ) ) { define( 'WP_PATCHER_REST_API', WP_PATCHER_INCLUDES . '/api' ); }