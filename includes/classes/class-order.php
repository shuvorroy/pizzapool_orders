<?php

namespace PizzaPool_Orders;

/**
 * The shortcode class
 */
class Order {

	/**
	 * Initialize the class
	 */
	public function __construct() {
		add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'pizzapool_orders_fields' ), 10 );
		add_action( 'woocommerce_process_shop_order_meta', array( $this, 'pizzapool_store_orders_fields' ), 999 );
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'checkout_update_data' ), 999, 2  );
		add_action( 'woocommerce_cart_calculate_fees',  array( $this, 'add_first_order_discount' ), 10, 1 );
	}

	/**
	 * Display custom fields
	 * 
	 * @param object $order
	 *
	 * @return void
	 */
	public function pizzapool_orders_fields( $order ) {
		ob_start();
		$pizzapool_order_type = $order->get_meta( 'pizzapool_order_type' );
		$order_types = array(
			''   => __('Select Type', 'pizzapool_orders'),
			'dinein'   => __('Dine-in', 'pizzapool_orders'),
			'delivery' => __('Delivery', 'pizzapool_orders'),
			'takeaway' => __('Take away', 'pizzapool_orders'),
		);
		include_once PIZZAPOOLORDERS_DIR . 'includes/views/admin/order-types.php';
		echo ob_get_clean();
	}

	/**
	 * Process order
	 * 
	 * @param int $order_id
	 * 
	 * @return void
	 */
	public function pizzapool_store_orders_fields( $order_id ) {
		if ( isset( $_POST[ 'pizzapool_order_type' ] ) ) {
			update_post_meta( $order_id, 'pizzapool_order_type', wc_clean( $_POST[ 'pizzapool_order_type' ] ) );
		}

		if ( $_POST[ 'pizzapool_order_type' ] === 'dinein' ) {
			$order = new \WC_Order( $order_id );
			//Update price if dine in order.
			foreach( $order->get_items() as $item ){
				$product = $item->get_product();
				$dine_in_price = get_post_meta( $product->get_id(), '_dine_price', true );
				if ( $dine_in_price ) {
					$item_new_price = $dine_in_price * $item->get_quantity();
					$item->set_subtotal( floatval( $item_new_price ) );
					$item->set_total( floatval( $item_new_price ) );
				} 
			}

			//Update fee if dine in order type
			foreach ( $order->get_fees() as $item_fee ) {
				if ( $item_fee->get_name() == 'Service Charge' ) {
					$order->remove_item( $item_fee->get_id() );
				}
			}

			$order->calculate_totals();
			$item_fee = new \WC_Order_Item_Fee();
			$item_fee->set_name( "Service Charge" );
			$item_fee->set_amount( $order->get_total() * 0.1 ); 
			$item_fee->set_total( $order->get_total() * 0.1 ); 
			$order->add_item( $item_fee );
			$order->save();
		}
	}

	/**
	 * Save order type data
	 *
	 * @param int $order_id
	 * @param array $data
	 *
	 * @return void
	 */
	public function checkout_update_data( $order_id, $data ) {
		$order = wc_get_order( $order_id );
		foreach ( $order->get_shipping_methods() as $shipping_method ) {
			$shipping_method_id = $shipping_method->get_method_id();
			if ( 'local_pickup' == $shipping_method_id ) {
				update_post_meta( $order_id, 'pizzapool_order_type', 'takeaway' );
			}
			if ( 'flat_rate' == $shipping_method_id ) {
				update_post_meta( $order_id, 'pizzapool_order_type', 'delivery' );
			}
		}
	}


	public function add_first_order_discount( $cart_object ) {

		if ( is_admin() && ! defined( 'DOING_AJAX' ) )
			return;
	
		$cart_total = $cart_object->cart_contents_total;

		if ( is_user_logged_in() ) {
			$order_args = array(
				'customer_id' => get_current_user_id(),
				'limit' => -1,
				'return' => 'ids'
			);
			$orders = wc_get_orders($order_args);
			if ( ! count( $orders ) ) {
				$percent = 40;
				$discount = $cart_total * $percent / 100;
				$cart_object->add_fee("First Order Discount ($percent%)", -$discount, true);
			}
		}
	}
		
}
