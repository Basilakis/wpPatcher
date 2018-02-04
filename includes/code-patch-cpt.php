<?php
// Register Custom Post Type.
function code_patch_post_type() {

	$labels = array(
		'name'                  => _x( 'Code Patches', 'Post Type General Name', WP_PATCHER_SLUG ),
		'singular_name'         => _x( 'Code Patch', 'Post Type Singular Name', WP_PATCHER_SLUG ),
		'menu_name'             => __( 'Code Patches', WP_PATCHER_SLUG ),
		'name_admin_bar'        => __( 'Code Patch', WP_PATCHER_SLUG ),
		'archives'              => __( 'Code Patch Archives', WP_PATCHER_SLUG ),
		'parent_item_colon'     => __( 'Code Patch:', WP_PATCHER_SLUG ),
		'all_items'             => __( 'All Code Patches', WP_PATCHER_SLUG ),
		'add_new_item'          => __( 'Add New Code Patch', WP_PATCHER_SLUG ),
		'add_new'               => __( 'Add New', WP_PATCHER_SLUG ),
		'new_item'              => __( 'New Code Patch', WP_PATCHER_SLUG ),
		'edit_item'             => __( 'Edit Code Patch', WP_PATCHER_SLUG ),
		'update_item'           => __( 'Update Code Patch', WP_PATCHER_SLUG ),
		'view_item'             => __( 'View Code Patch', WP_PATCHER_SLUG ),
		'search_items'          => __( 'Search Code Patch', WP_PATCHER_SLUG ),
		'not_found'             => __( 'Not found', WP_PATCHER_SLUG ),
		'not_found_in_trash'    => __( 'Not found in Trash', WP_PATCHER_SLUG ),
		'featured_image'        => __( 'Featured Image', WP_PATCHER_SLUG ),
		'set_featured_image'    => __( 'Set featured image', WP_PATCHER_SLUG ),
		'remove_featured_image' => __( 'Remove featured image', WP_PATCHER_SLUG ),
		'use_featured_image'    => __( 'Use as featured image', WP_PATCHER_SLUG ),
		'insert_into_item'      => __( 'Insert into code patch', WP_PATCHER_SLUG ),
		'uploaded_to_this_item' => __( 'Uploaded to this code patch', WP_PATCHER_SLUG ),
		'items_list'            => __( 'Code Patches list', WP_PATCHER_SLUG ),
		'items_list_navigation' => __( 'Code Patches list navigation', WP_PATCHER_SLUG ),
		'filter_items_list'     => __( 'Filter code patches list', WP_PATCHER_SLUG ),
	);
	$args = array(
		'label'                 => __( 'Code Patch', WP_PATCHER_SLUG ),
		'description'           => __( 'Code Patch Description', WP_PATCHER_SLUG ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor'),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 75,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => false,		
		'exclude_from_search'   => true,
		'publicly_queryable'    => false,
		'capability_type'       => 'page',
	);
	register_post_type( 'code_patch', $args );
}
add_action( 'init', 'code_patch_post_type', 0 );

// Add new and reorder columns.
function code_patch_columns_head($defaults) {
	$tmp = array(
		'title'	=> $defaults['title'],
		'for_slug' => __( 'Theme/Plugin Slug', WP_PATCHER_SLUG ),
		'number_ID'	=> __( '# Number ID', WP_PATCHER_SLUG ),
		'filename'	=> __( 'File', WP_PATCHER_SLUG ),
		'valid_patch_data'	=> __( 'Valid data', WP_PATCHER_SLUG ),
		'get_code'	=> __( 'Patcher code', WP_PATCHER_SLUG ),
		'shortcode'	=> __( 'Shortcode', WP_PATCHER_SLUG ),
		'date'	=> $defaults['date'],
	);
    return $tmp;
}
add_filter( 'manage_code_patch_posts_columns', 'code_patch_columns_head' );

function code_patch_columns_content( $column_name, $post_ID ) {

	$patchFor =  get_post_meta( $post_ID, 'code_patch_for', true );
	$patchSlug = get_post_meta( $post_ID, 'code_patch_slug', true );
	$numberID = get_post_meta( $post_ID, 'code_patch_number_ID', true );
	$patchFile = get_post_meta( $post_ID, 'code_patch_file_id', true );
	$patchFileName = get_post_meta( $post_ID, 'code_patch_filename', true );

	$isValidPatch = true;
	$invalidMsg = array();

	if( '' === $patchFor ){
		$isValidPatch = false;
		$invalidMsg[] = __( '', WP_PATCHER_SLUG );
	}

	if( '' === $patchSlug ){
		$isValidPatch = false;
		$invalidMsg[] = __( '', WP_PATCHER_SLUG );
	}

	if( '' === $numberID ){
		$isValidPatch = false;
		$invalidMsg[] = __( '', WP_PATCHER_SLUG );
	}

	if( '' === $patchFile ){
		$isValidPatch = false;
		$invalidMsg[] = __( '', WP_PATCHER_SLUG );
	}

	if( '' === $patchFileName ){
		$isValidPatch = false;
		$invalidMsg[] = __( '', WP_PATCHER_SLUG );
	}

    if( 'for_slug' === $column_name ) {

    	if( ! $patchSlug ){
    		if( 'plugin' === $patchFor ){
    			echo '<strong class="error-msg">' . __( "Plugin's slug is missing" , WP_PATCHER_SLUG ) . '</strong>';
    		}
    		else{
    			echo '<strong class="error-msg">' . __( "Theme slug is missing" , WP_PATCHER_SLUG ) . '</strong>';
    		}
    	}
    	else{
    		echo '<strong>' . $patchSlug . '</strong>';
	    	if( 'plugin' === $patchFor ){
	    		echo ' (<small>' . __( 'plugin', WP_PATCHER_SLUG ) . '</small>)';
	    	}
	    	else{
	    		echo ' (<small>' . __( 'theme', WP_PATCHER_SLUG ) . '</small>)';
	    	}
    	}
    }

    if( 'number_ID' === $column_name ) {

    	if( ! $numberID ){
    		echo '<strong class="error-msg">' . __( 'Is missing' , WP_PATCHER_SLUG ) . '</strong>';
    	}
    	else{
    		$endpoint_link = home_url() . '/wp-json/wp/v2/patch/' . $numberID . '/?site=' . home_url();
    		echo '<a href="' . $endpoint_link . '" title="" target="_blank"><strong>' . $numberID . '</strong></a>';
    	}
    }

    if( 'filename' === $column_name ) {

    	if( ! $patchFile ){
    		echo '<strong class="error-msg">' . __( "File is missing" , WP_PATCHER_SLUG ) . '</strong>';
    	}
    	elseif( ! $patchFileName ) {
    		echo '<strong class="error-msg">' . __( "File name is missing" , WP_PATCHER_SLUG ) . '</strong>';
    	}
    	else{
    		echo '<a href="' . get_patch_file_link( $post_ID ) . '/?site=' . home_url() . '" title=""><strong>' . $patchFileName . '</strong></a>';
    	}
    }

    if( 'valid_patch_data' === $column_name ) {

    	if( $isValidPatch ){
    		echo '<span class="validation-icon success dashicons dashicons-yes"></span>';
    	}
    	else{
    		echo '<span class="validation-icon error dashicons dashicons-no-alt"></span>';
    	}
    }

    if( 'get_code' === $column_name ) {
    	$getCode_link = home_url() . '/wp-json/wp/v2/patch/slug/code/' . $patchSlug . '/?site=' . home_url();

    	if( 'plugin' === $patchFor ){
    		echo '<a href="' . $getCode_link . '" title="" target="_blank"><strong>' . __( "Get plugin's code", WP_PATCHER_SLUG ) . '</strong></a>';
    	}
    	else{
	    	echo '<a href="' . $getCode_link . '" title="" target="_blank"><strong>' . __( "Get theme's code", WP_PATCHER_SLUG ) . '</strong></a>';
    	}
    }

    if( 'shortcode' === $column_name ) {
    	if( $patchSlug ){
    		echo '<small><input type="text" value="[wp_patcher_' . $patchSlug . ']" readonly /></small>';
    	}
    }
}
add_action( 'manage_code_patch_posts_custom_column', 'code_patch_columns_content', 10, 2 );

function remove_code_patch_content_media_uploader() {
	global $current_screen;
	if( 'code_patch' == $current_screen->post_type ){
		remove_action( 'media_buttons', 'media_buttons' );
	}
}
add_action( 'admin_head','remove_code_patch_content_media_uploader' );

function patch_data_form( $post ){

	global $WpPatcher_editPatch;

	$WpPatcher_editPatch = 'true';

	$patchFor =  get_post_meta( $post->ID, 'code_patch_for', true );
	$patchSlug = get_post_meta( $post->ID, 'code_patch_slug', true );
	$numberID = get_post_meta( $post->ID, 'code_patch_number_ID', true );
	$patchFile = get_post_meta( $post->ID, 'code_patch_file_id', true );
	$patchFileName = get_post_meta( $post->ID, 'code_patch_filename', true );
	$filterDomains = get_post_meta( $post->ID, 'code_patch_filter_domains', true );
	$filteredDomains = get_post_meta( $post->ID, 'code_patch_filtered_domains', true );

	?>
	<div class="patch-data-wrap">

		<div class="field">
			<label for="code-patch-for"><?php _e( 'Patch for', WP_PATCHER_SLUG ); ?> : </label>
			<select name="code-patch-for" id="code-patch-for" >
				<option value="theme" <?php if( ! $patchFor || 'theme' === $patchFor ){ echo 'selected'; } ?>><?php _e( 'Theme', WP_PATCHER_SLUG ); ?></option>
				<option value="plugin" <?php if( 'plugin' === $patchFor ){ echo 'selected'; } ?>><?php _e( 'Plugin', WP_PATCHER_SLUG ); ?></option>
			</select>
		</div>

		<div class="field">
			<label for="code-patch-slug"><?php _e( 'Plugin/theme slug', WP_PATCHER_SLUG ); ?> : </label>
			<input type="text" name="code-patch-slug" id="code-patch-slug" value="<?php echo $patchSlug; ?>" />
		</div>

		<div class="field">
			<label for=""><?php _e( '# Number ID', WP_PATCHER_SLUG ); ?> : </label>
			<input type="text" name="code-patch-number-ID" id="code-patch-number-ID" value="<?php echo $numberID; ?>" />
		</div>

		<div class="field">
			<label><?php _e( 'Files replaced', WP_PATCHER_SLUG ); ?> : </label>
			<span class="patch-file-name"><?php echo $patchFileName; ?></span>
		  	<a href="#" id="upload-patch-file" class="button"><?php _e( 'Upload', WP_PATCHER_SLUG ); ?></a>
		  	<a href="#" id="remove-patch-file" class=""><?php _e( 'Remove', WP_PATCHER_SLUG ); ?></a>
		  	<br/>
		  	<label></label>
		  	<small><i><?php _e( 'only ZIP files allowed', WP_PATCHER_SLUG ); ?></i></small>
	  	</div>

	  	<div class="field full">
			<label><?php _e( 'Web site based Patch', WP_PATCHER_SLUG ); ?> : </label>
			<select id="code-patch-filter-domains" name="code-patch-filter-domains" >
				<option <?php echo "yes" === $filterDomains ? 'selected="selected"' : "" ; ?> value="yes"><?php _e( 'Yes', WP_PATCHER_SLUG ) ?></option>
				<option <?php echo "yes" !== $filterDomains ? 'selected="selected"' : "" ; ?> value="no"><?php _e( 'No', WP_PATCHER_SLUG ) ?></option>
			</select>
			<span class="patch-domains-wrap <?php echo "yes" !== $filterDomains ? 'hidden' : '' ; ?>">
				<label></label>
				<span><?php _e( "Insert domains seperated by commas ", WP_PATCHER_SLUG ); ?><strong>`;`</strong></span>
				<br/>
				<label></label>
				<textarea name="code-patch-filtered-domains"><?php echo $filteredDomains; ?></textarea>
			</span>
		</div>

	  	<input type="hidden" id="code-patch-file-id" name="code-patch-file-id" value="<?php echo $patchFile; ?>" />
		<input type="hidden" id="code-patch-file-name" name="code-patch-file-name" value="<?php echo $patchFileName; ?>" />

  	</div>
	<?php
}

function code_patch_meta_boxes() {
	
	// Add 'Patch data' metabox.
	add_meta_box( 'patch-data', __( 'Patch data', WP_PATCHER_SLUG ), 'patch_data_form', 'code_patch' );

	// Remove 'Slug' metabox.
	remove_meta_box( 'slugdiv', 'code_patch', 'normal' );
}
add_action( 'add_meta_boxes', 'code_patch_meta_boxes', 100 );

function save_code_patch_meta_boxes( $post_id ) {

	if( isset( $_POST['post_type'] ) && 'code_patch' === $_POST['post_type'] ){

		if ( array_key_exists('code-patch-for', $_POST ) ) {
	        update_post_meta( $post_id, 'code_patch_for', trim( $_POST['code-patch-for'] ) );
	    }

	    if ( array_key_exists('code-patch-slug', $_POST ) ) {
	        update_post_meta( $post_id, 'code_patch_slug', trim( $_POST['code-patch-slug'] ) );
	    }

	    if ( array_key_exists('code-patch-number-ID', $_POST ) ) {
	        update_post_meta( $post_id, 'code_patch_number_ID', trim( $_POST['code-patch-number-ID']) );
	    }

	    if ( array_key_exists('code-patch-file-id', $_POST ) ) {
	        update_post_meta( $post_id, 'code_patch_file_id', trim( $_POST['code-patch-file-id'] ) );
	    }

	    if ( array_key_exists('code-patch-file-name', $_POST ) ) {
	        update_post_meta( $post_id, 'code_patch_filename', trim( $_POST['code-patch-file-name'] ) );
	    }

	    if ( array_key_exists('code-patch-filter-domains', $_POST ) ) {
	        update_post_meta( $post_id, 'code_patch_filter_domains', trim( $_POST['code-patch-filter-domains'] ) );
	    }

	    if ( array_key_exists('code-patch-filtered-domains', $_POST ) ) {
	        update_post_meta( $post_id, 'code_patch_filtered_domains', trim( $_POST['code-patch-filtered-domains'] ) );
	    }
	}
}
add_action( 'save_post', 'save_code_patch_meta_boxes' );

function code_patch_files_mimes($mime_types){
	global $current_screen;
    if( $current_screen->post_type === 'code_patch' ){
    	$mime_types = array( 'zip' => 'application/zip' );
    }
    return $mime_types;
}
add_filter( 'upload_mimes', 'code_patch_files_mimes', 1, 1 );

function code_patch_wp_handle_upload_prefilter( $file ) {

	if( isset( $_REQUEST['post_type'] ) && 'code_patch' === $_REQUEST['post_type'] && 'application/zip' !== $file['type'] ){
		$file['error'] = __( 'Sorry, you can upload only ZIP files.', WP_PATCHER_SLUG );
	}

	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'code_patch_wp_handle_upload_prefilter' );

function code_patch_wp_plupload( $params ) { 

    global $post_ID;

    if ( isset( $post_ID ) ){

    	$postType = get_post_type( $post_ID );
    	
    	if( 'code_patch' === $postType ){
	        $params['post_id'] = (int) $post_ID; 
	        $params['post_type'] = $postType;
	    }
    }

    return $params; 
}
add_filter( 'plupload_default_params', 'code_patch_wp_plupload' ); 