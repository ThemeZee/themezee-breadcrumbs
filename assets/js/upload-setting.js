jQuery(document).ready(function($) {
    
	// Settings uploader
	var file_frame;
	window.formfield = '';

	$('body').on('click', '.tzba_settings_upload_button', function(e) {

		e.preventDefault();

		var button = $(this);

		window.formfield = $(this).parent().prev();

		// If the media frame already exists, reopen it.
		if( file_frame ) {
			file_frame.open();
			return;
		}

		// Create the media frame
		file_frame = wp.media.frames.file_frame = wp.media({
			frame: 'post',
			state: 'insert',
			title: button.data( 'uploader_title' ),
			button: {
				text: button.data( 'uploader_button_text' )
			},
			multiple: false
		});

		file_frame.on( 'menu:render:default', function( view ) {
			// Store our views in an object,
			var views = {};

			// Unset default menu items
			view.unset( 'library-separator' );
			view.unset( 'gallery' );
			view.unset( 'featured-image' );
			view.unset( 'embed' );

			// Initialize the views in our view object
			view.set( views );
		});

		// When an image is selected, run a callback
		file_frame.on( 'insert', function() {
			var selection = file_frame.state().get( 'selection' );

			selection.each( function( attachment, index ) {
				attachment = attachment.toJSON();
				window.formfield.val(attachment.url);
			});
		});

		// Open the modal
		file_frame.open();
	});
	
});
