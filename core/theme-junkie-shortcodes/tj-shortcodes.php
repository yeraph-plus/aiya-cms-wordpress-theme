<?php
/**
 * Plugin Name:  TJ Shortcodes
 * Plugin URI:   http://www.theme-junkie.com/
 * Description:  A simple pack of shortcodes to enhance your site functionality.
 * Version:      0.1.3
 * Author:       Theme Junkie
 * Author URI:   http://www.theme-junkie.com/
 * Author Email: support@theme-junkie.com
 *
 * This plugin is a fork version from ZillaShortcodes http://www.themezilla.com/plugins/zillashortcodes,
 * we only add a few bug fixes and add another shortcodes options.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write 
 * to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @package    Theme_Junkie_Shortcodes
 * @since      0.1.0
 * @author     Theme Junkie
 * @copyright  Copyright (c) 2014-2015, Theme Junkie
 * @license    http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class TJ_Shortcodes {

	/**
	 * PHP5 constructor method.
	 *
	 * @since  0.1.0
	 */
	public function __construct() {

		// Set constant path to the plugin directory.
		add_action( 'plugins_loaded', array( &$this, 'constants' ), 1 );

		// Internationalize the text strings used.
		add_action( 'plugins_loaded', array( &$this, 'i18n' ), 2 );

		// Load the plugin functions files.
		add_action( 'plugins_loaded', array( &$this, 'includes' ), 3 );

		// Loads the admin styles and scripts.
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_scripts' ) );

		// Loads the frontend styles and scripts.
		add_action( 'wp_enqueue_scripts', array( &$this, 'frontend_scripts' ) ); 

	}

	/**
	 * Defines constants used by the plugin.
	 *
	 * @since  0.1.0
	 */
	public function constants() {

		// Set constant path to the plugin directory.
		define( 'TJSH_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

		// Set the constant path to the plugin directory URI.
		define( 'TJSH_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

		// Set the constant path to the inc directory.
		define( 'TJSH_INC', TJSH_DIR . trailingslashit( 'inc' ) );

		// Set the constant path to the shortcodes directory.
		define( 'TJSH_SH', TJSH_DIR . trailingslashit( 'shortcodes' ) );

		// Set the constant path to the assets directory.
		define( 'TJSH_ASSETS', TJSH_URI . trailingslashit( 'assets' ) );

	}

	/**
	 * Loads the translation files.
	 *
	 * @since  0.1.0
	 */
	public function i18n() {
		load_plugin_textdomain( 'junkie', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Loads the initial files needed by the plugin.
	 *
	 * @since  0.1.0
	 */
	public function includes() {
		require_once( TJSH_INC . 'class-junkie-editor-shortcodes.php' );

		// Loads the shortcodes
		require_once( TJSH_SH . 'alert.php' );
		require_once( TJSH_SH . 'button.php' );
		require_once( TJSH_SH . 'column.php' );
		require_once( TJSH_SH . 'tab.php' );
		require_once( TJSH_SH . 'toggle.php' );
		require_once( TJSH_SH . 'dropcap.php' );
		require_once( TJSH_SH . 'hightlights.php' );
	}

	/**
	 * Loads the admin styles and scripts.
	 *
	 * @since  0.1.0
	 */
	function admin_scripts() {

		// Check if the current screen is post base.
		if ( 'post' != get_current_screen()->base ) {
			return;
		}
		
		// Loads the popup custom style.
		wp_enqueue_style( 'junkie-popup-style', trailingslashit( TJSH_ASSETS ) . 'css/junkie-admin.css', null, null );

		// Sortable
		wp_enqueue_script( array( 'jquery', 'jquery-ui-sortable' ) );
		
	}

	/**
	 * Loads the frontend styles and scripts.
	 *
	 * @since  0.1.0
	 */
	function frontend_scripts() {

		// Load the shortcodes stylesheet.
		wp_enqueue_style( 'junkie-shortcodes', trailingslashit( TJSH_ASSETS ) . 'css/junkie-shortcodes.css' );

		// Load the shortcodes scripts.
		wp_enqueue_script( 'junkie-shortcodes-js', trailingslashit( TJSH_ASSETS ) . 'js/junkie-shortcodes.js', array( 'jquery', 'jquery-ui-accordion', 'jquery-ui-tabs' ), null, true );

	}

}

new TJ_Shortcodes;