<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://richpress.org
 * @since             1.0.0
 * @package           Wc_Pending_Split
 *
 * @wordpress-plugin
 * Plugin Name:       WC Pending Split
 * Plugin URI:        http://richpress.org/plugins/wcpendingsplit
 * Description:       Small plugin to split failed orders into smaller one through an admin dashboard widget.
 * Version:           1.0.0
 * Author:            Riadh Chougui
 * Author URI:        http://richpress.org
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc-pending-split
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wc-pending-split-activator.php
 */
function activate_wc_pending_split() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-pending-split-activator.php';
	Wc_Pending_Split_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wc-pending-split-deactivator.php
 */
function deactivate_wc_pending_split() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-pending-split-deactivator.php';
	Wc_Pending_Split_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wc_pending_split' );
register_deactivation_hook( __FILE__, 'deactivate_wc_pending_split' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wc-pending-split.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wc_pending_split() {

	$plugin = new Wc_Pending_Split();
	$plugin->run();

}
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    run_wc_pending_split();
}

