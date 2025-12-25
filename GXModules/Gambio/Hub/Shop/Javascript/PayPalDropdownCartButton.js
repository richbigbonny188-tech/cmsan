(function() {
	let initPayPalCartButton = function() {
		let dropdown = document.querySelector('#cart-container ul.dropdown-menu');
		if(!dropdown) {
			return;
		}

		let buttonConfiguration = {};
		let phrases = {};

		let newbutton = document.createElement('div');
		newbutton.classList.add('dropdown-footer');
        newbutton.classList.add('dropdown-paypal-button');
		newbutton.style.clear = 'both';
		newbutton.style.textAlign = 'center';
		newbutton.style.padding = '1ex 0 1em';
        newbutton.style.backgroundColor = 'inherit';
		let separator = document.createElement('div');
		separator.style.borderTop = '1px solid #ccc';
		separator.style.margin = '0 15px 20px';
		separator.style.position = 'relative';
		separator.style.backgroundColor = 'inherit';
		let introLabel = document.createElement('span');
		introLabel.style.display = 'block';
		introLabel.style.position = 'absolute';
		introLabel.style.top = '50%';
		introLabel.style.left = '50%';
		introLabel.style.transform = 'translateX(-50%) translateY(-50%)';
		introLabel.innerText = phrases.separatorLabel;
		introLabel.style.backgroundColor = 'inherit';
		separator.appendChild(introLabel);
		newbutton.appendChild(separator);
		let ppecs = document.createElement('span'),
			logoImg = document.createElement('img'),
			ppImg = document.createElement('img'),
			pplink = document.createElement('a');
		logoImg.src = buttonConfiguration.logoUrl;
		logoImg.style.height = '20px';
		ppImg.src = buttonConfiguration.ppUrl;
		ppImg.style.height = '20px';
		ppecs.style.background = buttonConfiguration.backgroundColor;
		ppecs.style.border = '1px solid ' + buttonConfiguration.borderColor;
		ppecs.style.padding = '7px 2em';
		ppecs.style.marginLeft = '15px';
		ppecs.style.marginRight = '15px';
		ppecs.appendChild(ppImg);
		ppecs.appendChild(document.createTextNode(' '));
		ppecs.appendChild(logoImg);
		pplink.classList.add('paypal-ecs-button');
		pplink.href = 'shopping_cart.php?display_mode=ecs';
		pplink.appendChild(ppecs);
		newbutton.appendChild(pplink);
		let newli = document.createElement('li');
        newli.classList.add('dropdown-paypal-li');
        newli.style.backgroundColor = 'inherit';
		newli.appendChild(newbutton);

		dropdown.appendChild(newli);
	};

	document.addEventListener('DOMContentLoaded', function() {
		initPayPalCartButton();
        if (typeof $ !== 'undefined') {
            let $body = $('body');
            $body.on('CART_DROPDOWN_UPDATE', function() {
                window.setTimeout(initPayPalCartButton, 3000);
            });
        }
	});
})();


