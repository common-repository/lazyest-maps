<?php

class LazyestMapsFrontend {
	
	var $maps;
	
	/**
	 * LazyestMapsFrontend::__construct()
	 * 
	 * @return void
	 */
	function __construct() {
		$this->init();
	}
	
	/**
	 * LazyestMapsFrontend::init()
	 * 
	 * @return void
	 */
	function init() {
		$this->maps = 0;
		$this->filters();
	}
	
	/**
	 * LazyestMapsFrontend::filters()
	 * 
	 * @return void
	 */
	function filters() {
		global $lazyest_maps;
		if ( $lazyest_maps->options['disable-gallery'] && $lazyest_maps->options['disable-shortcodes'] )
			return;
			
		// wordpress filters and actions	
		add_action( 'wp_head', array( &$this, 'wp_head'), 1 );
				
		add_shortcode( 'lazyestmap', array( &$this, 'shortcode' ) );	
		
		//lazyest-gallery filters and actions
		if ( ! $lazyest_maps->options['disable-gallery'] )		
			add_filter( 'lazyest_thumbs_folder_header', array( &$this, 'lazyest_thumbs_folder_header' ), 10, 2 );		
	}
	
	function wp_head() {
		global $lazyest_maps, $lg_gallery;
		$map_height = max( strval( $lazyest_maps->options['map-height'] ), 100 );		
		$img_width = strval( $lg_gallery->get_option( 'thumbwidth') );
		$img_height = strval( $lg_gallery->get_option( 'thumbheight') );
		echo "\n<!-- lazyest-maps -->
<script type='text/javascript'>
	var lazyestMarkers = new Array();
	lazyestMapHeight = $map_height;
</script>
<style type='text/css'>
div.lazyest-map img {
	max-width: inherit;
}
.lazyest-info-window {
	background-color: #fff;
	font-size:  75%;
	line-height: 120%; 
	margin: 0 auto;
	width: ". $img_width . "px
}
.lazyest-info-window h3 { 
	font-weight: bold;
	font-size: 1.1em; 
	line-height:1.2em; 
}
.lazyest-info-window a {
}
.lazyest-info-window a img {
	margin: 6px 0;
	max-width: " . $img_width . "px !important;
}
</style>";
	}
	
	function has_geodata( $folder ) {
		$hasdata = false;
		if ( count( $folder->list ) ) {
			foreach( $folder->list as $image ) {
				if ( '' != $image->extra_fields['geotag'] ) {
					$hasdata = true;
					break;
				}
			}
		}
		return $hasdata;
	}
	
	/**
	 * LazyestMapsFrontend::shortcode()
	 * Posts shortcode [lazyestmap="myfolder"] or [lazyestmap folder="myfolder"]
	 * 
	 * @param array $atts
	 * @return string html
	 */
	function shortcode( $atts ) {
		global $lazyest_maps;
		if ( $lazyest_maps->options['disable-shortcodes'] )
			return '';
			
		extract( shortcode_atts( array( 'folder' => '' ), $atts ) );
		
		if ( '' == $folder )
			$folder = trim( $atts[0], "=\"' " );
		$folder = trailingslashit( html_entity_decode( ltrim ( utf8_decode( $folder), '/') ) );
		$the_folder = new LazyestFolder( $folder );
			
		$shortcode = sprintf( __( 'Lazyest Gallery cannot access %s', 'lazyest-maps'), htmlentities( $folder ) );
		
		if ( $the_folder->valid() ) {
			$the_folder->load( 'thumbs' );
			$shortcode = '';
			if (  $this->has_geodata( $the_folder ) ) {
				$shortcode = "\n<div class='lazyest-maps-shortcode'>";
				$shortcode .= $this->draw_map( $the_folder, 'shortcode' );
				$shortcode .= "</div>\n";
			}
		} 
		unset( $the_folder );
		return $shortcode;
	}
	
	/**
	 * LazyestMapsFrontend::lazyest_thumbs_folder_header()
	 * Adds hidden map to Folder view and add button to show map
	 *  
	 * @param string $header
	 * @param LazyestFrontendFolder $folder
	 * @return
	 */
	function lazyest_thumbs_folder_header( $header, $folder ) {				
		
		if ( ! $this->has_geodata( $folder ) )
			return $header;
			
		// add button to show map
    $map_text = __( 'Show Images on a Map', 'lazyest-maps' );
    $header = "\n<div class='buttons'><a id='map-toggle-" . $this->maps . "' href='#' class='button map-toggler'>$map_text</a></div>\n" . $header;
    
		// add map to header
		$header .= "\n<style type='text/css'>#lazyest-map {display: none;}</style>";
		$header .= $this->draw_map( $folder );
    return $header;
	}
	
