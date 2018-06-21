<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Wp_Global_Nav
 *
 * @wordpress-plugin
 * Plugin Name:       Global Navigation
 * Plugin URI:        https://wordpress-global-nav-67052.firebaseapp.com/
 * Description:       A WordPress Plugin.
 * Version:           1.0.0
 * Author:            Samuli Ristimäki
 * Author URI:        https://samuliristimaki.life
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       wp-global-nav
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WP_GLOBAL_NAV_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-global-nav-activator.php
 */
function activate_wp_global_nav() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-global-nav-activator.php';
	Wp_Global_Nav_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-global-nav-deactivator.php
 */
function deactivate_wp_global_nav() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-global-nav-deactivator.php';
	Wp_Global_Nav_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_global_nav' );
register_deactivation_hook( __FILE__, 'deactivate_wp_global_nav' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-global-nav.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_global_nav() {

	$plugin = new Wp_Global_Nav();
	$plugin->run();

	// WP API v1.
	include_once 'includes/wp-api-menus-v1.php';
	// WP API v2.
	include_once 'includes/wp-api-menus-v2.php';

	if ( ! function_exists ( 'wp_rest_menus_init' ) ) :

		/**
		 * Init JSON REST API Menu routes.
		 *
		 * @since 1.0.0
		 */
		function wp_rest_menus_init() {

			if ( ! defined( 'JSON_API_VERSION' ) && ! in_array( 'json-rest-api/plugin.php', get_option( 'active_plugins' ) ) ) {
				$class = new WP_REST_Menus();
				add_filter( 'rest_api_init', array( $class, 'register_routes' ) );
			} else {
				$class = new WP_JSON_Menus();
				add_filter( 'json_endpoints', array( $class, 'register_routes' ) );
			}
		}

		add_action( 'init', 'wp_rest_menus_init' );

	endif;

	/* Plugin admin menu settings */

	add_action( 'admin_menu', 'wp_global_nav_add_admin_menu' );
	add_action( 'admin_init', 'wp_global_nav_settings_init' );


	function wp_global_nav_add_admin_menu() { 

		add_options_page( 'Global Navigation', 'Global Navigation', 'manage_options', 'global_navigation', 'wp_global_nav_options_page' );

	}


	function wp_global_nav_settings_init() { 

		register_setting( 'pluginPage', 'wp_global_nav_settings' );

		add_settings_section(
			'wp_global_nav_pluginPage_section', 
			'', 
			'', 
			'pluginPage'
		);

		add_settings_field( 
			'wp_global_nav_textarea_field_0', 
			__( 'CSS', 'wordpress' ), 
			'wp_global_nav_textarea_field_0_render', 
			'pluginPage', 
			'wp_global_nav_pluginPage_section' 
		);


	}


	function wp_global_nav_textarea_field_0_render() { 

		$options = get_option( 'wp_global_nav_settings' );
		?>
		<textarea cols='40' rows='5' name='wp_global_nav_settings[wp_global_nav_textarea_field_0]'> 
			<?php echo $options['wp_global_nav_textarea_field_0']; ?>
		</textarea>
		<?php

	}


	function wp_global_nav_options_page() { 

		?>
		<form action='options.php' method='post'>

			<h1>Global Navigation</h1>

			<?php
			settings_fields( 'pluginPage' );
			do_settings_sections( 'pluginPage' );
			submit_button();

			echo __( 'Dummy example element:', 'wordpress' );
			echo "<br>";

			echo '<code>&lt;wp-global-nav url="http://localhost:5000/wp-json/wp-global-nav/v2/menus/12"&gt;&lt;/wp-global-nav&gt;';
			echo '&lt;script src="http://localhost:5000/wp-global-nav.js"&gt;&lt;/script&gt;</code>';
			echo "<br>";

			echo '<wp-global-nav url="http://localhost:5000/wp-json/wp-global-nav/v2/menus/"></wp-global-nav>';
			echo '<script src="https://wordpress-global-nav-67052.firebaseapp.com/wp-global-nav.js"></script>';

			?>

			<a href="https://github.com/samuliristimaki/wordpress-global-nav-plugin" style="position: absolute; bottom: 0;">Project source</a>

		</form>
		<?php

	}

	function wp_global_nav_add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=global_navigation">' . __( 'Settings' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	$plugin = plugin_basename( __FILE__ );
	add_filter( "plugin_action_links_$plugin", 'wp_global_nav_add_settings_link' );

}
run_wp_global_nav();
