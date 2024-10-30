(function($) {
	
	$(document).ready(function(){			
		
		if ( 0 < window.location.search.indexOf( 'folder' ) ) {
			
			$('<div id="lazyest-map" class="postbox"><h3>Geo Data</h3><div id="map-inside" class="inside"><a id="geo-button" href="#" class="button">' + lazyestMapsText.getGeoData + ' </a><br class="clear"/></div></div>').insertAfter('#submitdiv');
			
			$('#geo-button').click( function() {				
				folderForm = $('#sort_images_form' );
				var data = {
					_wpnonce: jQuery('#_wpnonce' ).val(),
					action: 'lazyest_maps_reread',
    			folder : jQuery( "input[name='directory']", folderForm ).val()
				}
				jQuery.post(ajaxurl, data, function( response ) {
					if ( -1 < response.indexOf( '+' ) ) {
						lg_refreshFolder();
						var result = response.slice( 1 );
						message = '<div id="message" class="updated"><p>' + lazyestMapsText.folderSuccess.replace('%s', result ) + '</p></div>';	 
					} else {
						message = '<div id="message" class="error"><p>' + lazyestMapsText.folderFail + '</p></div>'; 
					}				
		      if ( jQuery('#message').length ){      
		      	jQuery( '#message' ).replaceWith(message);
					} else {
		      	jQuery( '#ajax-div' ).html(message);
					}
				});
			});			
		}			
	});	
	
})(jQuery);