// todo: call image loader for thumbnails
(function($) {
		
	function buildMap( mapID ) {	
		$("#lazyest-map-" + mapID ).show().height(lazyestMapHeight).width("100%").gmap3(
			{ action: "init",
				options: {
					mapTypeId: google.maps.MapTypeId.ROADMAP
				},
				navigationControl: true,
				scrollwheel: true
			},	
			{ action: "addMarkers",
				marker:{
					values: lazyestMarkers[mapID],
					options:{
						icon: new google.maps.MarkerImage( photoMarker ),
						shadow: new google.maps.MarkerImage( photoShadow ),
						draggable: false			
					},
					events:{
						click: function(marker, event, object){
							var map = $(this).gmap3("get"),infowindow = $(this).gmap3({get:{name:"infowindow"}});
							if (infowindow){
								infowindow.open(map, marker);
								infowindow.setContent(object.data);
							} else {
								$(this).gmap3({
									infowindow:{ 
										anchor:marker, 
										options:{content: object.data}
									}
								});
							}
						}
					}
				}
			},
			"autofit"
		);
	}
	
	function showMaps() {
		$(".lazyest-map.shortcode").each( function() {
			mapID = $(this).attr( 'id' ).slice(12);
			buildMap( mapID );
		});
	} 
	
	$(document).ready(function(){
	
		if ( lazyestMarkers.length ) {
			/* click to show map */
			$(".map-toggler").click(function( el ){
				mapID = $(this).attr( 'id' ).slice(11);
				buildMap( mapID );
				$(this).hide();
				return false;
			});
			
			if ( $(".lazyest-maps-shortcode").length ) {
	  		showMaps();
			}
		}
		
	});

})(jQuery);