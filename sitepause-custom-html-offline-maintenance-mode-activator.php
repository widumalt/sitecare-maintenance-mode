<?php
/**
 * Plugin Name: SitePause - Custom HTML Offline & Maintenance Mode Activator
 * Plugin URI: https://witeds.com/sitepause/
 * Description: A user-friendly maintenance mode plugin for showing visitors a custom HTML or preset offline page while administrators keep working.
 * Version: 1.0.0
 * Author: WiTEDS
 * Author URI: https://witeds.com/sitepause/
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: sitepause-custom-html-offline-maintenance-mode-activator
 * Domain Path: /languages
 *
 * @package SitePause
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Current plugin version.
 */
define( 'SITEPAUSE_MAINTENANCE_VERSION', '1.0.0' );

/**
 * Absolute path to this main plugin file.
 */
define( 'SITEPAUSE_MAINTENANCE_FILE', __FILE__ );

/**
 * Absolute path to the plugin directory.
 */
define( 'SITEPAUSE_MAINTENANCE_PATH', plugin_dir_path( __FILE__ ) );

/**
 * URL to the plugin directory.
 */
define( 'SITEPAUSE_MAINTENANCE_URL', plugin_dir_url( __FILE__ ) );

/**
 * Name of the option that stores plugin settings.
 */
define( 'SITEPAUSE_MAINTENANCE_OPTION', 'sitepause_maintenance_options' );

/**
 * Runs when the plugin is activated.
 *
 * The plugin stores a small option array with safe defaults. It does not create
 * custom database tables.
 *
 * @return void
 */
function sitepause_maintenance_activate() {
	// Store safe default settings if the option does not exist yet.
	if ( false === get_option( SITEPAUSE_MAINTENANCE_OPTION ) ) {
		add_option( SITEPAUSE_MAINTENANCE_OPTION, sitepause_maintenance_default_options() );
	}
}

/**
 * Runs when the plugin is deactivated.
 *
 * The plugin does not register cron jobs, rewrite rules, or other persistent
 * behavior, so there is nothing to clean up on deactivation.
 *
 * @return void
 */
function sitepause_maintenance_deactivate() {
	// Keep settings so the site owner does not lose their choice on deactivate.
}

register_activation_hook( __FILE__, 'sitepause_maintenance_activate' );
register_deactivation_hook( __FILE__, 'sitepause_maintenance_deactivate' );

/**
 * Gets the default plugin options.
 *
 * @return array
 */
