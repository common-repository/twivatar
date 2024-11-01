<?php
/**
 * Twivatar WordPress plugin.
 *
 * Adds a shortcode that echos the current Twitter avatar for a username.
 *
 * @package GaryJones\Twivatar
 * @author  Gary Jones <gary@garyjones.co.uk>
 * @license GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Twivatar
 * Plugin URI: https://github.com/GaryJones/twivatar/
 * Description: Adds a shortcode that echos the current Twitter avatar for a username. Image URL is cached.
 * Version: 1.0.2
 * Author: Gary Jones
 * Author URI: http://garyjones.co.uk
 * License: GPL-2.0+
 * Text Domain: twivatar
 * Domain Path: /languages/
 */

require plugin_dir_path( __FILE__ ) . 'class-twivatar.php';

$GLOBALS['twivatar'] = new Twivatar;