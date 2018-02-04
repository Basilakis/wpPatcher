<?php

class WP_REST_Patches_Controller extends WP_REST_Controller {

	/**
     * Register the routes for the objects of the controller
     */
    public function register_routes() {

		register_rest_route( 'wp/v2', '/patches', array(
	        array(
	            'methods' => WP_REST_Server::READABLE,
	            'callback' => array( $this, 'get_all_patches' ),
	            'args'            => $this->get_endpoint_args_for_item_schema( WP_REST_Server::READABLE ),
	        ),
	        'schema' => array( $this, 'get_public_item_schema' ),
	    ));

	    register_rest_route( 'wp/v2', '/patch/(?P<patch_number_id>[\w-]+)', array(
	        array(
	            'methods' => WP_REST_Server::READABLE,
	            'callback' => array( $this, 'get_patch' ),
	            'args'            => $this->get_endpoint_args_for_item_schema( WP_REST_Server::READABLE ),
	        ),
	        'schema' => array( $this, 'get_public_item_schema' ),
	    ));

	    register_rest_route( 'wp/v2', '/patches/slug/(?P<patch_slug>[\w-]+)', array(
	        array(
	            'methods' => WP_REST_Server::READABLE,
	            'callback' => array( $this, 'get_patch' ),
	            'args'            => $this->get_endpoint_args_for_item_schema( WP_REST_Server::READABLE ),
	        ),
	        'schema' => array( $this, 'get_public_item_schema' ),
	    ));

	    register_rest_route( 'wp/v2', '/patch/slug/code/(?P<patch_slug>[\w-]+)', array(
	        array(
	            'methods' => WP_REST_Server::READABLE,
	            'callback' => array( $this, 'get_patch_slug_code' ),
	            'args'            => $this->get_endpoint_args_for_item_schema( WP_REST_Server::READABLE ),
	        ),
	        'schema' => array( $this, 'get_public_item_schema' ),
	    ));

	    register_rest_route( 'wp/v2', '/patch_file/(?P<patch_number_id>[\w-]+)', array(
	        array(
	            'methods' => WP_REST_Server::READABLE,
	            'callback' => array( $this, 'get_patch_file' ),
	            'args'            => $this->get_endpoint_args_for_item_schema( WP_REST_Server::READABLE ),
	        ),
	        'schema' => array( $this, 'get_public_item_schema' ),
	    ));
	}

	public function get_patch_file( $request ) {

		$params = $request->get_params();

		if( isset( $params['patch_number_id'] ) && ! empty( $params['patch_number_id'] ) ){
			
            $requestFromDomain = isset( $params['site'] ) && ! empty( $params['site'] ) ? $params['site'] : false;

            if( false !== $requestFromDomain ){
            
                $patch = get_patch_info( $params['patch_number_id'], false, $requestFromDomain );

    			$patch_attachment_id = (int) $patch[0]['file_id'];

    			$filepath = get_attached_file( $patch_attachment_id );

    			$path_parts = pathinfo($filepath);

    			if( 'zip' === $path_parts['extension'] ){
    				wp_safe_redirect( wp_get_attachment_url( $patch_attachment_id ) );
    			}
            }
		}

		exit;
	}

	/**
     * Get all patches data
     */
	public function get_all_patches( $request ){

		$params = $request->get_params();

        $requestFromDomain = isset( $params['site'] ) && ! empty( $params['site'] ) ? $params['site'] : false;

        if( false !== $requestFromDomain ){

            $response = get_patch_info(false, false, $requestFromDomain);

            if( ! $response ) {
                return new WP_Error( __( 'None available patch.', WP_PATCHER_SLUG ) );
            }
        }
        else{

            $return = array(
                'code' => '',
                'msg' => __( 'Patches did not found.', WP_PATCHER_SLUG),
                'error' => 1,
            );
        }

        return $this->remove_invalid_patches( $response );
	}

	/**
     * Get single patch data
     */
	public function get_patch( $request ){

        $return = array();
		$response = array();

		$params = $request->get_params();

        $requestFromDomain = isset( $params['site'] ) && ! empty( $params['site'] ) ? $params['site'] : false;
    	
    	if( isset( $params['patch_number_id'] ) && ! empty( $params['patch_number_id'] ) ){
			$response = get_patch_info( $params['patch_number_id'], false, $requestFromDomain );
		}

		if( isset( $params['patch_slug'] ) && ! empty( $params['patch_slug'] ) ){
			$response = get_patch_info( false, $params['patch_slug'], $requestFromDomain );
		}

		if( $response && is_array( $response ) ) {
            $return = $this->remove_invalid_patches( $response );
        }
        else{
            $return = array(
                'code' => '',
                'msg' => __( 'Patch did not found.', WP_PATCHER_SLUG),
                'error' => 1,
            );
        }

        if( empty( $return ) ) {
            return new WP_Error( __( 'Failed to display patch data.', WP_PATCHER_SLUG ) );
        }

        return $return;
	}

