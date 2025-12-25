(function() {
	let initPayPalButton = function() {
		let buttonConfiguration = {};
		let phrases = {};

		let productInfoDetails = document.querySelector('div.product-info-details');
		let newbutton = document.createElement('div');
		newbutton.id = 'paypal-newbutton';
		let separator = document.createElement('div');
		separator.id = 'paypal-separator';
		let introLabel = document.createElement('span');
		introLabel.id = 'paypal-introlabel';
		introLabel.innerText = phrases.separatorLabel;
		separator.appendChild(introLabel);
		newbutton.appendChild(separator);
		let ppecs = document.createElement('span'),
			logoImg = document.createElement('img'),
			ppImg = document.createElement('img'),
			pplink = document.createElement('a');
		logoImg.src = buttonConfiguration.logoUrl;
		logoImg.id = 'paypal-logoimg';
		ppImg.src = buttonConfiguration.ppUrl;
		ppImg.id = 'paypal-nameimg';
		ppecs.id = 'paypal-ecsbutton';
		ppecs.style.background = buttonConfiguration.backgroundColor;
		ppecs.style.border = '1px solid ' + buttonConfiguration.borderColor;
		ppecs.appendChild(ppImg);
		ppecs.appendChild(document.createTextNode(' '));
		ppecs.appendChild(logoImg);
		pplink.classList.add('paypal-ecs-button');
		pplink.href = 'shopping_cart.php?display_mode=ecs';
		pplink.addEventListener('click', function(e) {
			let productsId = document.querySelector('#products-id').value,
				productsQty = document.querySelector('#attributes-calc-quantity').value;
			e.preventDefault();
			document.location = 'shop.php?do=PayPalHub/AddToCart&products_id=' + productsId + '&qty=' + productsQty;
		});
		pplink.append(ppecs);
		newbutton.appendChild(pplink);
		
		let bc = document.querySelector('div.button-container');
		bc.parentNode.insertBefore(newbutton, bc.nextSibling);
	};
	
	document.addEventListener('DOMContentLoaded', function() {
		let hasProperties = document.querySelectorAll('div.properties-selection-form').length > 0,
			hasAttributes = document.querySelectorAll('fieldset.attributes').length > 0,
			hasCustomizer = document.querySelectorAll('#customizer-form').length > 0,
			hasFetch = ("fetch" in window);
		
		if(hasFetch && !hasProperties && !hasAttributes && !hasCustomizer)
		{
			initPayPalButton();
		}
	});
})();
