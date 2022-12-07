<?php

namespace PizzaPool_Orders;

/**
 * The shortcode class
 */
class Product {

	/**
	 * Initialize the class
	 */
	public function __construct() {
		add_action( 'woocommerce_product_options_pricing', array( $this, 'pizzapool_dine_in_price_field' ) );
		add_action( 'woocommerce_process_product_meta',  array( $this, 'pizzapool_dine_in_price_field_save' ) );
		add_filter( 'woocommerce_is_purchasable', array( $this, 'is_purchasable_by_time' ), 10, 2 );
	}


	/**
	 * Add custom price field for dine in orders
	 *
	 * @return void
	 */
	public function pizzapool_dine_in_price_field() {
		global $woocommerce, $post;
		 woocommerce_wp_text_input(
			array(
				'id'        => '_dine_price',
				'value'     => get_post_meta( $post->ID, '_dine_price', true ),
				'label'     => __( 'Dine-in price', 'pizzapool_orders' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'data_type' => 'price',
			)
		);
	}

	/**
	 * Save custom price field for dine in orders
	 *
	 * @param int $post_id
	 * 
	 * @return void
	 */
	public function pizzapool_dine_in_price_field_save( $post_id ) {
		$_dine_price = $_POST['_dine_price'];
		update_post_meta( $post_id, '_dine_price', esc_attr( $_dine_price ) );
	}

	/**
	 * Disable add to cart button based on opening time.
	 *
	 * @param bool $purchasable
	 * @param object $product
	 * 
	 * @return void
	 */
	public function is_purchasable_by_time( $purchasable, $product ) {
		$pizzapool_openings = array(
			'Sunday'    => false,
			'Monday'    => false,
			'Tuesday'   => false,
			'Wednesday' => false,
			'Thursday'  => array(
				'start' => '16:00',
				'end'   => '22:00'
			),
			'Friday'    => array(
				'start' => '12:00',
				'end'   => '22:00'
			),
			'Saturday'  => array(
				'start' => '12:00',
				'end'   => '22:00'
			)
		);
		$today = date("l");
		if ( $pizzapool_openings[$today] ) {
			$start = strtotime($pizzapool_openings[$today]['start']);
			$end   = strtotime($pizzapool_openings[$today]['end']);
			$now = time();
			if($now >= $start && $now <= $end) {
				return true;
			} else {
				return false;
			}
		}
		return false;
	}
}
