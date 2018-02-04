<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Patcher
 * @subpackage WP_Patcher/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    WP_Patcher
 * @subpackage WP_Patcher/admin
 * @author     Author Name <author@name.com>
 */
class WP_Patcher_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $wp_patcher    The ID of this plugin.
	 */
	private $wp_patcher;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $wp_patcher       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $wp_patcher, $version ) {

		$this->wp_patcher = $wp_patcher;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		global $current_screen;

		if( 'code_patch' === $current_screen->post_type ){
		
			wp_enqueue_style( $this->wp_patcher, plugin_dir_url( __FILE__ ) . 'css/wp-patcher-admin.css', array(), $this->version, 'all' );
		
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		global $current_screen;
		
		if( 'code_patch' === $current_screen->post_type ){
		
			wp_enqueue_media();
			
			wp_register_script( $this->wp_patcher, plugin_dir_url( __FILE__ ) . 'js/wp-patcher-admin.js', array( 'jquery' ), $this->version, false );

			$translation_array = array(
				'ChoosePatchFile' => __( 'Choose Patch File', WP_PATCHER_SLUG ),
				'ChooseFile' => __( 'Choose File', WP_PATCHER_SLUG ),
			);

			wp_localize_script( $this->wp_patcher, 'WpPatcherTexts', $translation_array );

			wp_enqueue_script( $this->wp_patcher );
		}

	}

}
