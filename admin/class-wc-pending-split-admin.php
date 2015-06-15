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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wc-pending-split-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_register_script($this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wc-pending-split-admin.js', array( 'jquery' ,'underscore', 'backbone'), $this->version, false );

		$screen = get_current_screen();
		if ( in_array( str_replace( 'edit-', '', $screen->id ), wc_get_order_types( 'order-meta-boxes' ) ) ) {
			$js_data = array(
				'split_order_items_nonce' => wp_create_nonce( 'split_order_items' ),
			);
			wp_localize_script( $this->plugin_name, 'wc_split_order', $js_data );
			wp_enqueue_script( $this->plugin_name);
		}

	}

	/**
	 * Displays the plugin's button within the failed order's area.
	 *
	 * @since    1.0.0
	 */
	public function split_order_button( $order ) {
			$order_id = $order->id;
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wc-pending-split-button.php';
	}
	/**
	 * Handler for "split_order_items" ajax call.
	 *
	 * @since    1.0.0
	 */
	public function split_order_items_callback() {
		//Bail if missing information
		if(!isset($_POST['order_id']) || !isset($_POST['order_item_ids'])) {
			return false;
		}

		//Sanitize input
		$order_id = intval( $_POST['order_id'] );
		$order_item_ids = array();
		foreach ($_POST['order_item_ids'] as $item_id) {
			$order_item_ids[] = intval($item_id);
		}

		//Creating new order
		$new_order = $this->create_sub_order($order_id);
		
		foreach ($order_item_ids as $item_id) {
			//update the order id of the selected item to new created split order.
			wc_update_order_item($item_id, array('order_id'=>$new_order));
		}
		echo "success";

	}
	
	/**
	 * Creates an order and insert it to the database.
	 *
	 * @since    1.0.0
	 */
	private function create_sub_order($id = false){
		$args = array();
		if ($id) {
			$original_order = new WC_Order($id);
			
		}
		$order = wc_create_order();

		return $order->id;

	}
}

	
