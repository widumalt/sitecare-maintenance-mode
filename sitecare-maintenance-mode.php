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
		'enabled'        => 0,
		'title'          => 'We will be back soon.',
		'message'        => '{site name} is temporarily offline for maintenance. Please check back later.',
		'contact_email'  => '',
		'contact_phone'  => '',
		'facebook_url'   => '',
		'instagram_url'  => '',
		'linkedin_url'   => '',
		'footer_text'    => '',
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

	return array(
		'enabled'        => empty( $input['enabled'] ) ? 0 : 1,
		'title'          => $title,
		'message'        => $message,
		'contact_email'  => sanitize_email( $input['contact_email'] ),
		'contact_phone'  => sanitize_text_field( $input['contact_phone'] ),
		'facebook_url'   => esc_url_raw( $input['facebook_url'] ),
		'instagram_url'  => esc_url_raw( $input['instagram_url'] ),
		'linkedin_url'   => esc_url_raw( $input['linkedin_url'] ),
		'footer_text'    => sanitize_text_field( $input['footer_text'] ),
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
}
add_action( 'admin_init', 'sitecare_maintenance_register_settings' );

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
				background: #f6f7f7;
				color: #1d2327;
				font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
				line-height: 1.5;
			}
			.sitecare-maintenance-page {
				max-width: 640px;
				padding: 32px;
				text-align: center;
			}
			.sitecare-maintenance-page h1 {
				margin: 0 0 16px;
				font-size: 32px;
			}
			.sitecare-maintenance-page p {
				margin: 0;
				font-size: 18px;
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
				color: #646970;
			}
		</style>
	</head>
	<body>
		<main class="sitecare-maintenance-page">
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
