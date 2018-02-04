<?php
function get_patch_info( $patch_number_id = false, $patch_slug = false, $domain = false ){

	$patches = array();

	$args = array (
		'post_type'		=> array( 'code_patch' ),
		'post_status'	=> array( 'publish' ),
	);

	if( $patch_number_id ){
		$args['meta_key'] = 'code_patch_number_ID';
		$args['meta_value'] = $patch_number_id;
	}

	if( $patch_slug ){
		$args['meta_key'] = 'code_patch_slug';
		$args['meta_value'] = $patch_slug;
	}

	$query = new WP_Query( $args );

	$posts = get_posts( $args );

	if( $posts ){

		foreach( $posts as $k => $v ){

			$ok = false;

			$filterDomains = get_post_meta( $v->ID, 'code_patch_filter_domains', true );

			if( ! $filterDomains || 'yes' !== $filterDomains ){
				$ok = true;
			}
			else{

				if( $domain ){

					if( $domain === home_url() ){
						$ok = true;
					}
					else{
						
						$domain_host = parse_url( esc_url( $domain ) );

						$domain_hostPath = $domain_host['host'] . $domain_host['path'] ;
						$filteredDomains = explode( ";", get_post_meta( $v->ID, 'code_patch_filtered_domains', true ) );
						
						if( $filteredDomains && ! empty( $filteredDomains ) ){
						
							foreach( $filteredDomains as $x => $y ){
								
								$t = parse_url( esc_url( $y ) );
									
								if( isset( $t['host'] ) ){
									if( trailingslashit( $t['host'] . $t['path'] ) === trailingslashit( $domain_hostPath ) ){
										$ok = true;
										continue;
									}
								}
							}
						}
					}
				}
			}

			if( $ok ){
				$patches[] = array(
					'title'			=>	$v->post_title,
					'description'	=>	$v->post_content,
					'date_gmt'		=>	$v->post_date_gmt,
					'for'			=>	get_post_meta( $v->ID, 'code_patch_for', true ),
					'for_slug'		=>	get_post_meta( $v->ID, 'code_patch_slug', true ),
					'number_id'		=>	get_post_meta( $v->ID, 'code_patch_number_ID', true ),
					'file_id'		=>	get_post_meta( $v->ID, 'code_patch_file_id', true ),
					'filename'		=>	get_post_meta( $v->ID, 'code_patch_filename', true ),
				);
			}
		}
	}

	return $patches;
}

function get_patch_file_link( $postID ){
	$getFile_link = home_url() . '/wp-json/wp/v2/patch_file/' . get_post_meta( $postID, 'code_patch_number_ID', true );
	return $getFile_link;
}