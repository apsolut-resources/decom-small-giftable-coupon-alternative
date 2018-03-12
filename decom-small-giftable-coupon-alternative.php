<?php

/**
 * Plugin Name:   Decom Small Giftable Coupon Alternative
 * Plugin URI:    https://decom.ba
 * Description:
 * Author:        Decom.ba
 * Author URI:    https://decom.ba
 * Contributors:  Decom.ba
 * Version:       0.0.1
 * Text Domain:   decomplugin
 * Domain Path:   /languages
 * License:       GPLv2 or later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Giftable for WooCommerce is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Giftable for WooCommerce is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Giftable for WooCommerce. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}


/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {


	add_action( 'wp_enqueue_scripts', 'ajax_decom_sgcr_enqueue_scripts' );
	function ajax_decom_sgcr_enqueue_scripts() {
		if ( is_cart() && ( !WC()->cart->get_cart_contents_count() == 0 ) ) {
			wp_enqueue_script( 'decom-sgca', plugins_url( '/decom-small-giftable-coupon-alternative.js', __FILE__ ), array('jquery'), '0.0.1', true );
			wp_localize_script( 'decom-sgca', 'decomsgcapost', array(
				'ajax_url' => admin_url( 'admin-ajax.php' )
			));
        }
	}


	add_action( 'wp_ajax_nopriv_post_decom_sgca', 'post_decom_sgca' );
	add_action( 'wp_ajax_post_decom_sgca', 'post_decom_sgca' );
	function post_decom_sgca() {


		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			if( WC()->cart->applied_coupons &&  !WC()->cart->cart_contents_count == 0  ){
               $data = array(
                       'lets_hide_giftable' => 'true'
               );
				wp_send_json( $data );
            }
		}
		die();
	}


	// Put your plugin code here
	add_action( 'wp', 'decom_giftable_coupon_fix');

	function decom_giftable_coupon_fix() {

		if ( is_cart() ) {

			if( WC()->cart->applied_coupons &&  !WC()->cart->cart_contents_count == 0  ){
				// just default things
				$has_item = false;
				$is_product_id = true;
				foreach( WC()->cart->get_cart() as $key => $item ){
					// Check if the items to remove is in cart
					$product_id_real = wc_get_product( $item['product_id'] );
					//check is it a GIFT - remove all GIFTS
					if(  $product_id_real->is_type( 'dgfw_gift' ) ){
						$has_item = true;
						$key_to_remove = $key;
					}
					if( $has_item && $is_product_id ){
						WC()->cart->remove_cart_item( $key_to_remove );
					}
				}
				//wc_add_notice( __( 'Coupon code used, <strong>GIFTs</strong> have been removed from cart.', 'decomplugin' ), 'notice' );

				add_filter( 'body_class', 'decom_giftable_add_remove_class' );
				function decom_giftable_add_remove_class( $classes ) {
					$classes[] = 'remove-giftable-slider';
					return $classes;
				}
				add_filter( 'wp_footer', 'decom_giftable_add_remove_style', 99 );
				function decom_giftable_add_remove_style( ) { ?>
					<style>
			                .remove-giftable-slider .dgfw-available-gifts {
			                     display: none;
			                }
					</style>
				<?php }

				add_filter( 'wp_header', 'decom_giftable_add_remove_js', 99 );
				function decom_giftable_add_remove_js( ) { ?>
                    <script>
                        if ( jQuery('.remove-giftable-slider').length) {
                            jQuery('#dgfw-choose-gift').parent().hide();
                        }
                    </script>
				<?php }



			}
			else {

			}

		}
	}

}
