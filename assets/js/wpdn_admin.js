jQuery( document ).ready( function($) {

	
	var loading_icon = '<span class="saving-icon"><img src="/wp-admin/images/wpspin_light.gif"/> saving...</span>';
	var saved_icon = '<span class="saved-icon"><div class="dashicons dashicons-yes"></div> saved!</span>';


	// Add todo item
	$( 'body, .list-item-content' ).on( 'keydown', '.add-list-item', function(e) {
		if( e.keyCode == 13 && $( this ).val() != '' ) {

			var post_id = $( this ).closest( ".postbox" ).attr( 'id' );
			var list_item = '<div class="list-item"><div class="dashicons dashicons-menu wpdn-note-sortable"></div><input type="checkbox"><span class="list-item-content" contenteditable="true">' + $( this ).val() + '</span><div class="delete-item dashicons dashicons-no-alt"></div></div>';
			$( '#' + post_id + ' div.wp-dashboard-note' ).append( list_item );
			$( this ).val( '' ); // Clear text field
			
			$( this ).trigger( 'wpdn-update', this );

		}

	});
	
	
	// Delete todo item
	$( 'body' ).on( 'click', '.delete-item', function() {
		var post_id = $( this ).closest( ".postbox" ).attr( 'id' );
		$( this ).parent( '.list-item' ).remove();
		$( 'body' ).trigger( 'wpdn-update', ['', post_id]  );
	});
	
	
	// Toggle visibility
	$( 'body' ).on( 'click', '.wpdn-visibility', function() {
	
		$( this ).toggleClass( 'dashicons-lock dashicons-groups' );
		
		var visibility = $( this ).parent().attr( 'data-visibility' );
		if ( 'publish' == visibility ) {
			$( this ).parent( '.wpdn-toggle-visibility' ).attr( 'data-visibility', 'private' );
			$( this ).parent( '.wpdn-toggle-visibility' ).attr( 'title', 'Visibility: Private' );
		} else {
			$( this ).parent( '.wpdn-toggle-visibility' ).attr( 'data-visibility', 'publish' );
			$( this ).parent( '.wpdn-toggle-visibility' ).attr( 'title', 'Visibility: Public' );
		}
		
		$( this ).trigger( 'wpdn-update', this );
		
	});
	
	
	// Update note trigger
	$( 'body' ).on( 'wpdn-update', function( event, t, post_id ) {

		if ( t != '' ) {
			post_id = $( t ).closest( ".postbox" ).attr( 'id' );
		}
		
		$( '#' + post_id + ' .wp-dashboard-note-options .status' ).html( loading_icon ); 
		var data = { 
			action: 		'wpdn_update_note',
			post_id: 		post_id.replace( 'note_', '' ),
			post_content: 	$( '#' + post_id + ' div.wp-dashboard-note' ).html(),
			post_title: 	$( '#' + post_id + ' > h3 .wpdn-title' ).html(),
			post_status:	$( '[data-visibility]' ).attr( 'data-visibility' ),
			note_color:		$( '[data-color]' ).attr( 'data-note-color' )
		};

		$.post( ajaxurl, data, function( response ) {
			$( '#' + post_id + ' .wp-dashboard-note-options .status' ).html( saved_icon );
			$( '#' + post_id + ' .wp-dashboard-note-options .status *' ).fadeOut( 1000, function() { $( this ).html( '' ) });
		});

	});
	
	
	
	
	
	
	// Delete note
	$( 'body' ).on( 'click', '.wpdn-delete-note', function() {
	
		var post_id = $( this ).closest( ".postbox" ).attr( 'id' );		
		
		$( '#' + post_id ).fadeOut( 500, function() { $( this ).remove() } );
		
		var data = { 
			action: 'wpdn_delete_note',
			post_id: post_id.replace( 'note_', '' ),
		};

		$.post( ajaxurl, data, function( response ) {

		});
		
	});
	
	
	// Add note
	$( 'body' ).on( 'click', '.wpdn-add-note', function() {

		var data = { action: 'wpdn_add_note' };

		$.post( ajaxurl, data, function( response ) {
			response = jQuery.parseJSON( response );
			jQuery( '#postbox-container-1 #normal-sortables' ).prepend( response.note );
			jQuery( '#' + response.post_id + ' .add-list-item' ).focus();
			$('body, html').animate({ scrollTop: $( "#" + response.post_id ).offset().top - 50 }, 750);
		});
		
		// Stop scrollTop animation on user scroll
		$( 'html, body' ).bind("scroll mousedown DOMMouseScroll mousewheel keyup", function( e ){
			if ( e.which > 0 || e.type === "mousedown" || e.type === "mousewheel") {
				$( 'html, body' ).stop().unbind('scroll mousedown DOMMouseScroll mousewheel keyup');
			}
		});  
		
	});	
	
	// Change color
	$( 'body' ).on( 'click', '.color', function() {
		var color = $( this ).attr( 'data-select-color' );
		$( this ).closest( ".postbox" ).css( 'background-color', color );
		$( '[data-color]' ).attr( 'data-color', color );
		$( this ).trigger( 'wpdn-update', this );
	});
	
	
	// Edit/update note
	$( 'body' ).on( 'blur', '.list-item-content, [contenteditable=true]', function() {
  		$( this ).trigger( 'wpdn-update', this );
	});
	// Don't allow enter in note
	$( 'body' ).on( 'keydown', '[contenteditable=true]', function( e ) {
	    if ( e.keyCode == 13 ) {
      		$( this ).trigger( 'wpdn-update', this );
      		$( this ).blur();
			return false;
		}
	});
	
	
	// Edit title
	$( '.postbox h3' ).on( 'click', 'div', function( e ) {
		$( this ).prev().focus();
		document.execCommand( 'selectAll', false, null );
		e.stopPropagation();
	});

	
	// Note checkbox toggle
	$( 'input[type=checkbox]' ).change( function() {
	    if( this.checked ) {
	        $( this ).attr( 'checked', 'checked' );
	    } else {
		    $( this ).removeAttr( 'checked' );
	    }
  		$( this ).trigger( 'wpdn-update', this );
    });
    	

    // Make list sortable
		
});
jQuery(function($) {
    $( ".wp-dashboard-note" ).sortable({ handle: '.wpdn-note-sortable' });
  });