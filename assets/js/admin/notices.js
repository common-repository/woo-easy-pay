(function($) {

	function Notice() {
		this.params = worldpay_notice_params;
		$(document.body).on('click', '.wp-stripe-notice .notice-dismiss', this.remove_notice.bind(this));
		$(document.body).on('click', '.wp-download-stripe', this.download_stripe.bind(this));
	}

	Notice.prototype.remove_notice = function(e) {
		$.post(this.params.url, {}, function() {

		}.bind(this))
	}

	Notice.prototype.download_stripe = function(e) {
		e.preventDefault();
		$.blockUI({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		$.post(this.params.download_stripe, {}, function(response){
			window.location.href = response.redirect;
			$.unblockUI();
		}.bind(this), 'json');
	}

	new Notice();

}(jQuery))