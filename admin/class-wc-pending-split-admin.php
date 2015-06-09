<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://richpress.org
 * @since      1.0.0
 *
 * @package    Wc_Pending_Split
 * @subpackage Wc_Pending_Split/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wc_Pending_Split
 * @subpackage Wc_Pending_Split/admin
 * @author     Riadh Chougui <Chougui.riadh@gmail.com>
 */
class Wc_Pending_Split_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wc_Pending_Split_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wc_Pending_Split_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wc-pending-split-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wc_Pending_Split_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wc_Pending_Split_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wc-pending-split-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Displays the plugin's button within the failed order's area.
	 *
	 * @since    1.0.0
	 */
	public function split_order_button( $order ) {
		if("wc-failed" == $order->post_status) {
			$order_id = $order->id;
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wc-pending-split-button.php';
		}
	}
}
