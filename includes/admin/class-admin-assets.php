<?php
class WC_OWP_Admin_Assets {

	public function __construct() {
		add_action ( 'admin_enqueue_scripts', array( 
				$this, 'enqueue_scripts' 
		) );
	}

	public function enqueue_scripts() {
		$screen = get_current_screen ();
		$screen_id = $screen ? $screen->id : '';
		$sections = array( 'online_worldpay', 
				'online_worldpay_paypal', 
				'online_worldpay_api', 
				'online_worldpay_webhook' 
		);
		wp_register_script ( 'worldpay-admin-settings', worldpay ()->assets_url () . 'js/admin/gateway-settings.js', array( 
				'jquery' 
		), worldpay ()->version (), true );
		wp_register_style ( 'worldpay-admin-style', worldpay ()->assets_url () . 'css/admin/admin.css', array(), worldpay ()->version () );
		if (strpos ( $screen_id, 'wc-settings' ) != false) {
			if (isset ( $_REQUEST[ 'section' ] ) && in_array ( $_REQUEST[ 'section' ], $sections )) {
				wp_enqueue_script ( 'worldpay-admin-settings' );
				wp_enqueue_style ( 'worldpay-admin-style' );
				wp_enqueue_script ( 'worldpay-notices', worldpay ()->assets_url () . 'js/admin/notices.js', array( 
						'jquery', 'jquery-blockui' 
				), worldpay ()->version (), true );
				wp_localize_script ( 'worldpay-notices', 'worldpay_notice_params', array( 
						'url' => admin_url ( 'admin-ajax.php?action=worldpay_notice' ), 
						'download_stripe' => admin_url ( 'admin-ajax.php?action=worldpay_download_stripe' ) 
				) );
			}
		}
		if ($screen_id === 'shop_order') {
			wp_register_script ( 'worldpay-admin-order-metaboxes', worldpay ()->assets_url () . 'js/admin/meta-boxes-order.js', array( 
					'jquery' 
			), worldpay ()->version (), true );
			wp_enqueue_script ( 'worldpay-admin-order-metaboxes' );
			wp_enqueue_style ( 'worldpay-admin-style' );
			
			wp_localize_script ( 'worldpay-admin-order-metaboxes', 'worldpay_admin_meta_boxes', array( 
					'_wpnonce' => wp_create_nonce ( 'wp_rest' ), 
					'capture_url' => wp_nonce_url ( get_rest_url ( null, 'worldpay/v1/admin/order/capture-charge' ), 'wp_rest', '_wpnonce' ), 
					'cancel_url' => wp_nonce_url ( get_rest_url ( null, 'worldpay/v1/admin/order/cancel' ), 'wp_rest', '_wpnonce' ) 
			) );
		}
	}
}
new WC_OWP_Admin_Assets ();