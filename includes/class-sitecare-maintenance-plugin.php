<?php
/**
 * Main plugin bootstrap class.
 *
 * @package SiteCareMaintenanceMode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Starts the settings and frontend parts of the plugin.
 */
class SiteCare_Maintenance_Plugin {

	/**
	 * Settings handler.
	 *
	 * @var SiteCare_Maintenance_Settings
	 */
	private $settings;

	/**
	 * Frontend handler.
	 *
	 * @var SiteCare_Maintenance_Frontend
	 */
	private $frontend;

	/**
	 * Registers plugin hooks.
	 *
	 * @return void
	 */
	public function run() {
		$this->settings = new SiteCare_Maintenance_Settings();
		$this->frontend = new SiteCare_Maintenance_Frontend();

		$this->settings->register_hooks();
		$this->frontend->register_hooks();
	}
}
