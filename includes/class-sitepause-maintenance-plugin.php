<?php
/**
 * Main plugin bootstrap class.
 *
 * @package SitePause
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Starts the settings and frontend parts of the plugin.
 */
class SitePause_Maintenance_Plugin {

	/**
	 * Settings handler.
	 *
	 * @var SitePause_Maintenance_Settings
	 */
	private $settings;

	/**
	 * Frontend handler.
	 *
	 * @var SitePause_Maintenance_Frontend
	 */
	private $frontend;

	/**
	 * Registers plugin hooks.
	 *
	 * @return void
	 */
	public function run() {
		$this->settings = new SitePause_Maintenance_Settings();
		$this->frontend = new SitePause_Maintenance_Frontend();

		$this->settings->register_hooks();
		$this->frontend->register_hooks();
	}
}
