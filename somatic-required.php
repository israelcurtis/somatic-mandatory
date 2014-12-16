<?php
/*
Plugin Name: Somatic Required Plugins
Plugin URI: http://somaticstudios.com
Description: Make certain plugins required so that they cannot be (easily) deactivated.
Author: Somatic Studios
Version: 0.2
Domain: somatic-required-plugins
License: GPLv2
Path: languages
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Required plugins class
 *
 * @package WordPress
 *
 * @subpackage Project
 */
class soma_required_plugins {

	/**
	 * Instance of this class.
	 *
	 * @var WDS_Required_Plugins object
	 */
	public static $instance = null;

	/**
	 * Whether text-domain has been registered
	 * @var boolean
	 */
	private static $l10n_done = false;

	/**
	 * Creates or returns an instance of this class.
	 * @since  0.1.0
	 * @return WDS_Required_Plugins A single instance of this class.
	 */
	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initiate our hooks
	 *
	 * @since 0.1.0
	 */
	private function __construct() {
		add_filter( 'admin_init', array( $this, 'activate_if_not' ) );
		add_filter( 'plugin_action_links', array( $this, 'filter_plugin_links' ), 10, 2 );
		// load text domain
		add_action( 'plugins_loaded', array( $this, 'l10n' ) );
	}

	/**
	 * Activate required plugins if they are not.
	 *
	 * @since 0.1.1
	 */
	public function activate_if_not() {
		foreach ( $this->get_required_plugins() as $plugin ) {
			if ( ! is_plugin_active( $plugin ) ) {
				activate_plugin( $plugin );
			}
		}
	}

	/**
	 * Remove the deactivation link for all custom/required plugins
	 *
	 * @since 0.1.0
	 *
	 * @param $actions
	 * @param $plugin_file
	 * @param $plugin_data
	 * @param $context
	 *
	 * @return array
	 */
	public function filter_plugin_links( $actions = array(), $plugin_file ) {
		// Remove edit link for all plugins
		if ( array_key_exists( 'edit', $actions ) ) {
			unset( $actions['edit'] );
		}

		// Remove deactivate link for required plugins
		if( array_key_exists( 'deactivate', $actions ) && in_array( $plugin_file, $this->get_required_plugins() ) ) {
			$actions['deactivate'] = sprintf( '<span style="color: #888">%s</span>', __( 'WDS Required Plugin', 'wds-required-plugins' ) );
		}

		return $actions;
	}

	/**
	 * Get the plugins that are required for the project. Plugins will be registered by the wds_required_plugins filter
	 *
	 * @since  0.1.0
	 *
	 * @return array
	 */
	public function get_required_plugins() {
		return (array) apply_filters( 'wds_required_plugins', array() );
	}

	/**
	 * Load this library's text domain
	 * @since  0.2.1
	 */
	public function l10n() {
		// Only do this one time
		if ( self::$l10n_done ) {
			return;
		}

		$loaded = load_plugin_textdomain( 'wds-required-plugins', false, '/languages/' );
		if ( ! $loaded ) {
			$loaded = load_muplugin_textdomain( 'wds-required-plugins', '/languages/' );
		}
		if ( ! $loaded ) {
			$loaded = load_theme_textdomain( 'wds-required-plugins', '/languages/' );
		}

		if ( ! $loaded ) {
			$locale = apply_filters( 'plugin_locale', get_locale(), 'wds-required-plugins' );
			$mofile = dirname( __FILE__ ) . '/languages/wds-required-plugins-'. $locale .'.mo';
			load_textdomain( 'wds-required-plugins', $mofile );
		}
	}

	public function modify_debug() {
		// hide STRICT errors with debugging on (which seems to override php.ini settings)
		if ( WP_DEBUG ) {
		    error_reporting( E_ALL & ~E_STRICT );
		}
	}

}
soma_required_plugins::init();