function sitepause_maintenance_default_options() {
	return array(
		'enabled'            => 0,
		'schedule_enabled'   => 0,
		'schedule_start'     => '',
		'schedule_end'       => '',
		'title'              => __( 'We will be back soon.', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'message'            => __( '{site name} is temporarily offline for maintenance. Please check back later.', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'contact_email'      => '',
		'contact_phone'      => '',
		'facebook_url'       => '',
		'instagram_url'      => '',
		'linkedin_url'       => '',
		'footer_text'        => '',
		'logo_attachment_id' => 0,
		'background_color'   => '#f5f5f5',
		'text_color'         => '#111111',
		'layout_width'       => 'medium',
		'template_style'     => 'classic',
		'countdown_enabled'  => 0,
		'countdown_target'   => '',
		'custom_html_enabled' => 0,
		'custom_html'        => '',
		'bypass_roles'       => array( 'administrator' ),
		'ip_whitelist'       => array(),
	);
}

/**
 * Gets allowed frontend template choices.
 *
 * @return array
 */
function sitepause_maintenance_get_template_styles() {
	return array(
		'classic'     => array(
			'label'       => __( 'Classic', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			'description' => __( 'Traditional clean page with balanced spacing and a professional default style.', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		),
		'center-card' => array(
			'label'       => __( 'Center Card', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			'description' => __( 'Modern card layout with stronger contrast, rounded corners, and a soft shadow.', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		),
		'minimal'     => array(
			'label'       => __( 'Minimal', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			'description' => __( 'Typography-first page with less decoration for simple business updates.', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		),
		'bold-panel'  => array(
			'label'       => __( 'Bold Panel', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			'description' => __( 'High-impact panel layout with stronger visual emphasis.', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		),
		'split-screen' => array(
			'label'       => __( 'Split Screen', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			'description' => __( 'Two-column layout with message and details separated.', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		),
	);
}

/**
 * Gets valid WordPress role keys.
 *
 * @return array
 */
function sitepause_maintenance_get_valid_role_keys() {
	$roles = wp_roles();

	if ( empty( $roles->roles ) || ! is_array( $roles->roles ) ) {
		return array();
	}

	return array_keys( $roles->roles );
}

/**
 * Sanitizes selected bypass role keys.
 *
 * @param array $roles Raw selected role keys.
 * @return array
 */
function sitepause_maintenance_sanitize_bypass_roles( $roles ) {
	$roles      = is_array( $roles ) ? $roles : array();
	$valid_keys = sitepause_maintenance_get_valid_role_keys();
	$clean      = array();

	foreach ( $roles as $role ) {
		$role = sanitize_key( $role );

		if ( in_array( $role, $valid_keys, true ) ) {
			$clean[] = $role;
		}
	}

	return array_values( array_unique( $clean ) );
}

/**
 * Sanitizes IP whitelist entries.
 *
 * @param array|string $ips Raw IP list.
 * @return array
 */
function sitepause_maintenance_sanitize_ip_whitelist( $ips ) {
	if ( is_string( $ips ) ) {
		$ips = preg_split( '/\r\n|\r|\n/', $ips );
	}

	$ips   = is_array( $ips ) ? $ips : array();
	$clean = array();

	foreach ( $ips as $ip ) {
		$ip = trim( sanitize_text_field( $ip ) );

		if ( '' === $ip ) {
			continue;
		}

		if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
			$clean[] = $ip;
		}
	}

	return array_values( array_unique( $clean ) );
}

/**
 * Sanitizes a countdown datetime-local value.
 *
 * @param string $value Raw datetime-local value.
 * @return string
 */
function sitepause_maintenance_sanitize_countdown_datetime( $value ) {
	return sitepause_maintenance_sanitize_schedule_datetime( $value );
}

/**
 * Gets allowed layout width choices.
 *
 * @return array
 */
function sitepause_maintenance_get_layout_widths() {
	return array(
		'narrow' => array(
			'label'     => __( 'Narrow', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			'max_width' => '480px',
		),
		'medium' => array(
			'label'     => __( 'Medium', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			'max_width' => '640px',
		),
		'wide'   => array(
			'label'     => __( 'Wide', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			'max_width' => '820px',
		),
	);
}

/**
 * Gets saved plugin options merged with safe defaults.
 *
 * @return array
 */
function sitepause_maintenance_get_options() {
	$options = get_option( SITEPAUSE_MAINTENANCE_OPTION, array() );

	if ( ! is_array( $options ) ) {
		$options = array();
	}

	return wp_parse_args( $options, sitepause_maintenance_default_options() );
}

/**
 * Gets a readable WordPress timezone label for helper text.
 *
 * @return string
 */
function sitepause_maintenance_get_timezone_label() {
	$timezone = wp_timezone_string();

	if ( '' !== $timezone ) {
		return $timezone;
	}

	return 'UTC';
}

/**
 * Sanitizes a datetime-local value.
 *
 * The browser sends datetime-local values like 2026-05-23T14:30. The value is
 * saved in that local format and later interpreted using the WordPress site
 * timezone.
 *
 * @param string $value Raw datetime-local value.
 * @return string
 */
function sitepause_maintenance_sanitize_schedule_datetime( $value ) {
	$value = sanitize_text_field( $value );

	if ( '' === $value ) {
		return '';
	}

	if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $value ) ) {
		return '';
	}

	$date = DateTimeImmutable::createFromFormat( 'Y-m-d\TH:i', $value, wp_timezone() );
	$errors = DateTimeImmutable::getLastErrors();

	if ( false === $date || ( is_array( $errors ) && ( $errors['warning_count'] > 0 || $errors['error_count'] > 0 ) ) ) {
		return '';
	}

	return $date->format( 'Y-m-d\TH:i' );
}

/**
 * Converts a saved schedule value to a timestamp in the WordPress timezone.
 *
 * @param string $value Saved datetime-local value.
 * @return int|null
 */
function sitepause_maintenance_schedule_datetime_to_timestamp( $value ) {
	$value = sitepause_maintenance_sanitize_schedule_datetime( $value );

	if ( '' === $value ) {
		return null;
	}

	$date = DateTimeImmutable::createFromFormat( 'Y-m-d\TH:i', $value, wp_timezone() );
	$errors = DateTimeImmutable::getLastErrors();

	if ( false === $date || ( is_array( $errors ) && ( $errors['warning_count'] > 0 || $errors['error_count'] > 0 ) ) ) {
		return null;
	}

	return $date->getTimestamp();
}

/**
 * Sanitizes plugin settings before WordPress saves them.
 *
 * @param array $input Raw settings input.
 * @return array
 */
function sitepause_maintenance_sanitize_options( $input ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return sitepause_maintenance_get_options();
	}

	$input = is_array( $input ) ? $input : array();
	$defaults = sitepause_maintenance_default_options();
	$bypass_roles = array_key_exists( 'bypass_roles', $input ) ? sitepause_maintenance_sanitize_bypass_roles( (array) $input['bypass_roles'] ) : array();
	$ip_whitelist = array_key_exists( 'ip_whitelist', $input ) ? sitepause_maintenance_sanitize_ip_whitelist( $input['ip_whitelist'] ) : array();
	$input    = wp_parse_args( $input, $defaults );

	$title   = sanitize_text_field( $input['title'] );
	$message = sanitize_textarea_field( $input['message'] );

	if ( '' === trim( $title ) ) {
		$title = $defaults['title'];
	}

	if ( '' === trim( $message ) ) {
		$message = $defaults['message'];
	}

	$background_color = sanitize_hex_color( $input['background_color'] );
	$text_color       = sanitize_hex_color( $input['text_color'] );
	$layout_widths    = sitepause_maintenance_get_layout_widths();
	$layout_width     = sanitize_key( $input['layout_width'] );
	$template_styles  = sitepause_maintenance_get_template_styles();
	$template_style   = sanitize_key( $input['template_style'] );
	$countdown_enabled = empty( $input['countdown_enabled'] ) ? 0 : 1;
	$countdown_target  = sitepause_maintenance_sanitize_countdown_datetime( $input['countdown_target'] );
	$custom_html_enabled = empty( $input['custom_html_enabled'] ) ? 0 : 1;
	$custom_html         = wp_kses_post( $input['custom_html'] );

	if ( empty( $background_color ) ) {
		$background_color = $defaults['background_color'];
	}

	if ( empty( $text_color ) ) {
		$text_color = $defaults['text_color'];
	}

	if ( ! array_key_exists( $layout_width, $layout_widths ) ) {
		$layout_width = $defaults['layout_width'];
	}

	if ( ! array_key_exists( $template_style, $template_styles ) ) {
		$template_style = $defaults['template_style'];
	}

	$schedule_enabled = empty( $input['schedule_enabled'] ) ? 0 : 1;
	$schedule_start   = sitepause_maintenance_sanitize_schedule_datetime( $input['schedule_start'] );
	$schedule_end     = sitepause_maintenance_sanitize_schedule_datetime( $input['schedule_end'] );

	if ( $schedule_enabled ) {
		$start_timestamp = sitepause_maintenance_schedule_datetime_to_timestamp( $schedule_start );
		$end_timestamp   = sitepause_maintenance_schedule_datetime_to_timestamp( $schedule_end );

		if ( null === $start_timestamp || null === $end_timestamp ) {
			$schedule_enabled = 0;
			add_settings_error(
				SITEPAUSE_MAINTENANCE_OPTION,
				'sitepause_maintenance_schedule_missing_dates',
				__( 'Scheduled maintenance was not enabled. Please choose both a valid start date/time and end date/time.', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
				'error'
			);
		} elseif ( $end_timestamp <= $start_timestamp ) {
			$schedule_enabled = 0;
			add_settings_error(
				SITEPAUSE_MAINTENANCE_OPTION,
				'sitepause_maintenance_schedule_invalid_range',
				__( 'Scheduled maintenance was not enabled. The end date/time must be after the start date/time.', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
				'error'
			);
		}
	}

	return array(
		'enabled'            => empty( $input['enabled'] ) ? 0 : 1,
		'schedule_enabled'   => $schedule_enabled,
		'schedule_start'     => $schedule_start,
		'schedule_end'       => $schedule_end,
		'title'              => $title,
		'message'            => $message,
		'contact_email'      => sanitize_email( $input['contact_email'] ),
		'contact_phone'      => sanitize_text_field( $input['contact_phone'] ),
		'facebook_url'       => esc_url_raw( $input['facebook_url'] ),
		'instagram_url'      => esc_url_raw( $input['instagram_url'] ),
		'linkedin_url'       => esc_url_raw( $input['linkedin_url'] ),
		'footer_text'        => sanitize_text_field( $input['footer_text'] ),
		'logo_attachment_id' => absint( $input['logo_attachment_id'] ),
		'background_color'   => $background_color,
		'text_color'         => $text_color,
		'layout_width'       => $layout_width,
		'template_style'     => $template_style,
		'countdown_enabled'  => $countdown_enabled,
		'countdown_target'   => $countdown_target,
		'custom_html_enabled' => $custom_html_enabled,
		'custom_html'        => $custom_html,
		'bypass_roles'       => $bypass_roles,
		'ip_whitelist'       => $ip_whitelist,
	);
}

/**
 * Registers the settings used by the admin page.
 *
 * @return void
 */
function sitepause_maintenance_register_settings() {
	register_setting(
		'sitepause_maintenance_settings',
		SITEPAUSE_MAINTENANCE_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => array( 'SitePause_Maintenance_Settings', 'sanitize_options' ),
			'default'           => sitepause_maintenance_default_options(),
		)
	);

	$sections = array(
		'sitepause_maintenance_general_section' => array(
			'title'    => esc_html__( 'General', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			'callback' => 'sitepause_maintenance_render_general_intro',
		),
		'sitepause_maintenance_content_section' => array(
			'title'    => esc_html__( 'Content', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			'callback' => 'sitepause_maintenance_render_content_intro',
		),
		'sitepause_maintenance_custom_html_section' => array(
			'title'    => esc_html__( 'Custom HTML Override Mode', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			'callback' => 'sitepause_maintenance_render_custom_html_intro',
		),
		'sitepause_maintenance_contact_section' => array(
			'title'    => esc_html__( 'Contact Details', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			'callback' => 'sitepause_maintenance_render_contact_intro',
		),
		'sitepause_maintenance_templates_section' => array(
			'title'    => esc_html__( 'Templates', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			'callback' => 'sitepause_maintenance_render_templates_intro',
		),
		'sitepause_maintenance_design_section'  => array(
			'title'    => esc_html__( 'Branding & Design', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			'callback' => 'sitepause_maintenance_render_design_intro',
		),
		'sitepause_maintenance_bypass_section'  => array(
			'title'    => esc_html__( 'Bypass Rules', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			'callback' => 'sitepause_maintenance_render_bypass_intro',
		),
	);

	foreach ( $sections as $section_id => $section ) {
		add_settings_section(
			$section_id,
			$section['title'],
			$section['callback'],
			'sitepause-custom-html-offline-maintenance-mode-activator'
		);
	}

	add_settings_field(
		'sitepause_maintenance_enabled',
		esc_html__( 'Status', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause_maintenance_render_enabled_field',
		'sitepause-custom-html-offline-maintenance-mode-activator',
		'sitepause_maintenance_general_section'
	);

	add_settings_field(
		'sitepause_maintenance_schedule_enabled',
		esc_html__( 'Scheduled Maintenance', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause_maintenance_render_schedule_enabled_field',
		'sitepause-custom-html-offline-maintenance-mode-activator',
		'sitepause_maintenance_general_section'
	);

	add_settings_field(
		'sitepause_maintenance_schedule_start',
		esc_html__( 'Start Date/Time', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause_maintenance_render_schedule_start_field',
		'sitepause-custom-html-offline-maintenance-mode-activator',
		'sitepause_maintenance_general_section'
	);

	add_settings_field(
		'sitepause_maintenance_schedule_end',
		esc_html__( 'End Date/Time', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause_maintenance_render_schedule_end_field',
		'sitepause-custom-html-offline-maintenance-mode-activator',
		'sitepause_maintenance_general_section'
	);

	add_settings_field(
		'sitepause_maintenance_title',
		esc_html__( 'Page Title', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause_maintenance_render_title_field',
		'sitepause-custom-html-offline-maintenance-mode-activator',
		'sitepause_maintenance_content_section'
	);

	add_settings_field(
		'sitepause_maintenance_message',
		esc_html__( 'Message', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause_maintenance_render_message_field',
		'sitepause-custom-html-offline-maintenance-mode-activator',
		'sitepause_maintenance_content_section'
	);

	add_settings_field(
		'sitepause_maintenance_custom_html_enabled',
		esc_html__( 'Custom HTML Content', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause_maintenance_render_custom_html_enabled_field',
		'sitepause-custom-html-offline-maintenance-mode-activator',
		'sitepause_maintenance_custom_html_section'
	);

	add_settings_field(
		'sitepause_maintenance_custom_html',
		esc_html__( 'Custom HTML', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause_maintenance_render_custom_html_field',
		'sitepause-custom-html-offline-maintenance-mode-activator',
		'sitepause_maintenance_custom_html_section'
	);

	add_settings_field(
		'sitepause_maintenance_countdown_enabled',
		esc_html__( 'Countdown Timer', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause_maintenance_render_countdown_enabled_field',
		'sitepause-custom-html-offline-maintenance-mode-activator',
		'sitepause_maintenance_content_section'
	);

	add_settings_field(
		'sitepause_maintenance_countdown_target',
		esc_html__( 'Countdown Target Date/Time', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause_maintenance_render_countdown_target_field',
		'sitepause-custom-html-offline-maintenance-mode-activator',
		'sitepause_maintenance_content_section'
	);

	add_settings_field(
		'sitepause_maintenance_contact_email',
		esc_html__( 'Contact Email', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause_maintenance_render_contact_email_field',
		'sitepause-custom-html-offline-maintenance-mode-activator',
		'sitepause_maintenance_contact_section'
	);

	add_settings_field(
		'sitepause_maintenance_contact_phone',
		esc_html__( 'Contact Phone', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause_maintenance_render_contact_phone_field',
		'sitepause-custom-html-offline-maintenance-mode-activator',
		'sitepause_maintenance_contact_section'
	);

	add_settings_field(
		'sitepause_maintenance_facebook_url',
		esc_html__( 'Facebook URL', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause_maintenance_render_facebook_url_field',
		'sitepause-custom-html-offline-maintenance-mode-activator',
		'sitepause_maintenance_contact_section'
	);

	add_settings_field(
		'sitepause_maintenance_instagram_url',
		esc_html__( 'Instagram URL', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause_maintenance_render_instagram_url_field',
		'sitepause-custom-html-offline-maintenance-mode-activator',
		'sitepause_maintenance_contact_section'
	);

	add_settings_field(
		'sitepause_maintenance_linkedin_url',
		esc_html__( 'LinkedIn URL', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause_maintenance_render_linkedin_url_field',
		'sitepause-custom-html-offline-maintenance-mode-activator',
		'sitepause_maintenance_contact_section'
	);

	add_settings_field(
		'sitepause_maintenance_footer_text',
		esc_html__( 'Footer Text', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause_maintenance_render_footer_text_field',
		'sitepause-custom-html-offline-maintenance-mode-activator',
		'sitepause_maintenance_contact_section'
	);

	add_settings_field(
		'sitepause_maintenance_template_style',
		esc_html__( 'Design Preset', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause_maintenance_render_template_style_field',
		'sitepause-custom-html-offline-maintenance-mode-activator',
		'sitepause_maintenance_templates_section'
	);

	add_settings_field(
		'sitepause_maintenance_logo',
		esc_html__( 'Logo', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause_maintenance_render_logo_field',
		'sitepause-custom-html-offline-maintenance-mode-activator',
		'sitepause_maintenance_design_section'
	);

	add_settings_field(
		'sitepause_maintenance_background_color',
		esc_html__( 'Background Color', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause_maintenance_render_background_color_field',
		'sitepause-custom-html-offline-maintenance-mode-activator',
		'sitepause_maintenance_design_section'
	);

	add_settings_field(
		'sitepause_maintenance_text_color',
		esc_html__( 'Text Color', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause_maintenance_render_text_color_field',
		'sitepause-custom-html-offline-maintenance-mode-activator',
		'sitepause_maintenance_design_section'
	);

	add_settings_field(
		'sitepause_maintenance_layout_width',
		esc_html__( 'Layout Width', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause_maintenance_render_layout_width_field',
		'sitepause-custom-html-offline-maintenance-mode-activator',
		'sitepause_maintenance_design_section'
	);

	add_settings_field(
		'sitepause_maintenance_bypass_roles',
		esc_html__( 'Role Bypass', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause_maintenance_render_bypass_roles_field',
		'sitepause-custom-html-offline-maintenance-mode-activator',
		'sitepause_maintenance_bypass_section'
	);

	add_settings_field(
		'sitepause_maintenance_ip_whitelist',
		esc_html__( 'IP Whitelist', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause_maintenance_render_ip_whitelist_field',
		'sitepause-custom-html-offline-maintenance-mode-activator',
		'sitepause_maintenance_bypass_section'
	);
}
/**
 * Loads admin assets on this plugin's settings page.
 *
 * @param string $hook_suffix Current admin page hook suffix.
 * @return void
 */
function sitepause_maintenance_enqueue_admin_assets( $hook_suffix ) {
	if ( 'settings_page_sitepause-custom-html-offline-maintenance-mode-activator' !== $hook_suffix ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );
	wp_add_inline_style(
		'wp-color-picker',
		'.sitepause-maintenance-admin-page { max-width: 980px; }
		.sitepause-maintenance-status { display: inline-block; margin: 8px 0 12px; padding: 6px 12px; border-radius: 3px; font-weight: 600; }
		.sitepause-maintenance-status.is-on { background: #fcf0f1; color: #8a2424; }
		.sitepause-maintenance-status.is-off { background: #edfaef; color: #1f6f2d; }
		.sitepause-maintenance-status.is-warning { background: #fcf9e8; color: #7a4b00; border: 1px solid #f0c33c; }
		.sitepause-maintenance-custom-html-warning ul { margin: 8px 0 0 20px; list-style: disc; }
		.sitepause-maintenance-admin-page form { margin-top: 16px; }
		.sitepause-maintenance-tabs { margin-top: 18px; }
		.sitepause-maintenance-tab-panel { display: none; padding-top: 12px; }
		.sitepause-maintenance-tab-panel.is-active { display: block; }
		.sitepause-maintenance-tab-panel .sitepause-maintenance-actions-panel { margin-top: 12px; }
		.sitepause-maintenance-submit { margin-top: 18px; padding-top: 14px; border-top: 1px solid #dcdcde; }
		.sitepause-maintenance-admin-credit { margin: 18px 0 0; color: #646970; font-size: 12px; }
		.sitepause-maintenance-admin-credit a { color: inherit; }
		.sitepause-maintenance-import-export-panel { max-width: 760px; margin-top: 18px; padding: 16px 18px; background: #fff; border: 1px solid #dcdcde; }
		.sitepause-maintenance-import-export-panel h2 { margin-top: 0; padding-top: 0; border-top: 0; }
		.sitepause-maintenance-import-export-panel textarea { min-height: 180px; }
		.sitepause-maintenance-admin-page h2 { margin-top: 28px; padding-top: 18px; border-top: 1px solid #dcdcde; }
		.sitepause-maintenance-admin-page h2:first-of-type { margin-top: 18px; }
		.sitepause-maintenance-admin-page .form-table { margin-top: 8px; background: #fff; border: 1px solid #dcdcde; }
		.sitepause-maintenance-admin-page .form-table th { padding-left: 18px; }
		.sitepause-maintenance-admin-page .form-table td { padding-right: 18px; }
		.sitepause-maintenance-role-list { margin: 0; }
		.sitepause-maintenance-role-list li { margin: 0 0 8px; }
		.sitepause-maintenance-template-choices { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; max-width: 720px; align-items: stretch; }
		.sitepause-maintenance-template-card { display: flex; flex-direction: column; min-width: 0; height: 100%; margin: 0; padding: 12px; border: 1px solid #dcdcde; border-radius: 6px; background: #fff; cursor: pointer; box-sizing: border-box; }
		.sitepause-maintenance-template-card.is-selected { border-color: #2271b1; box-shadow: 0 0 0 1px #2271b1; }
		.sitepause-maintenance-template-card input { margin-right: 6px; }
		.sitepause-maintenance-template-name { display: inline-block; margin-bottom: 8px; font-weight: 600; }
		.sitepause-maintenance-template-card p { margin: 8px 0 0; line-height: 1.45; }
		.sitepause-maintenance-template-preview { width: 100%; height: 92px; margin-bottom: 10px; padding: 12px; border: 1px solid #dcdcde; border-radius: 4px; background: #f6f7f7; box-sizing: border-box; }
		.sitepause-maintenance-preview-logo { width: 28px; height: 10px; margin: 0 auto 10px; border-radius: 10px; background: #8c8f94; }
		.sitepause-maintenance-preview-title { width: 70%; height: 10px; margin: 0 auto 8px; border-radius: 10px; background: #1d2327; }
		.sitepause-maintenance-preview-line { width: 88%; height: 7px; margin: 0 auto 6px; border-radius: 10px; background: #a7aaad; }
		.sitepause-maintenance-preview-line.is-short { width: 54%; }
		.sitepause-maintenance-preview-buttons { display: flex; justify-content: center; gap: 5px; margin-top: 10px; }
		.sitepause-maintenance-preview-buttons span { width: 32px; height: 9px; border-radius: 10px; background: #72aee6; }
		.sitepause-maintenance-template-preview.is-classic { border-top: 4px solid #2271b1; background: linear-gradient(180deg, #ffffff, #f6f7f7); }
		.sitepause-maintenance-template-preview.is-center-card { padding: 10px; border-color: #1d2327; background: linear-gradient(135deg, #1d2327, #3858e9); }
		.sitepause-maintenance-preview-card { height: 70px; padding: 11px; border-radius: 7px; background: #fff; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.14); box-sizing: border-box; }
		.sitepause-maintenance-template-preview.is-minimal { border-color: #c3c4c7; background: #fff; }
		.sitepause-maintenance-template-preview.is-bold-panel { border-color: #1d2327; background: #1d2327; }
		.sitepause-maintenance-template-preview.is-bold-panel .sitepause-maintenance-preview-logo,
		.sitepause-maintenance-template-preview.is-bold-panel .sitepause-maintenance-preview-title { background: #f0c33c; }
		.sitepause-maintenance-template-preview.is-bold-panel .sitepause-maintenance-preview-line { background: rgba(255, 255, 255, 0.75); }
		.sitepause-maintenance-template-preview.is-split-screen { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; background: linear-gradient(90deg, #fff 0 50%, #f0f6fc 50% 100%); }
		.sitepause-maintenance-template-preview.is-split-screen .sitepause-maintenance-preview-buttons { grid-column: 2; grid-row: 1 / 5; flex-direction: column; align-items: center; margin-top: 8px; }
		.sitepause-maintenance-template-preview.is-center-card .sitepause-maintenance-preview-title { background: #111827; }
		.sitepause-maintenance-template-preview.is-center-card .sitepause-maintenance-preview-line { background: #9ca3af; }
		.sitepause-maintenance-template-preview.is-minimal .sitepause-maintenance-preview-logo { display: none; }
		.sitepause-maintenance-template-preview.is-minimal .sitepause-maintenance-preview-title,
		.sitepause-maintenance-template-preview.is-minimal .sitepause-maintenance-preview-line { margin-left: 0; margin-right: 0; }
		@media (max-width: 960px) { .sitepause-maintenance-template-choices { grid-template-columns: 1fr; max-width: 360px; } }
		.sitepause-maintenance-actions-panel { margin-top: 24px; padding: 16px 18px; background: #fff; border: 1px solid #dcdcde; }
		.sitepause-maintenance-actions-panel h2 { margin-top: 0; padding-top: 0; border-top: 0; }'
	);

	$script_data = array(
		'title'        => __( 'Choose Logo', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'buttonText'   => __( 'Use this logo', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'confirmReset' => __( 'Are you sure you want to reset all SitePause settings to defaults? This will not be saved until you click Save Settings.', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'invalidRange' => __( 'End Date/Time must be after Start Date/Time.', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'defaults'     => sitepause_maintenance_default_options(),
	);

	wp_add_inline_script(
		'wp-color-picker',
		'jQuery(function($) {
			$(".sitepause-maintenance-color-field").wpColorPicker();

			var mediaFrame;
			var strings = ' . wp_json_encode( $script_data ) . ';

			$("#sitepause-maintenance-logo-upload").on("click", function(event) {
				event.preventDefault();

				if (mediaFrame) {
					mediaFrame.open();
					return;
				}

				mediaFrame = wp.media({
					title: strings.title,
					button: {
						text: strings.buttonText
					},
					multiple: false,
					library: {
						type: "image"
					}
				});

				mediaFrame.on("select", function() {
					var attachment = mediaFrame.state().get("selection").first().toJSON();
					var previewUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

					$("#sitepause-maintenance-logo-id").val(attachment.id);
					$("#sitepause-maintenance-logo-preview").attr("src", previewUrl).show();
					$("#sitepause-maintenance-logo-remove").show();
				});

				mediaFrame.open();
			});

			$("#sitepause-maintenance-logo-remove").on("click", function(event) {
				event.preventDefault();

				$("#sitepause-maintenance-logo-id").val("0");
				$("#sitepause-maintenance-logo-preview").attr("src", "").hide();
				$(this).hide();
			});

			var resetButton = document.getElementById("sitepause-maintenance-reset-fields");

			if (resetButton) {
				resetButton.addEventListener("click", function() {
					if (!window.confirm(strings.confirmReset)) {
						return;
					}

					var defaults = strings.defaults;
					var fields = {
						"sitepause-maintenance-title": defaults.title,
						"sitepause-maintenance-message": defaults.message,
						"sitepause-maintenance-custom-html": "",
						"sitepause-maintenance-schedule-start": "",
						"sitepause-maintenance-schedule-end": "",
						"sitepause-maintenance-countdown-target": "",
						"sitepause-maintenance-contact-email": "",
						"sitepause-maintenance-contact-phone": "",
						"sitepause-maintenance-facebook-url": "",
						"sitepause-maintenance-instagram-url": "",
						"sitepause-maintenance-linkedin-url": "",
						"sitepause-maintenance-footer-text": "",
						"sitepause-maintenance-ip-whitelist": "",
						"sitepause-maintenance-background-color": defaults.background_color,
						"sitepause-maintenance-text-color": defaults.text_color,
						"sitepause-maintenance-layout-width": defaults.layout_width
					};

					var enabledField = document.getElementById("sitepause-maintenance-enabled");
					var scheduleEnabledField = document.getElementById("sitepause-maintenance-schedule-enabled");
					var countdownEnabledField = document.getElementById("sitepause-maintenance-countdown-enabled");
					var customHtmlEnabledField = document.getElementById("sitepause-maintenance-custom-html-enabled");
					var logoIdField = document.getElementById("sitepause-maintenance-logo-id");
					var logoPreview = document.getElementById("sitepause-maintenance-logo-preview");
					var logoRemoveButton = document.getElementById("sitepause-maintenance-logo-remove");
					var bypassRoleFields = document.querySelectorAll(".sitepause-maintenance-bypass-role");
					var templateStyleFields = document.querySelectorAll(".sitepause-maintenance-template-style");

					if (enabledField) {
						enabledField.checked = false;
					}

					if (scheduleEnabledField) {
						scheduleEnabledField.checked = false;
					}

					if (countdownEnabledField) {
						countdownEnabledField.checked = false;
					}

					if (customHtmlEnabledField) {
						customHtmlEnabledField.checked = false;
					}

					Object.keys(fields).forEach(function(fieldId) {
						var field = document.getElementById(fieldId);

						if (!field) {
							return;
						}

						field.value = fields[fieldId];
						field.dispatchEvent(new Event("change", { bubbles: true }));
					});

					if (logoIdField) {
						logoIdField.value = "";
					}

					if (logoPreview) {
						logoPreview.setAttribute("src", "");
						logoPreview.style.display = "none";
					}

					if (logoRemoveButton) {
						logoRemoveButton.style.display = "none";
					}

					bypassRoleFields.forEach(function(field) {
						field.checked = "administrator" === field.value;
					});

					templateStyleFields.forEach(function(field) {
						field.checked = defaults.template_style === field.value;
						field.closest(".sitepause-maintenance-template-card").classList.toggle("is-selected", field.checked);
					});
				});
			}

			$(".sitepause-maintenance-template-style").on("change", function() {
				$(".sitepause-maintenance-template-card").removeClass("is-selected");
				$(this).closest(".sitepause-maintenance-template-card").addClass("is-selected");
			});

			var scheduleEnabled = document.getElementById("sitepause-maintenance-schedule-enabled");
			var scheduleStart = document.getElementById("sitepause-maintenance-schedule-start");
			var scheduleEnd = document.getElementById("sitepause-maintenance-schedule-end");

			function updateScheduleRequiredFields() {
				var isRequired = scheduleEnabled && scheduleEnabled.checked;

				if (scheduleStart) {
					scheduleStart.required = isRequired;
				}

				if (scheduleEnd) {
					scheduleEnd.required = isRequired;
				}

				validateScheduleRange();
			}

			function validateScheduleRange() {
				if (!scheduleStart || !scheduleEnd) {
					return;
				}

				scheduleEnd.setCustomValidity("");

				if (!scheduleEnabled || !scheduleEnabled.checked || !scheduleStart.value || !scheduleEnd.value) {
					return;
				}

				if (scheduleEnd.value <= scheduleStart.value) {
					scheduleEnd.setCustomValidity(strings.invalidRange);
				}
			}

			if (scheduleEnabled) {
				scheduleEnabled.addEventListener("change", updateScheduleRequiredFields);
				updateScheduleRequiredFields();
			}

			if (scheduleStart) {
				scheduleStart.addEventListener("change", validateScheduleRange);
			}

			if (scheduleEnd) {
				scheduleEnd.addEventListener("change", validateScheduleRange);
			}

			var tabLinks = document.querySelectorAll(".sitepause-maintenance-tab-link");
			var tabPanels = document.querySelectorAll(".sitepause-maintenance-tab-panel");
			var activeTabKey = "sitepauseMaintenanceActiveTab";

			function activateTab(tabId) {
				var matchedPanel = document.getElementById(tabId);

				if (!matchedPanel) {
					return;
				}

				tabLinks.forEach(function(link) {
					var isActive = link.getAttribute("data-tab") === tabId;

					link.classList.toggle("nav-tab-active", isActive);
					link.setAttribute("aria-selected", isActive ? "true" : "false");
				});

				tabPanels.forEach(function(panel) {
					panel.classList.toggle("is-active", panel.id === tabId);
				});

				try {
					window.localStorage.setItem(activeTabKey, tabId);
				} catch (error) {}
			}

			tabLinks.forEach(function(link) {
				link.addEventListener("click", function(event) {
					event.preventDefault();
					activateTab(link.getAttribute("data-tab"));
				});
			});

			if (tabLinks.length) {
				var savedTab = "";

				try {
					savedTab = window.localStorage.getItem(activeTabKey) || "";
				} catch (error) {}

				activateTab(savedTab || tabLinks[0].getAttribute("data-tab"));
			}
		});'
	);
}
/**
 * Adds the plugin settings page under Settings.
 *
 * @return void
 */
function sitepause_maintenance_add_admin_menu() {
	add_options_page(
		esc_html__( 'SitePause - Custom HTML Offline & Maintenance Mode Activator', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		esc_html__( 'SitePause', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'manage_options',
		'sitepause-custom-html-offline-maintenance-mode-activator',
		'sitepause_maintenance_render_settings_page'
	);
}

/**
 * Gets the settings page URL.
 *
 * @param array $args Optional query args.
 * @return string
 */
function sitepause_maintenance_get_settings_url( $args = array() ) {
	$url = admin_url( 'options-general.php?page=sitepause-custom-html-offline-maintenance-mode-activator' );

	if ( ! empty( $args ) ) {
		$url = add_query_arg( $args, $url );
	}

	return $url;
}

/**
 * Redirects back to the settings page after import actions.
 *
 * @param string $result Import result key.
 * @return void
 */
function sitepause_maintenance_redirect_import_result( $result ) {
	wp_safe_redirect(
		sitepause_maintenance_get_settings_url(
			array(
				'sitepause_maintenance_import' => sanitize_key( $result ),
			)
		)
	);
	exit;
}

/**
 * Handles JSON settings export.
 *
 * @return void
 */
function sitepause_maintenance_handle_export_settings() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to export these settings.', 'sitepause-custom-html-offline-maintenance-mode-activator' ) );
	}

	check_admin_referer( 'sitepause_maintenance_export_settings' );

	$export = array(
		'plugin'      => 'sitepause-custom-html-offline-maintenance-mode-activator',
		'version'     => SITEPAUSE_MAINTENANCE_VERSION,
		'exported_at' => current_datetime()->format( 'Y-m-d H:i:s' ),
		'settings'    => sitepause_maintenance_get_options(),
	);

	nocache_headers();
	header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
	header( 'Content-Disposition: attachment; filename=sitepause-maintenance-settings.json' );
	header( 'X-Content-Type-Options: nosniff' );

	echo wp_json_encode( $export, JSON_PRETTY_PRINT );
	exit;
}

/**
 * Extracts a settings array from imported JSON data.
 *
 * @param mixed $decoded Decoded JSON value.
 * @return array|null
 */
function sitepause_maintenance_get_import_settings_from_json( $decoded ) {
	if ( ! is_array( $decoded ) ) {
		return null;
	}

	if ( isset( $decoded['plugin'] ) && 'sitepause-custom-html-offline-maintenance-mode-activator' !== sanitize_key( $decoded['plugin'] ) ) {
		return null;
	}

	if ( isset( $decoded['settings'] ) && is_array( $decoded['settings'] ) ) {
		return $decoded['settings'];
	}

	$defaults = sitepause_maintenance_default_options();

	foreach ( array_keys( $defaults ) as $option_key ) {
		if ( array_key_exists( $option_key, $decoded ) ) {
			return $decoded;
		}
	}

	return null;
}

/**
 * Handles JSON settings import.
 *
 * @return void
 */
function sitepause_maintenance_handle_import_settings() {
	if ( ! current_user_can( 'manage_options' ) ) {
		sitepause_maintenance_redirect_import_result( 'permission' );
	}

	if ( ! isset( $_POST['sitepause_maintenance_import_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sitepause_maintenance_import_nonce'] ) ), 'sitepause_maintenance_import_settings' ) ) {
		sitepause_maintenance_redirect_import_result( 'nonce' );
	}

	$json = isset( $_POST['sitepause_maintenance_import_json'] ) ? wp_unslash( $_POST['sitepause_maintenance_import_json'] ) : '';
	$json = trim( (string) $json );

	if ( '' === $json ) {
		sitepause_maintenance_redirect_import_result( 'empty' );
	}

	$decoded = json_decode( $json, true );

	if ( JSON_ERROR_NONE !== json_last_error() ) {
		sitepause_maintenance_redirect_import_result( 'invalid_json' );
	}

	$imported_settings = sitepause_maintenance_get_import_settings_from_json( $decoded );

	if ( empty( $imported_settings ) || ! is_array( $imported_settings ) ) {
		sitepause_maintenance_redirect_import_result( 'no_settings' );
	}

	$allowed_settings = array_intersect_key( $imported_settings, sitepause_maintenance_default_options() );

	if ( empty( $allowed_settings ) ) {
		sitepause_maintenance_redirect_import_result( 'no_settings' );
	}

	$sanitized_settings = sitepause_maintenance_sanitize_options( $allowed_settings );

	update_option( SITEPAUSE_MAINTENANCE_OPTION, $sanitized_settings );
	sitepause_maintenance_redirect_import_result( 'success' );
}

/**
 * Replaces the generic Settings API success flag with a plugin-specific flag.
 *
 * WordPress uses settings-updated=true to show the default "Settings saved."
 * notice. This keeps the normal save flow but lets this plugin show one custom
 * notice instead of two notices.
 *
 * @param string $location Redirect URL.
 * @return string
 */
function sitepause_maintenance_filter_settings_redirect( $location ) {
	if ( ! isset( $_POST['option_page'] ) ) {
		return $location;
	}

	$option_page = sanitize_text_field( wp_unslash( $_POST['option_page'] ) );

	if ( 'sitepause_maintenance_settings' !== $option_page ) {
		return $location;
	}

	$location = remove_query_arg( 'settings-updated', $location );

	return add_query_arg( 'sitepause_maintenance_saved', '1', $location );
}
/**
 * Renders admin success notices for this settings page.
 *
 * @return void
 */
function sitepause_maintenance_render_admin_notices() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! isset( $_GET['page'] ) || 'sitepause-custom-html-offline-maintenance-mode-activator' !== sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
		return;
	}

	$message = '';

	if ( isset( $_GET['sitepause_maintenance_saved'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['sitepause_maintenance_saved'] ) ) ) {
		$message = __( 'SitePause settings saved.', 'sitepause-custom-html-offline-maintenance-mode-activator' );
	}

	if ( isset( $_GET['sitepause_maintenance_import'] ) ) {
		$import_result = sanitize_key( wp_unslash( $_GET['sitepause_maintenance_import'] ) );
		$messages      = array(
			'success'      => __( 'Settings imported successfully.', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			'invalid_json' => __( 'Invalid JSON. Please check the pasted settings and try again.', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			'empty'        => __( 'No import data was provided.', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			'no_settings'  => __( 'No valid settings found in the imported JSON.', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			'permission'   => __( 'Import failed because your account does not have permission.', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			'nonce'        => __( 'Import failed because the security check did not pass. Please try again.', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		);

		if ( isset( $messages[ $import_result ] ) ) {
			$message = $messages[ $import_result ];
		}
	}

	if ( '' === $message ) {
		return;
	}

	$notice_class = ( isset( $import_result ) && 'success' !== $import_result ) ? 'notice notice-error is-dismissible' : 'notice notice-success is-dismissible';
	?>
	<div class="<?php echo esc_attr( $notice_class ); ?>">
		<p><?php echo esc_html( $message ); ?></p>
	</div>
	<?php
}

/**
 * Adds a red maintenance status item to the WordPress admin bar.
 *
 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance.
 * @return void
 */
function sitepause_maintenance_add_admin_bar_status( $wp_admin_bar ) {
	if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) || ! sitepause_maintenance_is_enabled() ) {
		return;
	}

	$wp_admin_bar->add_node(
		array(
			'id'    => 'sitepause-maintenance-status',
			'title' => esc_html__( 'Maintenance Mode ON', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			'href'  => admin_url( 'options-general.php?page=sitepause-custom-html-offline-maintenance-mode-activator' ),
			'meta'  => array(
				'class' => 'sitepause-maintenance-admin-bar-status',
			),
		)
	);
}

/**
 * Prints admin bar status styles on admin and frontend screens.
 *
 * @return void
 */
function sitepause_maintenance_print_admin_bar_status_styles() {
	if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) || ! sitepause_maintenance_is_enabled() ) {
		return;
	}
	?>
	<style>
		#wpadminbar #wp-admin-bar-sitepause-maintenance-status > .ab-item {
			background: #b32d2e;
			color: #fff;
			font-weight: 700;
		}

		#wpadminbar #wp-admin-bar-sitepause-maintenance-status > .ab-item:hover,
		#wpadminbar #wp-admin-bar-sitepause-maintenance-status > .ab-item:focus {
			background: #8a2424;
			color: #fff;
		}
	</style>
	<?php
}

/**
 * Renders the General section description.
 *
 * @return void
 */
function sitepause_maintenance_render_general_intro() {
	echo '<p>' . esc_html__( 'Turn maintenance mode on immediately, or schedule it to turn on automatically between a start and end time. The manual toggle works immediately and can keep maintenance mode on even outside the scheduled window.', 'sitepause-custom-html-offline-maintenance-mode-activator' ) . '</p>';
}

/**
 * Renders the Content section description.
 *
 * @return void
 */
function sitepause_maintenance_render_content_intro() {
	echo '<p>' . esc_html__( 'Control the main text visitors see on the maintenance page. The countdown timer is optional and does not turn maintenance mode off automatically.', 'sitepause-custom-html-offline-maintenance-mode-activator' ) . '</p>';
}

/**
 * Renders the Custom HTML section description.
 *
 * @return void
 */
function sitepause_maintenance_render_custom_html_intro() {
	?>
	<div class="notice notice-warning inline sitepause-maintenance-custom-html-warning">
		<p><strong><?php esc_html_e( 'Custom HTML Override Mode', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></strong></p>
		<p><?php esc_html_e( 'When enabled and the custom HTML field is not empty, ONLY the custom HTML below will be displayed on the maintenance page.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></p>
		<p><?php esc_html_e( 'The following built-in content will be hidden:', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></p>
		<ul>
			<li><?php esc_html_e( 'Page title', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></li>
			<li><?php esc_html_e( 'Message', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></li>
			<li><?php esc_html_e( 'Logo', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></li>
			<li><?php esc_html_e( 'Countdown timer', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></li>
			<li><?php esc_html_e( 'Contact details', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></li>
			<li><?php esc_html_e( 'Social links', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></li>
			<li><?php esc_html_e( 'Footer text', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></li>
			<li><?php esc_html_e( 'Template preset content', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></li>
		</ul>
		<p><?php esc_html_e( 'Your custom HTML replaces the built-in maintenance page content.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></p>
	</div>
	<?php
}

/**
 * Renders the Contact Details section description.
 *
 * @return void
 */
function sitepause_maintenance_render_contact_intro() {
	echo '<p>' . esc_html__( 'Add optional ways for visitors to contact you while the site is offline. Empty fields are hidden automatically.', 'sitepause-custom-html-offline-maintenance-mode-activator' ) . '</p>';
}

/**
 * Renders the Templates section description.
 *
 * @return void
 */
function sitepause_maintenance_render_templates_intro() {
	echo '<p>' . esc_html__( 'Choose a built-in design style. Presets use your saved title, message, logo, countdown, contact details, and footer text.', 'sitepause-custom-html-offline-maintenance-mode-activator' ) . '</p>';
}

/**
 * Renders the Branding & Design section description.
 *
 * @return void
 */
function sitepause_maintenance_render_design_intro() {
	echo '<p>' . esc_html__( 'Add simple branding and adjust the basic look of the maintenance page.', 'sitepause-custom-html-offline-maintenance-mode-activator' ) . '</p>';
}

/**
 * Renders the Bypass Rules section description.
 *
 * @return void
 */
function sitepause_maintenance_render_bypass_intro() {
	echo '<p>' . esc_html__( 'Choose which logged-in user roles can view the normal website while maintenance mode is active. Logged-out visitors still see the maintenance page.', 'sitepause-custom-html-offline-maintenance-mode-activator' ) . '</p>';
}

/**
 * Renders the enable checkbox field.
 *
 * @return void
 */
function sitepause_maintenance_render_enabled_field() {
	$options = sitepause_maintenance_get_options();
	?>
	<label for="sitepause-maintenance-enabled">
		<input
			type="checkbox"
			id="sitepause-maintenance-enabled"
			name="<?php echo esc_attr( SITEPAUSE_MAINTENANCE_OPTION ); ?>[enabled]"
			value="1"
			<?php checked( 1, (int) $options['enabled'] ); ?>
		/>
		<?php esc_html_e( 'Enable maintenance mode', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
	</label>
	<p class="description">
		<?php esc_html_e( 'Leave this unchecked while you are editing or previewing the page. Check it only when public visitors should see maintenance mode.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
	</p>
	<?php
}

/**
 * Renders the maintenance page title field.
 *
 * @return void
 */
function sitepause_maintenance_render_title_field() {
	$options = sitepause_maintenance_get_options();
	?>
	<input
		type="text"
		id="sitepause-maintenance-title"
		class="regular-text"
		name="<?php echo esc_attr( SITEPAUSE_MAINTENANCE_OPTION ); ?>[title]"
		value="<?php echo esc_attr( $options['title'] ); ?>"
	/>
	<p class="description">
		<?php esc_html_e( 'This appears as the main heading on the maintenance page.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
	</p>
	<?php
}

/**
 * Renders the maintenance page message field.
 *
 * @return void
 */
function sitepause_maintenance_render_message_field() {
	$options = sitepause_maintenance_get_options();
	?>
	<textarea
		id="sitepause-maintenance-message"
		class="large-text"
		rows="5"
		name="<?php echo esc_attr( SITEPAUSE_MAINTENANCE_OPTION ); ?>[message]"
	><?php echo esc_textarea( $options['message'] ); ?></textarea>
	<p class="description">
		<?php esc_html_e( 'Use {site name} to automatically show the WordPress site name.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
	</p>
	<?php
}

/**
 * Renders a text input for a contact setting.
 *
 * @param string $field_id Field ID suffix.
 * @param string $option_key Option array key.
 * @param string $type Input type.
 * @param string $description Help text.
 * @return void
 */
function sitepause_maintenance_render_text_input( $field_id, $option_key, $type, $description ) {
	$options = sitepause_maintenance_get_options();
	?>
	<input
		type="<?php echo esc_attr( $type ); ?>"
		id="<?php echo esc_attr( 'sitepause-maintenance-' . $field_id ); ?>"
		class="regular-text"
		name="<?php echo esc_attr( SITEPAUSE_MAINTENANCE_OPTION ); ?>[<?php echo esc_attr( $option_key ); ?>]"
		value="<?php echo esc_attr( $options[ $option_key ] ); ?>"
	/>
	<?php if ( '' !== $description ) : ?>
		<p class="description"><?php echo esc_html( $description ); ?></p>
	<?php endif; ?>
	<?php
}

/**
 * Renders the contact email field.
 *
 * @return void
 */
function sitepause_maintenance_render_contact_email_field() {
	sitepause_maintenance_render_text_input(
		'contact-email',
		'contact_email',
		'email',
		__( 'Displayed as a clickable email link when provided.', 'sitepause-custom-html-offline-maintenance-mode-activator' )
	);
}

/**
 * Renders the contact phone field.
 *
 * @return void
 */
function sitepause_maintenance_render_contact_phone_field() {
	sitepause_maintenance_render_text_input(
		'contact-phone',
		'contact_phone',
		'text',
		__( 'Displayed as a clickable phone link when provided.', 'sitepause-custom-html-offline-maintenance-mode-activator' )
	);
}

/**
 * Renders the Facebook URL field.
 *
 * @return void
 */
function sitepause_maintenance_render_facebook_url_field() {
	sitepause_maintenance_render_text_input(
		'facebook-url',
		'facebook_url',
		'url',
		__( 'Displayed as a Facebook icon link when provided.', 'sitepause-custom-html-offline-maintenance-mode-activator' )
	);
}

/**
 * Renders the Instagram URL field.
 *
 * @return void
 */
function sitepause_maintenance_render_instagram_url_field() {
	sitepause_maintenance_render_text_input(
		'instagram-url',
		'instagram_url',
		'url',
		__( 'Displayed as an Instagram icon link when provided.', 'sitepause-custom-html-offline-maintenance-mode-activator' )
	);
}

/**
 * Renders the LinkedIn URL field.
 *
 * @return void
 */
function sitepause_maintenance_render_linkedin_url_field() {
	sitepause_maintenance_render_text_input(
		'linkedin-url',
		'linkedin_url',
		'url',
		__( 'Displayed as a LinkedIn icon link when provided.', 'sitepause-custom-html-offline-maintenance-mode-activator' )
	);
}

/**
 * Renders the footer text field.
 *
 * @return void
 */
function sitepause_maintenance_render_footer_text_field() {
	sitepause_maintenance_render_text_input(
		'footer-text',
		'footer_text',
		'text',
		__( 'Displayed below the contact section when provided.', 'sitepause-custom-html-offline-maintenance-mode-activator' )
	);
}

/**
 * Renders the template style choices.
 *
 * @return void
 */
function sitepause_maintenance_render_template_style_field() {
	$options         = sitepause_maintenance_get_options();
	$template_styles = sitepause_maintenance_get_template_styles();
	$current         = $options['template_style'];
	?>
	<div class="sitepause-maintenance-template-choices">
		<?php foreach ( $template_styles as $template_key => $template ) : ?>
			<label class="sitepause-maintenance-template-card <?php echo esc_attr( $current === $template_key ? 'is-selected' : '' ); ?>" for="<?php echo esc_attr( 'sitepause-maintenance-template-' . $template_key ); ?>">
				<div class="<?php echo esc_attr( 'sitepause-maintenance-template-preview is-' . $template_key ); ?>" aria-hidden="true">
					<?php if ( 'center-card' === $template_key ) : ?>
						<div class="sitepause-maintenance-preview-card">
					<?php endif; ?>
					<div class="sitepause-maintenance-preview-logo"></div>
					<div class="sitepause-maintenance-preview-title"></div>
					<div class="sitepause-maintenance-preview-line"></div>
					<div class="sitepause-maintenance-preview-line is-short"></div>
					<?php if ( in_array( $template_key, array( 'classic', 'center-card', 'bold-panel', 'split-screen' ), true ) ) : ?>
						<div class="sitepause-maintenance-preview-buttons">
							<span></span>
							<span></span>
						</div>
					<?php endif; ?>
					<?php if ( 'center-card' === $template_key ) : ?>
						</div>
					<?php endif; ?>
				</div>
				<input
					type="radio"
					class="sitepause-maintenance-template-style"
					id="<?php echo esc_attr( 'sitepause-maintenance-template-' . $template_key ); ?>"
					name="<?php echo esc_attr( SITEPAUSE_MAINTENANCE_OPTION ); ?>[template_style]"
					value="<?php echo esc_attr( $template_key ); ?>"
					<?php checked( $current, $template_key ); ?>
				/>
				<span class="sitepause-maintenance-template-name"><?php echo esc_html( $template['label'] ); ?></span>
				<p class="description"><?php echo esc_html( $template['description'] ); ?></p>
			</label>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Renders the logo upload field.
 *
 * @return void
 */
function sitepause_maintenance_render_logo_field() {
	$options  = sitepause_maintenance_get_options();
	$logo_id  = absint( $options['logo_attachment_id'] );
	$logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'thumbnail' ) : '';
	?>
	<input
		type="hidden"
		id="sitepause-maintenance-logo-id"
		name="<?php echo esc_attr( SITEPAUSE_MAINTENANCE_OPTION ); ?>[logo_attachment_id]"
		value="<?php echo esc_attr( $logo_id ); ?>"
	/>
	<p>
		<img
			id="sitepause-maintenance-logo-preview"
			src="<?php echo esc_url( $logo_url ); ?>"
			alt="<?php esc_attr_e( 'Selected logo preview', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>"
			style="max-width: 160px; height: auto; <?php echo empty( $logo_url ) ? 'display: none;' : ''; ?>"
		/>
	</p>
	<p>
		<button type="button" class="button" id="sitepause-maintenance-logo-upload">
			<?php esc_html_e( 'Select Logo', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
		</button>
		<button
			type="button"
			class="button"
			id="sitepause-maintenance-logo-remove"
			style="<?php echo empty( $logo_url ) ? 'display: none;' : ''; ?>"
		>
			<?php esc_html_e( 'Remove Logo', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
		</button>
	</p>
	<p class="description">
		<?php esc_html_e( 'Displayed above the maintenance page title when provided.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
	</p>
	<?php
}

/**
 * Renders the background color field.
 *
 * @return void
 */
function sitepause_maintenance_render_background_color_field() {
	$options = sitepause_maintenance_get_options();
	?>
	<input
		type="text"
		id="sitepause-maintenance-background-color"
		class="sitepause-maintenance-color-field"
		name="<?php echo esc_attr( SITEPAUSE_MAINTENANCE_OPTION ); ?>[background_color]"
		value="<?php echo esc_attr( $options['background_color'] ); ?>"
		data-default-color="#f5f5f5"
	/>
	<p class="description">
		<?php esc_html_e( 'Choose the background color shown behind the maintenance page content.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
	</p>
	<?php
}

/**
 * Renders the custom HTML enable checkbox.
 *
 * @return void
 */
function sitepause_maintenance_render_custom_html_enabled_field() {
	$options = sitepause_maintenance_get_options();
	?>
	<label for="sitepause-maintenance-custom-html-enabled">
		<input
			type="checkbox"
			id="sitepause-maintenance-custom-html-enabled"
			name="<?php echo esc_attr( SITEPAUSE_MAINTENANCE_OPTION ); ?>[custom_html_enabled]"
			value="1"
			<?php checked( 1, (int) $options['custom_html_enabled'] ); ?>
		/>
		<?php esc_html_e( 'Enable Custom HTML Override', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
	</label>
	<p class="description">
		<?php esc_html_e( 'Use your own safe HTML instead of the built-in preset content. When enabled, only your custom HTML content is shown on the maintenance page.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
	</p>
	<?php
}

/**
 * Renders the custom HTML textarea.
 *
 * @return void
 */
function sitepause_maintenance_render_custom_html_field() {
	$options = sitepause_maintenance_get_options();
	?>
	<textarea
		id="sitepause-maintenance-custom-html"
		class="large-text code"
		rows="7"
		name="<?php echo esc_attr( SITEPAUSE_MAINTENANCE_OPTION ); ?>[custom_html]"
	><?php echo esc_textarea( $options['custom_html'] ); ?></textarea>
	<p class="description">
		<?php esc_html_e( 'Add limited safe HTML such as paragraphs, lists, links, bold text, and emphasis. Scripts, iframes, forms, styles, and unsafe markup are removed when settings are saved.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
	</p>
	<?php
}

/**
 * Renders the countdown enable checkbox.
 *
 * @return void
 */
function sitepause_maintenance_render_countdown_enabled_field() {
	$options = sitepause_maintenance_get_options();
	?>
	<label for="sitepause-maintenance-countdown-enabled">
		<input
			type="checkbox"
			id="sitepause-maintenance-countdown-enabled"
			name="<?php echo esc_attr( SITEPAUSE_MAINTENANCE_OPTION ); ?>[countdown_enabled]"
			value="1"
			<?php checked( 1, (int) $options['countdown_enabled'] ); ?>
		/>
		<?php esc_html_e( 'Enable countdown timer', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
	</label>
	<p class="description">
		<?php esc_html_e( 'Shows a visual countdown when the target date/time is valid and still in the future. It does not automatically disable maintenance mode.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
	</p>
	<?php
}

/**
 * Renders the countdown target datetime field.
 *
 * @return void
 */
function sitepause_maintenance_render_countdown_target_field() {
	$options = sitepause_maintenance_get_options();
	?>
	<input
		type="datetime-local"
		id="sitepause-maintenance-countdown-target"
		name="<?php echo esc_attr( SITEPAUSE_MAINTENANCE_OPTION ); ?>[countdown_target]"
		value="<?php echo esc_attr( $options['countdown_target'] ); ?>"
	/>
	<p class="description">
		<?php
		printf(
			/* translators: %s: WordPress timezone string. */
			esc_html__( 'Choose the time the visual countdown should count toward. Times use the WordPress site timezone: %s.', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			esc_html( sitepause_maintenance_get_timezone_label() )
		);
		?>
	</p>
	<?php
}

/**
 * Renders the schedule enable checkbox.
 *
 * @return void
 */
function sitepause_maintenance_render_schedule_enabled_field() {
	$options = sitepause_maintenance_get_options();
	?>
	<label for="sitepause-maintenance-schedule-enabled">
		<input
			type="checkbox"
			id="sitepause-maintenance-schedule-enabled"
			name="<?php echo esc_attr( SITEPAUSE_MAINTENANCE_OPTION ); ?>[schedule_enabled]"
			value="1"
			<?php checked( 1, (int) $options['schedule_enabled'] ); ?>
		/>
		<?php esc_html_e( 'Enable scheduled maintenance', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
	</label>
	<p class="description">
		<?php esc_html_e( 'When enabled, maintenance mode turns on automatically between the start and end time below. The manual toggle can still turn maintenance mode on immediately.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
	</p>
	<?php
}

/**
 * Renders a schedule datetime field.
 *
 * @param string $field_id Field ID suffix.
 * @param string $option_key Option array key.
 * @param string $description Help text.
 * @return void
 */
function sitepause_maintenance_render_schedule_datetime_field( $field_id, $option_key, $description ) {
	$options = sitepause_maintenance_get_options();
	?>
	<input
		type="datetime-local"
		id="<?php echo esc_attr( 'sitepause-maintenance-' . $field_id ); ?>"
		name="<?php echo esc_attr( SITEPAUSE_MAINTENANCE_OPTION ); ?>[<?php echo esc_attr( $option_key ); ?>]"
		value="<?php echo esc_attr( $options[ $option_key ] ); ?>"
	/>
	<p class="description">
		<?php echo esc_html( $description ); ?>
		<?php
		printf(
			/* translators: %s: WordPress timezone string. */
			esc_html__( ' Times use the WordPress site timezone: %s.', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			esc_html( sitepause_maintenance_get_timezone_label() )
		);
		?>
	</p>
	<?php
}

/**
 * Renders the schedule start field.
 *
 * @return void
 */
function sitepause_maintenance_render_schedule_start_field() {
	sitepause_maintenance_render_schedule_datetime_field(
		'schedule-start',
		'schedule_start',
		__( 'Choose when scheduled maintenance should begin.', 'sitepause-custom-html-offline-maintenance-mode-activator' )
	);
}

/**
 * Renders the schedule end field.
 *
 * @return void
 */
function sitepause_maintenance_render_schedule_end_field() {
	sitepause_maintenance_render_schedule_datetime_field(
		'schedule-end',
		'schedule_end',
		__( 'Choose when scheduled maintenance should end automatically.', 'sitepause-custom-html-offline-maintenance-mode-activator' )
	);
}

/**
 * Renders the text color field.
 *
 * @return void
 */
function sitepause_maintenance_render_text_color_field() {
	$options = sitepause_maintenance_get_options();
	?>
	<input
		type="text"
		id="sitepause-maintenance-text-color"
		class="sitepause-maintenance-color-field"
		name="<?php echo esc_attr( SITEPAUSE_MAINTENANCE_OPTION ); ?>[text_color]"
		value="<?php echo esc_attr( $options['text_color'] ); ?>"
		data-default-color="#111111"
	/>
	<p class="description">
		<?php esc_html_e( 'Choose the main text color for headings, messages, and footer text.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
	</p>
	<?php
}

/**
 * Renders the layout width dropdown.
 *
 * @return void
 */
function sitepause_maintenance_render_layout_width_field() {
	$options       = sitepause_maintenance_get_options();
	$layout_widths = sitepause_maintenance_get_layout_widths();
	?>
	<select name="<?php echo esc_attr( SITEPAUSE_MAINTENANCE_OPTION ); ?>[layout_width]" id="sitepause-maintenance-layout-width">
		<?php foreach ( $layout_widths as $value => $layout ) : ?>
			<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $options['layout_width'], $value ); ?>>
				<?php echo esc_html( $layout['label'] ); ?>
			</option>
		<?php endforeach; ?>
	</select>
	<p class="description">
		<?php esc_html_e( 'Controls the maximum width of the maintenance page content.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
	</p>
	<?php
}

/**
 * Renders the role bypass checkboxes.
 *
 * @return void
 */
function sitepause_maintenance_render_bypass_roles_field() {
	$options      = sitepause_maintenance_get_options();
	$bypass_roles = isset( $options['bypass_roles'] ) && is_array( $options['bypass_roles'] ) ? $options['bypass_roles'] : array();
	$wp_roles     = wp_roles();
	$roles        = isset( $wp_roles->roles ) && is_array( $wp_roles->roles ) ? $wp_roles->roles : array();

	if ( empty( $roles ) ) {
		echo '<p>' . esc_html__( 'No editable WordPress roles were found.', 'sitepause-custom-html-offline-maintenance-mode-activator' ) . '</p>';
		return;
	}
	?>
	<ul class="sitepause-maintenance-role-list">
		<?php foreach ( $roles as $role_key => $role ) : ?>
			<li>
				<label for="<?php echo esc_attr( 'sitepause-maintenance-bypass-role-' . $role_key ); ?>">
					<input
						type="checkbox"
						class="sitepause-maintenance-bypass-role"
						id="<?php echo esc_attr( 'sitepause-maintenance-bypass-role-' . $role_key ); ?>"
						name="<?php echo esc_attr( SITEPAUSE_MAINTENANCE_OPTION ); ?>[bypass_roles][]"
						value="<?php echo esc_attr( $role_key ); ?>"
						<?php checked( in_array( $role_key, $bypass_roles, true ) ); ?>
					/>
					<?php echo esc_html( translate_user_role( $role['name'] ) ); ?>
				</label>
			</li>
		<?php endforeach; ?>
	</ul>
	<p class="description">
		<?php esc_html_e( 'Selected logged-in roles bypass the maintenance page and can continue using the public website. Administrators also bypass through their normal WordPress capability.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
	</p>
	<?php
}

/**
 * Renders the IP whitelist textarea.
 *
 * @return void
 */
function sitepause_maintenance_render_ip_whitelist_field() {
	$options      = sitepause_maintenance_get_options();
	$ip_whitelist = isset( $options['ip_whitelist'] ) && is_array( $options['ip_whitelist'] ) ? $options['ip_whitelist'] : array();
	?>
	<textarea
		id="sitepause-maintenance-ip-whitelist"
		class="large-text code"
		rows="6"
		name="<?php echo esc_attr( SITEPAUSE_MAINTENANCE_OPTION ); ?>[ip_whitelist]"
	><?php echo esc_textarea( implode( "\n", $ip_whitelist ) ); ?></textarea>
	<p class="description">
		<?php esc_html_e( 'Add one IP address per line. IPv4 and IPv6 addresses are supported.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
		<?php esc_html_e( 'Only exact IP matches are supported in this phase. CIDR ranges are not supported yet.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
		<?php esc_html_e( 'Your current IP may appear different in LocalWP or when using a proxy or VPN.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
	</p>
	<?php
}

/**
 * Renders one registered Settings API section.
 *
 * WordPress normally prints every section for a page at once. This helper keeps
 * the same registered fields, but lets the settings page group sections into
 * simple tabs.
 *
 * @param string $page Settings API page slug.
 * @param string $section_id Registered section ID.
 * @return void
 */
function sitepause_maintenance_render_settings_section( $page, $section_id ) {
	global $wp_settings_sections, $wp_settings_fields;

	if ( empty( $wp_settings_sections[ $page ][ $section_id ] ) ) {
		return;
	}

	$section = $wp_settings_sections[ $page ][ $section_id ];

	if ( ! empty( $section['title'] ) ) {
		echo '<h2>' . esc_html( $section['title'] ) . '</h2>';
	}

	if ( ! empty( $section['callback'] ) ) {
		call_user_func( $section['callback'], $section );
	}

	if ( empty( $wp_settings_fields[ $page ][ $section_id ] ) ) {
		return;
	}

	echo '<table class="form-table" role="presentation">';
	do_settings_fields( $page, $section_id );
	echo '</table>';
}

/**
 * Checks whether Custom HTML Override is active.
 *
 * @param array|null $options Optional plugin options.
 * @return bool
 */
function sitepause_maintenance_is_custom_html_override_active( $options = null ) {
	if ( null === $options ) {
		$options = sitepause_maintenance_get_options();
	}

	return ! empty( $options['custom_html_enabled'] ) && '' !== trim( $options['custom_html'] );
}

/**
 * Renders the Import / Export admin tab content.
 *
 * @return void
 */
function sitepause_maintenance_render_import_export_tab() {
	$export_url = wp_nonce_url(
		admin_url( 'admin-post.php?action=sitepause_maintenance_export_settings' ),
		'sitepause_maintenance_export_settings'
	);
	?>
	<div class="sitepause-maintenance-import-export-panel">
		<h2><?php esc_html_e( 'Export Settings', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></h2>
		<p><?php esc_html_e( 'Export creates a JSON backup of the current SitePause settings only.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></p>
		<p>
			<a class="button button-secondary" href="<?php echo esc_url( $export_url ); ?>">
				<?php esc_html_e( 'Export Settings', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
			</a>
		</p>
	</div>

	<div class="sitepause-maintenance-import-export-panel">
		<h2><?php esc_html_e( 'Import Settings', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></h2>
		<p><?php esc_html_e( 'Paste exported JSON below. Import replaces current plugin settings after validation.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></p>
		<p><?php esc_html_e( 'Imported custom HTML is sanitized again. Unknown or unsafe values are ignored.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></p>
		<p>
			<label for="sitepause-maintenance-import-json">
				<strong><?php esc_html_e( 'Settings JSON', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></strong>
			</label>
		</p>
		<textarea
			id="sitepause-maintenance-import-json"
			class="large-text code"
			name="sitepause_maintenance_import_json"
			form="sitepause-maintenance-import-form"
			rows="10"
			placeholder="<?php esc_attr_e( 'Paste exported JSON here.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>"
		></textarea>
		<p>
			<button
				type="submit"
				class="button button-secondary"
				form="sitepause-maintenance-import-form"
			>
				<?php esc_html_e( 'Import Settings', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
			</button>
		</p>
	</div>
	<?php
}

/**
 * Renders the plugin settings page.
 *
 * @return void
 */
function sitepause_maintenance_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to access this page.', 'sitepause-custom-html-offline-maintenance-mode-activator' ) );
	}

	$preview_url = sitepause_maintenance_get_preview_url();
	$options     = sitepause_maintenance_get_options();
	$is_enabled  = sitepause_maintenance_is_enabled();
	$is_custom_html_override_active = sitepause_maintenance_is_custom_html_override_active( $options );
	$tabs        = array(
		'sitepause-maintenance-tab-general' => __( 'General', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause-maintenance-tab-content' => __( 'Content', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause-maintenance-tab-design'  => __( 'Branding', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause-maintenance-tab-custom-html' => __( 'Custom HTML', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause-maintenance-tab-bypass'  => __( 'Bypass', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause-maintenance-tab-import-export' => __( 'Import / Export', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
		'sitepause-maintenance-tab-preview' => __( 'Preview & Reset', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
	);
	?>
	<div class="wrap sitepause-maintenance-admin-page">
		<h1><?php esc_html_e( 'SitePause - Custom HTML Offline & Maintenance Mode Activator', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></h1>
		<?php settings_errors( SITEPAUSE_MAINTENANCE_OPTION ); ?>
		<p class="sitepause-maintenance-status <?php echo esc_attr( $is_enabled ? 'is-on' : 'is-off' ); ?>">
			<?php
			echo esc_html(
				$is_enabled
					? __( 'Maintenance mode is currently ON', 'sitepause-custom-html-offline-maintenance-mode-activator' )
					: __( 'Maintenance mode is currently OFF', 'sitepause-custom-html-offline-maintenance-mode-activator' )
			);
			?>
		</p>
		<?php if ( $is_enabled ) : ?>
			<div class="notice notice-warning inline">
				<p><?php esc_html_e( 'Maintenance mode is active. Public visitors may see the maintenance page until manual mode is turned off or the schedule ends.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></p>
			</div>
		<?php endif; ?>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'sitepause_maintenance_settings' );
			?>
			<nav class="nav-tab-wrapper sitepause-maintenance-tabs" role="tablist" aria-label="<?php esc_attr_e( 'SitePause settings tabs', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>">
				<?php foreach ( $tabs as $tab_id => $tab_label ) : ?>
					<a
						href="#<?php echo esc_attr( $tab_id ); ?>"
						class="nav-tab sitepause-maintenance-tab-link <?php echo esc_attr( 'sitepause-maintenance-tab-general' === $tab_id ? 'nav-tab-active' : '' ); ?>"
						data-tab="<?php echo esc_attr( $tab_id ); ?>"
						role="tab"
						aria-controls="<?php echo esc_attr( $tab_id ); ?>"
						aria-selected="<?php echo esc_attr( 'sitepause-maintenance-tab-general' === $tab_id ? 'true' : 'false' ); ?>"
					>
						<?php echo esc_html( $tab_label ); ?>
					</a>
				<?php endforeach; ?>
			</nav>

			<div id="sitepause-maintenance-tab-general" class="sitepause-maintenance-tab-panel is-active" role="tabpanel">
				<?php if ( $is_custom_html_override_active ) : ?>
					<p class="sitepause-maintenance-status is-warning">
						<?php esc_html_e( 'Custom HTML Override is ACTIVE', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
					</p>
				<?php endif; ?>
				<?php sitepause_maintenance_render_settings_section( 'sitepause-custom-html-offline-maintenance-mode-activator', 'sitepause_maintenance_general_section' ); ?>
			</div>

			<div id="sitepause-maintenance-tab-content" class="sitepause-maintenance-tab-panel" role="tabpanel">
				<?php
				sitepause_maintenance_render_settings_section( 'sitepause-custom-html-offline-maintenance-mode-activator', 'sitepause_maintenance_content_section' );
				sitepause_maintenance_render_settings_section( 'sitepause-custom-html-offline-maintenance-mode-activator', 'sitepause_maintenance_contact_section' );
				?>
			</div>

			<div id="sitepause-maintenance-tab-design" class="sitepause-maintenance-tab-panel" role="tabpanel">
				<?php if ( $is_custom_html_override_active ) : ?>
					<div class="notice notice-warning inline">
						<p><?php esc_html_e( 'Template presets are currently overridden by Custom HTML mode.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></p>
					</div>
				<?php endif; ?>
				<?php
				sitepause_maintenance_render_settings_section( 'sitepause-custom-html-offline-maintenance-mode-activator', 'sitepause_maintenance_templates_section' );
				sitepause_maintenance_render_settings_section( 'sitepause-custom-html-offline-maintenance-mode-activator', 'sitepause_maintenance_design_section' );
				?>
			</div>

			<div id="sitepause-maintenance-tab-custom-html" class="sitepause-maintenance-tab-panel" role="tabpanel">
				<?php sitepause_maintenance_render_settings_section( 'sitepause-custom-html-offline-maintenance-mode-activator', 'sitepause_maintenance_custom_html_section' ); ?>
			</div>

			<div id="sitepause-maintenance-tab-bypass" class="sitepause-maintenance-tab-panel" role="tabpanel">
				<?php sitepause_maintenance_render_settings_section( 'sitepause-custom-html-offline-maintenance-mode-activator', 'sitepause_maintenance_bypass_section' ); ?>
			</div>

			<div id="sitepause-maintenance-tab-import-export" class="sitepause-maintenance-tab-panel" role="tabpanel">
				<?php sitepause_maintenance_render_import_export_tab(); ?>
			</div>

			<div id="sitepause-maintenance-tab-preview" class="sitepause-maintenance-tab-panel" role="tabpanel">
				<div class="sitepause-maintenance-actions-panel">
					<h2><?php esc_html_e( 'Preview & Reset', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></h2>
					<p><?php esc_html_e( 'Preview the current saved offline page, or reset the visible form fields before saving.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></p>
					<p>
						<a class="button button-secondary" href="<?php echo esc_url( $preview_url ); ?>" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Preview Maintenance Page', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
						</a>
					</p>
					<h3><?php esc_html_e( 'Reset Settings', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></h3>
					<p><?php esc_html_e( 'Reset the visible form fields to default values. Nothing is saved until you click Save Settings.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></p>
					<button type="button" class="button button-secondary" id="sitepause-maintenance-reset-fields">
						<?php esc_html_e( 'Reset Settings', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
					</button>
				</div>
			</div>

			<div class="sitepause-maintenance-submit">
				<?php submit_button( esc_html__( 'Save Settings', 'sitepause-custom-html-offline-maintenance-mode-activator' ), 'primary', 'submit', false ); ?>
			</div>
		</form>
		<form id="sitepause-maintenance-import-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="sitepause_maintenance_import_settings" />
			<?php wp_nonce_field( 'sitepause_maintenance_import_settings', 'sitepause_maintenance_import_nonce' ); ?>
		</form>
		<p class="sitepause-maintenance-admin-credit">
			<?php esc_html_e( 'Created and developed by', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
			<a href="<?php echo esc_url( 'https://witeds.com/WP-Plugins/' ); ?>" target="_blank" rel="noopener noreferrer">
				<?php echo esc_html( 'WiTEDS' ); ?>
			</a>
		</p>
	</div>
	<?php
}

/**
 * Checks whether maintenance mode is enabled.
 *
 * @return bool
 */
function sitepause_maintenance_is_enabled() {
	$options = sitepause_maintenance_get_options();

	return ! empty( $options['enabled'] ) || sitepause_maintenance_is_schedule_active( $options );
}

/**
 * Checks whether scheduled maintenance is currently active.
 *
 * @param array|null $options Optional plugin options.
 * @return bool
 */
function sitepause_maintenance_is_schedule_active( $options = null ) {
	if ( null === $options ) {
		$options = sitepause_maintenance_get_options();
	}

	if ( empty( $options['schedule_enabled'] ) ) {
		return false;
	}

	$start_timestamp = sitepause_maintenance_schedule_datetime_to_timestamp( $options['schedule_start'] );
	$end_timestamp   = sitepause_maintenance_schedule_datetime_to_timestamp( $options['schedule_end'] );

	if ( null === $start_timestamp || null === $end_timestamp || $end_timestamp <= $start_timestamp ) {
		return false;
	}

	$current_timestamp = current_datetime()->getTimestamp();

	return $current_timestamp >= $start_timestamp && $current_timestamp <= $end_timestamp;
}

/**
 * Replaces supported placeholders in maintenance page text.
 *
 * @param string $text Raw saved text.
 * @return string
 */
function sitepause_maintenance_format_page_text( $text ) {
	return str_replace( '{site name}', get_bloginfo( 'name' ), $text );
}

/**
 * Builds a protected URL for previewing the maintenance page.
 *
 * @return string
 */
function sitepause_maintenance_get_preview_url() {
	$preview_url = add_query_arg(
		'sitepause_maintenance_preview',
		'1',
		home_url( '/' )
	);

	return wp_nonce_url(
		$preview_url,
		'sitepause_maintenance_preview',
		'sitepause_maintenance_preview_nonce'
	);
}

/**
 * Checks whether the current request is asking for preview mode.
 *
 * @return bool
 */
function sitepause_maintenance_is_preview_request() {
	return isset( $_GET['sitepause_maintenance_preview'] );
}

/**
 * Verifies the preview nonce from the current request.
 *
 * @return bool
 */
function sitepause_maintenance_has_valid_preview_nonce() {
	if ( ! isset( $_GET['sitepause_maintenance_preview_nonce'] ) ) {
		return false;
	}

	$nonce = sanitize_text_field( wp_unslash( $_GET['sitepause_maintenance_preview_nonce'] ) );

	return (bool) wp_verify_nonce( $nonce, 'sitepause_maintenance_preview' );
}

/**
 * Builds the social links that have been provided.
 *
 * @param array $options Plugin options.
 * @return array
 */
function sitepause_maintenance_get_social_links( $options ) {
	$platforms = array(
		'facebook_url'  => array(
			'label' => __( 'Facebook', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			'icon'  => 'dashicons-facebook-alt',
		),
		'instagram_url' => array(
			'label' => __( 'Instagram', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			'icon'  => 'dashicons-instagram',
		),
		'linkedin_url'  => array(
			'label' => __( 'LinkedIn', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
			'icon'  => 'dashicons-linkedin',
		),
	);

	$links = array();

	foreach ( $platforms as $option_key => $platform ) {
		if ( empty( $options[ $option_key ] ) ) {
			continue;
		}

		$links[] = array(
			'url'   => $options[ $option_key ],
			'label' => $platform['label'],
			'icon'  => $platform['icon'],
		);
	}

	return $links;
}

/**
 * Gets the frontend max-width value for the selected layout.
 *
 * @param string $layout_width Saved layout width key.
 * @return string
 */
function sitepause_maintenance_get_layout_max_width( $layout_width ) {
	$layout_widths = sitepause_maintenance_get_layout_widths();

	if ( ! array_key_exists( $layout_width, $layout_widths ) ) {
		$layout_width = sitepause_maintenance_default_options()['layout_width'];
	}

	return $layout_widths[ $layout_width ]['max_width'];
}

/**
 * Gets the CSS class for the selected frontend template.
 *
 * @param string $template_style Saved template key.
 * @return string
 */
function sitepause_maintenance_get_template_class( $template_style ) {
	$template_styles = sitepause_maintenance_get_template_styles();

	if ( ! array_key_exists( $template_style, $template_styles ) ) {
		$template_style = sitepause_maintenance_default_options()['template_style'];
	}

	return 'sitepause-template-' . $template_style;
}

/**
 * Gets the selected preset template file path.
 *
 * @param string $template_style Saved template key.
 * @return string
 */
function sitepause_maintenance_get_template_file( $template_style ) {
	$template_styles = sitepause_maintenance_get_template_styles();

	if ( ! array_key_exists( $template_style, $template_styles ) ) {
		$template_style = sitepause_maintenance_default_options()['template_style'];
	}

	$template_file = SITEPAUSE_MAINTENANCE_PATH . 'templates/presets/' . $template_style . '.php';

	if ( ! file_exists( $template_file ) ) {
		$template_file = SITEPAUSE_MAINTENANCE_PATH . 'templates/presets/classic.php';
	}

	return $template_file;
}

/**
 * Renders the selected preset template.
 *
 * @param array $template_data Prepared template data.
 * @return void
 */
function sitepause_maintenance_render_preset_template( $template_data ) {
	if ( ! is_array( $template_data ) ) {
		return;
	}

	$template_file = sitepause_maintenance_get_template_file( $template_data['template_style'] );

	include $template_file;
}

/**
 * Renders the logo markup for preset templates.
 *
 * @param string $logo_url Logo URL.
 * @return void
 */
function sitepause_maintenance_template_logo( $logo_url ) {
	if ( empty( $logo_url ) ) {
		return;
	}
	?>
	<img
		class="sitepause-maintenance-logo"
		src="<?php echo esc_url( $logo_url ); ?>"
		alt="<?php echo esc_attr( sprintf( __( '%s logo', 'sitepause-custom-html-offline-maintenance-mode-activator' ), get_bloginfo( 'name' ) ) ); ?>"
	/>
	<?php
}

/**
 * Renders the countdown markup for preset templates.
 *
 * @param int|null $countdown_timestamp Countdown timestamp.
 * @return void
 */
function sitepause_maintenance_template_countdown( $countdown_timestamp ) {
	if ( null === $countdown_timestamp ) {
		return;
	}
	?>
	<section
		class="sitepause-maintenance-countdown"
		data-countdown-target="<?php echo esc_attr( $countdown_timestamp * 1000 ); ?>"
		aria-label="<?php esc_attr_e( 'Maintenance countdown', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>"
	>
		<p class="sitepause-maintenance-countdown-fallback">
			<?php esc_html_e( 'Maintenance is scheduled to finish soon.', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
		</p>
		<div class="sitepause-maintenance-countdown-units" hidden>
			<span>
				<strong data-countdown-days>0</strong>
				<?php esc_html_e( 'Days', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
			</span>
			<span>
				<strong data-countdown-hours>0</strong>
				<?php esc_html_e( 'Hours', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
			</span>
			<span>
				<strong data-countdown-minutes>0</strong>
				<?php esc_html_e( 'Minutes', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
			</span>
			<span>
				<strong data-countdown-seconds>0</strong>
				<?php esc_html_e( 'Seconds', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?>
			</span>
		</div>
	</section>
	<?php
}

/**
 * Renders contact and social links for preset templates.
 *
 * @param array $template_data Prepared template data.
 * @return void
 */
function sitepause_maintenance_template_contact( $template_data ) {
	if ( empty( $template_data['has_contact'] ) ) {
		return;
	}

	$options      = $template_data['options'];
	$social_links = $template_data['social_links'];
	?>
	<section class="sitepause-maintenance-contact" aria-labelledby="sitepause-maintenance-contact-heading">
		<h2 id="sitepause-maintenance-contact-heading"><?php esc_html_e( 'Contact Us', 'sitepause-custom-html-offline-maintenance-mode-activator' ); ?></h2>
		<?php if ( ! empty( $options['contact_email'] ) || ! empty( $options['contact_phone'] ) ) : ?>
			<ul class="sitepause-maintenance-contact-list">
				<?php if ( ! empty( $options['contact_email'] ) ) : ?>
					<li>
						<a href="mailto:<?php echo esc_attr( $options['contact_email'] ); ?>">
							<span class="dashicons dashicons-email" aria-hidden="true"></span>
							<span><?php echo esc_html( $options['contact_email'] ); ?></span>
						</a>
					</li>
				<?php endif; ?>
				<?php if ( ! empty( $options['contact_phone'] ) ) : ?>
					<li>
						<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $options['contact_phone'] ) ); ?>">
							<span class="dashicons dashicons-phone" aria-hidden="true"></span>
							<span><?php echo esc_html( $options['contact_phone'] ); ?></span>
						</a>
					</li>
				<?php endif; ?>
			</ul>
		<?php endif; ?>
		<?php if ( ! empty( $social_links ) ) : ?>
			<ul class="sitepause-maintenance-social-list">
				<?php foreach ( $social_links as $social_link ) : ?>
					<li>
						<a href="<?php echo esc_url( $social_link['url'] ); ?>" target="_blank" rel="noopener noreferrer">
							<span class="dashicons <?php echo esc_attr( $social_link['icon'] ); ?>" aria-hidden="true"></span>
							<span><?php echo esc_html( $social_link['label'] ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</section>
	<?php
}

/**
 * Renders footer text for preset templates.
 *
 * @param string $footer_text Footer text.
 * @return void
 */
function sitepause_maintenance_template_footer( $footer_text ) {
	if ( empty( $footer_text ) ) {
		return;
	}
	?>
	<footer class="sitepause-maintenance-footer">
		<?php echo esc_html( sitepause_maintenance_format_page_text( $footer_text ) ); ?>
	</footer>
	<?php
}

/**
 * Renders the small plugin credit shown below preset content.
 *
 * @return void
 */
/**
 * Gets a future countdown target timestamp.
 *
 * @param array $options Plugin options.
 * @return int|null
 */
function sitepause_maintenance_get_countdown_target_timestamp( $options ) {
	if ( empty( $options['countdown_enabled'] ) ) {
		return null;
	}

	$target_timestamp = sitepause_maintenance_schedule_datetime_to_timestamp( $options['countdown_target'] );

	if ( null === $target_timestamp || $target_timestamp <= current_datetime()->getTimestamp() ) {
		return null;
	}

	return $target_timestamp;
}

/**
 * Sends headers for the visitor-facing maintenance response.
 *
 * A 503 status tells search engines the site is temporarily unavailable, not
 * permanently changed. Retry-After asks crawlers to come back later.
 *
 * @return void
 */
function sitepause_maintenance_send_unavailable_headers() {
	status_header( 503 );
	nocache_headers();
	header( 'Retry-After: 3600' );
}

/**
 * Checks whether the current logged-in user has a selected bypass role.
 *
 * @return bool
 */
function sitepause_maintenance_current_user_has_bypass_role() {
	if ( ! is_user_logged_in() ) {
		return false;
	}

	$options      = sitepause_maintenance_get_options();
	$bypass_roles = isset( $options['bypass_roles'] ) && is_array( $options['bypass_roles'] ) ? $options['bypass_roles'] : array();
	$bypass_roles = sitepause_maintenance_sanitize_bypass_roles( $bypass_roles );

	if ( empty( $bypass_roles ) ) {
		return false;
	}

	$user = wp_get_current_user();

	if ( empty( $user->roles ) || ! is_array( $user->roles ) ) {
		return false;
	}

	return ! empty( array_intersect( $user->roles, $bypass_roles ) );
}

/**
 * Checks whether the current visitor IP is whitelisted.
 *
 * @return bool
 */
function sitepause_maintenance_current_ip_is_whitelisted() {
	// Use REMOTE_ADDR for the beginner version because forwarded headers can be spoofed if the server is not configured to trust a proxy.
	if ( empty( $_SERVER['REMOTE_ADDR'] ) ) {
		return false;
	}

	$visitor_ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );

	if ( ! filter_var( $visitor_ip, FILTER_VALIDATE_IP ) ) {
		return false;
	}

	$options      = sitepause_maintenance_get_options();
	$ip_whitelist = isset( $options['ip_whitelist'] ) ? sitepause_maintenance_sanitize_ip_whitelist( $options['ip_whitelist'] ) : array();

	return in_array( $visitor_ip, $ip_whitelist, true );
}

/**
 * Decides whether the current request should bypass maintenance mode.
 *
 * @return bool
 */
function sitepause_maintenance_should_bypass() {
	global $pagenow;

	if ( is_admin() ) {
		return true;
	}

	if ( 'wp-login.php' === $pagenow ) {
		return true;
	}

	if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) {
		return true;
	}

	if ( function_exists( 'wp_doing_cron' ) && wp_doing_cron() ) {
		return true;
	}

	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return true;
	}

	if ( current_user_can( 'manage_options' ) ) {
		return true;
	}

	if ( sitepause_maintenance_current_user_has_bypass_role() ) {
		return true;
	}

	if ( sitepause_maintenance_current_ip_is_whitelisted() ) {
		return true;
	}

	return false;
}

/**
 * Renders the maintenance page layout.
 *
 * @param int $status_code HTTP status code to send.
 * @return void
 */
function sitepause_maintenance_render_page( $status_code = 503 ) {
	$status_code = absint( $status_code );
	$is_unavailable = ( 503 === $status_code );

	if ( $is_unavailable ) {
		sitepause_maintenance_send_unavailable_headers();
	} else {
		status_header( $status_code );
	}

	$options = sitepause_maintenance_get_options();
	$title   = sitepause_maintenance_format_page_text( $options['title'] );
	$message = sitepause_maintenance_format_page_text( $options['message'] );
	$custom_html = ! empty( $options['custom_html_enabled'] ) ? trim( $options['custom_html'] ) : '';
	$has_custom_html_override = sitepause_maintenance_is_custom_html_override_active( $options );
	$social_links = sitepause_maintenance_get_social_links( $options );
	$has_contact  = ! empty( $options['contact_email'] ) || ! empty( $options['contact_phone'] ) || ! empty( $social_links );
	$logo_url     = ! empty( $options['logo_attachment_id'] ) ? wp_get_attachment_image_url( absint( $options['logo_attachment_id'] ), 'medium' ) : '';
	$max_width    = sitepause_maintenance_get_layout_max_width( $options['layout_width'] );
	$template_class = sitepause_maintenance_get_template_class( $options['template_style'] );
	$countdown_timestamp = sitepause_maintenance_get_countdown_target_timestamp( $options );
	$template_data = array(
		'options'             => $options,
		'title'               => $title,
		'message'             => $message,
		'logo_url'            => $logo_url,
		'social_links'        => $social_links,
		'has_contact'         => $has_contact,
		'countdown_timestamp' => $countdown_timestamp,
		'template_style'      => $options['template_style'],
	);

	wp_enqueue_style( 'dashicons' );
	wp_enqueue_style(
		'sitepause-maintenance-page',
		SITEPAUSE_MAINTENANCE_URL . 'assets/sitepause-maintenance.css',
		array( 'dashicons' ),
		SITEPAUSE_MAINTENANCE_VERSION
	);
	?>
	<!doctype html>
	<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php if ( $is_unavailable ) : ?>
			<meta name="robots" content="noindex, nofollow">
		<?php endif; ?>
		<title><?php echo esc_html( $title ); ?></title>
		<?php wp_print_styles( array( 'dashicons', 'sitepause-maintenance-page' ) ); ?>
		<style>
			:root {
				--sitepause-maintenance-background: <?php echo esc_attr( $options['background_color'] ); ?>;
				--sitepause-maintenance-text: <?php echo esc_attr( $options['text_color'] ); ?>;
				--sitepause-maintenance-width: <?php echo esc_attr( $max_width ); ?>;
			}
		</style>
	</head>
	<body class="<?php echo esc_attr( 'sitepause-maintenance-body ' . ( $has_custom_html_override ? 'sitepause-custom-html-mode' : $template_class ) ); ?>">
		<main class="sitepause-maintenance-page">
			<?php if ( $has_custom_html_override ) : ?>
				<div class="sitepause-maintenance-custom-html">
					<?php echo wp_kses_post( $custom_html ); ?>
				</div>
			<?php else : ?>
				<?php sitepause_maintenance_render_preset_template( $template_data ); ?>
			<?php endif; ?>
		</main>
		<?php if ( ! $has_custom_html_override && null !== $countdown_timestamp ) : ?>
			<script>
				(function() {
					var countdown = document.querySelector(".sitepause-maintenance-countdown");

					if (!countdown) {
						return;
					}

					var target = parseInt(countdown.getAttribute("data-countdown-target"), 10);
					var fallback = countdown.querySelector(".sitepause-maintenance-countdown-fallback");
					var units = countdown.querySelector(".sitepause-maintenance-countdown-units");
					var days = countdown.querySelector("[data-countdown-days]");
					var hours = countdown.querySelector("[data-countdown-hours]");
					var minutes = countdown.querySelector("[data-countdown-minutes]");
					var seconds = countdown.querySelector("[data-countdown-seconds]");

					function updateCountdown() {
						var remaining = Math.max(0, target - Date.now());

						if (0 === remaining) {
							countdown.style.display = "none";
							return;
						}

						var totalSeconds = Math.floor(remaining / 1000);
						var totalDays = Math.floor(totalSeconds / 86400);
						var totalHours = Math.floor((totalSeconds % 86400) / 3600);
						var totalMinutes = Math.floor((totalSeconds % 3600) / 60);
						var visibleSeconds = totalSeconds % 60;

						days.textContent = totalDays;
						hours.textContent = totalHours;
						minutes.textContent = totalMinutes;
						seconds.textContent = visibleSeconds;

						if (fallback) {
							fallback.hidden = true;
						}

						if (units) {
							units.hidden = false;
						}
					}

					updateCountdown();
					window.setInterval(updateCountdown, 1000);
				}());
			</script>
		<?php endif; ?>
	</body>
	</html>
	<?php
	exit;
}

/**
 * Shows the maintenance page to preview users or normal frontend visitors.
 *
 * @return void
 */
function sitepause_maintenance_maybe_render_page() {
	if ( sitepause_maintenance_is_preview_request() ) {
		if ( ! current_user_can( 'manage_options' ) || ! sitepause_maintenance_has_valid_preview_nonce() ) {
			wp_die(
				esc_html__( 'You do not have permission to preview the maintenance page.', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
				esc_html__( 'Preview not allowed', 'sitepause-custom-html-offline-maintenance-mode-activator' ),
				array( 'response' => 403 )
			);
		}

		sitepause_maintenance_render_page( 200 );
	}

	if ( ! sitepause_maintenance_is_enabled() || sitepause_maintenance_should_bypass() ) {
		return;
	}

	sitepause_maintenance_render_page( 503 );
}
/**
 * Initialize the plugin.
 */
add_action( 'plugins_loaded', function() {
	require_once SITEPAUSE_MAINTENANCE_PATH . 'includes/class-sitepause-maintenance-plugin.php';
	require_once SITEPAUSE_MAINTENANCE_PATH . 'includes/class-sitepause-maintenance-settings.php';
	require_once SITEPAUSE_MAINTENANCE_PATH . 'includes/class-sitepause-maintenance-frontend.php';

	static $plugin = null;
	if ( null === $plugin ) {
		$plugin = new SitePause_Maintenance_Plugin();
		$plugin->run();
	}
} );
