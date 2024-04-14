<?php
/**
 * Plugin Name: Rather Simple Background Slideshow
 * Plugin URI:
 * Update URI: false
 * Version: 1.0
 * Requires at least: 5.3
 * Requires PHP: 7.4
 * Author: Oscar Ciutat
 * Author URI: http://oscarciutat.com/code/
 * Description: A really simple background slideshow
 * License: GPLv2 or later
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package rather_simple_background_slideshow
 */

/**
 * Core class used to implement the plugin.
 */
class Rather_Simple_Background_Slideshow {

	/**
	 * Plugin instance
	 *
	 * @var object $instance
	 */
	protected static $instance = null;

	/**
	 * Access this plugin’s working instance
	 */
	public static function get_instance() {

		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Used for regular plugin work
	 */
	public function plugin_setup() {

		$this->includes();

		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_shortcode( 'bgslideshow', array( $this, 'display_shortcode' ) );
	}

	/**
	 * Constructor. Intentionally left empty and public.
	 */
	public function __construct() {}

	/**
	 * Includes required core files used in admin and on the frontend
	 */
	protected function includes() {}

	/**
	 * Enqueues scripts in the frontend
	 */
	public function wp_enqueue_scripts() {
		wp_enqueue_style(
			'rather-simple-background-slideshow-css',
			plugins_url( '/assets/css/vegas.min.css', __FILE__ ),
			array(),
			filemtime( plugin_dir_path( __FILE__ ) . '/assets/css/vegas.min.css' )
		);
		wp_enqueue_script(
			'rather-simple-background-slideshow',
			plugins_url( '/assets/js/vegas.min.js', __FILE__ ),
			array( 'jquery' ),
			filemtime( plugin_dir_path( __FILE__ ) . '/assets/js/vegas.min.js' ),
			false
		);
	}

	/**
	 * Shows a background slideshow
	 */
	public function display_shortcode() {

		$html = '';

		$args = array(
			'post_type'      => 'attachment',
			'numberposts'    => -1,
			'post_status'    => null,
			'post_parent'    => get_the_ID(),
			'post_mime_type' => 'image',
			'orderby'        => 'rand',
		);

		$list        = '';
		$attachments = get_posts( $args );
		if ( $attachments ) {
			foreach ( $attachments as $attachment ) {
				$image_attributes = wp_get_attachment_image_src( $attachment->ID, 'full' );
				$list             = $list . '{ src:"' . $image_attributes[0] . '" },';
			}
		}
		$list = rtrim( $list, ',' );

		$selector = apply_filters( 'rsbs_selector', 'body' );

		$html .= '<script>
            jQuery( function( $ ) {
                $( "' . wp_strip_all_tags( $selector ) . '" ).vegas( {
                    slides: [' . $list . '],
                    delay: 15000,
                    timer: false
                } );
            } );
            </script>';

		return $html;
	}
}

add_action( 'plugins_loaded', array( Rather_Simple_Background_Slideshow::get_instance(), 'plugin_setup' ) );
