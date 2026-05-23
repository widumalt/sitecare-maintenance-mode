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
		'title'              => 'We will be back soon.',
		'message'            => '{site name} is temporarily offline for maintenance. Please check back later.',
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

	return array(
		'enabled'            => empty( $input['enabled'] ) ? 0 : 1,
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

	add_settings_section(
		'sitecare_maintenance_main_section',
		esc_html__( 'Maintenance Mode', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_settings_intro',
		'sitecare-maintenance-mode'
	);

	add_settings_field(
		'sitecare_maintenance_enabled',
		esc_html__( 'Status', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_enabled_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_main_section'
	);

	add_settings_field(
		'sitecare_maintenance_title',
		esc_html__( 'Page Title', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_title_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_main_section'
	);

	add_settings_field(
		'sitecare_maintenance_message',
		esc_html__( 'Message', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_message_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_main_section'
	);

	add_settings_field(
		'sitecare_maintenance_contact_email',
		esc_html__( 'Contact Email', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_contact_email_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_main_section'
	);

	add_settings_field(
		'sitecare_maintenance_contact_phone',
		esc_html__( 'Contact Phone', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_contact_phone_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_main_section'
	);

	add_settings_field(
		'sitecare_maintenance_facebook_url',
		esc_html__( 'Facebook URL', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_facebook_url_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_main_section'
	);

	add_settings_field(
		'sitecare_maintenance_instagram_url',
		esc_html__( 'Instagram URL', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_instagram_url_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_main_section'
	);

	add_settings_field(
		'sitecare_maintenance_linkedin_url',
		esc_html__( 'LinkedIn URL', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_linkedin_url_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_main_section'
	);

	add_settings_field(
		'sitecare_maintenance_footer_text',
		esc_html__( 'Footer Text', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_footer_text_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_main_section'
	);

	add_settings_field(
		'sitecare_maintenance_logo',
		esc_html__( 'Logo', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_logo_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_main_section'
	);

	add_settings_field(
		'sitecare_maintenance_background_color',
		esc_html__( 'Background Color', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_background_color_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_main_section'
	);

	add_settings_field(
		'sitecare_maintenance_text_color',
		esc_html__( 'Text Color', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_text_color_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_main_section'
	);

	add_settings_field(
		'sitecare_maintenance_layout_width',
		esc_html__( 'Layout Width', 'sitecare-maintenance-mode' ),
		'sitecare_maintenance_render_layout_width_field',
		'sitecare-maintenance-mode',
		'sitecare_maintenance_main_section'
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

	$script_data = array(
		'title'      => __( 'Choose Logo', 'sitecare-maintenance-mode' ),
		'buttonText' => __( 'Use this logo', 'sitecare-maintenance-mode' ),
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
 * Renders the short introduction on the settings page.
 *
 * @return void
 */
function sitecare_maintenance_render_settings_intro() {
	echo '<p>' . esc_html__( 'Enable maintenance mode to show logged-out visitors a simple offline page. Administrators can still view the normal site.', 'sitecare-maintenance-mode' ) . '</p>';
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
		<?php esc_html_e( 'When enabled, logged-out visitors will see a maintenance page. Administrators with manage_options bypass it.', 'sitecare-maintenance-mode' ); ?>
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
		class="sitecare-maintenance-color-field"
		name="<?php echo esc_attr( SITECARE_MAINTENANCE_OPTION ); ?>[background_color]"
		value="<?php echo esc_attr( $options['background_color'] ); ?>"
		data-default-color="#f5f5f5"
	/>
	<?php
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
		class="sitecare-maintenance-color-field"
		name="<?php echo esc_attr( SITECARE_MAINTENANCE_OPTION ); ?>[text_color]"
		value="<?php echo esc_attr( $options['text_color'] ); ?>"
		data-default-color="#111111"
	/>
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
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'SiteCare Maintenance Mode', 'sitecare-maintenance-mode' ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'sitecare_maintenance_settings' );
			do_settings_sections( 'sitecare-maintenance-mode' );
			submit_button( esc_html__( 'Save Settings', 'sitecare-maintenance-mode' ) );
			?>
		</form>
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

	return ! empty( $options['enabled'] );
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
 * Shows the maintenance page to normal frontend visitors when enabled.
 *
 * @return void
 */
function sitecare_maintenance_maybe_render_page() {
	if ( ! sitecare_maintenance_is_enabled() || sitecare_maintenance_should_bypass() ) {
		return;
	}

	status_header( 503 );
	nocache_headers();
	header( 'Retry-After: 3600' );

	$options = sitecare_maintenance_get_options();
	$title   = sitecare_maintenance_format_page_text( $options['title'] );
	$message = sitecare_maintenance_format_page_text( $options['message'] );
	$social_links = sitecare_maintenance_get_social_links( $options );
	$has_contact  = ! empty( $options['contact_email'] ) || ! empty( $options['contact_phone'] ) || ! empty( $social_links );
	$logo_url     = ! empty( $options['logo_attachment_id'] ) ? wp_get_attachment_image_url( absint( $options['logo_attachment_id'] ), 'medium' ) : '';
	$max_width    = sitecare_maintenance_get_layout_max_width( $options['layout_width'] );
	?>
	<!doctype html>
	<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo esc_html( $title ); ?></title>
		<link rel="stylesheet" href="<?php echo esc_url( includes_url( 'css/dashicons.min.css' ) ); ?>">
		<style>
			body {
				margin: 0;
				min-height: 100vh;
				display: flex;
				align-items: center;
				justify-content: center;
				background: <?php echo esc_attr( $options['background_color'] ); ?>;
				color: <?php echo esc_attr( $options['text_color'] ); ?>;
				font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
				line-height: 1.5;
			}
			.sitecare-maintenance-page {
				max-width: <?php echo esc_attr( $max_width ); ?>;
				padding: 32px;
				text-align: center;
			}
			.sitecare-maintenance-logo {
				display: block;
				max-width: 180px;
				max-height: 120px;
				width: auto;
				height: auto;
				margin: 0 auto 24px;
			}
			.sitecare-maintenance-page h1 {
				margin: 0 0 16px;
				font-size: 32px;
				color: <?php echo esc_attr( $options['text_color'] ); ?>;
			}
			.sitecare-maintenance-page p {
				margin: 0;
				font-size: 18px;
				color: <?php echo esc_attr( $options['text_color'] ); ?>;
			}
			.sitecare-maintenance-contact {
				margin-top: 28px;
				padding-top: 24px;
				border-top: 1px solid #dcdcde;
			}
			.sitecare-maintenance-contact h2 {
				margin: 0 0 12px;
				font-size: 20px;
			}
			.sitecare-maintenance-contact-list,
			.sitecare-maintenance-social-list {
				display: flex;
				flex-wrap: wrap;
				gap: 12px;
				justify-content: center;
				margin: 0;
				padding: 0;
				list-style: none;
			}
			.sitecare-maintenance-contact-list a,
			.sitecare-maintenance-social-list a {
				display: inline-flex;
				align-items: center;
				gap: 6px;
				color: #2271b1;
				text-decoration: none;
			}
			.sitecare-maintenance-contact-list a:hover,
			.sitecare-maintenance-social-list a:hover {
				color: #135e96;
				text-decoration: underline;
			}
			.sitecare-maintenance-social-list {
				margin-top: 14px;
			}
			.sitecare-maintenance-footer {
				margin-top: 24px;
				font-size: 14px;
				color: <?php echo esc_attr( $options['text_color'] ); ?>;
			}
		</style>
	</head>
	<body>
		<main class="sitecare-maintenance-page">
			<?php if ( ! empty( $logo_url ) ) : ?>
				<img
					class="sitecare-maintenance-logo"
					src="<?php echo esc_url( $logo_url ); ?>"
					alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
				/>
			<?php endif; ?>
			<h1><?php echo esc_html( $title ); ?></h1>
			<p><?php echo esc_html( $message ); ?></p>
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
add_action( 'template_redirect', 'sitecare_maintenance_maybe_render_page' );
