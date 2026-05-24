<?php
/**
 * Admin settings class.
 *
 * @package SitePause
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers admin settings hooks for the plugin.
 */
class SiteCare_Maintenance_Settings {

	/**
	 * Whether hooks have been registered.
	 *
	 * @var bool
	 */
	private $hooks_registered = false;

	/**
	 * Registers settings-related hooks.
	 *
	 * The rendering and sanitization functions stay procedural for now so this
	 * first class refactor remains easy to follow.
	 *
	 * @return void
	 */
	public function register_hooks() {
		if ( $this->hooks_registered ) {
			return;
		}

		$this->hooks_registered = true;

		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_notices', array( $this, 'render_admin_notices' ) );
		add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_status' ), 100 );
		add_action( 'admin_head', array( $this, 'print_admin_bar_status_styles' ) );
		add_action( 'wp_head', array( $this, 'print_admin_bar_status_styles' ) );
		add_action( 'admin_post_sitecare_maintenance_export_settings', array( $this, 'export_settings' ) );
		add_action( 'admin_post_sitecare_maintenance_import_settings', array( $this, 'import_settings' ) );
		add_filter( 'wp_redirect', array( $this, 'filter_settings_redirect' ) );
	}

	/**
	 * Registers plugin settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		sitecare_maintenance_register_settings();
	}

	/**
	 * Sanitizes plugin settings.
	 *
	 * @param array $input Raw settings input.
	 * @return array
	 */
	public static function sanitize_options( $input ) {
		return sitecare_maintenance_sanitize_options( $input );
	}

	/**
	 * Loads admin assets.
	 *
	 * @param string $hook_suffix Current admin page hook suffix.
	 * @return void
	 */
	public function enqueue_admin_assets( $hook_suffix ) {
		sitecare_maintenance_enqueue_admin_assets( $hook_suffix );
	}

	/**
	 * Adds the settings page.
	 *
	 * @return void
	 */
	public function add_admin_menu() {
		sitecare_maintenance_add_admin_menu();
	}

	/**
	 * Filters the settings redirect URL.
	 *
	 * @param string $location Redirect URL.
	 * @return string
	 */
	public function filter_settings_redirect( $location ) {
		return sitecare_maintenance_filter_settings_redirect( $location );
	}

	/**
	 * Renders settings notices.
	 *
	 * @return void
	 */
	public function render_admin_notices() {
		sitecare_maintenance_render_admin_notices();
	}

	/**
	 * Adds a visible admin bar item when maintenance mode is active.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance.
	 * @return void
	 */
	public function add_admin_bar_status( $wp_admin_bar ) {
		sitecare_maintenance_add_admin_bar_status( $wp_admin_bar );
	}

	/**
	 * Prints small admin bar styles.
	 *
	 * @return void
	 */
	public function print_admin_bar_status_styles() {
		sitecare_maintenance_print_admin_bar_status_styles();
	}

	/**
	 * Exports plugin settings as JSON.
	 *
	 * @return void
	 */
	public function export_settings() {
		sitecare_maintenance_handle_export_settings();
	}

	/**
	 * Imports plugin settings from JSON.
	 *
	 * @return void
	 */
	public function import_settings() {
		sitecare_maintenance_handle_import_settings();
	}
}
