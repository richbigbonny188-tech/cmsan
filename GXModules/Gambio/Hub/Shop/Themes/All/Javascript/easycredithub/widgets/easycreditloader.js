easycredithub.widgets.module(
	'easycreditloader',
    [
        gambio.source + '/libs/events'
    ],
	function (data) {
		'use strict';
		
		if (window.NodeList && !NodeList.prototype.forEach) {
			NodeList.prototype.forEach = Array.prototype.forEach;
		}
		
		let currentScript = document.currentScript;
		
		// ########## VARIABLE INITIALIZATION ##########
		var $this = $(this),
			defaults = {},
			options = $.extend(true, {}, defaults, data),
			module = {};
		
		module.init = function (done) {
			if (typeof(easyCreditParameters) !== 'undefined' && typeof(window.rkPlugin) === 'undefined') {
				let pluginSrc = 'https://ratenkauf.easycredit.de/ratenkauf/js/ratenrechner/v1/ratenrechner.js';
				let pluginScript = document.createElement('script');
				pluginScript.src = pluginSrc;
				pluginScript.onload = pluginLoaded;
				
				if(currentScript) {
					currentScript.parentNode.insertBefore(pluginScript, currentScript);
				} else {
					document.querySelector('script:last-of-type').parentNode.appendChild(pluginScript);
				}
			}
			done();
		};
		
		let pluginLoaded = function() {
			let placeholderElement = document.querySelector('div.easycredit-rr-container');
			if(!placeholderElement) {
				return;
			}
			if(!easyCreditParameters) {
				return;
			}
			
			let styleSrc = 'https://ratenkauf.easycredit.de/ratenkauf/js/ratenrechner/v1/ratenrechner.css';
			let pluginStyle = document.createElement('link');
			pluginStyle.rel = 'stylesheet';
			pluginStyle.type = 'text/css';
			pluginStyle.href = styleSrc;
			if(currentScript) {
				currentScript.parentNode.insertBefore(pluginStyle, currentScript);
			} else {
				document.querySelector('script:last-of-type').parentNode.appendChild(pluginStyle);
			}
            placeholderElement.style.backgroundImage = 'url("https://static.easycredit.de/content/image/logo/ratenkauf_42_55.png")';
			
			rkPlugin.anzeige(placeholderElement.id, {
				webshopId: easyCreditParameters.shopId,
				finanzierungsbetrag: easyCreditParameters.finanzierungsbetrag,
				euro: easyCreditParameters.euro,
				textVariante: easyCreditParameters.textVariante,
				linkText: easyCreditParameters.linkText,
				fromText: easyCreditParameters.fromText,
				installmentTemplate: easyCreditParameters.installmentTemplate,
				widgetTemplate: easyCreditParameters.widgetTemplate
			});

			if(easyCreditParameters.hasVariants) {
				$(window).on(jse.libs.theme.events.STICKYBOX_CONTENT_CHANGE(), function() {
                    let currentPriceText = $('form.product-info div.current-price-container').text();
                    let extractedPrice = currentPriceText.replace(/[0-9,.]+ (EUR|€) pro .*/, '')
                        .replace(/.*?([0-9,.]+ (EUR|€)(?!.*(EUR|€).*)).*/, '$1')
                        .replace(/[^0-9]*/g, '');
                    let currentPrice = parseInt(extractedPrice) / 100;
					console.log('price changed to ' + currentPrice);
					$(placeholderElement).empty();
					rkPlugin.anzeige(placeholderElement.id, {
						webshopId: easyCreditParameters.shopId,
						finanzierungsbetrag: currentPrice,
						euro: easyCreditParameters.euro,
						textVariante: easyCreditParameters.textVariante,
						linkText: easyCreditParameters.linkText,
						fromText: easyCreditParameters.fromText,
						installmentTemplate: easyCreditParameters.installmentTemplate,
						widgetTemplate: easyCreditParameters.widgetTemplate
					});
				});
			}
		}
		
		return module;
	}
);
