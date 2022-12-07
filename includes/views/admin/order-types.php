<?php
       woocommerce_wp_select( array(
        'id' => 'pizzapool_order_type',
        'label' => __('Order Type:', 'pizzapool_orders'),
        'value' => $pizzapool_order_type,
        'options' => $order_types,
        'wrapper_class' => 'form-field-wide'
    ) );
?>