	private function remove_invalid_patches( $patches = array() ){

		if( ! empty( $patches ) ){

			foreach ($patches as $key => $val) {

				if( '' === trim( $val['date_gmt'] ) || 
					'' === trim( $val['for'] ) || 
					'' === trim( $val['for_slug'] ) || 
					'' === trim( $val['number_id'] ) || 
					'' === trim( $val['file_id'] ) || 
					'' === trim( $val['filename'] ) ){
					unset( $patches[$key] );
				}

			}
		}

		return $patches;
	}

    private function check_requested_domain_url( $url = false ){
        $return = false;

        if( $url ){

        }

        return $return;
    }

    public function get_patch_slug_code( $request ){

        header("Content-Type: text/plain");

        $params = $request->get_params();

        if( isset( $params['patch_slug'] ) && ! empty( $params['patch_slug'] ) ) {

            $patch_data = array();

            $requestFromDomain = isset( $params['site'] ) && ! empty( (string) $params['site'] ) ? (string) $params['site'] : false;
                
            $patch_data = get_patch_info( false, $params['patch_slug'], $requestFromDomain );

            if( $patch_data && ! empty( $patch_data ) ){

                $patch_data = $patch_data[0];

                echo 'class WP_Patcher_'. $params['patch_slug'] .' {

    private $patches_for =  "' . $patch_data['for'] . '"; // "theme" or "plugin".
    private $patch_file_slug = "'. $params['patch_slug'] .'";
    
    private $patches_root_api = "'. home_url() .'/wp-json/wp/v2/";
    private $available_patches_api;

    private $patches = array();    

    public function __construct() {
        $this->available_patches_api = $this->patches_root_api . "patches/slug/" . $this->patch_file_slug;
        add_action( "init", array( $this, "check_to_apply_patch" ) );
    }

    public function patcher_shortcode( $attrs=array(), $content="" ) {
        return $this->patches_table_list();
    }

    /*
     * Check if requested to apply patch chanegs, and if is requested call the function.
     */
    public function check_to_apply_patch(){

        if( isset( $_GET["wp_patcher_apply_" . $this->patch_file_slug] ) && ! empty( $_GET["wp_patcher_apply_" . $this->patch_file_slug] ) ){
        
            if( isset( $_GET["apply_patch"] ) && ! empty( $_GET["apply_patch"] ) ){

                if( isset( $_GET["_wpnonce"] ) && ! empty( $_GET["_wpnonce"] ) && wp_verify_nonce( $_GET["_wpnonce"], "wp-patcher-" . $this->patch_file_slug . "-nonce" ) ){
        
                    $this->apply_patch( $_GET["apply_patch"], base64_decode( $_GET["wp_patcher_apply_" . $this->patch_file_slug] ) );

                }
        
            }
        
        }
    }

    private function init_patches() {
        $this->patches = $this->get_available_patches();
    }

    private function get_available_patches() {

        $return = array();
        
        $response = wp_remote_get( $this->available_patches_api . "?site=" . home_url() );

        if( is_array( $response ) && isset( $response["response"] ) && isset( $response["response"]["code"] ) && 404 !== $response["response"]["code"] ? true : false ) {
        
            $body = json_decode( $response["body"] );
            
            if( ! isset( $body->error ) || 1 !== (int) $body->error ){
                $return = $body;
            }
        }

        return $return;
    }

    private function apply_patch( $number_id = false, $file_url = false ) {

        if( $number_id && $file_url ){

            $file_url = esc_url( $file_url );

            $fileData = wp_remote_get( $file_url );

            if( $fileData && is_array( $fileData ) && isset( $fileData["response"] ) && isset( $fileData["response"]["code"] ) && 200 === $fileData["response"]["code"] ){

                if( $this->is_zip_file( $fileData ) ){

                    $patch_changes_location = false;

                    switch( $this->patches_for ){
                        case "plugin":
                            if( $this->plugin_is_installed( $this->patch_file_slug ) ){
                                $patch_changes_location = WP_PLUGIN_DIR . "/" . $this->patch_file_slug;
                            }
                            break;
                        case "theme":
                            if( $this->theme_is_installed( $this->patch_file_slug ) ){
                                $patch_changes_location = WP_CONTENT_DIR . "/themes/" . $this->patch_file_slug;
                            }
                            break;
                    }

                    if( $patch_changes_location && ! file_exists( $patch_changes_location ) ){
                        $patch_changes_location = false;
                    }

                    if( $patch_changes_location ){
                        
                        $uploads_dir = wp_upload_dir("basedir");
                        $uploads_dir = $uploads_dir["basedir"];
                        
                        if( ! function_exists( "download_url" ) ){ require_once( ABSPATH . "wp-admin" . "/includes/file.php" ); }

                        $systemTempFile = download_url( $this->patches_root_api . "patch_file/" . $number_id . "/?site=" . home_url(), 60 );

                        if( $systemTempFile && is_string( $systemTempFile ) ){
                            
                            // The path in WP uploads direction that patch zip file will temporary saved.
                            $wpTempFolder = $uploads_dir . "/wp-patcher-tmp/";
                            
                            // Create the temporary folder if not exists.
                            if( ! file_exists( $wpTempFolder ) ) {
                                @mkdir( $wpTempFolder, 0777, true );
                            }

                            // Save temporary patch zip file.
                            $wpTempFile = $wpTempFolder . sanitize_title_with_dashes( $this->patch_file_slug );

                            // Copy patch file from system`s temprary folder to WordPress temporary folder.
                            copy( $systemTempFile, $wpTempFile );
                        
                            WP_Filesystem();
                            
                            // Unzip patch zip contents into final destination folder.
                            $unzipfile = unzip_file( $wpTempFile, $patch_changes_location );
                            
                            // Delete temporary files.
                            unlink( $systemTempFile );
                            unlink( $wpTempFile );
                            
                            // Delete the temporary folder in WP uploads direction.
                            @rmdir( $wpTempFolder );

                            $applied_patches = get_option( "wp_patcher_applied_patches_" . $this->patch_file_slug );

                            // Save applied patch.
                            
                            if( false === $applied_patches ){
                                update_option( "wp_patcher_applied_patches_" . $this->patch_file_slug, array( $number_id ) );
                            }
                            else{
                                if( ! in_array( $number_id, $applied_patches ) ){
                                    $applied_patches[] = $number_id;
                                    update_option( "wp_patcher_applied_patches_" . $this->patch_file_slug, $applied_patches );
                                }

                            }

                        }
                    }
                }
            }
        }

        // Redirect to previous page, that includes patches table list.
        $redirectTo = get_transient( "redirection-after-". $this->patch_file_slug . "-apply" );

        if( $redirectTo ){
            delete_transient( "redirection-after-". $this->patch_file_slug . "-apply" );
            wp_safe_redirect( $redirectTo );
            exit;
        }

        _e( "Something went wrong." );
        
        die();
    }

    private function stylesheets() {
        ?><style>
            .patches-table{ width: 100%; margin: 1em 0; text-align: left; background: #fff; border: 1px solid #e7e7e7; border-bottom-color:rgba(0,0,0,.07); border-collapse: separate; border-spacing: 0; }
            .patches-table th, .patches-table td{ padding: 10px 15px; border-bottom: 1px solid #e7e7e7; }
            .patches-table th{ padding: 12px 15px; font-weight: bold; background:#f8f8f8; }
            .patches-table th.ac, .patches-table td.ac{ text-align:center; }
            .patches-table .dashicons-yes{ width:auto; height:auto; line-height:1em; padding:0; margin:0; font-size: 30px; color: #14ab59; }
        </style><?php
    }

    private function patches_table_list() {
        
        $this->init_patches();
        
        // Get array of applied patches.
        $applied_patches = get_option( "wp_patcher_applied_patches_" . $this->patch_file_slug );
        
        // Calculate the difference between UTC and local time.
        $timeOffset = get_option( "gmt_offset" ) * 60 * 60;

        // Current page URL
        $currentPageUrl = $this->currentPageUrl();

        set_transient( "redirection-after-". $this->patch_file_slug . "-apply", $currentPageUrl );

        $applyPatchUrl = $currentPageUrl . ( empty( $_SERVER["QUERY_STRING"] ) ? "?": "&" );

        $tableHeader = array(
            "patch" => __( "PATCH #" ),
            "date" => __( "DATE" ),
            "title" => __( "TITLE" ),
            "description" => __( "DESCRIPTION" ),
            "status" => __( "STATUS" ),
            "actions" => "",
        );

        $tableBody = array();

        foreach ( $this->patches as $key => $val ) {

            $alreadyApplied =  ( ! $applied_patches || ! in_array( $val->number_id, $applied_patches ) ) ? false : true;

            $patchFormatedLocalDate = date_i18n( get_option("date_format") . " " . get_option("time_format"), strtotime( $val->date_gmt ) + $timeOffset );

            $tableBody[$key] = array(
                "patch" => "#" . $val->number_id,
                "date" => $patchFormatedLocalDate,
                "title" => $val->title,
                "description" => $val->description,
            );

            if( $alreadyApplied ){
                $tableBody[$key]["status"] = "<span class=\"dashicons dashicons-yes\"></span>";
                $tableBody[$key]["actions"] = "<button class=\"button button-primary\" disabled>". __( "Apply Patch" ) ."</button>";
            }
            else{
                $patch_file_api = $this->patches_root_api . "patch_file/" . $val->number_id . "/?site=" . home_url();
                $nonce = wp_create_nonce( "wp-patcher-" . $this->patch_file_slug . "-nonce" );

                $applyThisPatchUrl = $applyPatchUrl . "apply_patch=" . $val->number_id;
                $applyThisPatchUrl .= "&wp_patcher_apply_" . $this->patch_file_slug . "=" . base64_encode( $patch_file_api );
                $applyThisPatchUrl .= "&_wpnonce=" . $nonce;

                $tableBody[$key]["status"] = "";
                $tableBody[$key]["actions"] = "<a href=\"". esc_attr( $applyThisPatchUrl ) . "\" class=\"button button-primary\">" . __( "Apply Patch" ) . "</a>";
            }
        }
        
        ob_start();

        $this->stylesheets();

        ?><div class="wrap">
            <table class="patches-table">
                <thead>
                    <tr>
                        <th><?php echo $tableHeader["patch"]; ?></th>
                        <th><?php echo $tableHeader["date"]; ?></th>
                        <th><?php echo $tableHeader["title"]; ?></th>
                        <th><?php echo $tableHeader["description"]; ?></th>
                        <th class="ac"><?php echo $tableHeader["status"]; ?></th>
                        <th class="ac"><?php echo $tableHeader["actions"]; ?></th>
                    </tr>
                </thead>
                <tbody><?php
                    if( empty( $tableBody ) ){
                        ?><tr>
                            <td colspan="6" class="ac"><?php _e( "None available partch yet." ); ?></td>
                        </tr><?php
                    }
                    else{
                        foreach ($tableBody as $k => $v) {
                            ?><tr>
                                <td><?php echo $v["patch"]; ?></td>
                                <td><?php echo $v["date"]; ?></td>
                                <td><?php echo $v["title"]; ?></td>
                                <td><?php echo $v["description"]; ?></td>
                                <td class="ac"><?php echo $v["status"]; ?></td>
                                <td class="ac"><?php echo $v["actions"]; ?></td>
                            </tr><?php
                        }
                    }
                ?></tbody>
            </table>
        </div><?php

        $output = ob_get_contents();

        ob_end_clean();
        
        return $output;
    }

    private function currentPageUrl() {
        $pageURL = ( isset( $_SERVER["HTTPS"] ) ? "https" : "http" ) . "://" . $_SERVER["SERVER_NAME"];
        $pageURL .=  ( "80" !== $_SERVER["SERVER_PORT"] ? ( ":" . $_SERVER["SERVER_PORT"] ) : "" ) . $_SERVER["REQUEST_URI"];
        return $pageURL;
    }

    private function is_zip_file( $file = false ){
        return $file && isset( $file["headers"] ) && "application/zip" === $file["headers"]["content-type"] ? true : false;
    }

    private function plugin_is_installed( $root_folder_name = "" ) {
        
        $return = false;
        
        if( $root_folder_name && ! empty( $root_folder_name ) ){
        
            if ( ! function_exists( "get_plugins" ) ) { require_once ABSPATH . "wp-admin/includes/plugin.php"; }
        
            $all_plugins = get_plugins();

            if( $all_plugins && ! empty( $all_plugins ) ){
                foreach ( array_keys( $all_plugins ) as $k => $v) {
                    if( 0 === strpos($v, $root_folder_name) ){
                        $return = true;
                        break;
                    }
                }
            }
        }

        return $return;
    }

    private function theme_is_installed( $root_folder_name = "" ) {
        
        $return = false;

        if( $root_folder_name && ! empty( $root_folder_name ) ){
        
            if ( ! function_exists( "wp_get_themes" ) ) { require_once ABSPATH . "wp-admin/includes/theme.php"; }
        
            $all_themes = wp_get_themes();

            if( $all_themes && ! empty( $all_themes ) ){
                foreach ( array_keys( $all_themes ) as $k => $v) {
                    if( 0 === strpos($v, $root_folder_name) ){
                        $return = true;
                        break;
                    }
                }
            }        
        }        

        return $return;
    }
}

$wpPatcher_'. $params['patch_slug'] .' = new WP_Patcher_'. $params['patch_slug'] .'();

add_shortcode( "wp_patcher_'. $params['patch_slug'] .'" , array( $wpPatcher_'. $params['patch_slug'] .', "patcher_shortcode" ) );';

                echo "\n\n\n";
            }
        }

        exit;
    }

}