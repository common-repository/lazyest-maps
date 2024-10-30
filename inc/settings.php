<?php

class LazyestMapSettings {
	
	var $settings;
	
	function __construct( $settings ) {
		$this->settings = $settings;
	}
// TODO: enable Gallery / Shortcodes	
	function display() {
		global $lg_gallery, $lazyest_map, $wp_version;
		?>
		<div id="lazyest-maps-settings" class="wrap">
			<?php screen_icon( 'lazyestmaps' ); ?>
      <h2><?php echo esc_html_e( 'Show your Image Locations on a Map', 'lazyest-maps' ); ?></h2> 
				<form id="lazyest-maps" method="post" action="admin.php">
					<?php wp_nonce_field( 'lazyest_maps' );  ?>
					<input type="hidden" name="action" value="lazyest-maps" /> 
							<fieldset>
							<legend><?php esc_html__( 'Map Settings', 'lazyest-maps' ); ?></legend>
								<?php $this->main_settings(); ?>
								<?php $this->map_settings(); ?>
								<?php $this->infowindow_settings(); ?>          	
         				<?php $this->sidebar() ?>
							</fieldset>
				</form>				 
		</div>	
				
		<?php
	}
	
	// settings page boxes
	
	function main_settings(){
		global $lazyest_maps;
		?>
			<h3><?php esc_html_e( 'Main Options', 'lazyest-maps' ); ?></h3>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="disable-gallery"><?php esc_html_e( 'Gallery Map', 'lazyest-maps' ); ?></label>
							</th>
							<td>
								<input type="checkbox" id="disable-gallery" name="lazyest-maps[disable-gallery]" value="1" <?php checked( '1', $lazyest_maps->options['disable-gallery'] ); ?> />
								<p class="description"><?php esc_html_e( 'Disable Maps in your Galleries', 'lazyest-maps' ); ?></p>
							</td>
						</tr>
						<tr>
						<th scope="row">
								<label for="disable-shortcodes"><?php esc_html_e( 'Shortcodes', 'lazyest-maps' ); ?></label>
							</th>
							<td>
								<input type="checkbox" id="disable-shortcodes" name="lazyest-maps[disable-shortcodes]" value="1" <?php checked( '1', $lazyest_maps->options['disable-shortcodes'] ); ?> />
								<p class="description"><?php esc_html_e( 'Disable Shortcodes in posts', 'lazyest-maps' ); ?></p>
							</td>
						</tr>
					</tbody>
				</table>
		<?php
	}
	
	/**
	 * LazyestMapSettings::map_settings()
	 * 
	 * @return void
	 */
	function map_settings() {	
		global $lazyest_maps;
		?>
			<h3><?php esc_html_e( 'Map Options', 'lazyest-maps' ); ?></h3>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="map-height"><?php esc_html_e( 'Height', 'lazyest-maps' ); ?></label>
							</th>
							<td>					
								<input class="small-text" type="number" id="map-height" name="lazyest-maps[map-height]" value="<?php echo $lazyest_maps->options['map-height'] ?>" step="1" min="100" />
								<p class="description"><?php esc_html_e( 'Height of Map in pixels', 'lazyest-maps' ); ?></p>
							</td>
						</tr>						
					</tbody>
				</table>
		<?php		
	}
	
	/**
	 * LazyestMapSettings::infowindow_settings()
	 * 
	 * @return void
	 */
	function infowindow_settings() {
		global $lazyest_maps;
		?>
			<h3><?php esc_html_e( 'Info Window Options', 'lazyest-maps' ); ?></h3>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="info-captions"><?php esc_html_e( 'Captions', 'lazyest-maps' ); ?></label>
							</th>
							<td>
								<input type="checkbox" id="info-captions" name="lazyest-maps[info-captions]" value="1" <?php checked( '1', $lazyest_maps->options['info-captions'] ); ?> />
								<p class="description"><?php esc_html_e( 'Show image captions in Info Window', 'lazyest-maps' ); ?></p>
							</td>
						</tr>
						<tr>
						<th scope="row">
								<label for="info-descriptions"><?php esc_html_e( 'Descriptions', 'lazyest-maps' ); ?></label>
							</th>
							<td>
								<input type="checkbox" id="info-descriptions" name="lazyest-maps[info-descriptions]" value="1" <?php checked( '1', $lazyest_maps->options['info-descriptions'] ); ?> />
								<p class="description"><?php esc_html_e( 'Show image descriptions in Info Window', 'lazyest-maps' ); ?></p>
							</td>
						</tr>
					</tbody>
				</table>
		<?php
	}
	
	/**
	 * LazyestMapSettings::sidebar()
	 * 
	 * @return void
	 */
	function sidebar() {
		?>
		<p class="submit">
			<input class="button-primary" type="submit" name="lazyest-maps[update]" value="<?php	esc_html_e( 'Save Changes', 'lazyest-maps' )	?>" />
			<p><a id="back_link" href="admin.php?page=lazyest-gallery" title="<?php esc_html_e( 'Back to Lazyest Gallery Settings', 'lazyest-maps' ) ?>"><?php esc_html_e( 'Back to Lazyest Gallery Settings', 'lazyest-maps' ) ?></a></p>		</p>
    <?php		
	}
	
} // LazyestMapSettings