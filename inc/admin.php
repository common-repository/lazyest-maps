<?php

class LazyestMapsAdmin {
	
	/**
	 * LazyestMapsAdmin::__construct()
	 * 
	 * @return void
	 */
	function __construct() {
		$this->init();
	}
	
	/**
	 * LazyestMapsAdmin::init()
	 * 
	 * @return void
	 */
	function init() {
		$this->filters();		
	}
	
	/**
	 * LazyestMapsAdmin::filters()
	 * 
	 * @return void
	 */
	function filters() {
		add_action( 'wp_ajax_lazyest_maps_reread', array( &$this, 'lazyest_maps_reread' ) ); 		
	}

	/**
	 * LazyestMapsAdmin::lazyest_maps_reread()
	 * AJAX called
	 * Get Geo data from Images exif data
	 * 
	 * @return void
	 */
	function lazyest_maps_reread() {
		global $lazyest_maps;
		$result = ' ';
		if ( isset( $_POST['folder'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'lg_manage_folder' ) ); {
			$folder = new LazyestFolder( utf8_decode( stripslashes( rawurldecode( $_POST['folder'] ) ) ) );
			$folder->load();
			$count = 0;
			if ( 0 < count( $folder->list ) ) {
				foreach( $folder->list as $image ) {
					$image->extra_fields['geotag'] = $lazyest_maps->mapper->extract_geodata( $image );
					if ( '' != $image->extra_fields['geotag'] )
						$count++;
				}
				$folder->save();
			}
			$result = '+' . $count;		
		}
		echo $result;
		die();
	}	
	
} // LazyestMapsAdmin

?>