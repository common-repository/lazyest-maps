<?php
/*
Plugin Name: Lazyest Maps
Plugin URI: http://brimosoft.nl/lazyest/maps/
Description: Show your Lazyest Gallery images location on Google Maps
Date: February 2013
Author: Brimosoft
Author URI: http://brimosoft.nl
Version: 0.6.0
License: GNU GPLv3
*/
 
 /**
 * LazyestMaps
 * 
 * @package Lazyest Gallery
 * @subpackage Lazyest Maps
 * @author Marcel Brinkkemper
 * @copyright 2012-2013 Marcel Brinkkemper
 * @version 0.4
 * @access public
 */
class LazyestMaps {
	
	var $options;
	var $mapper;
	var $display;
	
	/**
	 * LazyestMaps::__construct()
	 * 
	 * @return void
	 */
	function __construct() {
		$options = get_option( 'lazyest-maps' );		
		$this->options = $options ? $options : $this->defaults();
		$this->init();				
	}
	
	// lazyest-maps core functions
		
	function init() {	
		load_plugin_textdomain( 'lazyest-maps', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		$this->filters();	
	}
	
	/**
	 * LazyestMaps::filters()
	 * 
	 * @return void
	 */
	function filters() {
		// wordpress hooks
		register_uninstall_hook( __FILE__, array( 'LazyestMaps', 'uninstall' ) );
		register_activation_hook( __FILE__, array( &$this, 'activation' ) );	
		
		add_action( 'admin_action_lazyest-maps', array( &$this, 'do_action' ) );		
		add_action( 'admin_notices', array( &$this, 'admin_notices' ) );	
		add_action( 'admin_print_styles-settings_page_lazyest-gallery', array( &$this, 'manager_css' ) );	
		
		// lazyest-gallery actions and filters
		add_action( 'lazyest_ready', array( &$this, 'lazyest_ready' ), 5	);
		add_action( 'lazyest-gallery-settings_thumbnails', array( &$this, 'settings_thumbnails' ) );
		add_action( 'lazyest-gallery-settings_pages', array( &$this, 'settings_page' ) );			
		
		// wordpress actions and filters	
		add_action( 'wp_enqueue_scripts', array( &$this, 'register_scripts' ), 1 );	
		add_action( 'admin_print_scripts-toplevel_page_lazyest-filemanager', array( &$this, 'manager_js' ) );
				
		if ( $this->options['disable-gallery'] && $this->options['disable-shortcodes'] )
			return;
	}
	
	/**
	 * LazyestMaps::defaults()
	 * 
	 * @return
	 */
	function defaults() {
		return array(
			'disable-gallery' => 0,
			'disable-shortcodes' => 0,
			'info-captions' => 0,
			'info-descriptions' => 0,
			'map-height' => 400,
			'version' => $this->version()
		);
	}
	
	/**
	 * LazyestMaps::activation()
	 * 
	 * @return void
	 */
	function activation() {
		if ( ! isset( $this->options['version'] ) || version_compare( $this->options['version'], $this->version(), '<' ) )
			$this->upgrade();
	}
	
	/**
	 * LazyestMaps::uninstall()
	 * 
	 * @return
	 */
	function uninstall() {
		if ( __FILE__ != WP_UNINSTALL_PLUGIN )
  		return;
  	delete_option( 'lazyest-maps' );	
	}
	
	/**
	 * LazyestMaps::upgrade()
	 * 
	 * @return void
	 */
	function upgrade() {
		$defaults = $this->defaults();
		foreach( $default as $key => $value ) {
			$this->options[$key] = isset( $this->options[$key] ) ? $this->options[$key] : $value;
		}
		$this->options['version'] = $this->version();
		update_option( 'lazyest-maps', $this->options );
	}
	
	// wordpress actions and filters
	/**
	 * LazyestMaps::do_action()
	 * 
	 * @return void
	 */
	function do_action() {			
		$redirect = admin_url( 'admin.php?page=lazyest-gallery&subpage=lazyest-maps' );$nonce = $_POST['_wpnonce'];
		if ( wp_verify_nonce( $nonce, 'lazyest_maps' ) ) {
			$options = isset( $_POST['lazyest-maps'] ) ? $_POST['lazyest-maps'] : $this->options;
			$defaults =  $this->defaults();
			foreach( $defaults as $key => $value ) {
				$options[$key] = isset( $options[$key] ) ? $options[$key] : $value;				
			}
			update_option( 'lazyest-maps', $options );
			set_transient( 'lazyest_maps_notice', 'updated', 30 );
		}		
		wp_redirect( $redirect ); 
    exit();
	}
	
	/**
	 * LazyestMaps::admin_notices()
	 * 
	 * @return void
	 */
	function admin_notices() {
		$notice = get_transient( 'lazyest_maps_notice' );
		if ( $notice && ( 'updated' ==  $notice ) ){
			$message = esc_html__( 'Maps settings saved', 'lazyest-maps' );
			echo "<div class='updated'><p><strong>$message</strong></p></div>";
			delete_transient( 'lazyest_maps_notice' );
		}
	}
	
	/**
	 * LazyestMaps::scripts()
	 * 
	 * @return void
	 */
	function register_scripts() {	
		$j = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'js' : 'min.js';	
		wp_register_script( 'google-maps', 'http://maps.googleapis.com/maps/api/js?sensor=false', array( 'jquery'      ), 'V3',             true );
		wp_register_script( 'gmap',         plugins_url( "js/gmap3.$j",  __FILE__ ),              array( 'google-maps' ), '5.0b',           true );
		wp_register_script( 'lazyest-maps', plugins_url( "js/lazyest-maps.$j",  __FILE__ ),       array( 'gmap'        ), $this->version(), true );		
	}
	
	/**
	 * LazyestMaps::manager_css()
	 * 
	 * @return void
	 */
	function manager_css() {	
		wp_enqueue_style( 'maps-admin', plugins_url( 'css/admin.css',  __FILE__ ), $this->version()  );
	}
	
	// Lazyest Gallery filters and actions
	/**
	 * LazyestMaps::lazyest_ready()
	 * Registers extra field and invokes mapper functionality
	 * 
	 * @return void
	 */
	function lazyest_ready() {
		lg_add_extrafield( 'geotag', __( 'Image Location', 'lazyest-maps' ), 'image', true );
		require_once( plugin_dir_path( __FILE__ ) . 'inc/mapper.php' );
		$this->mapper = new LazyestMapper();
		if ( is_admin() ) {
			require_once( plugin_dir_path( __FILE__ ) . 'inc/admin.php' );
			$this->display = new LazyestMapsAdmin();
		} else {
			require_once( plugin_dir_path( __FILE__ ) . 'inc/frontend.php' );
			$this->display = new LazyestMapsFrontend();
		}						
	}	
	
	/**
	 * LazyestMaps::settings_thumbnails()
	 * Add row with Lazyest Maps button in Lazyest Gallery Thumbnail Settings
	 * 
	 * @return void
	 */
	function settings_thumbnails() {
		?>
		<tr>
      <th scope="row"><?php esc_html_e( 'Maps', 'lazyest-maps' ); ?></th>
      <td id="lazyest_maps_settings_button" >
        <p><a class="button" href="admin.php?page=lazyest-gallery&amp;subpage=lazyest-maps"><?php esc_html_e( 'Lazyest Maps Settings', 'lazyest-maps' ) ?></a></p>
        <p class="description"><?php esc_html_e( 'Show your Image Locations on a Map', 'lazyest-maps' ) ?></p>
      </td>  
    </tr>
		<?php
	}	
	
	/**
	 * LazyestMaps::settings_page()
	 * Request Lazyest Maps settings page
	 * 
	 * @param mixed $settings
	 * @return
	 */
	function settings_page( $settings ) {
		if ( ! isset( $_REQUEST['subpage'] ) || 'lazyest-maps' != $_REQUEST['subpage'] )
			return;
		$settings->other_page = true;	
		require_once( plugin_dir_path( __FILE__ ) . 'inc/settings.php' );
		$lazyest_map_settings = new LazyestMapSettings( $settings );
		$lazyest_map_settings->display();
		unset( $lazyest_map_settings ); 	
	}
	
	/**
	 * LazyestMaps::manager_js()
	 * Enqueue admin scripts
	 * 
	 * @return void
	 */
	function manager_js() {
		wp_enqueue_script( 'maps-admin', plugins_url( "js/admin.js",  __FILE__ ), array( 'jquery' ), $this->version(), true );
		wp_localize_script( 'maps-admin', 'lazyestMapsText', $this->localize_manager() );
	}	
	
	/**
	 * LazyestMaps::localize_manager()
	 * Strings for admin javascript
	 * 
	 * @return
	 */
	function localize_manager() {
		return array (
			'getGeoData' => __( 'Get Geo Data', 'lazyest-maps' ),
			'folderFail' => __( 'Could not read Geo Data', 'lazyest-maps' ),
			'folderSuccess' => __( 'Geo Data read from %s Images', 'lazyest-maps' ) 
		);
	}
	
	// utility functions
	/**
	 * LazyestMaps::version()
	 * 
	 * @return string this plugin version
	 */
	function version() {		  	
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$plugin_data = get_plugin_data( __FILE__ );
  	return $plugin_data['Version'];
	}
	
} // LazyestMaps

$lazyest_maps = new Lazyestmaps;

?>