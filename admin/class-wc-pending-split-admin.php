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
		//Security
		check_ajax_referer( 'split_order_items', 'security' );

		//Bail if missing information
		if(!isset($_POST['order_id']) || !isset($_POST['items'])) {
			return false;
		}
		//Bail if user can't edit order.
		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			die(-1);
		}
		// Parse the jQuery serialized items
		$items= array();
		parse_str( $_POST['items'], $items );
		
		// Storing the original order
		$order_id = $_POST['order_id'];
		$original_order = new WC_Order($order_id);

		//Creating new order
		$new_order = $this->create_sub_order($original_order);

		foreach($items['order_item_id'] as $order_item_id){
			// Get original line item specifications (product and qty)
			$product_id = $original_order->get_item_meta($order_item_id,'_product_id', true);
			$max_qty    = $original_order->get_item_meta($order_item_id,'_qty', true);
			
			// Validate the WC_product object of the current item to edit
			$_product   = wc_get_product( $product_id );
			if($_product){
				$new_qty = (isset($items["order_item_qty"][$order_item_id])) ? $items["order_item_qty"][$order_item_id] : 1;
				// Add the new line to the newly created order.
				$new_order->add_product($_product, $new_qty);
				
				// Add order note for future reference.
				$new_order->add_order_note( sprintf( __( 'Split Order created from original order #%s', 'woocommerce' ), $order_id) );

				// Update the line out of the original order
				$updated_qty = $max_qty - $new_qty;
				
				if($updated_qty > 0) {
					$args['qty'] = $updated_qty;
					$original_order->update_product( $order_item_id, $_product, $args);
				}else {
					// All the available quantity has been moved, no longer need the item.
					wc_delete_order_item($order_item_id);
				}
			}
		}



		echo "success";

	}
	
	/**
	 * Creates an order and insert it to the database.
	 *
	 * @since    1.0.0
	 */
	private function create_sub_order($original_order){
		
		$order = wc_create_order();
		// Identify the new order as a split order by a post meta
		update_post_meta( $order->id, '_is_split', true );
		update_post_meta( $order->id, '_split_from',  $original_order->id);
		/**
		 * Update the remaining metadata of newly created order via wordpress's meta function,
		 * since woocommerce's abstract order class provide a __get magic method but no set method,
		 *  we will set manually.
		 */
		update_post_meta( $order->id, '_billing_first_name', $original_order->billing_first_name);
		update_post_meta( $order->id, '_billing_last_name', $original_order->billing_last_name);
		update_post_meta( $order->id, '_billing_company', $original_order->billing_company);
		update_post_meta( $order->id, '_billing_address_1', $original_order->billing_address_1);
		update_post_meta( $order->id, '_billing_address_2', $original_order->billing_address_2);
		update_post_meta( $order->id, '_billing_city', $original_order->billing_city);
		update_post_meta( $order->id, '_billing_state', $original_order->billing_state);
		update_post_meta( $order->id, '_billing_postcode', $original_order->billing_postcode);
		update_post_meta( $order->id, '_billing_country', $original_order->billing_country);
		update_post_meta( $order->id, '_billing_phone', $original_order->billing_phone);
		update_post_meta( $order->id, '_billing_email', $original_order->billing_email);
		update_post_meta( $order->id, '_shipping_first_name', $original_order->shipping_first_name);
		update_post_meta( $order->id, '_shipping_last_name', $original_order->shipping_last_name);
		update_post_meta( $order->id, '_shipping_company', $original_order->shipping_company);
		update_post_meta( $order->id, '_shipping_address_1', $original_order->shipping_address_1);
		update_post_meta( $order->id, '_shipping_address_2', $original_order->shipping_address_2);
		update_post_meta( $order->id, '_shipping_city', $original_order->shipping_city);
		update_post_meta( $order->id, '_shipping_state', $original_order->shipping_state);
		update_post_meta( $order->id, '_shipping_postcode', $original_order->shipping_postcode);
		update_post_meta( $order->id, '_shipping_country', $original_order->shipping_country);
		update_post_meta( $order->id, '_cart_discount', $original_order->cart_discount);
		update_post_meta( $order->id, '_cart_discount_tax', $original_order->cart_discount_tax);
		update_post_meta( $order->id, '_shipping_method_title', $original_order->shipping_method_title);
		update_post_meta( $order->id, '_customer_user', $original_order->customer_user);
		update_post_meta( $order->id, '_order_discount', $original_order->order_discount);
		update_post_meta( $order->id, '_order_tax', $original_order->order_tax);
		update_post_meta( $order->id, '_order_shipping_tax', $original_order->order_shipping_tax);
		update_post_meta( $order->id, '_order_shipping', $original_order->order_shipping);
		update_post_meta( $order->id, '_order_total', $original_order->order_total);
		update_post_meta( $order->id, '_order_currency', $original_order->order_currency);
		update_post_meta( $order->id, '_payment_method', $original_order->payment_method);
		update_post_meta( $order->id, '_payment_method_title', $original_order->payment_method_title);
		update_post_meta( $order->id, '_customer_ip_address', $original_order->customer_ip_address);
		update_post_meta( $order->id, '_customer_user_agent', $original_order->customer_user_agent);
		return $order;

	}

	/**
	 * Identify and changes the names of orders created with this plugin.
	 *
	 * @since    1.0.0
	 */
	public function split_order_naming_display($name){
		if(get_post_meta( $name, '_is_split', true ) == true){
			$original_order_id = get_post_meta( $name, '_split_from', true );
			/**
			*@todo: make the "-1" a sequence in case multiple split orders (-1,-2,-3...)
			*/
			return $original_order_id."-1";
		}
		return $name;
	}
}

	
