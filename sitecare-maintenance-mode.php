<?php
/**
 * Plugin Name: SiteCare Maintenance Mode
 * Plugin URI: https://github.com/widumalt/sitecare-maintenance-mode
 * Description: A beginner-friendly maintenance mode plugin for showing visitors a simple offline page while administrators keep working.
 * Version: 1.0.0
 * Author: SiteCare Maintenance Mode Contributors
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: sitecare-maintenance-mode
 * Domain Path: /languages
 *
 * @package SiteCareMaintenanceMode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Current plugin version.
 */
define( 'SITECARE_MAINTENANCE_VERSION', '1.0.0' );

/**
 * Absolute path to this main plugin file.
 */
define( 'SITECARE_MAINTENANCE_FILE', __FILE__ );

/**
 * Absolute path to the plugin directory.
 */
define( 'SITECARE_MAINTENANCE_PATH', plugin_dir_path( __FILE__ ) );

/**
 * URL to the plugin directory.
 */
define( 'SITECARE_MAINTENANCE_URL', plugin_dir_url( __FILE__ ) );

/**
 * Name of the option that stores plugin settings.
 */
define( 'SITECARE_MAINTENANCE_OPTION', 'sitecare_maintenance_options' );

/**
 * Runs when the plugin is activated.
 *
 * The plugin stores a small option array with safe defaults. It does not create
 * custom database tables.
 *
 * @return void
 */
