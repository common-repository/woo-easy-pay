<?php
/**
 * Class that handles all updates. From time to time, data must be converted, deleted, etc.
 * @author User
 *
 */
class WC_Worldpay_Admin_Update {

	private static $updates = array( 
			'2.0.0' => 'updates/update-2.0.0.php', 
			'2.0.6' => 'updates/update-2.0.6.php' 
	);

	public static function init() {
		add_action ( 'admin_init', array( __CLASS__, 
				'update_plugin' 
		) );
		add_action ( 'admin_notices', array( __CLASS__, 
				'display_update_notice' 
		) );
		add_action ( 'wp_ajax_worldpay_notice', array( 
				__CLASS__, 'remove_notice' 
		) );
		add_action ( 'wp_ajax_worldpay_download_stripe', array( 
				__CLASS__, 'download_stripe' 
		) );
	}

	public static function update_plugin() {
		$current_version = get_option ( 'online_worldpay_version', '1.2.6' );
		/**
		 * If the current version is less than the latest version, perform the upgrade.
		 */
		if (version_compare ( $current_version, worldpay ()->version (), '<' )) {
			foreach ( self::$updates as $version => $path ) {
				/*
				 * If the current version is less than the version in the loop, then perform upgrade.
				 */
				if (version_compare ( $current_version, $version, '<' )) {
					include worldpay ()->base_path () . 'includes/admin/' . $path;
					$current_version = $version;
				}
			}
			// save latest version.
			update_option ( 'online_worldpay_version', worldpay ()->version () );
		}
		
		if (isset ( $_GET[ 'worldpay_upgrade_dismiss' ] ) && $_GET[ 'worldpay_upgrade_dismiss' ] == 'true') {
			delete_option ( 'worldpay_donations_message' );
		}
	}

	public static function display_update_notice() {
		if (get_option ( 'worldpay_stripe_notice', 1 ) == 0 ) {
			return;
		}
		if(isset($_GET['tab'], $_GET['section']) && $_GET['tab'] === 'checkout' && strstr($_GET['section'] , 'online_worldpay')){
			return;
		}
		?>
<div class="notice notice-success is-dismissible wp-stripe-notice" style="border-left-color: #6772e5">
	<div style="margin-top: 10px">
		<img style="width: 70px"
			src="<?php echo worldpay()->assets_url() . 'img/stripe_logo.svg'?>" />
	</div>
	<p style="font-size: 14px;">
		How is your business thinking about your site's conversion rate and
		new regulatory requirements involving SCA? <br> <br> By switching to
		our Stripe Payment Plugin, not only will you be SCA ready but your
		business can start offering Apple Pay, Google Pay, and local payment
		options such as iDEAL. <br> <br> Click <a class="wp-download-stripe"
			href="">here</a> to download our Stripe plugin and click <a
			href="https://dashboard.stripe.com/register" target="_blank">here</a>
		to register for your Stripe account.
	</p>
</div>
<?php
		add_action ( 'admin_footer', function () {
			wp_enqueue_script ( 'worldpay-notices', worldpay ()->assets_url () . 'js/admin/notices.js', array( 
					'jquery', 'jquery-blockui' 
			), worldpay ()->version (), true );
			wp_localize_script ( 'worldpay-notices', 'worldpay_notice_params', array( 
					'url' => admin_url ( 'admin-ajax.php?action=worldpay_notice' ), 
					'download_stripe' => admin_url ( 'admin-ajax.php?action=worldpay_download_stripe' ) 
			) );
		} );
	}

	public static function remove_notice() {
		update_option ( 'worldpay_stripe_notice', 0 );
	}

	public static function download_stripe() {
		include_once ( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		include_once ( ABSPATH . 'wp-admin/includes/plugin-install.php' );
		
		$api = plugins_api ( 'plugin_information', array( 
				'slug' => 'woo-stripe-payment', 
				'fields' => array( 'sections' => false 
				) 
		) );
		if (is_wp_error ( $api )) {
			wp_send_json ( array( 'success' => false, 
					'redirect' => 'https://wordpress.org/plugins/woo-stripe-payment/' 
			), 200 );
		}
		$upgrader = new Plugin_Upgrader ( new Worldpay_Plugin_Upgrader_Skin () );
		$result = $upgrader->install ( $api->download_link );
		if (is_wp_error ( $result ) || false == $result) {
			wp_send_json ( array( 'success' => false, 
					'redirect' => 'https://wordpress.org/plugins/woo-stripe-payment/' 
			), 200 );
		} else {
			self::remove_notice ();
			// activate the plugin
			$plugin_name = 'woo-stripe-payment/stripe-payments.php';
			wp_send_json ( array( 'success' => true, 
					'redirect' => admin_url ( 'plugins.php?action=activate&plugin=woo-stripe-payment/stripe-payments.php&plugin_status=all&_wpnonce=' . wp_create_nonce('activate-plugin_' . $plugin_name) ) 
			), 200 );
		}
	}
}
WC_Worldpay_Admin_Update::init ();

include_once ( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

class Worldpay_Plugin_Upgrader_Skin extends Plugin_Upgrader_Skin{
	
	public function header(){}
	
	public function footer(){}
	
	public function feedback($string, ...$args){
		// do nothing
	}
	
	protected function decrement_update_count($type){}
}

