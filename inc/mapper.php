<?php

/**
 * LazyestMapper
 * Retrieves EXIF GPS data from images 
 * Builds Google Maps javascript
 * 
 * @package Lazyest Gallery
 * @subpackage Lazyest Maps
 * @author Marcel Brinkkemper
 * @copyright 2012-2013 Marcel Brinkkemper
 * @version 0.5
 * @access public
 */
class LazyestMapper {
	
	/**
	 * LazyestMapper::__construct()
	 * 
	 * @return void
	 */
	function __construct() {
		global $lazyest_maps;
		add_filter( 'lazyest_image_found', array( &$this, 'lazyest_image_found' ) );
	}
	
	function extract_geodata( $image ) {
		$image_file = $image->original();		
		$geodata = '';
		if ( ! file_exists( $image_file ) ) 
			return $geodata;		
		
		if ( is_callable( 'exif_read_data' ) ) {				
			$exif = exif_read_data( $image_file );	
			if ( !empty( $exif['GPSLatitude'] ) ) {
				$gps['latitude_hour'] = $exif['GPSLatitude'][0];
				$gps['latitude_minute']  = $exif['GPSLatitude'][1];
				$gps['latitude_second']  = $exif['GPSLatitude'][2];
				$gps['longitude_hour']   = $exif['GPSLongitude'][0];
				$gps['longitude_minute'] = $exif['GPSLongitude'][1];
				$gps['longitude_second'] = $exif['GPSLongitude'][2];
				
				foreach( $gps as $key => $value ) {
					$pos = strpos( $value, '/' );
					if( $pos !== false ) { 
						$temp = explode( '/',$value ); 
						$gps[$key] = $temp[0] / $temp[1];
					}
				}
				
				$latitude = round( $gps['latitude_hour'] + ( $gps['latitude_minute'] / 60 ) + ( $gps['latitude_second'] / 3600 ), 5 );
				$latitude = $exif['GPSLatitudeRef'] == "S" ? -1 * $latitude : $latitude;
				
				$longitude = round( $gps['longitude_hour'] + ( $gps['longitude_minute'] / 60 ) + ( $gps['longitude_second'] / 3600 ), 5 );
				$longitude = ( $exif['GPSLongitudeRef'] == "W" ) ? -1 * $longitude : $longitude; 
				
				$geodata = $latitude . ',' . $longitude;
			}	
		} 				
		return $geodata; 
	}
	
	/**
	 * LazyestMapper::lazyest_image_found()
	 * Extract Geo data from newly added images
	 * 
	 * @param LazyestImage $image
	 * @return void
	 */
	function lazyest_image_found( $image ) {
		$image->extra_fields['geotag'] = $this->extract_geodata( $image );
		return $image;
	}		
}
	
?>