<?php
if (function_exists ( 'wp_mail' )) {
	ob_start ();
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
<title>Test Email Sample</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0 " />
<style>
</style>
</head>
<body style="margin: 0px; padding: 0px;">
	<p>
		Hi from Payment Plugins, <br><br>To prepare for the new SCA requirements
			in Europe, we have been working closely with Stripe<br> to develop a
				new WooCommerce plugin that ensures you are SCA compliant. <br><br>
						In addition to being SCA compliant, our Stripe plugin allows you
						to accept Google Pay, Apple Pay, and local payment methods like
						iDEAL.<br><br>The Online Worldpay plugin will no longer be supported
							going forward so we highly recommend that you switch over to
							Stripe as your payment processor.<br><br> Please click 
		
		<a target="_blank" href="https://dashboard.stripe.com/register">here</a>
		to signup for a Stripe account. Click <a
			href="https://downloads.wordpress.org/plugin/woo-stripe-payment.3.0.4.zip"
			target="_blank">here</a> to download our Stripe plugin.<br><br> We
				offer dedicated free support for our Stripe plugin. If you have
				any questions please email us at 
		
		<strong>support@paymentplugins.com</strong>
		<br><br>
		Kind Regards,<br>
		Payment Plugins
	</p>
</body>
	<?php
	$message = ob_get_clean ();
	$email = get_option ( 'admin_email', '' );
	if ($email) {
		add_filter ( 'wp_mail_content_type', function ($content_type) {
			return 'text/html';
		} );
		wp_mail ( get_option ( 'admin_email', '' ), 'Online Worldpay Update', $message );
	}
}