function sitecare_maintenance_activate() {
	// Store safe default settings if the option does not exist yet.
	if ( false === get_option( SITECARE_MAINTENANCE_OPTION ) ) {
		add_option( SITECARE_MAINTENANCE_OPTION, sitecare_maintenance_default_options() );
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
function sitecare_maintenance_deactivate() {
	// Keep settings so the site owner does not lose their choice on deactivate.
}

register_activation_hook( __FILE__, 'sitecare_maintenance_activate' );
register_deactivation_hook( __FILE__, 'sitecare_maintenance_deactivate' );

/**
 * Gets the default plugin options.
 *
 * @return array
 */
function sitecare_maintenance_default_options() {
	return array(
		'enabled'            => 0,
		'schedule_enabled'   => 0,
		'schedule_start'     => '',
		'schedule_end'       => '',
		'title'              => __( 'We will be back soon.', 'sitecare-maintenance-mode' ),
		'message'            => __( '{site name} is temporarily offline for maintenance. Please check back later.', 'sitecare-maintenance-mode' ),
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
	);
}

/**
 * Gets allowed layout width choices.
 *
 * @return array
 */
function sitecare_maintenance_get_layout_widths() {
	return array(
		'narrow' => array(
			'label'     => __( 'Narrow', 'sitecare-maintenance-mode' ),
			'max_width' => '480px',
		),
		'medium' => array(
			'label'     => __( 'Medium', 'sitecare-maintenance-mode' ),
			'max_width' => '640px',
		),
		'wide'   => array(
			'label'     => __( 'Wide', 'sitecare-maintenance-mode' ),
			'max_width' => '820px',
		),
	);
}

/**
 * Gets saved plugin options merged with safe defaults.
 *
 * @return array
 */
function sitecare_maintenance_get_options() {
	$options = get_option( SITECARE_MAINTENANCE_OPTION, array() );

	if ( ! is_array( $options ) ) {
		$options = array();
	}

	return wp_parse_args( $options, sitecare_maintenance_default_options() );
}

/**
 * Gets a readable WordPress timezone label for helper text.
 *
 * @return string
 */
function sitecare_maintenance_get_timezone_label() {
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
function sitecare_maintenance_sanitize_schedule_datetime( $value ) {
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
function sitecare_maintenance_schedule_datetime_to_timestamp( $value ) {
	$value = sitecare_maintenance_sanitize_schedule_datetime( $value );

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
function sitecare_maintenance_sanitize_options( $input ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return sitecare_maintenance_get_options();
	}

	$input = is_array( $input ) ? $input : array();
	$defaults = sitecare_maintenance_default_options();
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
	$layout_widths    = sitecare_maintenance_get_layout_widths();
	$layout_width     = sanitize_key( $input['layout_width'] );

	if ( empty( $background_color ) ) {
		$background_color = $defaults['background_color'];
	}

	if ( empty( $text_color ) ) {
		$text_color = $defaults['text_color'];
	}

	if ( ! array_key_exists( $layout_width, $layout_widths ) ) {
		$layout_width = $defaults['layout_width'];
	}

	$schedule_enabled = empty( $input['schedule_enabled'] ) ? 0 : 1;
	$schedule_start   = sitecare_maintenance_sanitize_schedule_datetime( $input['schedule_start'] );
	$schedule_end     = sitecare_maintenance_sanitize_schedule_datetime( $input['schedule_end'] );

	if ( $schedule_enabled ) {
		$start_timestamp = sitecare_maintenance_schedule_datetime_to_timestamp( $schedule_start );
		$end_timestamp   = sitecare_maintenance_schedule_datetime_to_timestamp( $schedule_end );

		if ( null === $start_timestamp || null === $end_timestamp ) {
			$schedule_enabled = 0;
			add_settings_error(
				SITECARE_MAINTENANCE_OPTION,
				'sitecare_maintenance_schedule_missing_dates',
				__( 'Scheduled maintenance was not enabled. Please choose both a valid start date/time and end date/time.', 'sitecare-maintenance-mode' ),
				'error'
			);
		} elseif ( $end_timestamp <= $start_timestamp ) {
			$schedule_enabled = 0;
			add_settings_error(
				SITECARE_MAINTENANCE_OPTION,
				'sitecare_maintenance_schedule_invalid_range',
				__( 'Scheduled maintenance was not enabled. The end date/time must be after the start date/time.', 'sitecare-maintenance-mode' ),
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
	);
}

/**
 * Registers the settings used by the admin page.
 *
 * @return void
 */
function sitecare_maintenance_register_settings() {
	register_setting(
		'sitecare_maintenance_settings',
		SITECARE_MAINTENANCE_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'sitecare_maintenance_sanitize_options',
			'default'           => sitecare_maintenance_default_options(),
		)
	);

	$sections = array(
		'sitecare_maintenance_general_section' => array(
			'title'    => esc_html__( 'General', 'sitecare-maintenance-mode' ),
			'callback' => 'sitecare_maintenance_render_general_intro',
		),
		'sitecare_maintenance_content_section' => array(
			'title'    => esc_html__( 'Content', 'sitecare-maintenance-mode' ),
			'callback' => 'sitecare_maintenance_render_content_intro',
		),
		'sitecare_maintenance_contact_section' => array(
			'title'    => esc_html__( 'Contact Details', 'sitecare-maintenance-mode' ),
			'callback' => 'sitecare_maintenance_render_contact_intro',
		),
		'sitecare_maintenance_design_section'  => array(
			'title'    => esc_html__( 'Branding & Design', 'sitecare-maintenance-mode' ),
			'callback' => 'sitecare_maintenance_render_design_intro',
		),
	);

	foreach ( $sections as $section_id => $section ) {
		add_settings_section(
			$section_id,
			$section['title'],
			$section['callback'],
			'sitecare-maintenance-mode'
		);
	}

	add_settings_field(
		'sitecare_maintenance_enabled',
		esc_html__( 'Status', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_enabled_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_general_section'
	);

	add_settings_field(
		'sitecare_maintenance_schedule_enabled',
		esc_html__( 'Scheduled Maintenance', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_schedule_enabled_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_general_section'
	);

	add_settings_field(
		'sitecare_maintenance_schedule_start',
		esc_html__( 'Start Date/Time', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_schedule_start_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_general_section'
	);

	add_settings_field(
		'sitecare_maintenance_schedule_end',
		esc_html__( 'End Date/Time', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_schedule_end_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_general_section'
	);

	add_settings_field(
		'sitecare_maintenance_title',
		esc_html__( 'Page Title', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_title_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_content_section'
	);

	add_settings_field(
		'sitecare_maintenance_message',
		esc_html__( 'Message', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_message_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_content_section'
	);

	add_settings_field(
		'sitecare_maintenance_contact_email',
		esc_html__( 'Contact Email', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_contact_email_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_contact_section'
	);

	add_settings_field(
		'sitecare_maintenance_contact_phone',
		esc_html__( 'Contact Phone', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_contact_phone_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_contact_section'
	);

	add_settings_field(
		'sitecare_maintenance_facebook_url',
		esc_html__( 'Facebook URL', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_facebook_url_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_contact_section'
	);

	add_settings_field(
		'sitecare_maintenance_instagram_url',
		esc_html__( 'Instagram URL', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_instagram_url_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_contact_section'
	);

	add_settings_field(
		'sitecare_maintenance_linkedin_url',
		esc_html__( 'LinkedIn URL', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_linkedin_url_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_contact_section'
	);

	add_settings_field(
		'sitecare_maintenance_footer_text',
		esc_html__( 'Footer Text', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_footer_text_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_contact_section'
	);

	add_settings_field(
		'sitecare_maintenance_logo',
		esc_html__( 'Logo', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_logo_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_design_section'
	);

	add_settings_field(
		'sitecare_maintenance_background_color',
		esc_html__( 'Background Color', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_background_color_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_design_section'
	);

	add_settings_field(
		'sitecare_maintenance_text_color',
		esc_html__( 'Text Color', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_text_color_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_design_section'
	);

	add_settings_field(
		'sitecare_maintenance_layout_width',
		esc_html__( 'Layout Width', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_layout_width_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_design_section'
	);
}
add_action( 'admin_init', 'sitecare_maintenance_register_settings' );

/**
 * Loads admin assets on this plugin's settings page.
 *
 * @param string $hook_suffix Current admin page hook suffix.
 * @return void
 */
function sitecare_maintenance_enqueue_admin_assets( $hook_suffix ) {
	if ( 'settings_page_sitecare-maintenance-mode' !== $hook_suffix ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );
	wp_add_inline_style(
		'wp-color-picker',
		'.sitecare-maintenance-admin-page { max-width: 980px; }
		.sitecare-maintenance-status { display: inline-block; margin: 8px 0 12px; padding: 6px 12px; border-radius: 3px; font-weight: 600; }
		.sitecare-maintenance-status.is-on { background: #fcf0f1; color: #8a2424; }
		.sitecare-maintenance-status.is-off { background: #edfaef; color: #1f6f2d; }
		.sitecare-maintenance-admin-page form { margin-top: 16px; }
		.sitecare-maintenance-admin-page h2 { margin-top: 28px; padding-top: 18px; border-top: 1px solid #dcdcde; }
		.sitecare-maintenance-admin-page h2:first-of-type { margin-top: 18px; }
		.sitecare-maintenance-admin-page .form-table { margin-top: 8px; background: #fff; border: 1px solid #dcdcde; }
		.sitecare-maintenance-admin-page .form-table th { padding-left: 18px; }
		.sitecare-maintenance-admin-page .form-table td { padding-right: 18px; }
		.sitecare-maintenance-actions-panel { margin-top: 24px; padding: 16px 18px; background: #fff; border: 1px solid #dcdcde; }
		.sitecare-maintenance-actions-panel h2 { margin-top: 0; padding-top: 0; border-top: 0; }'
	);

	$script_data = array(
		'title'        => __( 'Choose Logo', 'sitecare-maintenance-mode' ),
		'buttonText'   => __( 'Use this logo', 'sitecare-maintenance-mode' ),
		'confirmReset' => __( 'Are you sure you want to reset all SiteCare Maintenance settings to defaults? This will not be saved until you click Save Settings.', 'sitecare-maintenance-mode' ),
		'invalidRange' => __( 'End Date/Time must be after Start Date/Time.', 'sitecare-maintenance-mode' ),
		'defaults'     => sitecare_maintenance_default_options(),
	);

	wp_add_inline_script(
		'wp-color-picker',
		'jQuery(function($) {
			$(".sitecare-maintenance-color-field").wpColorPicker();

			var mediaFrame;
			var strings = ' . wp_json_encode( $script_data ) . ';

			$("#sitecare-maintenance-logo-upload").on("click", function(event) {
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

					$("#sitecare-maintenance-logo-id").val(attachment.id);
					$("#sitecare-maintenance-logo-preview").attr("src", previewUrl).show();
					$("#sitecare-maintenance-logo-remove").show();
				});

				mediaFrame.open();
			});

			$("#sitecare-maintenance-logo-remove").on("click", function(event) {
				event.preventDefault();

				$("#sitecare-maintenance-logo-id").val("0");
				$("#sitecare-maintenance-logo-preview").attr("src", "").hide();
				$(this).hide();
			});

			var resetButton = document.getElementById("sitecare-maintenance-reset-fields");

			if (resetButton) {
				resetButton.addEventListener("click", function() {
					if (!window.confirm(strings.confirmReset)) {
						return;
					}

					var defaults = strings.defaults;
					var fields = {
						"sitecare-maintenance-title": defaults.title,
						"sitecare-maintenance-message": defaults.message,
						"sitecare-maintenance-schedule-start": "",
						"sitecare-maintenance-schedule-end": "",
						"sitecare-maintenance-contact-email": "",
						"sitecare-maintenance-contact-phone": "",
						"sitecare-maintenance-facebook-url": "",
						"sitecare-maintenance-instagram-url": "",
						"sitecare-maintenance-linkedin-url": "",
						"sitecare-maintenance-footer-text": "",
						"sitecare-maintenance-background-color": defaults.background_color,
						"sitecare-maintenance-text-color": defaults.text_color,
						"sitecare-maintenance-layout-width": defaults.layout_width
					};

					var enabledField = document.getElementById("sitecare-maintenance-enabled");
					var scheduleEnabledField = document.getElementById("sitecare-maintenance-schedule-enabled");
					var logoIdField = document.getElementById("sitecare-maintenance-logo-id");
					var logoPreview = document.getElementById("sitecare-maintenance-logo-preview");
					var logoRemoveButton = document.getElementById("sitecare-maintenance-logo-remove");

					if (enabledField) {
						enabledField.checked = false;
					}

					if (scheduleEnabledField) {
						scheduleEnabledField.checked = false;
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
				});
			}

			var scheduleEnabled = document.getElementById("sitecare-maintenance-schedule-enabled");
			var scheduleStart = document.getElementById("sitecare-maintenance-schedule-start");
			var scheduleEnd = document.getElementById("sitecare-maintenance-schedule-end");

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
		});'
	);
}
add_action( 'admin_enqueue_scripts', 'sitecare_maintenance_enqueue_admin_assets' );

/**
 * Adds the plugin settings page under Settings.
 *
 * @return void
 */
function sitecare_maintenance_add_admin_menu() {
	add_options_page(
		esc_html__( 'SiteCare Maintenance Mode', 'sitecare-maintenance-mode' ),
		esc_html__( 'SiteCare Maintenance', 'sitecare-maintenance-mode' ),
		'manage_options',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_render_settings_page'
	);
}
add_action( 'admin_menu', 'sitecare_maintenance_add_admin_menu' );

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
function sitecare_maintenance_filter_settings_redirect( $location ) {
	if ( ! isset( $_POST['option_page'] ) ) {
		return $location;
	}

	$option_page = sanitize_text_field( wp_unslash( $_POST['option_page'] ) );

	if ( 'sitecare_maintenance_settings' !== $option_page ) {
		return $location;
	}

	$location = remove_query_arg( 'settings-updated', $location );

	return add_query_arg( 'sitecare_maintenance_saved', '1', $location );
}
add_filter( 'wp_redirect', 'sitecare_maintenance_filter_settings_redirect' );

/**
 * Renders admin success notices for this settings page.
 *
 * @return void
 */
function sitecare_maintenance_render_admin_notices() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! isset( $_GET['page'] ) || 'sitecare-maintenance-mode' !== sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
		return;
	}

	$message = '';

	if ( isset( $_GET['sitecare_maintenance_saved'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['sitecare_maintenance_saved'] ) ) ) {
		$message = __( 'SiteCare Maintenance Mode settings saved.', 'sitecare-maintenance-mode' );
	}

	if ( '' === $message ) {
		return;
	}
	?>
	<div class="notice notice-success is-dismissible">
		<p><?php echo esc_html( $message ); ?></p>
	</div>
	<?php
}
add_action( 'admin_notices', 'sitecare_maintenance_render_admin_notices' );

/**
 * Renders the General section description.
 *
 * @return void
 */
function sitecare_maintenance_render_general_intro() {
	echo '<p>' . esc_html__( 'Turn maintenance mode on immediately, or schedule it to turn on automatically between a start and end time. The manual toggle works immediately and can keep maintenance mode on even outside the scheduled window.', 'sitecare-maintenance-mode' ) . '</p>';
}

/**
 * Renders the Content section description.
 *
 * @return void
 */
function sitecare_maintenance_render_content_intro() {
	echo '<p>' . esc_html__( 'Control the main text visitors see on the maintenance page.', 'sitecare-maintenance-mode' ) . '</p>';
}

/**
 * Renders the Contact Details section description.
 *
 * @return void
 */
function sitecare_maintenance_render_contact_intro() {
	echo '<p>' . esc_html__( 'Add optional ways for visitors to contact you while the site is offline. Empty fields are hidden automatically.', 'sitecare-maintenance-mode' ) . '</p>';
}

/**
 * Renders the Branding & Design section description.
 *
 * @return void
 */
function sitecare_maintenance_render_design_intro() {
	echo '<p>' . esc_html__( 'Add simple branding and adjust the basic look of the maintenance page.', 'sitecare-maintenance-mode' ) . '</p>';
}

/**
 * Renders the enable checkbox field.
 *
 * @return void
 */
function sitecare_maintenance_render_enabled_field() {
	$options = sitecare_maintenance_get_options();
	?>
	<label for="sitecare-maintenance-enabled">
		<input
			type="checkbox"
			id="sitecare-maintenance-enabled"
			name="<?php echo esc_attr( SITECARE_MAINTENANCE_OPTION ); ?>[enabled]"
			value="1"
			<?php checked( 1, (int) $options['enabled'] ); ?>
		/>
		<?php esc_html_e( 'Enable maintenance mode', 'sitecare-maintenance-mode' ); ?>
	</label>
	<p class="description">
		<?php esc_html_e( 'Leave this unchecked while you are editing or previewing the page. Check it only when public visitors should see maintenance mode.', 'sitecare-maintenance-mode' ); ?>
	</p>
	<?php
}

/**
 * Renders the maintenance page title field.
 *
 * @return void
 */
function sitecare_maintenance_render_title_field() {
	$options = sitecare_maintenance_get_options();
	?>
	<input
		type="text"
		id="sitecare-maintenance-title"
		class="regular-text"
		name="<?php echo esc_attr( SITECARE_MAINTENANCE_OPTION ); ?>[title]"
		value="<?php echo esc_attr( $options['title'] ); ?>"
	/>
	<p class="description">
		<?php esc_html_e( 'This appears as the main heading on the maintenance page.', 'sitecare-maintenance-mode' ); ?>
	</p>
	<?php
}

/**
 * Renders the maintenance page message field.
 *
 * @return void
 */
function sitecare_maintenance_render_message_field() {
	$options = sitecare_maintenance_get_options();
	?>
	<textarea
		id="sitecare-maintenance-message"
		class="large-text"
		rows="5"
		name="<?php echo esc_attr( SITECARE_MAINTENANCE_OPTION ); ?>[message]"
	><?php echo esc_textarea( $options['message'] ); ?></textarea>
	<p class="description">
		<?php esc_html_e( 'Use {site name} to automatically show the WordPress site name.', 'sitecare-maintenance-mode' ); ?>
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
function sitecare_maintenance_render_text_input( $field_id, $option_key, $type, $description ) {
	$options = sitecare_maintenance_get_options();
	?>
	<input
		type="<?php echo esc_attr( $type ); ?>"
		id="<?php echo esc_attr( 'sitecare-maintenance-' . $field_id ); ?>"
		class="regular-text"
		name="<?php echo esc_attr( SITECARE_MAINTENANCE_OPTION ); ?>[<?php echo esc_attr( $option_key ); ?>]"
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
function sitecare_maintenance_render_contact_email_field() {
	sitecare_maintenance_render_text_input(
		'contact-email',
		'contact_email',
		'email',
		__( 'Displayed as a clickable email link when provided.', 'sitecare-maintenance-mode' )
	);
}

/**
 * Renders the contact phone field.
 *
 * @return void
 */
function sitecare_maintenance_render_contact_phone_field() {
	sitecare_maintenance_render_text_input(
		'contact-phone',
		'contact_phone',
		'text',
		__( 'Displayed as a clickable phone link when provided.', 'sitecare-maintenance-mode' )
	);
}

/**
 * Renders the Facebook URL field.
 *
 * @return void
 */
function sitecare_maintenance_render_facebook_url_field() {
	sitecare_maintenance_render_text_input(
		'facebook-url',
		'facebook_url',
		'url',
		__( 'Displayed as a Facebook icon link when provided.', 'sitecare-maintenance-mode' )
	);
}

/**
 * Renders the Instagram URL field.
 *
 * @return void
 */
function sitecare_maintenance_render_instagram_url_field() {
	sitecare_maintenance_render_text_input(
		'instagram-url',
		'instagram_url',
		'url',
		__( 'Displayed as an Instagram icon link when provided.', 'sitecare-maintenance-mode' )
	);
}

/**
 * Renders the LinkedIn URL field.
 *
 * @return void
 */
function sitecare_maintenance_render_linkedin_url_field() {
	sitecare_maintenance_render_text_input(
		'linkedin-url',
		'linkedin_url',
		'url',
		__( 'Displayed as a LinkedIn icon link when provided.', 'sitecare-maintenance-mode' )
	);
}

/**
 * Renders the footer text field.
 *
 * @return void
 */
function sitecare_maintenance_render_footer_text_field() {
	sitecare_maintenance_render_text_input(
		'footer-text',
		'footer_text',
		'text',
		__( 'Displayed below the contact section when provided.', 'sitecare-maintenance-mode' )
	);
}

/**
 * Renders the logo upload field.
 *
 * @return void
 */
function sitecare_maintenance_render_logo_field() {
	$options  = sitecare_maintenance_get_options();
	$logo_id  = absint( $options['logo_attachment_id'] );
	$logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'thumbnail' ) : '';
	?>
	<input
		type="hidden"
		id="sitecare-maintenance-logo-id"
		name="<?php echo esc_attr( SITECARE_MAINTENANCE_OPTION ); ?>[logo_attachment_id]"
		value="<?php echo esc_attr( $logo_id ); ?>"
	/>
	<p>
		<img
			id="sitecare-maintenance-logo-preview"
			src="<?php echo esc_url( $logo_url ); ?>"
			alt="<?php esc_attr_e( 'Selected logo preview', 'sitecare-maintenance-mode' ); ?>"
			style="max-width: 160px; height: auto; <?php echo empty( $logo_url ) ? 'display: none;' : ''; ?>"
		/>
	</p>
	<p>
		<button type="button" class="button" id="sitecare-maintenance-logo-upload">
			<?php esc_html_e( 'Select Logo', 'sitecare-maintenance-mode' ); ?>
		</button>
		<button
			type="button"
			class="button"
			id="sitecare-maintenance-logo-remove"
			style="<?php echo empty( $logo_url ) ? 'display: none;' : ''; ?>"
		>
			<?php esc_html_e( 'Remove Logo', 'sitecare-maintenance-mode' ); ?>
		</button>
	</p>
	<p class="description">
		<?php esc_html_e( 'Displayed above the maintenance page title when provided.', 'sitecare-maintenance-mode' ); ?>
	</p>
	<?php
}

/**
 * Renders the background color field.
 *
 * @return void
 */
function sitecare_maintenance_render_background_color_field() {
	$options = sitecare_maintenance_get_options();
	?>
	<input
		type="text"
		id="sitecare-maintenance-background-color"
		class="sitecare-maintenance-color-field"
		name="<?php echo esc_attr( SITECARE_MAINTENANCE_OPTION ); ?>[background_color]"
		value="<?php echo esc_attr( $options['background_color'] ); ?>"
		data-default-color="#f5f5f5"
	/>
	<p class="description">
		<?php esc_html_e( 'Choose the background color shown behind the maintenance page content.', 'sitecare-maintenance-mode' ); ?>
	</p>
	<?php
}

/**
 * Renders the schedule enable checkbox.
 *
 * @return void
 */
function sitecare_maintenance_render_schedule_enabled_field() {
	$options = sitecare_maintenance_get_options();
	?>
	<label for="sitecare-maintenance-schedule-enabled">
		<input
			type="checkbox"
			id="sitecare-maintenance-schedule-enabled"
			name="<?php echo esc_attr( SITECARE_MAINTENANCE_OPTION ); ?>[schedule_enabled]"
			value="1"
			<?php checked( 1, (int) $options['schedule_enabled'] ); ?>
		/>
		<?php esc_html_e( 'Enable scheduled maintenance', 'sitecare-maintenance-mode' ); ?>
	</label>
	<p class="description">
		<?php esc_html_e( 'When enabled, maintenance mode turns on automatically between the start and end time below. The manual toggle can still turn maintenance mode on immediately.', 'sitecare-maintenance-mode' ); ?>
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
function sitecare_maintenance_render_schedule_datetime_field( $field_id, $option_key, $description ) {
	$options = sitecare_maintenance_get_options();
	?>
	<input
		type="datetime-local"
		id="<?php echo esc_attr( 'sitecare-maintenance-' . $field_id ); ?>"
		name="<?php echo esc_attr( SITECARE_MAINTENANCE_OPTION ); ?>[<?php echo esc_attr( $option_key ); ?>]"
		value="<?php echo esc_attr( $options[ $option_key ] ); ?>"
	/>
	<p class="description">
		<?php echo esc_html( $description ); ?>
		<?php
		printf(
			/* translators: %s: WordPress timezone string. */
			esc_html__( ' Times use the WordPress site timezone: %s.', 'sitecare-maintenance-mode' ),
			esc_html( sitecare_maintenance_get_timezone_label() )
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
function sitecare_maintenance_render_schedule_start_field() {
	sitecare_maintenance_render_schedule_datetime_field(
		'schedule-start',
		'schedule_start',
		__( 'Choose when scheduled maintenance should begin.', 'sitecare-maintenance-mode' )
	);
}

/**
 * Renders the schedule end field.
 *
 * @return void
 */
function sitecare_maintenance_render_schedule_end_field() {
	sitecare_maintenance_render_schedule_datetime_field(
		'schedule-end',
		'schedule_end',
		__( 'Choose when scheduled maintenance should end automatically.', 'sitecare-maintenance-mode' )
	);
}

/**
 * Renders the text color field.
 *
 * @return void
 */
function sitecare_maintenance_render_text_color_field() {
	$options = sitecare_maintenance_get_options();
	?>
	<input
		type="text"
		id="sitecare-maintenance-text-color"
		class="sitecare-maintenance-color-field"
		name="<?php echo esc_attr( SITECARE_MAINTENANCE_OPTION ); ?>[text_color]"
		value="<?php echo esc_attr( $options['text_color'] ); ?>"
		data-default-color="#111111"
	/>
	<p class="description">
		<?php esc_html_e( 'Choose the main text color for headings, messages, and footer text.', 'sitecare-maintenance-mode' ); ?>
	</p>
	<?php
}

/**
 * Renders the layout width dropdown.
 *
 * @return void
 */
function sitecare_maintenance_render_layout_width_field() {
	$options       = sitecare_maintenance_get_options();
	$layout_widths = sitecare_maintenance_get_layout_widths();
	?>
	<select name="<?php echo esc_attr( SITECARE_MAINTENANCE_OPTION ); ?>[layout_width]" id="sitecare-maintenance-layout-width">
		<?php foreach ( $layout_widths as $value => $layout ) : ?>
			<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $options['layout_width'], $value ); ?>>
				<?php echo esc_html( $layout['label'] ); ?>
			</option>
		<?php endforeach; ?>
	</select>
	<p class="description">
		<?php esc_html_e( 'Controls the maximum width of the maintenance page content.', 'sitecare-maintenance-mode' ); ?>
	</p>
	<?php
}

/**
 * Renders the plugin settings page.
 *
 * @return void
 */
function sitecare_maintenance_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to access this page.', 'sitecare-maintenance-mode' ) );
	}

	$preview_url = sitecare_maintenance_get_preview_url();
	$options     = sitecare_maintenance_get_options();
	$is_enabled  = sitecare_maintenance_is_enabled();
	?>
	<div class="wrap sitecare-maintenance-admin-page">
		<h1><?php esc_html_e( 'SiteCare Maintenance Mode', 'sitecare-maintenance-mode' ); ?></h1>
		<?php settings_errors( SITECARE_MAINTENANCE_OPTION ); ?>
		<p class="sitecare-maintenance-status <?php echo esc_attr( $is_enabled ? 'is-on' : 'is-off' ); ?>">
			<?php
			echo esc_html(
				$is_enabled
					? __( 'Maintenance mode is currently ON', 'sitecare-maintenance-mode' )
					: __( 'Maintenance mode is currently OFF', 'sitecare-maintenance-mode' )
			);
			?>
		</p>
		<?php if ( $is_enabled ) : ?>
			<div class="notice notice-warning inline">
				<p><?php esc_html_e( 'Maintenance mode is active. Public visitors may see the maintenance page until manual mode is turned off or the schedule ends.', 'sitecare-maintenance-mode' ); ?></p>
			</div>
		<?php endif; ?>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'sitecare_maintenance_settings' );
			do_settings_sections( 'sitecare-maintenance-mode' );
			submit_button( esc_html__( 'Save Settings', 'sitecare-maintenance-mode' ) );
			?>
		</form>
		<div class="sitecare-maintenance-actions-panel">
			<h2><?php esc_html_e( 'Preview & Reset', 'sitecare-maintenance-mode' ); ?></h2>
			<p><?php esc_html_e( 'Preview the current saved maintenance page, or reset the visible form fields before saving.', 'sitecare-maintenance-mode' ); ?></p>
			<p>
				<a class="button button-secondary" href="<?php echo esc_url( $preview_url ); ?>" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Preview Maintenance Page', 'sitecare-maintenance-mode' ); ?>
				</a>
			</p>
			<h3><?php esc_html_e( 'Reset Settings', 'sitecare-maintenance-mode' ); ?></h3>
			<p><?php esc_html_e( 'Reset the visible form fields to default values. Nothing is saved until you click Save Settings.', 'sitecare-maintenance-mode' ); ?></p>
			<button type="button" class="button button-secondary" id="sitecare-maintenance-reset-fields">
				<?php esc_html_e( 'Reset Settings', 'sitecare-maintenance-mode' ); ?>
			</button>
		</div>
	</div>
	<?php
}

/**
 * Checks whether maintenance mode is enabled.
 *
 * @return bool
 */
function sitecare_maintenance_is_enabled() {
	$options = sitecare_maintenance_get_options();

	return ! empty( $options['enabled'] ) || sitecare_maintenance_is_schedule_active( $options );
}

/**
 * Checks whether scheduled maintenance is currently active.
 *
 * @param array|null $options Optional plugin options.
 * @return bool
 */
function sitecare_maintenance_is_schedule_active( $options = null ) {
	if ( null === $options ) {
		$options = sitecare_maintenance_get_options();
	}

	if ( empty( $options['schedule_enabled'] ) ) {
		return false;
	}

	$start_timestamp = sitecare_maintenance_schedule_datetime_to_timestamp( $options['schedule_start'] );
	$end_timestamp   = sitecare_maintenance_schedule_datetime_to_timestamp( $options['schedule_end'] );

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
function sitecare_maintenance_format_page_text( $text ) {
	return str_replace( '{site name}', get_bloginfo( 'name' ), $text );
}

/**
 * Builds a protected URL for previewing the maintenance page.
 *
 * @return string
 */
function sitecare_maintenance_get_preview_url() {
	$preview_url = add_query_arg(
		'sitecare_maintenance_preview',
		'1',
		home_url( '/' )
	);

	return wp_nonce_url(
		$preview_url,
		'sitecare_maintenance_preview',
		'sitecare_maintenance_preview_nonce'
	);
}

/**
 * Checks whether the current request is asking for preview mode.
 *
 * @return bool
 */
function sitecare_maintenance_is_preview_request() {
	return isset( $_GET['sitecare_maintenance_preview'] );
}

/**
 * Verifies the preview nonce from the current request.
 *
 * @return bool
 */
function sitecare_maintenance_has_valid_preview_nonce() {
	if ( ! isset( $_GET['sitecare_maintenance_preview_nonce'] ) ) {
		return false;
	}

	$nonce = sanitize_text_field( wp_unslash( $_GET['sitecare_maintenance_preview_nonce'] ) );

	return (bool) wp_verify_nonce( $nonce, 'sitecare_maintenance_preview' );
}

/**
 * Builds the social links that have been provided.
 *
 * @param array $options Plugin options.
 * @return array
 */
function sitecare_maintenance_get_social_links( $options ) {
	$platforms = array(
		'facebook_url'  => array(
			'label' => __( 'Facebook', 'sitecare-maintenance-mode' ),
			'icon'  => 'dashicons-facebook-alt',
		),
		'instagram_url' => array(
			'label' => __( 'Instagram', 'sitecare-maintenance-mode' ),
			'icon'  => 'dashicons-instagram',
		),
		'linkedin_url'  => array(
			'label' => __( 'LinkedIn', 'sitecare-maintenance-mode' ),
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
function sitecare_maintenance_get_layout_max_width( $layout_width ) {
	$layout_widths = sitecare_maintenance_get_layout_widths();

	if ( ! array_key_exists( $layout_width, $layout_widths ) ) {
		$layout_width = sitecare_maintenance_default_options()['layout_width'];
	}

	return $layout_widths[ $layout_width ]['max_width'];
}

/**
 * Sends headers for the visitor-facing maintenance response.
 *
 * A 503 status tells search engines the site is temporarily unavailable, not
 * permanently changed. Retry-After asks crawlers to come back later.
 *
 * @return void
 */
function sitecare_maintenance_send_unavailable_headers() {
	status_header( 503 );
	nocache_headers();
	header( 'Retry-After: 3600' );
}

/**
 * Decides whether the current request should bypass maintenance mode.
 *
 * @return bool
 */
function sitecare_maintenance_should_bypass() {
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

	return false;
}

/**
 * Renders the maintenance page layout.
 *
 * @param int $status_code HTTP status code to send.
 * @return void
 */
function sitecare_maintenance_render_page( $status_code = 503 ) {
	$status_code = absint( $status_code );
	$is_unavailable = ( 503 === $status_code );

	if ( $is_unavailable ) {
		sitecare_maintenance_send_unavailable_headers();
	} else {
		status_header( $status_code );
	}

	$options = sitecare_maintenance_get_options();
	$title   = sitecare_maintenance_format_page_text( $options['title'] );
	$message = sitecare_maintenance_format_page_text( $options['message'] );
	$social_links = sitecare_maintenance_get_social_links( $options );
	$has_contact  = ! empty( $options['contact_email'] ) || ! empty( $options['contact_phone'] ) || ! empty( $social_links );
	$logo_url     = ! empty( $options['logo_attachment_id'] ) ? wp_get_attachment_image_url( absint( $options['logo_attachment_id'] ), 'medium' ) : '';
	$max_width    = sitecare_maintenance_get_layout_max_width( $options['layout_width'] );

	wp_enqueue_style( 'dashicons' );
	wp_enqueue_style(
		'sitecare-maintenance-page',
		SITECARE_MAINTENANCE_URL . 'assets/sitecare-maintenance.css',
		array( 'dashicons' ),
		SITECARE_MAINTENANCE_VERSION
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
		<?php wp_print_styles( array( 'dashicons', 'sitecare-maintenance-page' ) ); ?>
		<style>
			:root {
				--sitecare-maintenance-background: <?php echo esc_attr( $options['background_color'] ); ?>;
				--sitecare-maintenance-text: <?php echo esc_attr( $options['text_color'] ); ?>;
				--sitecare-maintenance-width: <?php echo esc_attr( $max_width ); ?>;
			}
		</style>
	</head>
	<body class="sitecare-maintenance-body">
		<main class="sitecare-maintenance-page">
			<?php if ( ! empty( $logo_url ) ) : ?>
				<img
					class="sitecare-maintenance-logo"
					src="<?php echo esc_url( $logo_url ); ?>"
					alt="<?php echo esc_attr( sprintf( __( '%s logo', 'sitecare-maintenance-mode' ), get_bloginfo( 'name' ) ) ); ?>"
				/>
			<?php endif; ?>
			<h1><?php echo esc_html( $title ); ?></h1>
			<p class="sitecare-maintenance-message"><?php echo esc_html( $message ); ?></p>
			<?php if ( $has_contact ) : ?>
				<section class="sitecare-maintenance-contact" aria-labelledby="sitecare-maintenance-contact-heading">
					<h2 id="sitecare-maintenance-contact-heading"><?php esc_html_e( 'Contact Us', 'sitecare-maintenance-mode' ); ?></h2>
					<?php if ( ! empty( $options['contact_email'] ) || ! empty( $options['contact_phone'] ) ) : ?>
						<ul class="sitecare-maintenance-contact-list">
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
						<ul class="sitecare-maintenance-social-list">
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
			<?php endif; ?>
			<?php if ( ! empty( $options['footer_text'] ) ) : ?>
				<footer class="sitecare-maintenance-footer">
					<?php echo esc_html( sitecare_maintenance_format_page_text( $options['footer_text'] ) ); ?>
				</footer>
			<?php endif; ?>
		</main>
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
function sitecare_maintenance_maybe_render_page() {
	if ( sitecare_maintenance_is_preview_request() ) {
		if ( ! current_user_can( 'manage_options' ) || ! sitecare_maintenance_has_valid_preview_nonce() ) {
			wp_die(
				esc_html__( 'You do not have permission to preview the maintenance page.', 'sitecare-maintenance-mode' ),
				esc_html__( 'Preview not allowed', 'sitecare-maintenance-mode' ),
				array( 'response' => 403 )
			);
		}

		sitecare_maintenance_render_page( 200 );
	}

	if ( ! sitecare_maintenance_is_enabled() || sitecare_maintenance_should_bypass() ) {
		return;
	}

	sitecare_maintenance_render_page( 503 );
}
add_action( 'template_redirect', 'sitecare_maintenance_maybe_render_page' );
