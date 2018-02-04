(function( $ ) {
	
	'use strict';
	
	jQuery(function() {
		
		var $patchFileUploadButton = jQuery('#upload-patch-file'),
			$patchFileRemoveButton = jQuery('#remove-patch-file'),
			$patchFilterDomains = jQuery('#code-patch-filter-domains'),
			$patchFileInput,
			patchMediaUploader;
		
		if( $patchFileUploadButton.length ){

			$patchFileRemoveButton.click(function(e){
				e.preventDefault();
				jQuery('#code-patch-file-id').val( '' );
				jQuery('#code-patch-file-name').val( '' );
				jQuery('.patch-file-name').html( '' );
				$patchFileRemoveButton.addClass('hidden');
				$patchFileUploadButton.removeClass('hidden');
			});

			$patchFileUploadButton.click(function(e) {
			
				e.preventDefault();
				
				if ( patchMediaUploader ) {
					patchMediaUploader.open();
					return;
				}
				
				patchMediaUploader = wp.media.frames.file_frame = wp.media({
					title: WpPatcherTexts.ChoosePatchFile,
					button: {
						text: WpPatcherTexts.ChooseFile,
					},
					multiple: false,
					library: { type : 'application/zip'},
				});

				patchMediaUploader.on('select', function() {
					var attachment = patchMediaUploader.state().get('selection').first().toJSON();
					console.log( attachment );
					jQuery('#code-patch-file-id').val( attachment.id );
					jQuery('#code-patch-file-name').val( attachment.filename );
					jQuery('.patch-file-name').html( attachment.filename );
					$patchFileRemoveButton.removeClass( 'hidden' );
					$patchFileUploadButton.addClass( 'hidden' );
				});

				patchMediaUploader.open();

				return false;
			});


			if( '' === jQuery('#code-patch-file-id').val() ){
				$patchFileRemoveButton.addClass('hidden');
			}
			else{
				$patchFileUploadButton.addClass('hidden');
			}

		}

		if( $patchFilterDomains.length ) {

			if( 'yes' === $patchFilterDomains[0].value) {
				jQuery('.patch-domains-wrap').removeClass('hidden');
			}
			else{
				jQuery('.patch-domains-wrap').addClass('hidden');
			}

			$patchFilterDomains.on( 'change', function(){
				if( 'yes' === this.value) {
					jQuery('.patch-domains-wrap').removeClass('hidden');
				}
				else{
					jQuery('.patch-domains-wrap').addClass('hidden');
				}
			});
		}

	});

})( jQuery );
