/* --------------------------------------------------------------
   PayPalLoader.js 2023-04-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


(function() {
	if (window.NodeList && !NodeList.prototype.forEach) {
		NodeList.prototype.forEach = Array.prototype.forEach;
	}

	let currentScript = document.currentScript;
	
	let consoleInfo = function(...params) {
		if (typeof payPalButtonSettings.developmentMode !== 'boolean' || payPalButtonSettings.developmentMode === false) {
			return;
		}
		console.info('PayPalLoader', ...params);
	}

	let handleAntiqueBrowser = function() {
		consoleInfo('Sorry, antique browser not supported.');
		
		let pppay = document.querySelector('li.gambio_hub-PayPal2Hub');
		if (pppay) {
			pppay.remove();
		}
	}
	
	let initDisplayModeECS_ButtonReplace = function() {
		let paypalButtonContainer = document.querySelector('#paypal-button-container'),
			footerTotalRow = document.querySelector('table.order-total tr.footer.total'),
			newTotalRow = document.createElement('tr'),
			newTotalRowCell = document.createElement('td');
		newTotalRowCell.colSpan = '2';
		newTotalRowCell.style.width = '100%';
		newTotalRowCell.style.borderTop = 'none';
		newTotalRowCell.appendChild(paypalButtonContainer);
		newTotalRow.appendChild(newTotalRowCell);
		footerTotalRow.parentNode.appendChild(newTotalRow);
		footerTotalRow.querySelectorAll('td').forEach(function(td) {
			td.style.paddingBottom = '15px';
		});
	};

	let initDisplayModeECS = function() {
		if(window.location.search.match(/(\?|&)display_mode=ecs($|&)/)) {
			let checkoutButtons = document.querySelector('div.checkout-buttons');
			if(checkoutButtons) {
				checkoutButtons.style.display = 'none';
			}
			let checkoutSubmitButton = document.querySelector('div.shopping-cart-button a.button-submit');
			if(checkoutSubmitButton === null) {
				checkoutSubmitButton = document.querySelector('tr.checkout-button');
			}
			if(checkoutSubmitButton) {
				checkoutSubmitButton.style.display = 'none';
			}
			let shoppingCartButton = document.querySelector('div.shopping-cart-button');
			if(shoppingCartButton) {
				shoppingCartButton.classList.add('paypal-ecs-mode');
			}
			let ppiContainer = document.querySelector('div.paypalinstallmentcontainer');
			if(ppiContainer) {
				ppiContainer.style.display = 'none';
			}
			initDisplayModeECS_ButtonReplace();
		}
	}

	let initJSSDKPayPalButtonECS = function(amount) {
        if (amount === undefined) {
            amount = payPalButtonSettings.cartAmount;
        } 
        consoleInfo('initJSSDKPayPalButtonECS cart amount: ' + amount);
        if (amount < 0.01) {
            consoleInfo('ECS: not showing, cart amount too low');
            return;
        }
		let buttonContainer = document.querySelector('#paypal-button-container');
		if (!buttonContainer) {
			return;
		}
        let ecsIntro = document.querySelector('div.ecs_intro');
        if (document.querySelector('tr.checkout-button .button-disabled') !== null) {
            if (ecsIntro) {
                ecsIntro.style.display = 'none';
            }
            buttonContainer.style.display = 'none';
        } else {
            if (ecsIntro) {
                ecsIntro.style.display = 'block';
            }
            buttonContainer.style.display = 'block';
            initDisplayModeECS();
        }

		paypal.Buttons({
			style: payPalButtonSettings.style,
			createOrder: function(data, actions) {
				return fetch(payPalButtonSettings.createOrderUrl, {
					method: 'post',
					headers: {
						'Content-Type': 'application/json'
					}
					})
					.then((res) => { return res.json() })
					.then((orderdata) => {
						consoleInfo('order created: ' + orderdata.id, orderdata);
						return orderdata.id;
					});
				},
			onApprove: function(data, actions) {
				consoleInfo('Approved data:', data);
				return fetch(payPalButtonSettings.approvedOrderUrl + '&orderId=' + data.orderID, {
					method: 'post',
					headers: {
						'Content-Type': 'application/json'
					},
					body: JSON.stringify(data)
				})
					.then((res) => { return res.json(); })
					.then((responsedata) => {
						consoleInfo('response data:', responsedata);
						document.location = payPalButtonSettings.checkoutUrl;
					});
			},
			onError: function(err) {
				let ppButtonContainer = document.querySelector('#paypal-button-container');
				let ecsIntro = document.querySelector('div.ecs_intro');
				if(ppButtonContainer) {
					let errorMessage = document.querySelector('div.paypal-error');
					if(!errorMessage) {
						errorMessage = document.createElement('div');
						errorMessage.classList.add('paypal-error');
						errorMessage.classList.add('alert');
						errorMessage.classList.add('alert-warning');
						errorMessage.style.textAlign = 'left';
						errorMessage.style.color = '#555';
					}
					errorMessage.innerText = payPalText.paypalUnavailable + ' ';
					let shoppingCartButton = document.querySelector('div.shopping-cart-button');
					if(shoppingCartButton && shoppingCartButton.classList.contains('paypal-ecs-mode')) {
						let linkUrl = window.location.toString().replace('display_mode=ecs', 'display_mode=normal');
						let continueLink = document.createElement('a');
						continueLink.setAttribute('href', linkUrl);
						continueLink.innerText = payPalText.errorContinue;
						errorMessage.append(continueLink);
					}
					ppButtonContainer.parentNode.append(errorMessage);
					ppButtonContainer.style.display = 'none';
				}
				if(ecsIntro) {
					ecsIntro.style.display = 'none';
				}
			}
		}).render('#paypal-button-container');

		let observerTarget = document.querySelector('#paypal-button-container');
		let observer = new MutationObserver(function(mutations, observer) {
			mutations.forEach(function(mutation) {
				if(mutation.removedNodes.length > 0) {
					consoleInfo('re-init PayPal buttons');
                    let totalSumCell = document.querySelector('tr.total.sum td:nth-child(2)');
                    if (totalSumCell) {
                        let amount = parseFloat(totalSumCell.textContent.replace(/[^0-9]/g, '')) / 100;
                        initJSSDKPayPalButtonECS(amount);
                        initInstallmentBanners(amount);
                    } else {
                        initJSSDKPayPalButtonECS();
                        initInstallmentBanners();
                    }
				}
			});
		});
		observer.observe(observerTarget, {childList: true});
	};

    
    let addButtonContainerDecoration = function(buttonContainer, continueButtonBlock)
    {
        buttonContainer.style.width = 'auto';
        buttonContainer.style.textAlign = 'center';
        buttonContainer.style.fontStyle = 'italic';
        if (payPalText.continueToPayPal) {
            let labelToButtonDistance = 3;
            let lineHeight = '-' + window.getComputedStyle(continueButtonBlock).lineHeight;
            buttonContainer.style.marginTop = 'calc(' + lineHeight + ' - ' + labelToButtonDistance + 'px)';
            let continueLabel = document.createElement('span');
            continueLabel.classList.add('paypal-continue-label');
            continueLabel.textContent = payPalText.continueToPayPal
            continueLabel.style.paddingBottom = labelToButtonDistance + 'px'; 
            buttonContainer.appendChild(continueLabel) ;
        }
    }
    
    
	let initJSSDKPayPalButtonECM = function(continueButtonBlock)
	{
        let paypalButtonContainer = document.createElement('div');
        
        paypalButtonContainer.id = 'paypal-button-container';
        paypalButtonContainer.style.display = 'none';
        addButtonContainerDecoration(paypalButtonContainer, continueButtonBlock);
        continueButtonBlock.appendChild(paypalButtonContainer);

		paypal.Buttons({
            fundingSource: 'paypal',
			style: payPalButtonSettings.style,
			createOrder: function(data, actions) {
				return fetch(payPalButtonSettings.createOrderUrl, {
					method: 'post',
					headers: {
						'Content-Type': 'application/json'
					}
				})
					.then((res) => { return res.json() })
					.then((orderdata) => {
						consoleInfo('order created: ' + orderdata.id, orderdata);
						return orderdata.id;
					});
			},
			onApprove: function(data, actions) {
				document.querySelector('#checkout_payment input[name="PayPal2HubOrderId"]').value = data.orderID;
				document.querySelector('#checkout_payment input[name="PayPal2HubPayerId"]').value = data.payerID;
				document.querySelector('#checkout_payment').submit();
			}
		}).render('#paypal-button-container');
        
        return paypalButtonContainer;
	}

    let initPayLaterButton = function(continueButtonBlock)
    {
        let payLaterItem = document.querySelector('li.gambio_hub-PayPal2Hub-paylater');
        if (payLaterItem === null) {
            return null;
        }
    
        let paylaterButton = paypal.Buttons({
            fundingSource: 'paylater',
            style: payPalButtonSettings.style,
            createOrder: function(data, actions) {
                return fetch(payPalButtonSettings.createOrderUrl, {
                    method: 'post',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                    .then((res) => { return res.json() })
                    .then((orderdata) => {
                        consoleInfo('order created: ' + orderdata.id, orderdata);
                        return orderdata.id;
                    });
            },
            onApprove: function(data, actions) {
                document.querySelector('#checkout_payment input[name="PayPal2HubOrderId"]').value = data.orderID;
                document.querySelector('#checkout_payment input[name="PayPal2HubPayerId"]').value = data.payerID;
                document.querySelector('#checkout_payment').submit();
            }
        });
    
        if (paylaterButton.isEligible()) {
            let paylaterButtonId = 'paypal-paylater-button-container',
                paylaterButtonContainer = document.createElement('div');
            paylaterButtonContainer.id = paylaterButtonId;
            paylaterButtonContainer.style.display = 'none';
            addButtonContainerDecoration(paylaterButtonContainer, continueButtonBlock);
            continueButtonBlock.appendChild(paylaterButtonContainer);
            paylaterButton.render('#' + paylaterButtonId);
            consoleInfo('PayPal Paylater: eligible and initialized');
            return paylaterButtonContainer;
        } else {
            consoleInfo('PayPal Paylater: not eligible');
            payLaterItem.remove();
        }
        
        return null;
    }
    
    
    let initSepaButton = function(continueButtonBlock)
    {
        let sepaItem = document.querySelector('li.gambio_hub-PayPal2Hub-sepa');
        if (sepaItem === null) {
            return null;
        }
        let sepaButtonStyle = payPalButtonSettings.style;
        if (sepaButtonStyle.color === 'gold' || sepaButtonStyle.color === 'blue') {
            sepaButtonStyle.color = 'silver';
        }
        
        let sepaButton = paypal.Buttons({
            fundingSource: 'sepa',
            style: payPalButtonSettings.style,
            createOrder: function(data, actions) {
                return fetch(payPalButtonSettings.createOrderUrl, {
                    method: 'post',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                    .then((res) => { return res.json() })
                    .then((orderdata) => {
                        consoleInfo('order created: ' + orderdata.id, orderdata);
                        return orderdata.id;
                    });
            },
            onApprove: function(data, actions) {
                document.querySelector('#checkout_payment input[name="PayPal2HubOrderId"]').value = data.orderID;
                document.querySelector('#checkout_payment input[name="PayPal2HubPayerId"]').value = data.payerID;
                document.querySelector('#checkout_payment').submit();
            }
        });
        
        if (sepaButton.isEligible()) {
            let sepaButtonId = 'paypal-sepa-button-container',
                sepaButtonContainer = document.createElement('div');
            sepaButtonContainer.id = sepaButtonId;
            sepaButtonContainer.style.display = 'none';
            addButtonContainerDecoration(sepaButtonContainer, continueButtonBlock);
            continueButtonBlock.appendChild(sepaButtonContainer);
            sepaButton.render('#' + sepaButtonId);
            consoleInfo('PayPal SEPA: eligible and initialized');
            return sepaButtonContainer;
        } else {
            consoleInfo('PayPal SEPA: not eligible');
            sepaItem.remove();
        }
        
        return null;
    }
    
    
    let initCreditCardButton = function(continueButtonBlock)
    {
        let brandedCreditCardsItem = document.querySelector('li.gambio_hub-PayPal2Hub-creditcardbutton');
        if (brandedCreditCardsItem === null) {
            return null;
        }
        
        let cardButtonStyle = payPalButtonSettings.style;
        cardButtonStyle.color = 'black';
        cardButtonStyle.shape = 'pill';

        let creditCardButton = paypal.Buttons({
            fundingSource: 'card',
            style: cardButtonStyle,
            createOrder: function(data, actions) {
                return fetch(payPalButtonSettings.createOrderUrl, {
                    method: 'post',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                    .then((res) => { return res.json() })
                    .then((orderdata) => {
                        consoleInfo('order created: ' + orderdata.id, orderdata);
                        return orderdata.id;
                    });
            },
            onApprove: function(data, actions) {
                document.querySelector('#checkout_payment input[name="PayPal2HubOrderId"]').value = data.orderID;
                document.querySelector('#checkout_payment input[name="PayPal2HubPayerId"]').value = data.payerID;
                document.querySelector('#checkout_payment').submit();
            }
        });
        
        consoleInfo(creditCardButton);
        
        if (creditCardButton.isEligible()) {
            consoleInfo('Eligible for branded cc payments');
            let creditCardButtonId = 'paypal-creditcard-button-container',
                creditCardButtonContainer = document.createElement('div');
            creditCardButtonContainer.id = creditCardButtonId;
            creditCardButtonContainer.style.display = 'none';
            addButtonContainerDecoration(creditCardButtonContainer, continueButtonBlock);
            continueButtonBlock.appendChild(creditCardButtonContainer);
            creditCardButton.render('#' + creditCardButtonId);
            return creditCardButtonContainer;
        } else {
            consoleInfo('NOT eligible for branded cc payments');
            brandedCreditCardsItem.remove();
        }
        
        return null;
    }
    

    let initHostedCreditCard = function() {
        let theLabel = document.querySelector('#ppcc-card-holder-field').closest('label'),
            theDiv = document.createElement('div');
    
        theDiv.classList.add('payment-module-container');
        theLabel.parentNode.appendChild(theDiv);
        while(theLabel.hasChildNodes()) {
            theDiv.appendChild(theLabel.firstChild);
        }
        theLabel.remove();
        
        let theListItem = document.querySelector('li.gambio_hub-PayPal2Hub-creditcard');
        theListItem.style.cursor = 'pointer';
        theListItem.addEventListener('click', function() {
            $(this).find('input:radio:not(:disabled):not(.placeholder-radio)').first().prop('checked', true).trigger('change');
        });
    
        let cardHolderField = document.querySelector('#ppcc-card-holder-field'),
            fieldStyle = getComputedStyle(cardHolderField),
            orderIdElement = document.querySelector('#checkout_payment input[name="PayPal2HubOrderId"]');
    
        paypal.HostedFields.render({
            createOrder: function() {
                return fetch(payPalButtonSettings.createOrderUrl, {
                    method: 'post',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                    .then((res) => {
                        return res.json()
                    })
                    .then((orderdata) => {
                        orderIdElement.value = orderdata.id;
                        consoleInfo('credit card order created: ' + orderdata.id, orderdata);
                        return orderdata.id;
                    });
            },
            styles: {
                'input': {
                    'color': fieldStyle.color,
                    'font-size': fieldStyle.fontSize,
                    'font-family': fieldStyle.fontFamily,
                    'padding': fieldStyle.padding,
                    'line-height': fieldStyle.lineHeight
                },
                '.valid': {
                    'color': 'green'
                },
                '.invalid': {
                    'color': 'red'
                }
            },
            fields: {
                number: {
                    'selector': '#ppcc-card-number',
                    'placeholder': '4111111111111111',
                },
                cvv: {
                    'selector': '#ppcc-cvv',
                    'placeholder': '123',
                },
                expirationDate: {
                    'selector': '#ppcc-expiration-date',
                    'placeholder': 'MM/YY',
                }
            }
        }).then(function(cardFields) {
            consoleInfo('PayPal: CC fields initialized', cardFields);
            let paymentForm = document.querySelector('#checkout_payment');
            let cardFieldsSubmitted = false;
            paymentForm.addEventListener('submit', function(event) {
                if (cardFieldsSubmitted === false && paymentForm.payment.value === 'gambio_hub-PayPal2Hub-creditcard') {
                    event.preventDefault();
                    let billingAddressData = JSON.parse(document.querySelector('#ppcc-billingaddress').textContent);
                    cardFields.submit({
                        cardholderName: paymentForm.ppcc_card_holder.value,
                        billingAddress: billingAddressData,
                        contingencies: ['SCA_WHEN_REQUIRED']
                    }).then(function() {
                        // OK
                        consoleInfo('cardFields submitted');
                        cardFieldsSubmitted = true;
                        paymentForm.submit();
                    }).catch(function(err) {
                        orderIdElement.value = '';
                        consoleInfo(err);
                        alert(payPalText.errorCheckData);
                        document.querySelector('input[name="payment"]:checked').scrollIntoView(false);
                    });
                }
            });
        });
    }
    
	let initCheckoutPayment = function() {
		let continueButtonBlock = document.querySelector('#checkout_payment div.continue_button'),
			continueButton = continueButtonBlock.querySelector('input[type="submit"]'),
			continueButtonDisplay = continueButton.style.display,
			paypalButtonContainer = document.createElement('div'),
			plusContainer = document.querySelector('#gambiohub-ppplus'),
            ccForm = document.querySelector('div.paypal-cc-form'),
            orderIdElement = document.querySelector('#checkout_payment input[name="PayPal2HubOrderId"]');
        
        if (plusContainer === null && ccForm !== null) {
            consoleInfo('PayPal: Credit Card form found on page.');
            let hostedFieldsEligible = paypal.HostedFields.isEligible();
            let brandedCreditCardsOption = document.querySelector('li.gambio_hub-PayPal2Hub-creditcardbutton');
            if (hostedFieldsEligible) {
                consoleInfo('PayPal: eligible for hosted fields');
                if (brandedCreditCardsOption) {
                    brandedCreditCardsOption.remove();
                }
                
                initHostedCreditCard();
            }
            else {
                consoleInfo('PayPal: NOT eligible for hosted fields');
                let ccListItem = document.querySelector('li.gambio_hub-PayPal2Hub-creditcard');
                ccListItem.remove();
            }
        }

		if (plusContainer === null && !payPalButtonSettings.paymentApproved) {
            let backButtonBlock = continueButtonBlock.parentElement.querySelector('div.back_button');
            if (backButtonBlock !== null && backButtonBlock.classList.contains('col-xs-6')) {
                backButtonBlock.classList.remove('col-xs-6');
                backButtonBlock.classList.add('col-xs-4');
                continueButtonBlock.classList.remove('col-xs-6');
                continueButtonBlock.classList.add('col-xs-8');
                let backButton = backButtonBlock.querySelector('a.btn');
                payPalButtonSettings.style.height = parseInt(getComputedStyle(backButton).height);
            }

            let paypalButtonContainer = initJSSDKPayPalButtonECM(continueButtonBlock);
            let paylaterButtonContainer = initPayLaterButton(continueButtonBlock);
            let creditCardButtonContainer = initCreditCardButton(continueButtonBlock);
            let sepaButtonContainer = initSepaButton(continueButtonBlock);

			let paymentItemClickListener = function() {
				let selected_payment = this.querySelector('input[name="payment"]');
				if (null !== selected_payment) {
                    if (selected_payment.value === 'gambio_hub-PayPal2Hub' ||
                        (selected_payment.value === 'gambio_hub' && selected_payment.dataset.module_code
                            === 'PayPal2Hub')) {
                        continueButton.style.display = 'none';
                        paypalButtonContainer.style.display = 'block';
                        if (paylaterButtonContainer !== null) {
                            paylaterButtonContainer.style.display = 'none';
                        }
                        if (creditCardButtonContainer !== null) {
                            creditCardButtonContainer.style.display = 'none';
                        }
                        if (sepaButtonContainer !== null) {
                            sepaButtonContainer.style.display = 'none';
                        }
                    }
                    else if(selected_payment.value === 'gambio_hub-PayPal2Hub-paylater' && paylaterButtonContainer !== null) {
                        continueButton.style.display = 'none';
                        paypalButtonContainer.style.display = 'none';
                        paylaterButtonContainer.style.display = 'block';
                        if (creditCardButtonContainer !== null) {
                            creditCardButtonContainer.style.display = 'none';
                        }
                        if (sepaButtonContainer !== null) {
                            sepaButtonContainer.style.display = 'none';
                        }
                    } else if(selected_payment.value === 'gambio_hub-PayPal2Hub-sepa' && sepaButtonContainer !== null) {
                        continueButton.style.display = 'none';
                        paypalButtonContainer.style.display = 'none';
                        if (paylaterButtonContainer !== null) {
                            paylaterButtonContainer.style.display = 'none';
                        }
                        if (creditCardButtonContainer !== null) {
                            creditCardButtonContainer.style.display = 'none';
                        }
                        sepaButtonContainer.style.display = 'block';
                    } else if(selected_payment.value === 'gambio_hub-PayPal2Hub-creditcardbutton' && creditCardButtonContainer !== null) {
                        continueButton.style.display = 'none';
                        paypalButtonContainer.style.display = 'none';
                        creditCardButtonContainer.style.display = 'block';
                        if (paylaterButtonContainer !== null) {
                            paylaterButtonContainer.style.display = 'none';
                        }
                        if (sepaButtonContainer !== null) {
                            sepaButtonContainer.style.display = 'none';
                        }
                    } else {
                        continueButton.style.display = continueButtonDisplay;
                        paypalButtonContainer.style.display = 'none';
                        if (paylaterButtonContainer !== null) {
                            paylaterButtonContainer.style.display = 'none';
                        }
                        if (creditCardButtonContainer !== null) {
                            creditCardButtonContainer.style.display = 'none';
                        }
                        if (sepaButtonContainer !== null) {
                            sepaButtonContainer.style.display = 'none';
                        }
                    }
				}
			};

			let paymentItems = document.querySelectorAll('#checkout_payment input[name="payment"], #checkout_payment li.list-group-item');
			paymentItems.forEach(function(paymentItem) {
				paymentItem.addEventListener('click', paymentItemClickListener)
			});

			let paymentListEntries = document.querySelectorAll('#checkout_payment li');
			paymentListEntries.forEach(function(paymentOption) {
				if (paymentOption.querySelector('input[name="payment"]:checked') || paymentListEntries.length === 1) {
					paymentOption.dispatchEvent(new Event('click'));
				}
			});
		}
	}

	/****
	 ****  Installment Banners
 	 */

	let initInstallmentBanners = function(amount) {
		consoleInfo('Initialising PayPal Installments banners', 'CartAmount = ' + payPalBannerSettings.cartAmount);
		let banners = document.querySelectorAll('.paypal-installments');
		banners.forEach(function(bannerElement) {
			let position = bannerElement.dataset.ppinstPos;
			consoleInfo('found banner on position ' + position);
			if (payPalBannerSettings.positions && payPalBannerSettings.positions[position]) {
				consoleInfo('settings found for position ' + position, payPalBannerSettings.positions[position]);
				if (payPalBannerSettings.positions[position].style.layout === 'none') {
					consoleInfo('position disabled: ' + position);
					return;
				}

				if (amount === undefined) {
                    amount = 0.00;
                }
				if (payPalBannerSettings.cartAmount) {
					amount += payPalBannerSettings.cartAmount;
				}
                if (payPalBannerSettings.productsPrice) {
                    amount += payPalBannerSettings.productsPrice;
                } else {
					let p = document.querySelector('div.current-price-container');
					if(p) {
						let priceText = p.innerText;
						priceText = priceText.replace(/.*?[\d,.]+\s+\D{1,3}.*?([\d,.]+\s+\D{1,3})/s, '$1');
						amount += 0.01 * parseFloat(priceText.replace(/.*?(((\d{1,3}[.,])+)(\d{2})).*/, '$1').replace(/[.,]/g, ''));
						consoleInfo('Product amount for banner: ' + amount);
					}
                }
				if (amount < 99) {
					consoleInfo('Not showing PayPal Installments banner for amount ' + amount);
                    return;
				}

				if(bannerElement.classList.contains('paypal-installments-cartbottom')) {
					let observerTarget = document.querySelector('div.shopping-cart-button');
					if (observerTarget) {
						let cartSumElement = document.querySelector('tr.total.sum td:nth-child(2)');
						let amountString = '0';
						if(cartSumElement) {
							amountString = cartSumElement.textContent.trim();
						} else {
							cartSumElement = document.querySelector('tr.footer.total td:nth-child(2)');
							if(cartSumElement) {
								amountString = cartSumElement.textContent.trim()
									.replace(/(\n|\t|\.|\,)/g, '')
									.replace(/.*?([0-9.,]+)\s+EUR.*/, '$1');
							}
						}
                        amount = 0.01 * parseInt(amountString.replace(/[^0-9]/g, ''));
						consoleInfo('cart amount ' + amount);

						let observer = new MutationObserver(function(mutations, observer) {
							mutations.forEach(function(mutation) {
								if(mutation.removedNodes.length > 0) {
									consoleInfo('re-init PayPal installments banner');
									initInstallmentBanners();
								}
							});
						});
						observer.observe(observerTarget, {childList: true});
					}
				}

				paypal.Messages({
					amount: amount,
					currency: payPalBannerSettings.currency,
					style: payPalBannerSettings.positions[position].style,
					placement: payPalBannerSettings.positions[position].placement
				}).render(bannerElement)
                    .then(function() {
                        let legacyInstallmentContainer = document.querySelector('.paypalinstallmentcontainer');
                        if(legacyInstallmentContainer) {
                            legacyInstallmentContainer.remove();
                        }
                    });
			}
		});
	};

	let paypalSdkLoaded = function() {
		consoleInfo('PayPalSDK loaded');
		if(window.location.pathname.match(/shopping_cart.php/)) {
			consoleInfo('Initializing ECS button');
			initJSSDKPayPalButtonECS();
		}
		if(window.location.pathname.match(/checkout_payment.php/)) {
			consoleInfo('Initializing PayPal on payment page');
			initCheckoutPayment();
		}
		initInstallmentBanners();
	};

    let disableJavascriptFeatures = function(disable = true) {
        /*
        let pppay = document.querySelector('ul:not(.paypal3-plus-checkout) li.gambio_hub-PayPal2Hub');
        if (pppay) {
            consoleInfo('Removing PayPal payment option');
            pppay.remove();
        }
        */

        let newStyle = disable ? 'none' : 'block';
        
        let ecsButton = document.querySelector('div#paypal-newbutton');
        if (ecsButton) {
            ecsButton.style.display = newStyle;
        }

        let ccPaymentOption = document.querySelector('#checkout_payment li.gambio_hub-PayPal2Hub-creditcard');
        if (ccPaymentOption) {
            ccPaymentOption.style.display = newStyle;
        }
        
        let puiPaymentOption = document.querySelector('#checkout_payment li.gambio_hub-PayPal2Hub-pui');
        if (puiPaymentOption) {
            puiPaymentOption.style.display = newStyle;
        }
        
        let paylaterPaymentOption = document.querySelector('#checkout_payment li.gambio_hub-PayPal2Hub-paylater');
        if (paylaterPaymentOption) {
            paylaterPaymentOption.style.display = newStyle;
        }
    
        let sepaPaymentOption = document.querySelector('#checkout_payment li.gambio_hub-PayPal2Hub-sepa');
        if (sepaPaymentOption) {
            sepaPaymentOption.style.display = newStyle;
        }
    
        let ecmPaymentOption = document.querySelector('#checkout_payment li.gambio_hub-PayPal2Hub');
        if (ecmPaymentOption && disable) {
            let paypalModeInput = document.createElement('input');
            paypalModeInput.type = 'hidden';
            paypalModeInput.name = 'PayPalMode';
            paypalModeInput.value = 'sdkless';
            ecmPaymentOption.appendChild(paypalModeInput);
        }
    }
    
    let disablePayPalOptions = function(disable = true) {
        let paymentOptions = document.querySelectorAll('#checkout_payment li.list-group-item');
        paymentOptions.forEach((paymentOption, key, parent) => {
            paymentOption.classList.forEach((className) => {
                if (className.match(/.*PayPal2Hub.*/)) {
                    if (disable) {
                        paymentOption.querySelector('input[name="payment"]').setAttribute('disabled', 'disabled');
                        paymentOption.style.opacity = '0.5';
                    } else {
                        paymentOption.querySelector('input[name="payment"]').removeAttribute('disabled');
                        paymentOption.style.opacity = '1';
                    }
                }
            });
        });
    };
    
    let enablePayPalOptions = function() {
        disablePayPalOptions(false);
    }
    
    
	let readyCallback = function() {
        consoleInfo('readyCallback start');
		let jssrc = '';
        if (typeof (payPalButtonSettings) !== 'undefined' && payPalButtonSettings.jssrc) {
            jssrc = payPalButtonSettings.jssrc;
        } else if (typeof (payPalBannerSettings) !== 'undefined' && payPalBannerSettings.jssrc) {
            jssrc = payPalBannerSettings.jssrc;
        }
        let pageHasBanners = document.querySelectorAll('.paypal-installments').length > 0;
        let isCheckoutPage = window.location.pathname.match(/shopping_cart.php/) ||
            window.location.pathname.match(/checkout_payment.php/);
        
        let consentGiven = undefined;
        let purposeId = null;
        let consentDataElement = document.getElementById('paypalconsent');
        if (consentDataElement !== null) {
            let consentData = JSON.parse(consentDataElement.textContent);
            purposeId = consentData.purpose_id;
            let gxConsentsString = document.cookie.split('; ').find((row) => row.startsWith('GXConsents='));
            let gxConsents = gxConsentsString ? gxConsentsString.split('=')[1] : null;
            if (gxConsents) {
                let gxConsentsData = JSON.parse(gxConsents);
                consentGiven = gxConsentsData.purposeConsents[''+purposeId];
            }
        }
        
        if (jssrc && (pageHasBanners || isCheckoutPage) && typeof(window.paypal) === 'undefined') {
            let loadTimeout = null;
            
            let onPayPalSdkLoad = function() {
                clearTimeout(loadTimeout);
                paypalSdkLoaded();
                disableJavascriptFeatures(false);
                enablePayPalOptions();
            };
            let onPayPalSdkError = function() {
                consoleInfo('ERROR loading PayPal Javascript - blocked?');
                consoleInfo('PayPal JSSDK unavailable, disabling features');
                disableJavascriptFeatures();
                enablePayPalOptions();
            };
            window.initPayPalJSSDK = function() {
                if (typeof(window.paypal) !== 'undefined') {
                    return;
                }
                let jssdk = document.createElement('script');
                jssdk.src = payPalButtonSettings.jssrc ? payPalButtonSettings.jssrc : payPalBannerSettings.jssrc;
    
                let acdcIdentity = document.querySelector('#paypal_cc_identity');
                if (acdcIdentity !== null) {
                    let clientIdentity = JSON.parse(acdcIdentity.textContent);
                    if (typeof clientIdentity.client_token !== 'undefined') {
                        jssdk.dataset.clientToken = clientIdentity.client_token;
                    }
                }
                jssdk.onerror = onPayPalSdkError;
                jssdk.onload = onPayPalSdkLoad;
    
                if(document.currentScript) {
                    document.currentScript.parentNode.insertBefore(jssdk, document.currentScript);
                } else {
                    document.querySelector('script:last-of-type').parentNode.appendChild(jssdk);
                }
            }
            
            disablePayPalOptions();
            if (purposeId !== null) {
                consoleInfo("External PayPal Javascript is managed by CookieConsent, purpose " + purposeId);
                if (window.PayPalAsOilClearToLoad) {
                    consoleInfo('OIL has cleared PayPal to load');
                    window.initPayPalJSSDK();
                } else {
                    consoleInfo('OIL has not yet cleared PayPal to load');
                    disableJavascriptFeatures();
                    window.addEventListener('PayPalClearedToLoad', evt => {
                        consoleInfo('OIL has cleared PayPal to load (Event)');
                        window.initPayPalJSSDK();
                    });
                    if (isCheckoutPage) {
                        consoleInfo('on checkout page; waiting 5s for OIL')
                        loadTimeout = setTimeout(onPayPalSdkError, 5000);
                    }
                }
            } else {
                console.info("Not using CookieConsent integration for PayPal, loading directly");
                window.initPayPalJSSDK();
            }
		} else {
			consoleInfo('PayPal JSSDK unavailable or not required, disabling features');
            disableJavascriptFeatures();
		}
        consoleInfo('readyCallback end');
	};

	let mainCallback = ("fetch" in window) ? readyCallback : handleAntiqueBrowser;
	
	if(document.readyState === 'complete' || (document.readyState !== 'loading' && !document.documentElement.doScroll)) {
        consoleInfo('Readystate complete');
		mainCallback();
	} else {
        consoleInfo('Adding EventListener for DOMContentLoaded');
		document.addEventListener('DOMContentLoaded', mainCallback);
	}
	
}());
