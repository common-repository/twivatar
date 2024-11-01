<?php
/**
 * Twivatar WordPress plugin.
 *
 * Adds a shortcode that echos the current Twitter avatar for a username.
 *
 * @package GaryJones\Twivatar
 * @author  Gary Jones <gary@garyjones.co.uk>
 * @license GPL-2.0+
 */

/**
 * Plugin class for Twivatar
 *
 * @package GaryJones\Twivatar
 * @author Gary Jones <gary@garyjones.co.uk>
 */
class Twivatar {
	/**
	 * Holds copy of instance, so other plugins can remove our hooks.
	 *
	 * @since 1.0.0
	 *
	 * @link http://core.trac.wordpress.org/attachment/ticket/16149/query-standard-format-posts.php
	 * @link http://twitter.com/#!/markjaquith/status/66862769030438912
	 *
	 * @var Twivatar
	 */
	static $instance;

	/**
	 * Base name of the transient.
	 *
	 * The actual cache values are held under a transient key with a _size
	 * suffix e.g. twivatar_images_normal.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $transient_name = 'twivatar_images';

	/**
	 * Constructor.
	 *
	 * Set static instance of object and kick start the initialisation.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		self::$instance = $this;
		add_action( 'init', array( &$this, 'init' ) );
	}

	/**
	 * Add i18n support and shortcodes.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$domain = 'twivatar';
		// The "plugin_locale" filter is also used in load_plugin_textdomain()
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		load_textdomain( 'twivatar', WP_LANG_DIR . '/twivatar/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( 'twivatar', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		add_shortcode( 'twivatar', array( $this, 'shortcode' ) );
	}

	/**
	 * Add shortcode functionality.
	 *
	 * @since 1.0.0
	 *
	 * @uses Twivatar::image()
	 *
	 * @param array|string $atts Array of attribute => values given when shortcode was
	 *                           used. Empty string if no attributes given.
	 *
	 * @return string XHTML markup for image.
	 */
	public function shortcode( $atts ) {
		$args = shortcode_atts(
			array(
				'class'		=> '',
				'link'      => true,
				'linkclass' => '',
				'name'      => '',
				'rel'       => '',
				'size'      => 'normal',
				'title'     => ''
			),
			$atts
		);

		return $this->image( $args, false );
	}

	/**
	 * Echo HTML string for Twitter image for $screen_name.
	 *
	 * @since 1.0.0
	 *
	 * @uses Twivatar::get_url()
	 * @see  Twivatar::shortcode() for list of arguments
	 *
	 * @param array $args Arguments for creating the optional link and image.
	 * @param bool  $echo Echo (default) instead of return.
	 *
	 * @return bool|string False if no Twitter handle given. Otherwise XHTML
	 *                     string for image (if $echo argument set to false).
	 */
	protected function image( array $args = array(), $echo = true ) {
		extract($args);

		if ( ! $name ) {
			return false;
		}

		$image_size = array(
			'mini'   => 24,
			'normal' => 48,
			'bigger' => 73,
		);

		if ( ! array_key_exists( $size, $image_size ) ) {
			$size = 'normal';
		}

		$class = $class ? ' class="' . esc_attr( $class ) . '"' : '';
		$linkclass = $linkclass ? ' class="' . esc_attr( $linkclass ) . '"' : '';
		$rel = $rel ? ' rel="' . esc_attr( $rel ) . '"' : '';
		$title = $title ? ' title="' . esc_attr( $title ) . '"' : '';

		$html = $link ? '<a href="' . esc_url( 'http://twitter.com/' . $name ) . '"' . $linkclass . $rel . '>' : '';
		$html .= '<img src="' . esc_url( $this->get_url( $name, $size ) ) . '" width="' . esc_attr( $image_size[$size] ) . '" height="' . esc_attr( $image_size[$size] ) . '" alt="' . esc_attr( $name ) . '"' . $class . $title . '/>';
		$html .= $link ? '</a>' : '';

		if ( $echo ) {
			echo $html;
		}
		return $html;
	}

	/**
	 * Return source URL for Twitter image for $screen_name.
	 *
	 * Checks and stores source URL as a transient, for 1 day.
	 * Returns a default image if profile image cannot be determined.
	 *
	 * @since 1.0.0
	 *
	 * @uses Twivatar::cache_url() Caches and returns the image URL if not
	 *                             already cached.
	 *
	 * @param string $screen_name Twitter handle.
	 * @param string $size         One of 'mini', 'normal' or 'bigger'. Default
	 *                             is normal.
	 *
	 * @return string URL of user profile image or default image.
	 */
	protected function get_url( $screen_name, $size = 'normal' ) {
		// Attempt to get the cached data
		$images = get_transient( $this->transient_name . '_' . $size );

		// If it exists, and if the $screen_name key exists, return it
		if ( false !== $images && isset( $images[$screen_name] ) ) {
			return $images[$screen_name];
		}

		// If not, make a request to Twitter for the user profile in JSON format.
		return $this->cache_url( $screen_name, $size );
	}

	/**
	 * Return the image URL, once it's been retrieved and cached as a transient.
	 * Private.
	 *
	 * @since 1.0.0
	 *
	 * @param string $screen_name Twitter handle.
	 * @param string $size        One of 'mini', 'normal' or 'bigger'. Default
	 *                            is normal.
	 *
	 * @return string URL of user profile image or default image
	 *
	 * @todo Use Settings API for transient cache length and no-twitter-image
	 * default.
	 */
	protected function cache_url( $screen_name, $size ) {
		$query_args = array(
			'screen_name' => $screen_name,
			'size' => $size,
		);
		$url = add_query_arg( $query_args, 'http://api.twitter.com/1/users/show.json' );
		$response = wp_remote_get( $url );

		// Attempt to get the cached data
		$images = get_transient( $this->transient_name . '_' . $size );

		if ( ! $images ) {
			$images = array();
		}

		if ( ! is_wp_error( $response ) ) {

			// If the profile can't be found (or maybe if Twitter is down),
			// return a default image from the theme folder.
			if ( 200 != wp_remote_retrieve_response_code( $response ) )
				return get_stylesheet_directory_uri() . '/images/no-twitter-image.png';

			// Decode the response body
			$value = json_decode( $response['body'], true );

			// Add / edit the collection of URLs
			$images[$screen_name] = $value['profile_image_url'];

			// Cache the amended array for another 1 day.
			set_transient( $this->transient_name . '_' . $size, $images, 60 * 60 * 24 );

			// Return the URL for this $screen_name
			return $images[$screen_name];
		}
	}
}
