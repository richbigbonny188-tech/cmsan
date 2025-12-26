document.addEventListener('DOMContentLoaded', function() {
	let continueButton = document.querySelector('#checkout_payment div.continue_button input[type="submit"]'),
		continueButtonText = continueButton.value;

	let paymentItemClickListener = function() {
		let selected_payment = this.querySelector('input[name="payment"]');
		if(null !== selected_payment) {
			if (selected_payment.value === 'gambio_hub' && selected_payment.dataset.module_code === 'PayPal2InstallmentsHub') {
				continueButton.value = '#apply_for_installments';
			} else {
				continueButton.value = continueButtonText;
			}
		}
	};
	
	let paymentItems = document.querySelectorAll('#checkout_payment input[name="payment"], #checkout_payment li.list-group-item');
	paymentItems.forEach(function(paymentItem) {
		paymentItem.addEventListener('click', paymentItemClickListener)
	});
	
	document.querySelectorAll('#checkout_payment li').forEach(function(paymentOption) {
		if(paymentOption.querySelector('input[name="payment"]:checked')) {
			paymentOption.dispatchEvent(new Event('click'));
		}
	})
});