	// functions
	
	/**
	 * LazyestMapsFrontend::draw_map()
	 * Output html, styles and javascript to show the Google Map
	 * 
	 * @param LazyestFrontendFolder $folder
	 * @return string html rendering of map
	 */
	function draw_map( $folder, $shortcode = '' ) {
		global $lg_gallery, $lg_pagei, $lazyest_maps;		
    
		$map =''; 
		$end = count( $folder->list );
    if ( 0 == $end )
    	return $map;
    
    $options = $lazyest_maps->options;
		$perpage = $lg_gallery->get_option( 'thumbs_page' );
		$start = 1;      
    if ( 0 < $perpage ) {    
      $total_pages = ceil( count( $folder->list ) / $perpage ); 
      $query_var = 'lg_pagei';
      if ( isset ( $lg_pagei ) ) {
        $current = max( 1, $lg_pagei);
      } else {      
        $current = isset( $_REQUEST[$query_var] ) ? absint( $_REQUEST[$query_var] ) : 0;	
        $current = min( max( 1, $current ), $total_pages );
      }
      $start = ( $current - 1 ) * $perpage + 1;
      $end = min( count( $folder->list ), $current * $perpage );
    }
    $icon = plugins_url( 'images/photo.png',  __FILE__ );
    $shadow = plugins_url( 'images/shadow.png',  __FILE__ ); 
        
    // build the google map javascript
    $map .= "\n<script type='text/javascript'>";
    $map .= 'lazyestMarkers[' . $this->maps . '] = [';
    for ( $i = $start - 1; $i < $end; $i++ ) {
			$image = $folder->list[$i];
			if ( '' != $image->extra_fields['geotag'] ) {
				$temp = explode ( ',', $image->extra_fields['geotag'] );
				$latitude = $temp[0];
				$longitude = $temp[1];
				$caption = $image->title();
				$onclick = $image->on_click( 'widget' );
				$class= 'thumb';
				
		    $thumbfile = $lg_gallery->root . $image->folder->curdir . $lg_gallery->get_option( 'thumb_folder' ) . $image->image;
				$src = $image->src();
		    if ( ( 'TRUE' == $lg_gallery->get_option( 'async_cache' ) ) && ! file_exists( $thumbfile ) ) 
		    	$src = admin_url( 'admin-ajax.php' ) . '?action=lg_image_request&amp;file=' . lg_nice_link( $image->folder->realdir() . $image->image ) . '&amp;thumb=1';
											
				$h3text = ( 1 == $options['info-captions'] ) ? '<h3>' . lg_html( $image->caption() ) . '</h3>' : '';
				$description = ( 1 == $options['info-descriptions'] ) ? '<p>' . lg_html( $image->description() ) . '</p>' : '';
				$data = json_encode( sprintf ( "<div class='lazyest-info-window'>%s<a title='%s' class='%s' rel='%s' href='%s'><img class='%s' src='%s' alt='thumbnail' /></a>%s</div>",				
					$h3text,
					$caption,
					$onclick['class'],
					$onclick['rel'],
					$onclick['href'],
					$class,
					$src,
					$description 
				) );
				
				$map .= '
					{latLng:[' . $latitude . ', ' . $longitude .'], 
						data: ' . $data . ',
						options: {								
							title: ' . json_encode( $image->title() ) . '	
						}			
					},';
			}		
    }	
		$map = substr( $map, 0, -1 ); // just remove the last comma
    $map .= '
					];
					photoMarker = "' . $icon . '";
					photoShadow = "' . $shadow . '";
		</script>';
    
		wp_enqueue_script( 'lazyest-maps' );	
			
    // add the map holding element
    $mapclass = 'lazyest-map';
		$mapclass .= ( '' != $shortcode ) ? ' ' . $shortcode : '';
    $map .= "\n<div id='lazyest-map-" . $this->maps . "' class='$mapclass' style='display:none'></div>\n";
		
		$this->maps++;
		
		return $map;    
	}
	
} // LazyestMapsFrontend

	
?>