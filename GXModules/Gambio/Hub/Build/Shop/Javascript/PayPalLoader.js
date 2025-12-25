'use strict';

/* --------------------------------------------------------------
   PayPalLoader.js 2023-04-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

(function () {
    if (window.NodeList && !NodeList.prototype.forEach) {
        NodeList.prototype.forEach = Array.prototype.forEach;
    }

    var currentScript = document.currentScript;

    var consoleInfo = function consoleInfo() {
        var _console;

        if (typeof payPalButtonSettings.developmentMode !== 'boolean' || payPalButtonSettings.developmentMode === false) {
            return;
        }

        for (var _len = arguments.length, params = Array(_len), _key = 0; _key < _len; _key++) {
            params[_key] = arguments[_key];
        }

        (_console = console).info.apply(_console, ['PayPalLoader'].concat(params));
    };

    var handleAntiqueBrowser = function handleAntiqueBrowser() {
        consoleInfo('Sorry, antique browser not supported.');

        var pppay = document.querySelector('li.gambio_hub-PayPal2Hub');
        if (pppay) {
            pppay.remove();
        }
    };

    var initDisplayModeECS_ButtonReplace = function initDisplayModeECS_ButtonReplace() {
        var paypalButtonContainer = document.querySelector('#paypal-button-container'),
            footerTotalRow = document.querySelector('table.order-total tr.footer.total'),
            newTotalRow = document.createElement('tr'),
            newTotalRowCell = document.createElement('td');
        newTotalRowCell.colSpan = '2';
        newTotalRowCell.style.width = '100%';
        newTotalRowCell.style.borderTop = 'none';
        newTotalRowCell.appendChild(paypalButtonContainer);
        newTotalRow.appendChild(newTotalRowCell);
        footerTotalRow.parentNode.appendChild(newTotalRow);
        footerTotalRow.querySelectorAll('td').forEach(function (td) {
            td.style.paddingBottom = '15px';
        });
    };

    var initDisplayModeECS = function initDisplayModeECS() {
        if (window.location.search.match(/(\?|&)display_mode=ecs($|&)/)) {
            var checkoutButtons = document.querySelector('div.checkout-buttons');
            if (checkoutButtons) {
                checkoutButtons.style.display = 'none';
            }
            var checkoutSubmitButton = document.querySelector('div.shopping-cart-button a.button-submit');
            if (checkoutSubmitButton === null) {
                checkoutSubmitButton = document.querySelector('tr.checkout-button');
            }
            if (checkoutSubmitButton) {
                checkoutSubmitButton.style.display = 'none';
            }
            var shoppingCartButton = document.querySelector('div.shopping-cart-button');
            if (shoppingCartButton) {
                shoppingCartButton.classList.add('paypal-ecs-mode');
            }
            var ppiContainer = document.querySelector('div.paypalinstallmentcontainer');
            if (ppiContainer) {
                ppiContainer.style.display = 'none';
            }
            initDisplayModeECS_ButtonReplace();
        }
    };

    var initJSSDKPayPalButtonECS = function initJSSDKPayPalButtonECS(amount) {
        if (amount === undefined) {
            amount = payPalButtonSettings.cartAmount;
        }
        consoleInfo('initJSSDKPayPalButtonECS cart amount: ' + amount);
        if (amount < 0.01) {
            consoleInfo('ECS: not showing, cart amount too low');
            return;
        }
        var buttonContainer = document.querySelector('#paypal-button-container');
        if (!buttonContainer) {
            return;
        }
        var ecsIntro = document.querySelector('div.ecs_intro');
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
            createOrder: function createOrder(data, actions) {
                return fetch(payPalButtonSettings.createOrderUrl, {
                    method: 'post',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }).then(function (res) {
                    return res.json();
                }).then(function (orderdata) {
                    consoleInfo('order created: ' + orderdata.id, orderdata);
                    return orderdata.id;
                });
            },
            onApprove: function onApprove(data, actions) {
                consoleInfo('Approved data:', data);
                return fetch(payPalButtonSettings.approvedOrderUrl + '&orderId=' + data.orderID, {
                    method: 'post',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                }).then(function (res) {
                    return res.json();
                }).then(function (responsedata) {
                    consoleInfo('response data:', responsedata);
                    document.location = payPalButtonSettings.checkoutUrl;
                });
            },
            onError: function onError(err) {
                var ppButtonContainer = document.querySelector('#paypal-button-container');
                var ecsIntro = document.querySelector('div.ecs_intro');
                if (ppButtonContainer) {
                    var errorMessage = document.querySelector('div.paypal-error');
                    if (!errorMessage) {
                        errorMessage = document.createElement('div');
                        errorMessage.classList.add('paypal-error');
                        errorMessage.classList.add('alert');
                        errorMessage.classList.add('alert-warning');
                        errorMessage.style.textAlign = 'left';
                        errorMessage.style.color = '#555';
                    }
                    errorMessage.innerText = payPalText.paypalUnavailable + ' ';
                    var shoppingCartButton = document.querySelector('div.shopping-cart-button');
                    if (shoppingCartButton && shoppingCartButton.classList.contains('paypal-ecs-mode')) {
                        var linkUrl = window.location.toString().replace('display_mode=ecs', 'display_mode=normal');
                        var continueLink = document.createElement('a');
                        continueLink.setAttribute('href', linkUrl);
                        continueLink.innerText = payPalText.errorContinue;
                        errorMessage.append(continueLink);
                    }
                    ppButtonContainer.parentNode.append(errorMessage);
                    ppButtonContainer.style.display = 'none';
                }
                if (ecsIntro) {
                    ecsIntro.style.display = 'none';
                }
            }
        }).render('#paypal-button-container');

        var observerTarget = document.querySelector('#paypal-button-container');
        var observer = new MutationObserver(function (mutations, observer) {
            mutations.forEach(function (mutation) {
                if (mutation.removedNodes.length > 0) {
                    consoleInfo('re-init PayPal buttons');
                    var totalSumCell = document.querySelector('tr.total.sum td:nth-child(2)');
                    if (totalSumCell) {
                        var _amount = parseFloat(totalSumCell.textContent.replace(/[^0-9]/g, '')) / 100;
                        initJSSDKPayPalButtonECS(_amount);
                        initInstallmentBanners(_amount);
                    } else {
                        initJSSDKPayPalButtonECS();
                        initInstallmentBanners();
                    }
                }
            });
        });
        observer.observe(observerTarget, { childList: true });
    };

    var addButtonContainerDecoration = function addButtonContainerDecoration(buttonContainer, continueButtonBlock) {
        buttonContainer.style.width = 'auto';
        buttonContainer.style.textAlign = 'center';
        buttonContainer.style.fontStyle = 'italic';
        if (payPalText.continueToPayPal) {
            var labelToButtonDistance = 3;
            var lineHeight = '-' + window.getComputedStyle(continueButtonBlock).lineHeight;
            buttonContainer.style.marginTop = 'calc(' + lineHeight + ' - ' + labelToButtonDistance + 'px)';
            var continueLabel = document.createElement('span');
            continueLabel.classList.add('paypal-continue-label');
            continueLabel.textContent = payPalText.continueToPayPal;
            continueLabel.style.paddingBottom = labelToButtonDistance + 'px';
            buttonContainer.appendChild(continueLabel);
        }
    };

    var initJSSDKPayPalButtonECM = function initJSSDKPayPalButtonECM(continueButtonBlock) {
        var paypalButtonContainer = document.createElement('div');

        paypalButtonContainer.id = 'paypal-button-container';
        paypalButtonContainer.style.display = 'none';
        addButtonContainerDecoration(paypalButtonContainer, continueButtonBlock);
        continueButtonBlock.appendChild(paypalButtonContainer);

        paypal.Buttons({
            fundingSource: 'paypal',
            style: payPalButtonSettings.style,
            createOrder: function createOrder(data, actions) {
                return fetch(payPalButtonSettings.createOrderUrl, {
                    method: 'post',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }).then(function (res) {
                    return res.json();
                }).then(function (orderdata) {
                    consoleInfo('order created: ' + orderdata.id, orderdata);
                    return orderdata.id;
                });
            },
            onApprove: function onApprove(data, actions) {
                document.querySelector('#checkout_payment input[name="PayPal2HubOrderId"]').value = data.orderID;
                document.querySelector('#checkout_payment input[name="PayPal2HubPayerId"]').value = data.payerID;
                document.querySelector('#checkout_payment').submit();
            }
        }).render('#paypal-button-container');

        return paypalButtonContainer;
    };

    var initPayLaterButton = function initPayLaterButton(continueButtonBlock) {
        var payLaterItem = document.querySelector('li.gambio_hub-PayPal2Hub-paylater');
        if (payLaterItem === null) {
            return null;
        }

        var paylaterButton = paypal.Buttons({
            fundingSource: 'paylater',
            style: payPalButtonSettings.style,
            createOrder: function createOrder(data, actions) {
                return fetch(payPalButtonSettings.createOrderUrl, {
                    method: 'post',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }).then(function (res) {
                    return res.json();
                }).then(function (orderdata) {
                    consoleInfo('order created: ' + orderdata.id, orderdata);
                    return orderdata.id;
                });
            },
            onApprove: function onApprove(data, actions) {
                document.querySelector('#checkout_payment input[name="PayPal2HubOrderId"]').value = data.orderID;
                document.querySelector('#checkout_payment input[name="PayPal2HubPayerId"]').value = data.payerID;
                document.querySelector('#checkout_payment').submit();
            }
        });

        if (paylaterButton.isEligible()) {
            var paylaterButtonId = 'paypal-paylater-button-container',
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
    };

    var initSepaButton = function initSepaButton(continueButtonBlock) {
        var sepaItem = document.querySelector('li.gambio_hub-PayPal2Hub-sepa');
        if (sepaItem === null) {
            return null;
        }
        var sepaButtonStyle = payPalButtonSettings.style;
        if (sepaButtonStyle.color === 'gold' || sepaButtonStyle.color === 'blue') {
            sepaButtonStyle.color = 'silver';
        }

        var sepaButton = paypal.Buttons({
            fundingSource: 'sepa',
            style: payPalButtonSettings.style,
            createOrder: function createOrder(data, actions) {
                return fetch(payPalButtonSettings.createOrderUrl, {
                    method: 'post',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }).then(function (res) {
                    return res.json();
                }).then(function (orderdata) {
                    consoleInfo('order created: ' + orderdata.id, orderdata);
                    return orderdata.id;
                });
            },
            onApprove: function onApprove(data, actions) {
                document.querySelector('#checkout_payment input[name="PayPal2HubOrderId"]').value = data.orderID;
                document.querySelector('#checkout_payment input[name="PayPal2HubPayerId"]').value = data.payerID;
                document.querySelector('#checkout_payment').submit();
            }
        });

        if (sepaButton.isEligible()) {
            var sepaButtonId = 'paypal-sepa-button-container',
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
    };

    var initCreditCardButton = function initCreditCardButton(continueButtonBlock) {
        var brandedCreditCardsItem = document.querySelector('li.gambio_hub-PayPal2Hub-creditcardbutton');
        if (brandedCreditCardsItem === null) {
            return null;
        }

        var cardButtonStyle = payPalButtonSettings.style;
        cardButtonStyle.color = 'black';
        cardButtonStyle.shape = 'pill';

        var creditCardButton = paypal.Buttons({
            fundingSource: 'card',
            style: cardButtonStyle,
            createOrder: function createOrder(data, actions) {
                return fetch(payPalButtonSettings.createOrderUrl, {
                    method: 'post',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }).then(function (res) {
                    return res.json();
                }).then(function (orderdata) {
                    consoleInfo('order created: ' + orderdata.id, orderdata);
                    return orderdata.id;
                });
            },
            onApprove: function onApprove(data, actions) {
                document.querySelector('#checkout_payment input[name="PayPal2HubOrderId"]').value = data.orderID;
                document.querySelector('#checkout_payment input[name="PayPal2HubPayerId"]').value = data.payerID;
                document.querySelector('#checkout_payment').submit();
            }
        });

        consoleInfo(creditCardButton);

        if (creditCardButton.isEligible()) {
            consoleInfo('Eligible for branded cc payments');
            var creditCardButtonId = 'paypal-creditcard-button-container',
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
    };

    var initHostedCreditCard = function initHostedCreditCard() {
        var theLabel = document.querySelector('#ppcc-card-holder-field').closest('label'),
            theDiv = document.createElement('div');

        theDiv.classList.add('payment-module-container');
        theLabel.parentNode.appendChild(theDiv);
        while (theLabel.hasChildNodes()) {
            theDiv.appendChild(theLabel.firstChild);
        }
        theLabel.remove();

        var theListItem = document.querySelector('li.gambio_hub-PayPal2Hub-creditcard');
        theListItem.style.cursor = 'pointer';
        theListItem.addEventListener('click', function () {
            $(this).find('input:radio:not(:disabled):not(.placeholder-radio)').first().prop('checked', true).trigger('change');
        });

        var cardHolderField = document.querySelector('#ppcc-card-holder-field'),
            fieldStyle = getComputedStyle(cardHolderField),
            orderIdElement = document.querySelector('#checkout_payment input[name="PayPal2HubOrderId"]');

        paypal.HostedFields.render({
            createOrder: function createOrder() {
                return fetch(payPalButtonSettings.createOrderUrl, {
                    method: 'post',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }).then(function (res) {
                    return res.json();
                }).then(function (orderdata) {
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
                    'placeholder': '4111111111111111'
                },
                cvv: {
                    'selector': '#ppcc-cvv',
                    'placeholder': '123'
                },
                expirationDate: {
                    'selector': '#ppcc-expiration-date',
                    'placeholder': 'MM/YY'
                }
            }
        }).then(function (cardFields) {
            consoleInfo('PayPal: CC fields initialized', cardFields);
            var paymentForm = document.querySelector('#checkout_payment');
            var cardFieldsSubmitted = false;
            paymentForm.addEventListener('submit', function (event) {
                if (cardFieldsSubmitted === false && paymentForm.payment.value === 'gambio_hub-PayPal2Hub-creditcard') {
                    event.preventDefault();
                    var billingAddressData = JSON.parse(document.querySelector('#ppcc-billingaddress').textContent);
                    cardFields.submit({
                        cardholderName: paymentForm.ppcc_card_holder.value,
                        billingAddress: billingAddressData,
                        contingencies: ['SCA_WHEN_REQUIRED']
                    }).then(function () {
                        // OK
                        consoleInfo('cardFields submitted');
                        cardFieldsSubmitted = true;
                        paymentForm.submit();
                    }).catch(function (err) {
                        orderIdElement.value = '';
                        consoleInfo(err);
                        alert(payPalText.errorCheckData);
                        document.querySelector('input[name="payment"]:checked').scrollIntoView(false);
                    });
                }
            });
        });
    };

    var initCheckoutPayment = function initCheckoutPayment() {
        var continueButtonBlock = document.querySelector('#checkout_payment div.continue_button'),
            continueButton = continueButtonBlock.querySelector('input[type="submit"]'),
            continueButtonDisplay = continueButton.style.display,
            paypalButtonContainer = document.createElement('div'),
            plusContainer = document.querySelector('#gambiohub-ppplus'),
            ccForm = document.querySelector('div.paypal-cc-form'),
            orderIdElement = document.querySelector('#checkout_payment input[name="PayPal2HubOrderId"]');

        if (plusContainer === null && ccForm !== null) {
            consoleInfo('PayPal: Credit Card form found on page.');
            var hostedFieldsEligible = paypal.HostedFields.isEligible();
            var brandedCreditCardsOption = document.querySelector('li.gambio_hub-PayPal2Hub-creditcardbutton');
            if (hostedFieldsEligible) {
                consoleInfo('PayPal: eligible for hosted fields');
                if (brandedCreditCardsOption) {
                    brandedCreditCardsOption.remove();
                }

                initHostedCreditCard();
            } else {
                consoleInfo('PayPal: NOT eligible for hosted fields');
                var ccListItem = document.querySelector('li.gambio_hub-PayPal2Hub-creditcard');
                ccListItem.remove();
            }
        }

        if (plusContainer === null && !payPalButtonSettings.paymentApproved) {
            var backButtonBlock = continueButtonBlock.parentElement.querySelector('div.back_button');
            if (backButtonBlock !== null && backButtonBlock.classList.contains('col-xs-6')) {
                backButtonBlock.classList.remove('col-xs-6');
                backButtonBlock.classList.add('col-xs-4');
                continueButtonBlock.classList.remove('col-xs-6');
                continueButtonBlock.classList.add('col-xs-8');
                var backButton = backButtonBlock.querySelector('a.btn');
                payPalButtonSettings.style.height = parseInt(getComputedStyle(backButton).height);
            }

            var _paypalButtonContainer = initJSSDKPayPalButtonECM(continueButtonBlock);
            var paylaterButtonContainer = initPayLaterButton(continueButtonBlock);
            var creditCardButtonContainer = initCreditCardButton(continueButtonBlock);
            var sepaButtonContainer = initSepaButton(continueButtonBlock);

            var paymentItemClickListener = function paymentItemClickListener() {
                var selected_payment = this.querySelector('input[name="payment"]');
                if (null !== selected_payment) {
                    if (selected_payment.value === 'gambio_hub-PayPal2Hub' || selected_payment.value === 'gambio_hub' && selected_payment.dataset.module_code === 'PayPal2Hub') {
                        continueButton.style.display = 'none';
                        _paypalButtonContainer.style.display = 'block';
                        if (paylaterButtonContainer !== null) {
                            paylaterButtonContainer.style.display = 'none';
                        }
                        if (creditCardButtonContainer !== null) {
                            creditCardButtonContainer.style.display = 'none';
                        }
                        if (sepaButtonContainer !== null) {
                            sepaButtonContainer.style.display = 'none';
                        }
                    } else if (selected_payment.value === 'gambio_hub-PayPal2Hub-paylater' && paylaterButtonContainer !== null) {
                        continueButton.style.display = 'none';
                        _paypalButtonContainer.style.display = 'none';
                        paylaterButtonContainer.style.display = 'block';
                        if (creditCardButtonContainer !== null) {
                            creditCardButtonContainer.style.display = 'none';
                        }
                        if (sepaButtonContainer !== null) {
                            sepaButtonContainer.style.display = 'none';
                        }
                    } else if (selected_payment.value === 'gambio_hub-PayPal2Hub-sepa' && sepaButtonContainer !== null) {
                        continueButton.style.display = 'none';
                        _paypalButtonContainer.style.display = 'none';
                        if (paylaterButtonContainer !== null) {
                            paylaterButtonContainer.style.display = 'none';
                        }
                        if (creditCardButtonContainer !== null) {
                            creditCardButtonContainer.style.display = 'none';
                        }
                        sepaButtonContainer.style.display = 'block';
                    } else if (selected_payment.value === 'gambio_hub-PayPal2Hub-creditcardbutton' && creditCardButtonContainer !== null) {
                        continueButton.style.display = 'none';
                        _paypalButtonContainer.style.display = 'none';
                        creditCardButtonContainer.style.display = 'block';
                        if (paylaterButtonContainer !== null) {
                            paylaterButtonContainer.style.display = 'none';
                        }
                        if (sepaButtonContainer !== null) {
                            sepaButtonContainer.style.display = 'none';
                        }
                    } else {
                        continueButton.style.display = continueButtonDisplay;
                        _paypalButtonContainer.style.display = 'none';
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

            var paymentItems = document.querySelectorAll('#checkout_payment input[name="payment"], #checkout_payment li.list-group-item');
            paymentItems.forEach(function (paymentItem) {
                paymentItem.addEventListener('click', paymentItemClickListener);
            });

            var paymentListEntries = document.querySelectorAll('#checkout_payment li');
            paymentListEntries.forEach(function (paymentOption) {
                if (paymentOption.querySelector('input[name="payment"]:checked') || paymentListEntries.length === 1) {
                    paymentOption.dispatchEvent(new Event('click'));
                }
            });
        }
    };

    /****
     ****  Installment Banners
    	 */

    var initInstallmentBanners = function initInstallmentBanners(amount) {
        consoleInfo('Initialising PayPal Installments banners', 'CartAmount = ' + payPalBannerSettings.cartAmount);
        var banners = document.querySelectorAll('.paypal-installments');
        banners.forEach(function (bannerElement) {
            var position = bannerElement.dataset.ppinstPos;
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
                    var p = document.querySelector('div.current-price-container');
                    if (p) {
                        var priceText = p.innerText;
                        priceText = priceText.replace(/.*?[\d,.]+\s+\D{1,3}.*?([\d,.]+\s+\D{1,3})/s, '$1');
                        amount += 0.01 * parseFloat(priceText.replace(/.*?(((\d{1,3}[.,])+)(\d{2})).*/, '$1').replace(/[.,]/g, ''));
                        consoleInfo('Product amount for banner: ' + amount);
                    }
                }
                if (amount < 99) {
                    consoleInfo('Not showing PayPal Installments banner for amount ' + amount);
                    return;
                }

                if (bannerElement.classList.contains('paypal-installments-cartbottom')) {
                    var observerTarget = document.querySelector('div.shopping-cart-button');
                    if (observerTarget) {
                        var cartSumElement = document.querySelector('tr.total.sum td:nth-child(2)');
                        var amountString = '0';
                        if (cartSumElement) {
                            amountString = cartSumElement.textContent.trim();
                        } else {
                            cartSumElement = document.querySelector('tr.footer.total td:nth-child(2)');
                            if (cartSumElement) {
                                amountString = cartSumElement.textContent.trim().replace(/(\n|\t|\.|\,)/g, '').replace(/.*?([0-9.,]+)\s+EUR.*/, '$1');
                            }
                        }
                        amount = 0.01 * parseInt(amountString.replace(/[^0-9]/g, ''));
                        consoleInfo('cart amount ' + amount);

                        var observer = new MutationObserver(function (mutations, observer) {
                            mutations.forEach(function (mutation) {
                                if (mutation.removedNodes.length > 0) {
                                    consoleInfo('re-init PayPal installments banner');
                                    initInstallmentBanners();
                                }
                            });
                        });
                        observer.observe(observerTarget, { childList: true });
                    }
                }

                paypal.Messages({
                    amount: amount,
                    currency: payPalBannerSettings.currency,
                    style: payPalBannerSettings.positions[position].style,
                    placement: payPalBannerSettings.positions[position].placement
                }).render(bannerElement).then(function () {
                    var legacyInstallmentContainer = document.querySelector('.paypalinstallmentcontainer');
                    if (legacyInstallmentContainer) {
                        legacyInstallmentContainer.remove();
                    }
                });
            }
        });
    };

    var paypalSdkLoaded = function paypalSdkLoaded() {
        consoleInfo('PayPalSDK loaded');
        if (window.location.pathname.match(/shopping_cart.php/)) {
            consoleInfo('Initializing ECS button');
            initJSSDKPayPalButtonECS();
        }
        if (window.location.pathname.match(/checkout_payment.php/)) {
            consoleInfo('Initializing PayPal on payment page');
            initCheckoutPayment();
        }
        initInstallmentBanners();
    };

    var disableJavascriptFeatures = function disableJavascriptFeatures() {
        var disable = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;

        /*
        let pppay = document.querySelector('ul:not(.paypal3-plus-checkout) li.gambio_hub-PayPal2Hub');
        if (pppay) {
            consoleInfo('Removing PayPal payment option');
            pppay.remove();
        }
        */

        var newStyle = disable ? 'none' : 'block';

        var ecsButton = document.querySelector('div#paypal-newbutton');
        if (ecsButton) {
            ecsButton.style.display = newStyle;
        }

        var ccPaymentOption = document.querySelector('#checkout_payment li.gambio_hub-PayPal2Hub-creditcard');
        if (ccPaymentOption) {
            ccPaymentOption.style.display = newStyle;
        }

        var puiPaymentOption = document.querySelector('#checkout_payment li.gambio_hub-PayPal2Hub-pui');
        if (puiPaymentOption) {
            puiPaymentOption.style.display = newStyle;
        }

        var paylaterPaymentOption = document.querySelector('#checkout_payment li.gambio_hub-PayPal2Hub-paylater');
        if (paylaterPaymentOption) {
            paylaterPaymentOption.style.display = newStyle;
        }

        var sepaPaymentOption = document.querySelector('#checkout_payment li.gambio_hub-PayPal2Hub-sepa');
        if (sepaPaymentOption) {
            sepaPaymentOption.style.display = newStyle;
        }

        var ecmPaymentOption = document.querySelector('#checkout_payment li.gambio_hub-PayPal2Hub');
        if (ecmPaymentOption && disable) {
            var paypalModeInput = document.createElement('input');
            paypalModeInput.type = 'hidden';
            paypalModeInput.name = 'PayPalMode';
            paypalModeInput.value = 'sdkless';
            ecmPaymentOption.appendChild(paypalModeInput);
        }
    };

    var disablePayPalOptions = function disablePayPalOptions() {
        var disable = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;

        var paymentOptions = document.querySelectorAll('#checkout_payment li.list-group-item');
        paymentOptions.forEach(function (paymentOption, key, parent) {
            paymentOption.classList.forEach(function (className) {
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

    var enablePayPalOptions = function enablePayPalOptions() {
        disablePayPalOptions(false);
    };

    var readyCallback = function readyCallback() {
        consoleInfo('readyCallback start');
        var jssrc = '';
        if (typeof payPalButtonSettings !== 'undefined' && payPalButtonSettings.jssrc) {
            jssrc = payPalButtonSettings.jssrc;
        } else if (typeof payPalBannerSettings !== 'undefined' && payPalBannerSettings.jssrc) {
            jssrc = payPalBannerSettings.jssrc;
        }
        var pageHasBanners = document.querySelectorAll('.paypal-installments').length > 0;
        var isCheckoutPage = window.location.pathname.match(/shopping_cart.php/) || window.location.pathname.match(/checkout_payment.php/);

        var consentGiven = undefined;
        var purposeId = null;
        var consentDataElement = document.getElementById('paypalconsent');
        if (consentDataElement !== null) {
            var consentData = JSON.parse(consentDataElement.textContent);
            purposeId = consentData.purpose_id;
            var gxConsentsString = document.cookie.split('; ').find(function (row) {
                return row.startsWith('GXConsents=');
            });
            var gxConsents = gxConsentsString ? gxConsentsString.split('=')[1] : null;
            if (gxConsents) {
                var gxConsentsData = JSON.parse(gxConsents);
                consentGiven = gxConsentsData.purposeConsents['' + purposeId];
            }
        }

        if (jssrc && (pageHasBanners || isCheckoutPage) && typeof window.paypal === 'undefined') {
            var loadTimeout = null;

            var onPayPalSdkLoad = function onPayPalSdkLoad() {
                clearTimeout(loadTimeout);
                paypalSdkLoaded();
                disableJavascriptFeatures(false);
                enablePayPalOptions();
            };
            var onPayPalSdkError = function onPayPalSdkError() {
                consoleInfo('ERROR loading PayPal Javascript - blocked?');
                consoleInfo('PayPal JSSDK unavailable, disabling features');
                disableJavascriptFeatures();
                enablePayPalOptions();
            };
            window.initPayPalJSSDK = function () {
                if (typeof window.paypal !== 'undefined') {
                    return;
                }
                var jssdk = document.createElement('script');
                jssdk.src = payPalButtonSettings.jssrc ? payPalButtonSettings.jssrc : payPalBannerSettings.jssrc;

                var acdcIdentity = document.querySelector('#paypal_cc_identity');
                if (acdcIdentity !== null) {
                    var clientIdentity = JSON.parse(acdcIdentity.textContent);
                    if (typeof clientIdentity.client_token !== 'undefined') {
                        jssdk.dataset.clientToken = clientIdentity.client_token;
                    }
                }
                jssdk.onerror = onPayPalSdkError;
                jssdk.onload = onPayPalSdkLoad;

                if (document.currentScript) {
                    document.currentScript.parentNode.insertBefore(jssdk, document.currentScript);
                } else {
                    document.querySelector('script:last-of-type').parentNode.appendChild(jssdk);
                }
            };

            disablePayPalOptions();
            if (purposeId !== null) {
                consoleInfo("External PayPal Javascript is managed by CookieConsent, purpose " + purposeId);
                if (window.PayPalAsOilClearToLoad) {
                    consoleInfo('OIL has cleared PayPal to load');
                    window.initPayPalJSSDK();
                } else {
                    consoleInfo('OIL has not yet cleared PayPal to load');
                    disableJavascriptFeatures();
                    window.addEventListener('PayPalClearedToLoad', function (evt) {
                        consoleInfo('OIL has cleared PayPal to load (Event)');
                        window.initPayPalJSSDK();
                    });
                    if (isCheckoutPage) {
                        consoleInfo('on checkout page; waiting 5s for OIL');
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

    var mainCallback = "fetch" in window ? readyCallback : handleAntiqueBrowser;

    if (document.readyState === 'complete' || document.readyState !== 'loading' && !document.documentElement.doScroll) {
        consoleInfo('Readystate complete');
        mainCallback();
    } else {
        consoleInfo('Adding EventListener for DOMContentLoaded');
        document.addEventListener('DOMContentLoaded', mainCallback);
    }
})();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIlNob3AvSmF2YXNjcmlwdC9QYXlQYWxMb2FkZXIuanMiXSwibmFtZXMiOlsid2luZG93IiwiTm9kZUxpc3QiLCJwcm90b3R5cGUiLCJmb3JFYWNoIiwiQXJyYXkiLCJjdXJyZW50U2NyaXB0IiwiZG9jdW1lbnQiLCJjb25zb2xlSW5mbyIsInBheVBhbEJ1dHRvblNldHRpbmdzIiwiZGV2ZWxvcG1lbnRNb2RlIiwicGFyYW1zIiwiaW5mbyIsImhhbmRsZUFudGlxdWVCcm93c2VyIiwicHBwYXkiLCJxdWVyeVNlbGVjdG9yIiwicmVtb3ZlIiwiaW5pdERpc3BsYXlNb2RlRUNTX0J1dHRvblJlcGxhY2UiLCJwYXlwYWxCdXR0b25Db250YWluZXIiLCJmb290ZXJUb3RhbFJvdyIsIm5ld1RvdGFsUm93IiwiY3JlYXRlRWxlbWVudCIsIm5ld1RvdGFsUm93Q2VsbCIsImNvbFNwYW4iLCJzdHlsZSIsIndpZHRoIiwiYm9yZGVyVG9wIiwiYXBwZW5kQ2hpbGQiLCJwYXJlbnROb2RlIiwicXVlcnlTZWxlY3RvckFsbCIsInRkIiwicGFkZGluZ0JvdHRvbSIsImluaXREaXNwbGF5TW9kZUVDUyIsImxvY2F0aW9uIiwic2VhcmNoIiwibWF0Y2giLCJjaGVja291dEJ1dHRvbnMiLCJkaXNwbGF5IiwiY2hlY2tvdXRTdWJtaXRCdXR0b24iLCJzaG9wcGluZ0NhcnRCdXR0b24iLCJjbGFzc0xpc3QiLCJhZGQiLCJwcGlDb250YWluZXIiLCJpbml0SlNTREtQYXlQYWxCdXR0b25FQ1MiLCJhbW91bnQiLCJ1bmRlZmluZWQiLCJjYXJ0QW1vdW50IiwiYnV0dG9uQ29udGFpbmVyIiwiZWNzSW50cm8iLCJwYXlwYWwiLCJCdXR0b25zIiwiY3JlYXRlT3JkZXIiLCJkYXRhIiwiYWN0aW9ucyIsImZldGNoIiwiY3JlYXRlT3JkZXJVcmwiLCJtZXRob2QiLCJoZWFkZXJzIiwidGhlbiIsInJlcyIsImpzb24iLCJvcmRlcmRhdGEiLCJpZCIsIm9uQXBwcm92ZSIsImFwcHJvdmVkT3JkZXJVcmwiLCJvcmRlcklEIiwiYm9keSIsIkpTT04iLCJzdHJpbmdpZnkiLCJyZXNwb25zZWRhdGEiLCJjaGVja291dFVybCIsIm9uRXJyb3IiLCJlcnIiLCJwcEJ1dHRvbkNvbnRhaW5lciIsImVycm9yTWVzc2FnZSIsInRleHRBbGlnbiIsImNvbG9yIiwiaW5uZXJUZXh0IiwicGF5UGFsVGV4dCIsInBheXBhbFVuYXZhaWxhYmxlIiwiY29udGFpbnMiLCJsaW5rVXJsIiwidG9TdHJpbmciLCJyZXBsYWNlIiwiY29udGludWVMaW5rIiwic2V0QXR0cmlidXRlIiwiZXJyb3JDb250aW51ZSIsImFwcGVuZCIsInJlbmRlciIsIm9ic2VydmVyVGFyZ2V0Iiwib2JzZXJ2ZXIiLCJNdXRhdGlvbk9ic2VydmVyIiwibXV0YXRpb25zIiwibXV0YXRpb24iLCJyZW1vdmVkTm9kZXMiLCJsZW5ndGgiLCJ0b3RhbFN1bUNlbGwiLCJwYXJzZUZsb2F0IiwidGV4dENvbnRlbnQiLCJpbml0SW5zdGFsbG1lbnRCYW5uZXJzIiwib2JzZXJ2ZSIsImNoaWxkTGlzdCIsImFkZEJ1dHRvbkNvbnRhaW5lckRlY29yYXRpb24iLCJjb250aW51ZUJ1dHRvbkJsb2NrIiwiZm9udFN0eWxlIiwiY29udGludWVUb1BheVBhbCIsImxhYmVsVG9CdXR0b25EaXN0YW5jZSIsImxpbmVIZWlnaHQiLCJnZXRDb21wdXRlZFN0eWxlIiwibWFyZ2luVG9wIiwiY29udGludWVMYWJlbCIsImluaXRKU1NES1BheVBhbEJ1dHRvbkVDTSIsImZ1bmRpbmdTb3VyY2UiLCJ2YWx1ZSIsInBheWVySUQiLCJzdWJtaXQiLCJpbml0UGF5TGF0ZXJCdXR0b24iLCJwYXlMYXRlckl0ZW0iLCJwYXlsYXRlckJ1dHRvbiIsImlzRWxpZ2libGUiLCJwYXlsYXRlckJ1dHRvbklkIiwicGF5bGF0ZXJCdXR0b25Db250YWluZXIiLCJpbml0U2VwYUJ1dHRvbiIsInNlcGFJdGVtIiwic2VwYUJ1dHRvblN0eWxlIiwic2VwYUJ1dHRvbiIsInNlcGFCdXR0b25JZCIsInNlcGFCdXR0b25Db250YWluZXIiLCJpbml0Q3JlZGl0Q2FyZEJ1dHRvbiIsImJyYW5kZWRDcmVkaXRDYXJkc0l0ZW0iLCJjYXJkQnV0dG9uU3R5bGUiLCJzaGFwZSIsImNyZWRpdENhcmRCdXR0b24iLCJjcmVkaXRDYXJkQnV0dG9uSWQiLCJjcmVkaXRDYXJkQnV0dG9uQ29udGFpbmVyIiwiaW5pdEhvc3RlZENyZWRpdENhcmQiLCJ0aGVMYWJlbCIsImNsb3Nlc3QiLCJ0aGVEaXYiLCJoYXNDaGlsZE5vZGVzIiwiZmlyc3RDaGlsZCIsInRoZUxpc3RJdGVtIiwiY3Vyc29yIiwiYWRkRXZlbnRMaXN0ZW5lciIsIiQiLCJmaW5kIiwiZmlyc3QiLCJwcm9wIiwidHJpZ2dlciIsImNhcmRIb2xkZXJGaWVsZCIsImZpZWxkU3R5bGUiLCJvcmRlcklkRWxlbWVudCIsIkhvc3RlZEZpZWxkcyIsInN0eWxlcyIsImZvbnRTaXplIiwiZm9udEZhbWlseSIsInBhZGRpbmciLCJmaWVsZHMiLCJudW1iZXIiLCJjdnYiLCJleHBpcmF0aW9uRGF0ZSIsImNhcmRGaWVsZHMiLCJwYXltZW50Rm9ybSIsImNhcmRGaWVsZHNTdWJtaXR0ZWQiLCJldmVudCIsInBheW1lbnQiLCJwcmV2ZW50RGVmYXVsdCIsImJpbGxpbmdBZGRyZXNzRGF0YSIsInBhcnNlIiwiY2FyZGhvbGRlck5hbWUiLCJwcGNjX2NhcmRfaG9sZGVyIiwiYmlsbGluZ0FkZHJlc3MiLCJjb250aW5nZW5jaWVzIiwiY2F0Y2giLCJhbGVydCIsImVycm9yQ2hlY2tEYXRhIiwic2Nyb2xsSW50b1ZpZXciLCJpbml0Q2hlY2tvdXRQYXltZW50IiwiY29udGludWVCdXR0b24iLCJjb250aW51ZUJ1dHRvbkRpc3BsYXkiLCJwbHVzQ29udGFpbmVyIiwiY2NGb3JtIiwiaG9zdGVkRmllbGRzRWxpZ2libGUiLCJicmFuZGVkQ3JlZGl0Q2FyZHNPcHRpb24iLCJjY0xpc3RJdGVtIiwicGF5bWVudEFwcHJvdmVkIiwiYmFja0J1dHRvbkJsb2NrIiwicGFyZW50RWxlbWVudCIsImJhY2tCdXR0b24iLCJoZWlnaHQiLCJwYXJzZUludCIsInBheW1lbnRJdGVtQ2xpY2tMaXN0ZW5lciIsInNlbGVjdGVkX3BheW1lbnQiLCJkYXRhc2V0IiwibW9kdWxlX2NvZGUiLCJwYXltZW50SXRlbXMiLCJwYXltZW50SXRlbSIsInBheW1lbnRMaXN0RW50cmllcyIsInBheW1lbnRPcHRpb24iLCJkaXNwYXRjaEV2ZW50IiwiRXZlbnQiLCJwYXlQYWxCYW5uZXJTZXR0aW5ncyIsImJhbm5lcnMiLCJiYW5uZXJFbGVtZW50IiwicG9zaXRpb24iLCJwcGluc3RQb3MiLCJwb3NpdGlvbnMiLCJsYXlvdXQiLCJwcm9kdWN0c1ByaWNlIiwicCIsInByaWNlVGV4dCIsImNhcnRTdW1FbGVtZW50IiwiYW1vdW50U3RyaW5nIiwidHJpbSIsIk1lc3NhZ2VzIiwiY3VycmVuY3kiLCJwbGFjZW1lbnQiLCJsZWdhY3lJbnN0YWxsbWVudENvbnRhaW5lciIsInBheXBhbFNka0xvYWRlZCIsInBhdGhuYW1lIiwiZGlzYWJsZUphdmFzY3JpcHRGZWF0dXJlcyIsImRpc2FibGUiLCJuZXdTdHlsZSIsImVjc0J1dHRvbiIsImNjUGF5bWVudE9wdGlvbiIsInB1aVBheW1lbnRPcHRpb24iLCJwYXlsYXRlclBheW1lbnRPcHRpb24iLCJzZXBhUGF5bWVudE9wdGlvbiIsImVjbVBheW1lbnRPcHRpb24iLCJwYXlwYWxNb2RlSW5wdXQiLCJ0eXBlIiwibmFtZSIsImRpc2FibGVQYXlQYWxPcHRpb25zIiwicGF5bWVudE9wdGlvbnMiLCJrZXkiLCJwYXJlbnQiLCJjbGFzc05hbWUiLCJvcGFjaXR5IiwicmVtb3ZlQXR0cmlidXRlIiwiZW5hYmxlUGF5UGFsT3B0aW9ucyIsInJlYWR5Q2FsbGJhY2siLCJqc3NyYyIsInBhZ2VIYXNCYW5uZXJzIiwiaXNDaGVja291dFBhZ2UiLCJjb25zZW50R2l2ZW4iLCJwdXJwb3NlSWQiLCJjb25zZW50RGF0YUVsZW1lbnQiLCJnZXRFbGVtZW50QnlJZCIsImNvbnNlbnREYXRhIiwicHVycG9zZV9pZCIsImd4Q29uc2VudHNTdHJpbmciLCJjb29raWUiLCJzcGxpdCIsInJvdyIsInN0YXJ0c1dpdGgiLCJneENvbnNlbnRzIiwiZ3hDb25zZW50c0RhdGEiLCJwdXJwb3NlQ29uc2VudHMiLCJsb2FkVGltZW91dCIsIm9uUGF5UGFsU2RrTG9hZCIsImNsZWFyVGltZW91dCIsIm9uUGF5UGFsU2RrRXJyb3IiLCJpbml0UGF5UGFsSlNTREsiLCJqc3NkayIsInNyYyIsImFjZGNJZGVudGl0eSIsImNsaWVudElkZW50aXR5IiwiY2xpZW50X3Rva2VuIiwiY2xpZW50VG9rZW4iLCJvbmVycm9yIiwib25sb2FkIiwiaW5zZXJ0QmVmb3JlIiwiUGF5UGFsQXNPaWxDbGVhclRvTG9hZCIsInNldFRpbWVvdXQiLCJjb25zb2xlIiwibWFpbkNhbGxiYWNrIiwicmVhZHlTdGF0ZSIsImRvY3VtZW50RWxlbWVudCIsImRvU2Nyb2xsIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBV0MsYUFBVztBQUNYLFFBQUlBLE9BQU9DLFFBQVAsSUFBbUIsQ0FBQ0EsU0FBU0MsU0FBVCxDQUFtQkMsT0FBM0MsRUFBb0Q7QUFDbkRGLGlCQUFTQyxTQUFULENBQW1CQyxPQUFuQixHQUE2QkMsTUFBTUYsU0FBTixDQUFnQkMsT0FBN0M7QUFDQTs7QUFFRCxRQUFJRSxnQkFBZ0JDLFNBQVNELGFBQTdCOztBQUVBLFFBQUlFLGNBQWMsU0FBZEEsV0FBYyxHQUFvQjtBQUFBOztBQUNyQyxZQUFJLE9BQU9DLHFCQUFxQkMsZUFBNUIsS0FBZ0QsU0FBaEQsSUFBNkRELHFCQUFxQkMsZUFBckIsS0FBeUMsS0FBMUcsRUFBaUg7QUFDaEg7QUFDQTs7QUFIb0MsMENBQVJDLE1BQVE7QUFBUkEsa0JBQVE7QUFBQTs7QUFJckMsNkJBQVFDLElBQVIsa0JBQWEsY0FBYixTQUFnQ0QsTUFBaEM7QUFDQSxLQUxEOztBQU9BLFFBQUlFLHVCQUF1QixTQUF2QkEsb0JBQXVCLEdBQVc7QUFDckNMLG9CQUFZLHVDQUFaOztB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LHNCQUFYLEVBQW1DO0FBQy9CeFEsZ0NBQVksZ0NBQVo7QUFDQVAsMkJBQU9xUSxlQUFQO0FBQ0gsaUJBSEQsTUFHTztBQUNIOVAsZ0NBQVksd0NBQVo7QUFDQW9OO0FBQ0EzTiwyQkFBTzhJLGdCQUFQLENBQXdCLHFCQUF4QixFQUErQyxlQUFPO0FBQ2xEdkksb0NBQVksd0NBQVo7QUFDQVAsK0JBQU9xUSxlQUFQO0FBQ0gscUJBSEQ7QUFJQSx3QkFBSW5CLGNBQUosRUFBb0I7QUFDaEIzTyxvQ0FBWSxzQ0FBWjtBQUNBMFAsc0NBQWNlLFdBQVdaLGdCQUFYLEVBQTZCLElBQTdCLENBQWQ7QUFDSDtBQUNKO0FBQ0osYUFqQkQsTUFpQk87QUFDSGEsd0JBQVF0USxJQUFSLENBQWEsa0VBQWI7QUFDQVgsdUJBQU9xUSxlQUFQO0FBQ0g7QUFDVixTQTdESyxNQTZEQztBQUNOOVAsd0JBQVksOERBQVo7QUFDU29OO0FBQ1Q7QUFDS3BOLG9CQUFZLG1CQUFaO0FBQ04sS0E1RkQ7O0FBOEZBLFFBQUkyUSxlQUFnQixXQUFXbFIsTUFBWixHQUFzQitPLGFBQXRCLEdBQXNDbk8sb0JBQXpEOztBQUVBLFFBQUdOLFNBQVM2USxVQUFULEtBQXdCLFVBQXhCLElBQXVDN1EsU0FBUzZRLFVBQVQsS0FBd0IsU0FBeEIsSUFBcUMsQ0FBQzdRLFNBQVM4USxlQUFULENBQXlCQyxRQUF6RyxFQUFvSDtBQUM3RzlRLG9CQUFZLHFCQUFaO0FBQ04yUTtBQUNBLEtBSEQsTUFHTztBQUNBM1Esb0JBQVksMkNBQVo7QUFDTkQsaUJBQVN3SSxnQkFBVCxDQUEwQixrQkFBMUIsRUFBOENvSSxZQUE5QztBQUNBO0FBRUQsQ0F0MkJBLEdBQUQiLCJmaWxlIjoiU2hvcC9KYXZhc2NyaXB0L1BheVBhbExvYWRlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gICBQYXlQYWxMb2FkZXIuanMgMjAyMy0wNC0xMlxuICAgR2FtYmlvIEdtYkhcbiAgIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gICBDb3B5cmlnaHQgKGMpIDIwMjMgR2FtYmlvIEdtYkhcbiAgIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuICAgW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gICAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuKi9cblxuXG4oZnVuY3Rpb24oKSB7XG5cdGlmICh3aW5kb3cuTm9kZUxpc3QgJiYgIU5vZGVMaXN0LnByb3RvdHlwZS5mb3JFYWNoKSB7XG5cdFx0Tm9kZUxpc3QucHJvdG90eXBlLmZvckVhY2ggPSBBcnJheS5wcm90b3R5cGUuZm9yRWFjaDtcblx0fVxuXG5cdGxldCBjdXJyZW50U2NyaXB0ID0gZG9jdW1lbnQuY3VycmVudFNjcmlwdDtcblx0XG5cdGxldCBjb25zb2xlSW5mbyA9IGZ1bmN0aW9uKC4uLnBhcmFtcykge1xuXHRcdGlmICh0eXBlb2YgcGF5UGFsQnV0dG9uU2V0dGluZ3MuZGV2ZWxvcG1lbnRNb2RlICE9PSAnYm9vbGVhbicgfHwgcGF5UGFsQnV0dG9uU2V0dGluZ3MuZGV2ZWxvcG1lbnRNb2RlID09PSBmYWxzZSkge1xuXHRcdFx0cmV0dXJuO1xuXHRcdH1cblx0XHRjb25zb2xlLmluZm8oJ1BheVBhbExvYWRlcicsIC4uLnBhcmFtcyk7XG5cdH1cblxuXHRsZXQgaGFuZGxlQW50aXF1ZUJyb3dzZXIgPSBmdW5jdGlvbigpIHtcblx0XHRjb25zb2xlSW5mbygnU29ycnksIGFudGlxdWUgYnJvd3NlciBub3Qgc3VwcG9ydGVkLicpO1xuXHRcdFxuXHRcdGxldCBwcHBheSA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJ2xpLmdhbWJpb19odWItUGF5UGFsMkh1YicpO1xuXHRcdGlmIChwcHBheSkge1xuXHRcdFx0cHBwYXkucmVtb3ZlKCk7XG5cdFx0fVxuXHR9XG5cdFxuXHRsZXQgaW5pdERpc3BsYXlNb2RlRUNTX0J1dHRvblJlcGxhY2UgPSBmdW5jdGlvbigpIHtcblx0XHRsZXQgcGF5cGFsQnV0dG9uQ29udGFpbmVyID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignI3BheXBhbC1idXR0b24tY29udGFpbmVyJyksXG5cdFx0XHRmb290ZXJUb3RhbFJvdyA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJ3RhYmxlLm9yZGVyLXRvdGFsIHRyLmZvb3Rlci50b3RhbCcpLFxuXHRcdFx0bmV3VG90YWxSb3cgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCd0cicpLFxuXHRcdFx0bmV3VG90YWxSb3dDZWxsID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgndGQnKTtcblx0XHRuZXdUb3RhbFJvd0NlbGwuY29sU3BhbiA9ICcyJztcblx0XHRuZXdUb3RhbFJvd0NlbGwuc3R5bGUud2lkdGggPSAnMTAwJSc7XG5cdFx0bmV3VG90YWxSb3dDZWxsLnN0eWxlLmJvcmRlclRvcCA9ICdub25lJztcblx0XHRuZXdUb3RhbFJvd0NlbGwuYXBwZW5kQ2hpbGQocGF5cGFsQnV0dG9uQ29udGFpbmVyKTtcblx0XHRuZXdUb3RhbFJvdy5hcHBlbmRDaGlsZChuZXdUb3RhbFJvd0NlbGwpO1xuXHRcdGZvb3RlclRvdGFsUm93LnBhcmVudE5vZGUuYXBwZW5kQ2hpbGQobmV3VG90YWxSb3cpO1xuXHRcdGZvb3RlclRvdGFsUm93LnF1ZXJ5U2VsZWN0b3JBbGwoJ3RkJykuZm9yRWFjaChmdW5jdGlvbih0ZCkge1xuXHRcdFx0dGQuc3R5bGUucGFkZGluZ0JvdHRvbSA9ICcxNXB4Jztcblx0XHR9KTtcblx0fTtcblxuXHRsZXQgaW5pdERpc3BsYXlNb2RlRUNTID0gZnVuY3Rpb24oKSB7XG5cdFx0aWYod2luZG93LmxvY2F0aW9uLnNlYXJjaC5tYXRjaCgvKFxcP3wmKWRpc3BsYXlfbW9kZT1lY3MoJHwmKS8pKSB7XG5cdFx0XHRsZXQgY2hlY2tvdXRCdXR0b25zID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignZGl2LmNoZWNrb3V0LWJ1dHRvbnMnKTtcblx0XHRcdGlmKGNoZWNrb3V0QnV0dG9ucykge1xuXHRcdFx0XHRjaGVja291dEJ1dHRvbnMuc3R5bGUuZGlzcGxheSA9ICdub25lJztcblx0XHRcdH1cblx0XHRcdGxldCBjaGVja291dFN1Ym1pdEJ1dHRvbiA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJ2Rpdi5zaG9wcGluZy1jYXJ0LWJ1dHRvbiBhLmJ1dHRvbi1zdWJtaXQnKTtcblx0XHRcdGlmKGNoZWNrb3V0U3VibWl0QnV0dG9uID09PSBudWxsKSB7XG5cdFx0XHRcdGNoZWNrb3V0U3VibWl0QnV0dG9uID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcigndHIuY2hlY2tvdXQtYnV0dG9uJyk7XG5cdFx0XHR9XG5cdFx0XHRpZihjaGVja291dFN1Ym1pdEJ1dHRvbikge1xuXHRcdFx0XHRjaGVja291dFN1Ym1pdEJ1dHRvbi5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuXHRcdFx0fVxuXHRcdFx0bGV0IHNob3BwaW5nQ2FydEJ1dHRvbiA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJ2Rpdi5zaG9wcGluZy1jYXJ0LWJ1dHRvbicpO1xuXHRcdFx0aWYoc2hvcHBpbmdDYXJ0QnV0dG9uKSB7XG5cdFx0XHRcdHNob3BwaW5nQ2FydEJ1dHRvbi5jbGFzc0xpc3QuYWRkKCdwYXlwYWwtZWNzLW1vZGUnKTtcblx0XHRcdH1cblx0XHRcdGxldCBwcGlDb250YWluZXIgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCdkaXYucGF5cGFsaW5zdGFsbG1lbnRjb250YWluZXInKTtcblx0XHRcdGlmKHBwaUNvbnRhaW5lcikge1xuXHRcdFx0XHRwcGlDb250YWluZXIuc3R5bGUuZGlzcGxheSA9ICdub25lJztcblx0XHRcdH1cblx0XHRcdGluaXREaXNwbGF5TW9kZUVDU19CdXR0b25SZXBsYWNlKCk7XG5cdFx0fVxuXHR9XG5cblx0bGV0IGluaXRKU1NES1BheVBhbEJ1dHRvbkVDUyA9IGZ1bmN0aW9uKGFtb3VudCkge1xuICAgICAgICBpZiAoYW1vdW50ID09PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICAgIGFtb3VudCA9IHBheVBhbEJ1dHRvblNldHRpbmdzLmNhcnRBbW91bnQ7XG4gICAgICAgIH0gXG4gICAgICAgIGNvbnNvbGVJbmZvKCdpbml0SlNTREtQYXlQYWxCdXR0b25FQ1MgY2FydCBhbW91bnQ6ICcgKyBhbW91bnQpO1xuICAgICAgICBpZiAoYW1vdW50IDwgMC4wMSkge1xuICAgICAgICAgICAgY29uc29sZUluZm8oJ0VDUzogbm90IHNob3dpbmcsIGNhcnQgYW1vdW50IHRvbyBsb3cnKTtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXHRcdGxldCBidXR0b25Db250YWluZXIgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjcGF5cGFsLWJ1dHRvbi1jb250YWluZXInKTtcblx0XHRpZiAoIWJ1dHRvbkNvbnRhaW5lcikge1xuXHRcdFx0cmV0dXJuO1xuXHRcdH1cbiAgICAgICAgbGV0IGVjc0ludHJvID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignZGl2LmVjc19pbnRybycpO1xuICAgICAgICBpZiAoZG9jdW1lbnQucXVlcnlTZWxlY3RvcigndHIuY2hlY2tvdXQtYnV0dG9uIC5idXR0b24tZGlzYWJsZWQnKSAhPT0gbnVsbCkge1xuICAgICAgICAgICAgaWYgKGVjc0ludHJvKSB7XG4gICAgICAgICAgICAgICAgZWNzSW50cm8uc3R5bGUuZGlzcGxheSA9ICdub25lJztcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGJ1dHRvbkNvbnRhaW5lci5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgaWYgKGVjc0ludHJvKSB7XG4gICAgICAgICAgICAgICAgZWNzSW50cm8uc3R5bGUuZGlzcGxheSA9ICdibG9jayc7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBidXR0b25Db250YWluZXIuc3R5bGUuZGlzcGxheSA9ICdibG9jayc7XG4gICAgICAgICAgICBpbml0RGlzcGxheU1vZGVFQ1MoKTtcbiAgICAgICAgfVxuXG5cdFx0cGF5cGFsLkJ1dHRvbnMoe1xuXHRcdFx0c3R5bGU6IHBheVBhbEJ1dHRvblNldHRpbmdzLnN0eWxlLFxuXHRcdFx0Y3JlYXRlT3JkZXI6IGZ1bmN0aW9uKGRhdGEsIGFjdGlvbnMpIHtcblx0XHRcdFx0cmV0dXJuIGZldGNoKHBheVBhbEJ1dHRvblNldHRpbmdzLmNyZWF0ZU9yZGVyVXJsLCB7XG5cdFx0XHRcdFx0bWV0aG9kOiAncG9zdCcsXG5cdFx0XHRcdFx0aGVhZGVyczoge1xuXHRcdFx0XHRcdFx0J0NvbnRlbnQtVHlwZSc6ICdhcHBsaWNhdGlvbi9qc29uJ1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9KVxuXHRcdFx0XHRcdC50aGVuKChyZXMpID0+IHsgcmV0dXJuIHJlcy5qc29uKCkgfSlcblx0XHRcdFx0XHQudGhlbigob3JkZXJkYXRhKSA9PiB7XG5cdFx0XHRcdFx0XHRjb25zb2xlSW5mbygnb3JkZXIgY3JlYXRlZDogJyArIG9yZGVyZGF0YS5pZCwgb3JkZXJkYXRhKTtcblx0XHRcdFx0XHRcdHJldHVybiBvcmRlcmRhdGEuaWQ7XG5cdFx0XHRcdFx0fSk7XG5cdFx0XHRcdH0sXG5cdFx0XHRvbkFwcHJvdmU6IGZ1bmN0aW9uKGRhdGEsIGFjdGlvbnMpIHtcblx0XHRcdFx0Y29uc29sZUluZm8oJ0FwcHJvdmVkIGRhdGE6JywgZGF0YSk7XG5cdFx0XHRcdHJldHVybiBmZXRjaChwYXlQYWxCdXR0b25TZXR0aW5ncy5hcHByb3ZlZE9yZGVyVXJsICsgJyZvcmRlcklkPScgKyBkYXRhLm9yZGVySUQsIHtcblx0XHRcdFx0XHRtZXRob2Q6ICdwb3N0Jyxcblx0XHRcdFx0XHRoZWFkZXJzOiB7XG5cdFx0XHRcdFx0XHQnQ29udGVudC1UeXBlJzogJ2FwcGxpY2F0aW9uL2pzb24nXG5cdFx0XHRcdFx0fSxcblx0XHRcdFx0XHRib2R5OiBKU09OLnN0cmluZ2lmeShkYXRhKVxuXHRcdFx0XHR9KVxuXHRcdFx0XHRcdC50aGVuKChyZXMpID0+IHsgcmV0dXJuIHJlcy5qc29uKCk7IH0pXG5cdFx0XHRcdFx0LnRoZW4oKHJlc3BvbnNlZGF0YSkgPT4ge1xuXHRcdFx0XHRcdFx0Y29uc29sZUluZm8oJ3Jlc3BvbnNlIGRhdGE6JywgcmVzcG9uc2VkYXRhKTtcblx0XHRcdFx0XHRcdGRvY3VtZW50LmxvY2F0aW9uID0gcGF5UGFsQnV0dG9uU2V0dGluZ3MuY2hlY2tvdXRVcmw7XG5cdFx0XHRcdFx0fSk7XG5cdFx0XHR9LFxuXHRcdFx0b25FcnJvcjogZnVuY3Rpb24oZXJyKSB7XG5cdFx0XHRcdGxldCBwcEJ1dHRvbkNvbnRhaW5lciA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyNwYXlwYWwtYnV0dG9uLWNvbnRhaW5lcicpO1xuXHRcdFx0XHRsZXQgZWNzSW50cm8gPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCdkaXYuZWNzX2ludHJvJyk7XG5cdFx0XHRcdGlmKHBwQnV0dG9uQ29udGFpbmVyKSB7XG5cdFx0XHRcdFx0bGV0IGVycm9yTWVzc2FnZSA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJ2Rpdi5wYXlwYWwtZXJyb3InKTtcblx0XHRcdFx0XHRpZighZXJyb3JNZXNzYWdlKSB7XG5cdFx0XHRcdFx0XHRlcnJvck1lc3NhZ2UgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKTtcblx0XHRcdFx0XHRcdGVycm9yTWVzc2FnZS5jbGFzc0xpc3QuYWRkKCdwYXlwYWwtZXJyb3InKTtcblx0XHRcdFx0XHRcdGVycm9yTWVzc2FnZS5jbGFzc0xpc3QuYWRkKCdhbGVydCcpO1xuXHRcdFx0XHRcdFx0ZXJyb3JNZXNzYWdlLmNsYXNzTGlzdC5hZGQoJ2FsZXJ0LXdhcm5pbmcnKTtcblx0XHRcdFx0XHRcdGVycm9yTWVzc2FnZS5zdHlsZS50ZXh0QWxpZ24gPSAnbGVmdCc7XG5cdFx0XHRcdFx0XHRlcnJvck1lc3NhZ2Uuc3R5bGUuY29sb3IgPSAnIzU1NSc7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdGVycm9yTWVzc2FnZS5pbm5lclRleHQgPSBwYXlQYWxUZXh0LnBheXBhbFVuYXZhaWxhYmxlICsgJyAnO1xuXHRcdFx0XHRcdGxldCBzaG9wcGluZ0NhcnRCdXR0b24gPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCdkaXYuc2hvcHBpbmctY2FydC1idXR0b24nKTtcblx0XHRcdFx0XHRpZihzaG9wcGluZ0NhcnRCdXR0b24gJiYgc2hvcHBpbmdDYXJ0QnV0dG9uLmNsYXNzTGlzdC5jb250YWlucygncGF5cGFsLWVjcy1tb2RlJykpIHtcblx0XHRcdFx0XHRcdGxldCBsaW5rVXJsID0gd2luZG93LmxvY2F0aW9uLnRvU3RyaW5nKCkucmVwbGFjZSgnZGlzcGxheV9tb2RlPWVjcycsICdkaXNwbGF5X21vZGU9bm9ybWFsJyk7XG5cdFx0XHRcdFx0XHRsZXQgY29udGludWVMaW5rID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnYScpO1xuXHRcdFx0XHRcdFx0Y29udGludWVMaW5rLnNldEF0dHJpYnV0ZSgnaHJlZicsIGxpbmtVcmwpO1xuXHRcdFx0XHRcdFx0Y29udGludWVMaW5rLmlubmVyVGV4dCA9IHBheVBhbFRleHQuZXJyb3JDb250aW51ZTtcblx0XHRcdFx0XHRcdGVycm9yTWVzc2FnZS5hcHBlbmQoY29udGludWVMaW5rKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0cHBCdXR0b25Db250YWluZXIucGFyZW50Tm9kZS5hcHBlbmQoZXJyb3JNZXNzYWdlKTtcblx0XHRcdFx0XHRwcEJ1dHRvbkNvbnRhaW5lci5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuXHRcdFx0XHR9XG5cdFx0XHRcdGlmKGVjc0ludHJvKSB7XG5cdFx0XHRcdFx0ZWNzSW50cm8uc3R5bGUuZGlzcGxheSA9ICdub25lJztcblx0XHRcdFx0fVxuXHRcdFx0fVxuXHRcdH0pLnJlbmRlcignI3BheXBhbC1idXR0b24tY29udGFpbmVyJyk7XG5cblx0XHRsZXQgb2JzZXJ2ZXJUYXJnZXQgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjcGF5cGFsLWJ1dHRvbi1jb250YWluZXInKTtcblx0XHRsZXQgb2JzZXJ2ZXIgPSBuZXcgTXV0YXRpb25PYnNlcnZlcihmdW5jdGlvbihtdXRhdGlvbnMsIG9ic2VydmVyKSB7XG5cdFx0XHRtdXRhdGlvbnMuZm9yRWFjaChmdW5jdGlvbihtdXRhdGlvbikge1xuXHRcdFx0XHRpZihtdXRhdGlvbi5yZW1vdmVkTm9kZXMubGVuZ3RoID4gMCkge1xuXHRcdFx0XHRcdGNvbnNvbGVJbmZvKCdyZS1pbml0IFBheVBhbCBidXR0b25zJyk7XG4gICAgICAgICAgICAgICAgICAgIGxldCB0b3RhbFN1bUNlbGwgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCd0ci50b3RhbC5zdW0gdGQ6bnRoLWNoaWxkKDIpJyk7XG4gICAgICAgICAgICAgICAgICAgIGlmICh0b3RhbFN1bUNlbGwpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGxldCBhbW91bnQgPSBwYXJzZUZsb2F0KHRvdGFsU3VtQ2VsbC50ZXh0Q29udGVudC5yZXBsYWNlKC9bXjAtOV0vZywgJycpKSAvIDEwMDtcbiAgICAgICAgICAgICAgICAgICAgICAgIGluaXRKU1NES1BheVBhbEJ1dHRvbkVDUyhhbW91bnQpO1xuICAgICAgICAgICAgICAgICAgICAgICAgaW5pdEluc3RhbGxtZW50QmFubmVycyhhbW91bnQpO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgaW5pdEpTU0RLUGF5UGFsQnV0dG9uRUNTKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICBpbml0SW5zdGFsbG1lbnRCYW5uZXJzKCk7XG4gICAgICAgICAgICAgICAgICAgIH1cblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0fSk7XG5cdFx0b2JzZXJ2ZXIub2JzZXJ2ZShvYnNlcnZlclRhcmdldCwge2NoaWxkTGlzdDogdHJ1ZX0pO1xuXHR9O1xuXG4gICAgXG4gICAgbGV0IGFkZEJ1dHRvbkNvbnRhaW5lckRlY29yYXRpb24gPSBmdW5jdGlvbihidXR0b25Db250YWluZXIsIGNvbnRpbnVlQnV0dG9uQmxvY2spXG4gICAge1xuICAgICAgICBidXR0b25Db250YWluZXIuc3R5bGUud2lkdGggPSAnYXV0byc7XG4gICAgICAgIGJ1dHRvbkNvbnRhaW5lci5zdHlsZS50ZXh0QWxpZ24gPSAnY2VudGVyJztcbiAgICAgICAgYnV0dG9uQ29udGFpbmVyLnN0eWxlLmZvbnRTdHlsZSA9ICdpdGFsaWMnO1xuICAgICAgICBpZiAocGF5UGFsVGV4dC5jb250aW51ZVRvUGF5UGFsKSB7XG4gICAgICAgICAgICBsZXQgbGFiZWxUb0J1dHRvbkRpc3RhbmNlID0gMztcbiAgICAgICAgICAgIGxldCBsaW5lSGVpZ2h0ID0gJy0nICsgd2luZG93LmdldENvbXB1dGVkU3R5bGUoY29udGludWVCdXR0b25CbG9jaykubGluZUhlaWdodDtcbiAgICAgICAgICAgIGJ1dHRvbkNvbnRhaW5lci5zdHlsZS5tYXJnaW5Ub3AgPSAnY2FsYygnICsgbGluZUhlaWdodCArICcgLSAnICsgbGFiZWxUb0J1dHRvbkRpc3RhbmNlICsgJ3B4KSc7XG4gICAgICAgICAgICBsZXQgY29udGludWVMYWJlbCA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ3NwYW4nKTtcbiAgICAgICAgICAgIGNvbnRpbnVlTGFiZWwuY2xhc3NMaXN0LmFkZCgncGF5cGFsLWNvbnRpbnVlLWxhYmVsJyk7XG4gICAgICAgICAgICBjb250aW51ZUxhYmVsLnRleHRDb250ZW50ID0gcGF5UGFsVGV4dC5jb250aW51ZVRvUGF5UGFsXG4gICAgICAgICAgICBjb250aW51ZUxhYmVsLnN0eWxlLnBhZGRpbmdCb3R0b20gPSBsYWJlbFRvQnV0dG9uRGlzdGFuY2UgKyAncHgnOyBcbiAgICAgICAgICAgIGJ1dHRvbkNvbnRhaW5lci5hcHBlbmRDaGlsZChjb250aW51ZUxhYmVsKSA7XG4gICAgICAgIH1cbiAgICB9XG4gICAgXG4gICAgXG5cdGxldCBpbml0SlNTREtQYXlQYWxCdXR0b25FQ00gPSBmdW5jdGlvbihjb250aW51ZUJ1dHRvbkJsb2NrKVxuXHR7XG4gICAgICAgIGxldCBwYXlwYWxCdXR0b25Db250YWluZXIgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKTtcbiAgICAgICAgXG4gICAgICAgIHBheXBhbEJ1dHRvbkNvbnRhaW5lci5pZCA9ICdwYXlwYWwtYnV0dG9uLWNvbnRhaW5lcic7XG4gICAgICAgIHBheXBhbEJ1dHRvbkNvbnRhaW5lci5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICBhZGRCdXR0b25Db250YWluZXJEZWNvcmF0aW9uKHBheXBhbEJ1dHRvbkNvbnRhaW5lciwgY29udGludWVCdXR0b25CbG9jayk7XG4gICAgICAgIGNvbnRpbnVlQnV0dG9uQmxvY2suYXBwZW5kQ2hpbGQocGF5cGFsQnV0dG9uQ29udGFpbmVyKTtcblxuXHRcdHBheXBhbC5CdXR0b25zKHtcbiAgICAgICAgICAgIGZ1bmRpbmdTb3VyY2U6ICdwYXlwYWwnLFxuXHRcdFx0c3R5bGU6IHBheVBhbEJ1dHRvblNldHRpbmdzLnN0eWxlLFxuXHRcdFx0Y3JlYXRlT3JkZXI6IGZ1bmN0aW9uKGRhdGEsIGFjdGlvbnMpIHtcblx0XHRcdFx0cmV0dXJuIGZldGNoKHBheVBhbEJ1dHRvblNldHRpbmdzLmNyZWF0ZU9yZGVyVXJsLCB7XG5cdFx0XHRcdFx0bWV0aG9kOiAncG9zdCcsXG5cdFx0XHRcdFx0aGVhZGVyczoge1xuXHRcdFx0XHRcdFx0J0NvbnRlbnQtVHlwZSc6ICdhcHBsaWNhdGlvbi9qc29uJ1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSlcblx0XHRcdFx0XHQudGhlbigocmVzKSA9PiB7IHJldHVybiByZXMuanNvbigpIH0pXG5cdFx0XHRcdFx0LnRoZW4oKG9yZGVyZGF0YSkgPT4ge1xuXHRcdFx0XHRcdFx0Y29uc29sZUluZm8oJ29yZGVyIGNyZWF0ZWQ6ICcgKyBvcmRlcmRhdGEuaWQsIG9yZGVyZGF0YSk7XG5cdFx0XHRcdFx0XHRyZXR1cm4gb3JkZXJkYXRhLmlkO1xuXHRcdFx0XHRcdH0pO1xuXHRcdFx0fSxcblx0XHRcdG9uQXBwcm92ZTogZnVuY3Rpb24oZGF0YSwgYWN0aW9ucykge1xuXHRcdFx0XHRkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjY2hlY2tvdXRfcGF5bWVudCBpbnB1dFtuYW1lPVwiUGF5UGFsMkh1Yk9yZGVySWRcIl0nKS52YWx1ZSA9IGRhdGEub3JkZXJJRDtcblx0XHRcdFx0ZG9jdW1lbnQucXVlcnlTZWxlY3RvcignI2NoZWNrb3V0X3BheW1lbnQgaW5wdXRbbmFtZT1cIlBheVBhbDJIdWJQYXllcklkXCJdJykudmFsdWUgPSBkYXRhLnBheWVySUQ7XG5cdFx0XHRcdGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyNjaGVja291dF9wYXltZW50Jykuc3VibWl0KCk7XG5cdFx0XHR9XG5cdFx0fSkucmVuZGVyKCcjcGF5cGFsLWJ1dHRvbi1jb250YWluZXInKTtcbiAgICAgICAgXG4gICAgICAgIHJldHVybiBwYXlwYWxCdXR0b25Db250YWluZXI7XG5cdH1cblxuICAgIGxldCBpbml0UGF5TGF0ZXJCdXR0b24gPSBmdW5jdGlvbihjb250aW51ZUJ1dHRvbkJsb2NrKVxuICAgIHtcbiAgICAgICAgbGV0IHBheUxhdGVySXRlbSA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJ2xpLmdhbWJpb19odWItUGF5UGFsMkh1Yi1wYXlsYXRlcicpO1xuICAgICAgICBpZiAocGF5TGF0ZXJJdGVtID09PSBudWxsKSB7XG4gICAgICAgICAgICByZXR1cm4gbnVsbDtcbiAgICAgICAgfVxuICAgIFxuICAgICAgICBsZXQgcGF5bGF0ZXJCdXR0b24gPSBwYXlwYWwuQnV0dG9ucyh7XG4gICAgICAgICAgICBmdW5kaW5nU291cmNlOiAncGF5bGF0ZXInLFxuICAgICAgICAgICAgc3R5bGU6IHBheVBhbEJ1dHRvblNldHRpbmdzLnN0eWxlLFxuICAgICAgICAgICAgY3JlYXRlT3JkZXI6IGZ1bmN0aW9uKGRhdGEsIGFjdGlvbnMpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gZmV0Y2gocGF5UGFsQnV0dG9uU2V0dGluZ3MuY3JlYXRlT3JkZXJVcmwsIHtcbiAgICAgICAgICAgICAgICAgICAgbWV0aG9kOiAncG9zdCcsXG4gICAgICAgICAgICAgICAgICAgIGhlYWRlcnM6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICdDb250ZW50LVR5cGUnOiAnYXBwbGljYXRpb24vanNvbidcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAgICAgICAgIC50aGVuKChyZXMpID0+IHsgcmV0dXJuIHJlcy5qc29uKCkgfSlcbiAgICAgICAgICAgICAgICAgICAgLnRoZW4oKG9yZGVyZGF0YSkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgY29uc29sZUluZm8oJ29yZGVyIGNyZWF0ZWQ6ICcgKyBvcmRlcmRhdGEuaWQsIG9yZGVyZGF0YSk7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gb3JkZXJkYXRhLmlkO1xuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBvbkFwcHJvdmU6IGZ1bmN0aW9uKGRhdGEsIGFjdGlvbnMpIHtcbiAgICAgICAgICAgICAgICBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjY2hlY2tvdXRfcGF5bWVudCBpbnB1dFtuYW1lPVwiUGF5UGFsMkh1Yk9yZGVySWRcIl0nKS52YWx1ZSA9IGRhdGEub3JkZXJJRDtcbiAgICAgICAgICAgICAgICBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjY2hlY2tvdXRfcGF5bWVudCBpbnB1dFtuYW1lPVwiUGF5UGFsMkh1YlBheWVySWRcIl0nKS52YWx1ZSA9IGRhdGEucGF5ZXJJRDtcbiAgICAgICAgICAgICAgICBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjY2hlY2tvdXRfcGF5bWVudCcpLnN1Ym1pdCgpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICBcbiAgICAgICAgaWYgKHBheWxhdGVyQnV0dG9uLmlzRWxpZ2libGUoKSkge1xuICAgICAgICAgICAgbGV0IHBheWxhdGVyQnV0dG9uSWQgPSAncGF5cGFsLXBheWxhdGVyLWJ1dHRvbi1jb250YWluZXInLFxuICAgICAgICAgICAgICAgIHBheWxhdGVyQnV0dG9uQ29udGFpbmVyID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2Jyk7XG4gICAgICAgICAgICBwYXlsYXRlckJ1dHRvbkNvbnRhaW5lci5pZCA9IHBheWxhdGVyQnV0dG9uSWQ7XG4gICAgICAgICAgICBwYXlsYXRlckJ1dHRvbkNvbnRhaW5lci5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICAgICAgYWRkQnV0dG9uQ29udGFpbmVyRGVjb3JhdGlvbihwYXlsYXRlckJ1dHRvbkNvbnRhaW5lciwgY29udGludWVCdXR0b25CbG9jayk7XG4gICAgICAgICAgICBjb250aW51ZUJ1dHRvbkJsb2NrLmFwcGVuZENoaWxkKHBheWxhdGVyQnV0dG9uQ29udGFpbmVyKTtcbiAgICAgICAgICAgIHBheWxhdGVyQnV0dG9uLnJlbmRlcignIycgKyBwYXlsYXRlckJ1dHRvbklkKTtcbiAgICAgICAgICAgIGNvbnNvbGVJbmZvKCdQYXlQYWwgUGF5bGF0ZXI6IGVsaWdpYmxlIGFuZCBpbml0aWFsaXplZCcpO1xuICAgICAgICAgICAgcmV0dXJuIHBheWxhdGVyQnV0dG9uQ29udGFpbmVyO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgY29uc29sZUluZm8oJ1BheVBhbCBQYXlsYXRlcjogbm90IGVsaWdpYmxlJyk7XG4gICAgICAgICAgICBwYXlMYXRlckl0ZW0ucmVtb3ZlKCk7XG4gICAgICAgIH1cbiAgICAgICAgXG4gICAgICAgIHJldHVybiBudWxsO1xuICAgIH1cbiAgICBcbiAgICBcbiAgICBsZXQgaW5pdFNlcGFCdXR0b24gPSBmdW5jdGlvbihjb250aW51ZUJ1dHRvbkJsb2NrKVxuICAgIHtcbiAgICAgICAgbGV0IHNlcGFJdGVtID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignbGkuZ2FtYmlvX2h1Yi1QYXlQYWwySHViLXNlcGEnKTtcbiAgICAgICAgaWYgKHNlcGFJdGVtID09PSBudWxsKSB7XG4gICAgICAgICAgICByZXR1cm4gbnVsbDtcbiAgICAgICAgfVxuICAgICAgICBsZXQgc2VwYUJ1dHRvblN0eWxlID0gcGF5UGFsQnV0dG9uU2V0dGluZ3Muc3R5bGU7XG4gICAgICAgIGlmIChzZXBhQnV0dG9uU3R5bGUuY29sb3IgPT09ICdnb2xkJyB8fCBzZXBhQnV0dG9uU3R5bGUuY29sb3IgPT09ICdibHVlJykge1xuICAgICAgICAgICAgc2VwYUJ1dHRvblN0eWxlLmNvbG9yID0gJ3NpbHZlcic7XG4gICAgICAgIH1cbiAgICAgICAgXG4gICAgICAgIGxldCBzZXBhQnV0dG9uID0gcGF5cGFsLkJ1dHRvbnMoe1xuICAgICAgICAgICAgZnVuZGluZ1NvdXJjZTogJ3NlcGEnLFxuICAgICAgICAgICAgc3R5bGU6IHBheVBhbEJ1dHRvblNldHRpbmdzLnN0eWxlLFxuICAgICAgICAgICAgY3JlYXRlT3JkZXI6IGZ1bmN0aW9uKGRhdGEsIGFjdGlvbnMpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gZmV0Y2gocGF5UGFsQnV0dG9uU2V0dGluZ3MuY3JlYXRlT3JkZXJVcmwsIHtcbiAgICAgICAgICAgICAgICAgICAgbWV0aG9kOiAncG9zdCcsXG4gICAgICAgICAgICAgICAgICAgIGhlYWRlcnM6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICdDb250ZW50LVR5cGUnOiAnYXBwbGljYXRpb24vanNvbidcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAgICAgICAgIC50aGVuKChyZXMpID0+IHsgcmV0dXJuIHJlcy5qc29uKCkgfSlcbiAgICAgICAgICAgICAgICAgICAgLnRoZW4oKG9yZGVyZGF0YSkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgY29uc29sZUluZm8oJ29yZGVyIGNyZWF0ZWQ6ICcgKyBvcmRlcmRhdGEuaWQsIG9yZGVyZGF0YSk7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gb3JkZXJkYXRhLmlkO1xuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBvbkFwcHJvdmU6IGZ1bmN0aW9uKGRhdGEsIGFjdGlvbnMpIHtcbiAgICAgICAgICAgICAgICBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjY2hlY2tvdXRfcGF5bWVudCBpbnB1dFtuYW1lPVwiUGF5UGFsMkh1Yk9yZGVySWRcIl0nKS52YWx1ZSA9IGRhdGEub3JkZXJJRDtcbiAgICAgICAgICAgICAgICBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjY2hlY2tvdXRfcGF5bWVudCBpbnB1dFtuYW1lPVwiUGF5UGFsMkh1YlBheWVySWRcIl0nKS52YWx1ZSA9IGRhdGEucGF5ZXJJRDtcbiAgICAgICAgICAgICAgICBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjY2hlY2tvdXRfcGF5bWVudCcpLnN1Ym1pdCgpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICAgICAgXG4gICAgICAgIGlmIChzZXBhQnV0dG9uLmlzRWxpZ2libGUoKSkge1xuICAgICAgICAgICAgbGV0IHNlcGFCdXR0b25JZCA9ICdwYXlwYWwtc2VwYS1idXR0b24tY29udGFpbmVyJyxcbiAgICAgICAgICAgICAgICBzZXBhQnV0dG9uQ29udGFpbmVyID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2Jyk7XG4gICAgICAgICAgICBzZXBhQnV0dG9uQ29udGFpbmVyLmlkID0gc2VwYUJ1dHRvbklkO1xuICAgICAgICAgICAgc2VwYUJ1dHRvbkNvbnRhaW5lci5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICAgICAgYWRkQnV0dG9uQ29udGFpbmVyRGVjb3JhdGlvbihzZXBhQnV0dG9uQ29udGFpbmVyLCBjb250aW51ZUJ1dHRvbkJsb2NrKTtcbiAgICAgICAgICAgIGNvbnRpbnVlQnV0dG9uQmxvY2suYXBwZW5kQ2hpbGQoc2VwYUJ1dHRvbkNvbnRhaW5lcik7XG4gICAgICAgICAgICBzZXBhQnV0dG9uLnJlbmRlcignIycgKyBzZXBhQnV0dG9uSWQpO1xuICAgICAgICAgICAgY29uc29sZUluZm8oJ1BheVBhbCBTRVBBOiBlbGlnaWJsZSBhbmQgaW5pdGlhbGl6ZWQnKTtcbiAgICAgICAgICAgIHJldHVybiBzZXBhQnV0dG9uQ29udGFpbmVyO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgY29uc29sZUluZm8oJ1BheVBhbCBTRVBBOiBub3QgZWxpZ2libGUnKTtcbiAgICAgICAgICAgIHNlcGFJdGVtLnJlbW92ZSgpO1xuICAgICAgICB9XG4gICAgICAgIFxuICAgICAgICByZXR1cm4gbnVsbDtcbiAgICB9XG4gICAgXG4gICAgXG4gICAgbGV0IGluaXRDcmVkaXRDYXJkQnV0dG9uID0gZnVuY3Rpb24oY29udGludWVCdXR0b25CbG9jaylcbiAgICB7XG4gICAgICAgIGxldCBicmFuZGVkQ3JlZGl0Q2FyZHNJdGVtID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignbGkuZ2FtYmlvX2h1Yi1QYXlQYWwySHViLWNyZWRpdGNhcmRidXR0b24nKTtcbiAgICAgICAgaWYgKGJyYW5kZWRDcmVkaXRDYXJkc0l0ZW0gPT09IG51bGwpIHtcbiAgICAgICAgICAgIHJldHVybiBudWxsO1xuICAgICAgICB9XG4gICAgICAgIFxuICAgICAgICBsZXQgY2FyZEJ1dHRvblN0eWxlID0gcGF5UGFsQnV0dG9uU2V0dGluZ3Muc3R5bGU7XG4gICAgICAgIGNhcmRCdXR0b25TdHlsZS5jb2xvciA9ICdibGFjayc7XG4gICAgICAgIGNhcmRCdXR0b25TdHlsZS5zaGFwZSA9ICdwaWxsJztcblxuICAgICAgICBsZXQgY3JlZGl0Q2FyZEJ1dHRvbiA9IHBheXBhbC5CdXR0b25zKHtcbiAgICAgICAgICAgIGZ1bmRpbmdTb3VyY2U6ICdjYXJkJyxcbiAgICAgICAgICAgIHN0eWxlOiBjYXJkQnV0dG9uU3R5bGUsXG4gICAgICAgICAgICBjcmVhdGVPcmRlcjogZnVuY3Rpb24oZGF0YSwgYWN0aW9ucykge1xuICAgICAgICAgICAgICAgIHJldHVybiBmZXRjaChwYXlQYWxCdXR0b25TZXR0aW5ncy5jcmVhdGVPcmRlclVybCwge1xuICAgICAgICAgICAgICAgICAgICBtZXRob2Q6ICdwb3N0JyxcbiAgICAgICAgICAgICAgICAgICAgaGVhZGVyczoge1xuICAgICAgICAgICAgICAgICAgICAgICAgJ0NvbnRlbnQtVHlwZSc6ICdhcHBsaWNhdGlvbi9qc29uJ1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICAgICAgICAgLnRoZW4oKHJlcykgPT4geyByZXR1cm4gcmVzLmpzb24oKSB9KVxuICAgICAgICAgICAgICAgICAgICAudGhlbigob3JkZXJkYXRhKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjb25zb2xlSW5mbygnb3JkZXIgY3JlYXRlZDogJyArIG9yZGVyZGF0YS5pZCwgb3JkZXJkYXRhKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBvcmRlcmRhdGEuaWQ7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIG9uQXBwcm92ZTogZnVuY3Rpb24oZGF0YSwgYWN0aW9ucykge1xuICAgICAgICAgICAgICAgIGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyNjaGVja291dF9wYXltZW50IGlucHV0W25hbWU9XCJQYXlQYWwySHViT3JkZXJJZFwiXScpLnZhbHVlID0gZGF0YS5vcmRlcklEO1xuICAgICAgICAgICAgICAgIGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyNjaGVja291dF9wYXltZW50IGlucHV0W25hbWU9XCJQYXlQYWwySHViUGF5ZXJJZFwiXScpLnZhbHVlID0gZGF0YS5wYXllcklEO1xuICAgICAgICAgICAgICAgIGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyNjaGVja291dF9wYXltZW50Jykuc3VibWl0KCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgICAgICBcbiAgICAgICAgY29uc29sZUluZm8oY3JlZGl0Q2FyZEJ1dHRvbik7XG4gICAgICAgIFxuICAgICAgICBpZiAoY3JlZGl0Q2FyZEJ1dHRvbi5pc0VsaWdpYmxlKCkpIHtcbiAgICAgICAgICAgIGNvbnNvbGVJbmZvKCdFbGlnaWJsZSBmb3IgYnJhbmRlZCBjYyBwYXltZW50cycpO1xuICAgICAgICAgICAgbGV0IGNyZWRpdENhcmRCdXR0b25JZCA9ICdwYXlwYWwtY3JlZGl0Y2FyZC1idXR0b24tY29udGFpbmVyJyxcbiAgICAgICAgICAgICAgICBjcmVkaXRDYXJkQnV0dG9uQ29udGFpbmVyID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2Jyk7XG4gICAgICAgICAgICBjcmVkaXRDYXJkQnV0dG9uQ29udGFpbmVyLmlkID0gY3JlZGl0Q2FyZEJ1dHRvbklkO1xuICAgICAgICAgICAgY3JlZGl0Q2FyZEJ1dHRvbkNvbnRhaW5lci5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICAgICAgYWRkQnV0dG9uQ29udGFpbmVyRGVjb3JhdGlvbihjcmVkaXRDYXJkQnV0dG9uQ29udGFpbmVyLCBjb250aW51ZUJ1dHRvbkJsb2NrKTtcbiAgICAgICAgICAgIGNvbnRpbnVlQnV0dG9uQmxvY2suYXBwZW5kQ2hpbGQoY3JlZGl0Q2FyZEJ1dHRvbkNvbnRhaW5lcik7XG4gICAgICAgICAgICBjcmVkaXRDYXJkQnV0dG9uLnJlbmRlcignIycgKyBjcmVkaXRDYXJkQnV0dG9uSWQpO1xuICAgICAgICAgICAgcmV0dXJuIGNyZWRpdENhcmRCdXR0b25Db250YWluZXI7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICBjb25zb2xlSW5mbygnTk9UIGVsaWdpYmxlIGZvciBicmFuZGVkIGNjIHBheW1lbnRzJyk7XG4gICAgICAgICAgICBicmFuZGVkQ3JlZGl0Q2FyZHNJdGVtLnJlbW92ZSgpO1xuICAgICAgICB9XG4gICAgICAgIFxuICAgICAgICByZXR1cm4gbnVsbDtcbiAgICB9XG4gICAgXG5cbiAgICBsZXQgaW5pdEhvc3RlZENyZWRpdENhcmQgPSBmdW5jdGlvbigpIHtcbiAgICAgICAgbGV0IHRoZUxhYmVsID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignI3BwY2MtY2FyZC1ob2xkZXItZmllbGQnKS5jbG9zZXN0KCdsYWJlbCcpLFxuICAgICAgICAgICAgdGhlRGl2ID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2Jyk7XG4gICAgXG4gICAgICAgIHRoZURpdi5jbGFzc0xpc3QuYWRkKCdwYXltZW50LW1vZHVsZS1jb250YWluZXInKTtcbiAgICAgICAgdGhlTGFiZWwucGFyZW50Tm9kZS5hcHBlbmRDaGlsZCh0aGVEaXYpO1xuICAgICAgICB3aGlsZSh0aGVMYWJlbC5oYXNDaGlsZE5vZGVzKCkpIHtcbiAgICAgICAgICAgIHRoZURpdi5hcHBlbmRDaGlsZCh0aGVMYWJlbC5maXJzdENoaWxkKTtcbiAgICAgICAgfVxuICAgICAgICB0aGVMYWJlbC5yZW1vdmUoKTtcbiAgICAgICAgXG4gICAgICAgIGxldCB0aGVMaXN0SXRlbSA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJ2xpLmdhbWJpb19odWItUGF5UGFsMkh1Yi1jcmVkaXRjYXJkJyk7XG4gICAgICAgIHRoZUxpc3RJdGVtLnN0eWxlLmN1cnNvciA9ICdwb2ludGVyJztcbiAgICAgICAgdGhlTGlzdEl0ZW0uYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICQodGhpcykuZmluZCgnaW5wdXQ6cmFkaW86bm90KDpkaXNhYmxlZCk6bm90KC5wbGFjZWhvbGRlci1yYWRpbyknKS5maXJzdCgpLnByb3AoJ2NoZWNrZWQnLCB0cnVlKS50cmlnZ2VyKCdjaGFuZ2UnKTtcbiAgICAgICAgfSk7XG4gICAgXG4gICAgICAgIGxldCBjYXJkSG9sZGVyRmllbGQgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjcHBjYy1jYXJkLWhvbGRlci1maWVsZCcpLFxuICAgICAgICAgICAgZmllbGRTdHlsZSA9IGdldENvbXB1dGVkU3R5bGUoY2FyZEhvbGRlckZpZWxkKSxcbiAgICAgICAgICAgIG9yZGVySWRFbGVtZW50ID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignI2NoZWNrb3V0X3BheW1lbnQgaW5wdXRbbmFtZT1cIlBheVBhbDJIdWJPcmRlcklkXCJdJyk7XG4gICAgXG4gICAgICAgIHBheXBhbC5Ib3N0ZWRGaWVsZHMucmVuZGVyKHtcbiAgICAgICAgICAgIGNyZWF0ZU9yZGVyOiBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gZmV0Y2gocGF5UGFsQnV0dG9uU2V0dGluZ3MuY3JlYXRlT3JkZXJVcmwsIHtcbiAgICAgICAgICAgICAgICAgICAgbWV0aG9kOiAncG9zdCcsXG4gICAgICAgICAgICAgICAgICAgIGhlYWRlcnM6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICdDb250ZW50LVR5cGUnOiAnYXBwbGljYXRpb24vanNvbidcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAgICAgICAgIC50aGVuKChyZXMpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiByZXMuanNvbigpXG4gICAgICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAgICAgICAgIC50aGVuKChvcmRlcmRhdGEpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIG9yZGVySWRFbGVtZW50LnZhbHVlID0gb3JkZXJkYXRhLmlkO1xuICAgICAgICAgICAgICAgICAgICAgICAgY29uc29sZUluZm8oJ2NyZWRpdCBjYXJkIG9yZGVyIGNyZWF0ZWQ6ICcgKyBvcmRlcmRhdGEuaWQsIG9yZGVyZGF0YSk7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gb3JkZXJkYXRhLmlkO1xuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBzdHlsZXM6IHtcbiAgICAgICAgICAgICAgICAnaW5wdXQnOiB7XG4gICAgICAgICAgICAgICAgICAgICdjb2xvcic6IGZpZWxkU3R5bGUuY29sb3IsXG4gICAgICAgICAgICAgICAgICAgICdmb250LXNpemUnOiBmaWVsZFN0eWxlLmZvbnRTaXplLFxuICAgICAgICAgICAgICAgICAgICAnZm9udC1mYW1pbHknOiBmaWVsZFN0eWxlLmZvbnRGYW1pbHksXG4gICAgICAgICAgICAgICAgICAgICdwYWRkaW5nJzogZmllbGRTdHlsZS5wYWRkaW5nLFxuICAgICAgICAgICAgICAgICAgICAnbGluZS1oZWlnaHQnOiBmaWVsZFN0eWxlLmxpbmVIZWlnaHRcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICcudmFsaWQnOiB7XG4gICAgICAgICAgICAgICAgICAgICdjb2xvcic6ICdncmVlbidcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICcuaW52YWxpZCc6IHtcbiAgICAgICAgICAgICAgICAgICAgJ2NvbG9yJzogJ3JlZCdcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgZmllbGRzOiB7XG4gICAgICAgICAgICAgICAgbnVtYmVyOiB7XG4gICAgICAgICAgICAgICAgICAgICdzZWxlY3Rvcic6ICcjcHBjYy1jYXJkLW51bWJlcicsXG4gICAgICAgICAgICAgICAgICAgICdwbGFjZWhvbGRlcic6ICc0MTExMTExMTExMTExMTExJyxcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgIGN2djoge1xuICAgICAgICAgICAgICAgICAgICAnc2VsZWN0b3InOiAnI3BwY2MtY3Z2JyxcbiAgICAgICAgICAgICAgICAgICAgJ3BsYWNlaG9sZGVyJzogJzEyMycsXG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICBleHBpcmF0aW9uRGF0ZToge1xuICAgICAgICAgICAgICAgICAgICAnc2VsZWN0b3InOiAnI3BwY2MtZXhwaXJhdGlvbi1kYXRlJyxcbiAgICAgICAgICAgICAgICAgICAgJ3BsYWNlaG9sZGVyJzogJ01NL1lZJyxcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pLnRoZW4oZnVuY3Rpb24oY2FyZEZpZWxkcykge1xuICAgICAgICAgICAgY29uc29sZUluZm8oJ1BheVBhbDogQ0MgZmllbGRzIGluaXRpYWxpemVkJywgY2FyZEZpZWxkcyk7XG4gICAgICAgICAgICBsZXQgcGF5bWVudEZvcm0gPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjY2hlY2tvdXRfcGF5bWVudCcpO1xuICAgICAgICAgICAgbGV0IGNhcmRGaWVsZHNTdWJtaXR0ZWQgPSBmYWxzZTtcbiAgICAgICAgICAgIHBheW1lbnRGb3JtLmFkZEV2ZW50TGlzdGVuZXIoJ3N1Ym1pdCcsIGZ1bmN0aW9uKGV2ZW50KSB7XG4gICAgICAgICAgICAgICAgaWYgKGNhcmRGaWVsZHNTdWJtaXR0ZWQgPT09IGZhbHNlICYmIHBheW1lbnRGb3JtLnBheW1lbnQudmFsdWUgPT09ICdnYW1iaW9faHViLVBheVBhbDJIdWItY3JlZGl0Y2FyZCcpIHtcbiAgICAgICAgICAgICAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICAgICAgbGV0IGJpbGxpbmdBZGRyZXNzRGF0YSA9IEpTT04ucGFyc2UoZG9jdW1lbnQucXVlcnlTZWxlY3RvcignI3BwY2MtYmlsbGluZ2FkZHJlc3MnKS50ZXh0Q29udGVudCk7XG4gICAgICAgICAgICAgICAgICAgIGNhcmRGaWVsZHMuc3VibWl0KHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNhcmRob2xkZXJOYW1lOiBwYXltZW50Rm9ybS5wcGNjX2NhcmRfaG9sZGVyLnZhbHVlLFxuICAgICAgICAgICAgICAgICAgICAgICAgYmlsbGluZ0FkZHJlc3M6IGJpbGxpbmdBZGRyZXNzRGF0YSxcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnRpbmdlbmNpZXM6IFsnU0NBX1dIRU5fUkVRVUlSRUQnXVxuICAgICAgICAgICAgICAgICAgICB9KS50aGVuKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgLy8gT0tcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnNvbGVJbmZvKCdjYXJkRmllbGRzIHN1Ym1pdHRlZCcpO1xuICAgICAgICAgICAgICAgICAgICAgICAgY2FyZEZpZWxkc1N1Ym1pdHRlZCA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgICAgICBwYXltZW50Rm9ybS5zdWJtaXQoKTtcbiAgICAgICAgICAgICAgICAgICAgfSkuY2F0Y2goZnVuY3Rpb24oZXJyKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBvcmRlcklkRWxlbWVudC52YWx1ZSA9ICcnO1xuICAgICAgICAgICAgICAgICAgICAgICAgY29uc29sZUluZm8oZXJyKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGFsZXJ0KHBheVBhbFRleHQuZXJyb3JDaGVja0RhdGEpO1xuICAgICAgICAgICAgICAgICAgICAgICAgZG9jdW1lbnQucXVlcnlTZWxlY3RvcignaW5wdXRbbmFtZT1cInBheW1lbnRcIl06Y2hlY2tlZCcpLnNjcm9sbEludG9WaWV3KGZhbHNlKTtcbiAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH0pO1xuICAgIH1cbiAgICBcblx0bGV0IGluaXRDaGVja291dFBheW1lbnQgPSBmdW5jdGlvbigpIHtcblx0XHRsZXQgY29udGludWVCdXR0b25CbG9jayA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyNjaGVja291dF9wYXltZW50IGRpdi5jb250aW51ZV9idXR0b24nKSxcblx0XHRcdGNvbnRpbnVlQnV0dG9uID0gY29udGludWVCdXR0b25CbG9jay5xdWVyeVNlbGVjdG9yKCdpbnB1dFt0eXBlPVwic3VibWl0XCJdJyksXG5cdFx0XHRjb250aW51ZUJ1dHRvbkRpc3BsYXkgPSBjb250aW51ZUJ1dHRvbi5zdHlsZS5kaXNwbGF5LFxuXHRcdFx0cGF5cGFsQnV0dG9uQ29udGFpbmVyID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2JyksXG5cdFx0XHRwbHVzQ29udGFpbmVyID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignI2dhbWJpb2h1Yi1wcHBsdXMnKSxcbiAgICAgICAgICAgIGNjRm9ybSA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJ2Rpdi5wYXlwYWwtY2MtZm9ybScpLFxuICAgICAgICAgICAgb3JkZXJJZEVsZW1lbnQgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjY2hlY2tvdXRfcGF5bWVudCBpbnB1dFtuYW1lPVwiUGF5UGFsMkh1Yk9yZGVySWRcIl0nKTtcbiAgICAgICAgXG4gICAgICAgIGlmIChwbHVzQ29udGFpbmVyID09PSBudWxsICYmIGNjRm9ybSAhPT0gbnVsbCkge1xuICAgICAgICAgICAgY29uc29sZUluZm8oJ1BheVBhbDogQ3JlZGl0IENhcmQgZm9ybSBmb3VuZCBvbiBwYWdlLicpO1xuICAgICAgICAgICAgbGV0IGhvc3RlZEZpZWxkc0VsaWdpYmxlID0gcGF5cGFsLkhvc3RlZEZpZWxkcy5pc0VsaWdpYmxlKCk7XG4gICAgICAgICAgICBsZXQgYnJhbmRlZENyZWRpdENhcmRzT3B0aW9uID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignbGkuZ2FtYmlvX2h1Yi1QYXlQYWwySHViLWNyZWRpdGNhcmRidXR0b24nKTtcbiAgICAgICAgICAgIGlmIChob3N0ZWRGaWVsZHNFbGlnaWJsZSkge1xuICAgICAgICAgICAgICAgIGNvbnNvbGVJbmZvKCdQYXlQYWw6IGVsaWdpYmxlIGZvciBob3N0ZWQgZmllbGRzJyk7XG4gICAgICAgICAgICAgICAgaWYgKGJyYW5kZWRDcmVkaXRDYXJkc09wdGlvbikge1xuICAgICAgICAgICAgICAgICAgICBicmFuZGVkQ3JlZGl0Q2FyZHNPcHRpb24ucmVtb3ZlKCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIFxuICAgICAgICAgICAgICAgIGluaXRIb3N0ZWRDcmVkaXRDYXJkKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBlbHNlIHtcbiAgICAgICAgICAgICAgICBjb25zb2xlSW5mbygnUGF5UGFsOiBOT1QgZWxpZ2libGUgZm9yIGhvc3RlZCBmaWVsZHMnKTtcbiAgICAgICAgICAgICAgICBsZXQgY2NMaXN0SXRlbSA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJ2xpLmdhbWJpb19odWItUGF5UGFsMkh1Yi1jcmVkaXRjYXJkJyk7XG4gICAgICAgICAgICAgICAgY2NMaXN0SXRlbS5yZW1vdmUoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG5cdFx0aWYgKHBsdXNDb250YWluZXIgPT09IG51bGwgJiYgIXBheVBhbEJ1dHRvblNldHRpbmdzLnBheW1lbnRBcHByb3ZlZCkge1xuICAgICAgICAgICAgbGV0IGJhY2tCdXR0b25CbG9jayA9IGNvbnRpbnVlQnV0dG9uQmxvY2sucGFyZW50RWxlbWVudC5xdWVyeVNlbGVjdG9yKCdkaXYuYmFja19idXR0b24nKTtcbiAgICAgICAgICAgIGlmIChiYWNrQnV0dG9uQmxvY2sgIT09IG51bGwgJiYgYmFja0J1dHRvbkJsb2NrLmNsYXNzTGlzdC5jb250YWlucygnY29sLXhzLTYnKSkge1xuICAgICAgICAgICAgICAgIGJhY2tCdXR0b25CbG9jay5jbGFzc0xpc3QucmVtb3ZlKCdjb2wteHMtNicpO1xuICAgICAgICAgICAgICAgIGJhY2tCdXR0b25CbG9jay5jbGFzc0xpc3QuYWRkKCdjb2wteHMtNCcpO1xuICAgICAgICAgICAgICAgIGNvbnRpbnVlQnV0dG9uQmxvY2suY2xhc3NMaXN0LnJlbW92ZSgnY29sLXhzLTYnKTtcbiAgICAgICAgICAgICAgICBjb250aW51ZUJ1dHRvbkJsb2NrLmNsYXNzTGlzdC5hZGQoJ2NvbC14cy04Jyk7XG4gICAgICAgICAgICAgICAgbGV0IGJhY2tCdXR0b24gPSBiYWNrQnV0dG9uQmxvY2sucXVlcnlTZWxlY3RvcignYS5idG4nKTtcbiAgICAgICAgICAgICAgICBwYXlQYWxCdXR0b25TZXR0aW5ncy5zdHlsZS5oZWlnaHQgPSBwYXJzZUludChnZXRDb21wdXRlZFN0eWxlKGJhY2tCdXR0b24pLmhlaWdodCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGxldCBwYXlwYWxCdXR0b25Db250YWluZXIgPSBpbml0SlNTREtQYXlQYWxCdXR0b25FQ00oY29udGludWVCdXR0b25CbG9jayk7XG4gICAgICAgICAgICBsZXQgcGF5bGF0ZXJCdXR0b25Db250YWluZXIgPSBpbml0UGF5TGF0ZXJCdXR0b24oY29udGludWVCdXR0b25CbG9jayk7XG4gICAgICAgICAgICBsZXQgY3JlZGl0Q2FyZEJ1dHRvbkNvbnRhaW5lciA9IGluaXRDcmVkaXRDYXJkQnV0dG9uKGNvbnRpbnVlQnV0dG9uQmxvY2spO1xuICAgICAgICAgICAgbGV0IHNlcGFCdXR0b25Db250YWluZXIgPSBpbml0U2VwYUJ1dHRvbihjb250aW51ZUJ1dHRvbkJsb2NrKTtcblxuXHRcdFx0bGV0IHBheW1lbnRJdGVtQ2xpY2tMaXN0ZW5lciA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRsZXQgc2VsZWN0ZWRfcGF5bWVudCA9IHRoaXMucXVlcnlTZWxlY3RvcignaW5wdXRbbmFtZT1cInBheW1lbnRcIl0nKTtcblx0XHRcdFx0aWYgKG51bGwgIT09IHNlbGVjdGVkX3BheW1lbnQpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKHNlbGVjdGVkX3BheW1lbnQudmFsdWUgPT09ICdnYW1iaW9faHViLVBheVBhbDJIdWInIHx8XG4gICAgICAgICAgICAgICAgICAgICAgICAoc2VsZWN0ZWRfcGF5bWVudC52YWx1ZSA9PT0gJ2dhbWJpb19odWInICYmIHNlbGVjdGVkX3BheW1lbnQuZGF0YXNldC5tb2R1bGVfY29kZVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgID09PSAnUGF5UGFsMkh1YicpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjb250aW51ZUJ1dHRvbi5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICAgICAgICAgICAgICAgICAgcGF5cGFsQnV0dG9uQ29udGFpbmVyLnN0eWxlLmRpc3BsYXkgPSAnYmxvY2snO1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHBheWxhdGVyQnV0dG9uQ29udGFpbmVyICE9PSBudWxsKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcGF5bGF0ZXJCdXR0b25Db250YWluZXIuc3R5bGUuZGlzcGxheSA9ICdub25lJztcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChjcmVkaXRDYXJkQnV0dG9uQ29udGFpbmVyICE9PSBudWxsKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY3JlZGl0Q2FyZEJ1dHRvbkNvbnRhaW5lci5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHNlcGFCdXR0b25Db250YWluZXIgIT09IG51bGwpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzZXBhQnV0dG9uQ29udGFpbmVyLnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgZWxzZSBpZihzZWxlY3RlZF9wYXltZW50LnZhbHVlID09PSAnZ2FtYmlvX2h1Yi1QYXlQYWwySHViLXBheWxhdGVyJyAmJiBwYXlsYXRlckJ1dHRvbkNvbnRhaW5lciAhPT0gbnVsbCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgY29udGludWVCdXR0b24uc3R5bGUuZGlzcGxheSA9ICdub25lJztcbiAgICAgICAgICAgICAgICAgICAgICAgIHBheXBhbEJ1dHRvbkNvbnRhaW5lci5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICAgICAgICAgICAgICAgICAgcGF5bGF0ZXJCdXR0b25Db250YWluZXIuc3R5bGUuZGlzcGxheSA9ICdibG9jayc7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoY3JlZGl0Q2FyZEJ1dHRvbkNvbnRhaW5lciAhPT0gbnVsbCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNyZWRpdENhcmRCdXR0b25Db250YWluZXIuc3R5bGUuZGlzcGxheSA9ICdub25lJztcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChzZXBhQnV0dG9uQ29udGFpbmVyICE9PSBudWxsKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc2VwYUJ1dHRvbkNvbnRhaW5lci5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYoc2VsZWN0ZWRfcGF5bWVudC52YWx1ZSA9PT0gJ2dhbWJpb19odWItUGF5UGFsMkh1Yi1zZXBhJyAmJiBzZXBhQnV0dG9uQ29udGFpbmVyICE9PSBudWxsKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjb250aW51ZUJ1dHRvbi5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICAgICAgICAgICAgICAgICAgcGF5cGFsQnV0dG9uQ29udGFpbmVyLnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAocGF5bGF0ZXJCdXR0b25Db250YWluZXIgIT09IG51bGwpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBwYXlsYXRlckJ1dHRvbkNvbnRhaW5lci5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGNyZWRpdENhcmRCdXR0b25Db250YWluZXIgIT09IG51bGwpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjcmVkaXRDYXJkQnV0dG9uQ29udGFpbmVyLnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICBzZXBhQnV0dG9uQ29udGFpbmVyLnN0eWxlLmRpc3BsYXkgPSAnYmxvY2snO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYoc2VsZWN0ZWRfcGF5bWVudC52YWx1ZSA9PT0gJ2dhbWJpb19odWItUGF5UGFsMkh1Yi1jcmVkaXRjYXJkYnV0dG9uJyAmJiBjcmVkaXRDYXJkQnV0dG9uQ29udGFpbmVyICE9PSBudWxsKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjb250aW51ZUJ1dHRvbi5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICAgICAgICAgICAgICAgICAgcGF5cGFsQnV0dG9uQ29udGFpbmVyLnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG4gICAgICAgICAgICAgICAgICAgICAgICBjcmVkaXRDYXJkQnV0dG9uQ29udGFpbmVyLnN0eWxlLmRpc3BsYXkgPSAnYmxvY2snO1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHBheWxhdGVyQnV0dG9uQ29udGFpbmVyICE9PSBudWxsKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcGF5bGF0ZXJCdXR0b25Db250YWluZXIuc3R5bGUuZGlzcGxheSA9ICdub25lJztcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChzZXBhQnV0dG9uQ29udGFpbmVyICE9PSBudWxsKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc2VwYUJ1dHRvbkNvbnRhaW5lci5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgY29udGludWVCdXR0b24uc3R5bGUuZGlzcGxheSA9IGNvbnRpbnVlQnV0dG9uRGlzcGxheTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHBheXBhbEJ1dHRvbkNvbnRhaW5lci5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHBheWxhdGVyQnV0dG9uQ29udGFpbmVyICE9PSBudWxsKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcGF5bGF0ZXJCdXR0b25Db250YWluZXIuc3R5bGUuZGlzcGxheSA9ICdub25lJztcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChjcmVkaXRDYXJkQnV0dG9uQ29udGFpbmVyICE9PSBudWxsKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY3JlZGl0Q2FyZEJ1dHRvbkNvbnRhaW5lci5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHNlcGFCdXR0b25Db250YWluZXIgIT09IG51bGwpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzZXBhQnV0dG9uQ29udGFpbmVyLnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH1cblx0XHRcdFx0fVxuXHRcdFx0fTtcblxuXHRcdFx0bGV0IHBheW1lbnRJdGVtcyA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJyNjaGVja291dF9wYXltZW50IGlucHV0W25hbWU9XCJwYXltZW50XCJdLCAjY2hlY2tvdXRfcGF5bWVudCBsaS5saXN0LWdyb3VwLWl0ZW0nKTtcblx0XHRcdHBheW1lbnRJdGVtcy5mb3JFYWNoKGZ1bmN0aW9uKHBheW1lbnRJdGVtKSB7XG5cdFx0XHRcdHBheW1lbnRJdGVtLmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgcGF5bWVudEl0ZW1DbGlja0xpc3RlbmVyKVxuXHRcdFx0fSk7XG5cblx0XHRcdGxldCBwYXltZW50TGlzdEVudHJpZXMgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKCcjY2hlY2tvdXRfcGF5bWVudCBsaScpO1xuXHRcdFx0cGF5bWVudExpc3RFbnRyaWVzLmZvckVhY2goZnVuY3Rpb24ocGF5bWVudE9wdGlvbikge1xuXHRcdFx0XHRpZiAocGF5bWVudE9wdGlvbi5xdWVyeVNlbGVjdG9yKCdpbnB1dFtuYW1lPVwicGF5bWVudFwiXTpjaGVja2VkJykgfHwgcGF5bWVudExpc3RFbnRyaWVzLmxlbmd0aCA9PT0gMSkge1xuXHRcdFx0XHRcdHBheW1lbnRPcHRpb24uZGlzcGF0Y2hFdmVudChuZXcgRXZlbnQoJ2NsaWNrJykpO1xuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHR9XG5cdH1cblxuXHQvKioqKlxuXHQgKioqKiAgSW5zdGFsbG1lbnQgQmFubmVyc1xuIFx0ICovXG5cblx0bGV0IGluaXRJbnN0YWxsbWVudEJhbm5lcnMgPSBmdW5jdGlvbihhbW91bnQpIHtcblx0XHRjb25zb2xlSW5mbygnSW5pdGlhbGlzaW5nIFBheVBhbCBJbnN0YWxsbWVudHMgYmFubmVycycsICdDYXJ0QW1vdW50ID0gJyArIHBheVBhbEJhbm5lclNldHRpbmdzLmNhcnRBbW91bnQpO1xuXHRcdGxldCBiYW5uZXJzID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnLnBheXBhbC1pbnN0YWxsbWVudHMnKTtcblx0XHRiYW5uZXJzLmZvckVhY2goZnVuY3Rpb24oYmFubmVyRWxlbWVudCkge1xuXHRcdFx0bGV0IHBvc2l0aW9uID0gYmFubmVyRWxlbWVudC5kYXRhc2V0LnBwaW5zdFBvcztcblx0XHRcdGNvbnNvbGVJbmZvKCdmb3VuZCBiYW5uZXIgb24gcG9zaXRpb24gJyArIHBvc2l0aW9uKTtcblx0XHRcdGlmIChwYXlQYWxCYW5uZXJTZXR0aW5ncy5wb3NpdGlvbnMgJiYgcGF5UGFsQmFubmVyU2V0dGluZ3MucG9zaXRpb25zW3Bvc2l0aW9uXSkge1xuXHRcdFx0XHRjb25zb2xlSW5mbygnc2V0dGluZ3MgZm91bmQgZm9yIHBvc2l0aW9uICcgKyBwb3NpdGlvbiwgcGF5UGFsQmFubmVyU2V0dGluZ3MucG9zaXRpb25zW3Bvc2l0aW9uXSk7XG5cdFx0XHRcdGlmIChwYXlQYWxCYW5uZXJTZXR0aW5ncy5wb3NpdGlvbnNbcG9zaXRpb25dLnN0eWxlLmxheW91dCA9PT0gJ25vbmUnKSB7XG5cdFx0XHRcdFx0Y29uc29sZUluZm8oJ3Bvc2l0aW9uIGRpc2FibGVkOiAnICsgcG9zaXRpb24pO1xuXHRcdFx0XHRcdHJldHVybjtcblx0XHRcdFx0fVxuXG5cdFx0XHRcdGlmIChhbW91bnQgPT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgICAgICBhbW91bnQgPSAwLjAwO1xuICAgICAgICAgICAgICAgIH1cblx0XHRcdFx0aWYgKHBheVBhbEJhbm5lclNldHRpbmdzLmNhcnRBbW91bnQpIHtcblx0XHRcdFx0XHRhbW91bnQgKz0gcGF5UGFsQmFubmVyU2V0dGluZ3MuY2FydEFtb3VudDtcblx0XHRcdFx0fVxuICAgICAgICAgICAgICAgIGlmIChwYXlQYWxCYW5uZXJTZXR0aW5ncy5wcm9kdWN0c1ByaWNlKSB7XG4gICAgICAgICAgICAgICAgICAgIGFtb3VudCArPSBwYXlQYWxCYW5uZXJTZXR0aW5ncy5wcm9kdWN0c1ByaWNlO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG5cdFx0XHRcdFx0bGV0IHAgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCdkaXYuY3VycmVudC1wcmljZS1jb250YWluZXInKTtcblx0XHRcdFx0XHRpZihwKSB7XG5cdFx0XHRcdFx0XHRsZXQgcHJpY2VUZXh0ID0gcC5pbm5lclRleHQ7XG5cdFx0XHRcdFx0XHRwcmljZVRleHQgPSBwcmljZVRleHQucmVwbGFjZSgvLio/W1xcZCwuXStcXHMrXFxEezEsM30uKj8oW1xcZCwuXStcXHMrXFxEezEsM30pL3MsICckMScpO1xuXHRcdFx0XHRcdFx0YW1vdW50ICs9IDAuMDEgKiBwYXJzZUZsb2F0KHByaWNlVGV4dC5yZXBsYWNlKC8uKj8oKChcXGR7MSwzfVsuLF0pKykoXFxkezJ9KSkuKi8sICckMScpLnJlcGxhY2UoL1suLF0vZywgJycpKTtcblx0XHRcdFx0XHRcdGNvbnNvbGVJbmZvKCdQcm9kdWN0IGFtb3VudCBmb3IgYmFubmVyOiAnICsgYW1vdW50KTtcblx0XHRcdFx0XHR9XG4gICAgICAgICAgICAgICAgfVxuXHRcdFx0XHRpZiAoYW1vdW50IDwgOTkpIHtcblx0XHRcdFx0XHRjb25zb2xlSW5mbygnTm90IHNob3dpbmcgUGF5UGFsIEluc3RhbGxtZW50cyBiYW5uZXIgZm9yIGFtb3VudCAnICsgYW1vdW50KTtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuXHRcdFx0XHR9XG5cblx0XHRcdFx0aWYoYmFubmVyRWxlbWVudC5jbGFzc0xpc3QuY29udGFpbnMoJ3BheXBhbC1pbnN0YWxsbWVudHMtY2FydGJvdHRvbScpKSB7XG5cdFx0XHRcdFx0bGV0IG9ic2VydmVyVGFyZ2V0ID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignZGl2LnNob3BwaW5nLWNhcnQtYnV0dG9uJyk7XG5cdFx0XHRcdFx0aWYgKG9ic2VydmVyVGFyZ2V0KSB7XG5cdFx0XHRcdFx0XHRsZXQgY2FydFN1bUVsZW1lbnQgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCd0ci50b3RhbC5zdW0gdGQ6bnRoLWNoaWxkKDIpJyk7XG5cdFx0XHRcdFx0XHRsZXQgYW1vdW50U3RyaW5nID0gJzAnO1xuXHRcdFx0XHRcdFx0aWYoY2FydFN1bUVsZW1lbnQpIHtcblx0XHRcdFx0XHRcdFx0YW1vdW50U3RyaW5nID0gY2FydFN1bUVsZW1lbnQudGV4dENvbnRlbnQudHJpbSgpO1xuXHRcdFx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRcdFx0Y2FydFN1bUVsZW1lbnQgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCd0ci5mb290ZXIudG90YWwgdGQ6bnRoLWNoaWxkKDIpJyk7XG5cdFx0XHRcdFx0XHRcdGlmKGNhcnRTdW1FbGVtZW50KSB7XG5cdFx0XHRcdFx0XHRcdFx0YW1vdW50U3RyaW5nID0gY2FydFN1bUVsZW1lbnQudGV4dENvbnRlbnQudHJpbSgpXG5cdFx0XHRcdFx0XHRcdFx0XHQucmVwbGFjZSgvKFxcbnxcXHR8XFwufFxcLCkvZywgJycpXG5cdFx0XHRcdFx0XHRcdFx0XHQucmVwbGFjZSgvLio/KFswLTkuLF0rKVxccytFVVIuKi8sICckMScpO1xuXHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHR9XG4gICAgICAgICAgICAgICAgICAgICAgICBhbW91bnQgPSAwLjAxICogcGFyc2VJbnQoYW1vdW50U3RyaW5nLnJlcGxhY2UoL1teMC05XS9nLCAnJykpO1xuXHRcdFx0XHRcdFx0Y29uc29sZUluZm8oJ2NhcnQgYW1vdW50ICcgKyBhbW91bnQpO1xuXG5cdFx0XHRcdFx0XHRsZXQgb2JzZXJ2ZXIgPSBuZXcgTXV0YXRpb25PYnNlcnZlcihmdW5jdGlvbihtdXRhdGlvbnMsIG9ic2VydmVyKSB7XG5cdFx0XHRcdFx0XHRcdG11dGF0aW9ucy5mb3JFYWNoKGZ1bmN0aW9uKG11dGF0aW9uKSB7XG5cdFx0XHRcdFx0XHRcdFx0aWYobXV0YXRpb24ucmVtb3ZlZE5vZGVzLmxlbmd0aCA+IDApIHtcblx0XHRcdFx0XHRcdFx0XHRcdGNvbnNvbGVJbmZvKCdyZS1pbml0IFBheVBhbCBpbnN0YWxsbWVudHMgYmFubmVyJyk7XG5cdFx0XHRcdFx0XHRcdFx0XHRpbml0SW5zdGFsbG1lbnRCYW5uZXJzKCk7XG5cdFx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdFx0b2JzZXJ2ZXIub2JzZXJ2ZShvYnNlcnZlclRhcmdldCwge2NoaWxkTGlzdDogdHJ1ZX0pO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fVxuXG5cdFx0XHRcdHBheXBhbC5NZXNzYWdlcyh7XG5cdFx0XHRcdFx0YW1vdW50OiBhbW91bnQsXG5cdFx0XHRcdFx0Y3VycmVuY3k6IHBheVBhbEJhbm5lclNldHRpbmdzLmN1cnJlbmN5LFxuXHRcdFx0XHRcdHN0eWxlOiBwYXlQYWxCYW5uZXJTZXR0aW5ncy5wb3NpdGlvbnNbcG9zaXRpb25dLnN0eWxlLFxuXHRcdFx0XHRcdHBsYWNlbWVudDogcGF5UGFsQmFubmVyU2V0dGluZ3MucG9zaXRpb25zW3Bvc2l0aW9uXS5wbGFjZW1lbnRcblx0XHRcdFx0fSkucmVuZGVyKGJhbm5lckVsZW1lbnQpXG4gICAgICAgICAgICAgICAgICAgIC50aGVuKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgbGV0IGxlZ2FjeUluc3RhbGxtZW50Q29udGFpbmVyID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignLnBheXBhbGluc3RhbGxtZW50Y29udGFpbmVyJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZihsZWdhY3lJbnN0YWxsbWVudENvbnRhaW5lcikge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxlZ2FjeUluc3RhbGxtZW50Q29udGFpbmVyLnJlbW92ZSgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9KTtcblx0XHRcdH1cblx0XHR9KTtcblx0fTtcblxuXHRsZXQgcGF5cGFsU2RrTG9hZGVkID0gZnVuY3Rpb24oKSB7XG5cdFx0Y29uc29sZUluZm8oJ1BheVBhbFNESyBsb2FkZWQnKTtcblx0XHRpZih3aW5kb3cubG9jYXRpb24ucGF0aG5hbWUubWF0Y2goL3Nob3BwaW5nX2NhcnQucGhwLykpIHtcblx0XHRcdGNvbnNvbGVJbmZvKCdJbml0aWFsaXppbmcgRUNTIGJ1dHRvbicpO1xuXHRcdFx0aW5pdEpTU0RLUGF5UGFsQnV0dG9uRUNTKCk7XG5cdFx0fVxuXHRcdGlmKHdpbmRvdy5sb2NhdGlvbi5wYXRobmFtZS5tYXRjaCgvY2hlY2tvdXRfcGF5bWVudC5waHAvKSkge1xuXHRcdFx0Y29uc29sZUluZm8oJ0luaXRpYWxpemluZyBQYXlQYWwgb24gcGF5bWVudCBwYWdlJyk7XG5cdFx0XHRpbml0Q2hlY2tvdXRQYXltZW50KCk7XG5cdFx0fVxuXHRcdGluaXRJbnN0YWxsbWVudEJhbm5lcnMoKTtcblx0fTtcblxuICAgIGxldCBkaXNhYmxlSmF2YXNjcmlwdEZlYXR1cmVzID0gZnVuY3Rpb24oZGlzYWJsZSA9IHRydWUpIHtcbiAgICAgICAgLypcbiAgICAgICAgbGV0IHBwcGF5ID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcigndWw6bm90KC5wYXlwYWwzLXBsdXMtY2hlY2tvdXQpIGxpLmdhbWJpb19odWItUGF5UGFsMkh1YicpO1xuICAgICAgICBpZiAocHBwYXkpIHtcbiAgICAgICAgICAgIGNvbnNvbGVJbmZvKCdSZW1vdmluZyBQYXlQYWwgcGF5bWVudCBvcHRpb24nKTtcbiAgICAgICAgICAgIHBwcGF5LnJlbW92ZSgpO1xuICAgICAgICB9XG4gICAgICAgICovXG5cbiAgICAgICAgbGV0IG5ld1N0eWxlID0gZGlzYWJsZSA/ICdub25lJyA6ICdibG9jayc7XG4gICAgICAgIFxuICAgICAgICBsZXQgZWNzQnV0dG9uID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignZGl2I3BheXBhbC1uZXdidXR0b24nKTtcbiAgICAgICAgaWYgKGVjc0J1dHRvbikge1xuICAgICAgICAgICAgZWNzQnV0dG9uLnN0eWxlLmRpc3BsYXkgPSBuZXdTdHlsZTtcbiAgICAgICAgfVxuXG4gICAgICAgIGxldCBjY1BheW1lbnRPcHRpb24gPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjY2hlY2tvdXRfcGF5bWVudCBsaS5nYW1iaW9faHViLVBheVBhbDJIdWItY3JlZGl0Y2FyZCcpO1xuICAgICAgICBpZiAoY2NQYXltZW50T3B0aW9uKSB7XG4gICAgICAgICAgICBjY1BheW1lbnRPcHRpb24uc3R5bGUuZGlzcGxheSA9IG5ld1N0eWxlO1xuICAgICAgICB9XG4gICAgICAgIFxuICAgICAgICBsZXQgcHVpUGF5bWVudE9wdGlvbiA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyNjaGVja291dF9wYXltZW50IGxpLmdhbWJpb19odWItUGF5UGFsMkh1Yi1wdWknKTtcbiAgICAgICAgaWYgKHB1aVBheW1lbnRPcHRpb24pIHtcbiAgICAgICAgICAgIHB1aVBheW1lbnRPcHRpb24uc3R5bGUuZGlzcGxheSA9IG5ld1N0eWxlO1xuICAgICAgICB9XG4gICAgICAgIFxuICAgICAgICBsZXQgcGF5bGF0ZXJQYXltZW50T3B0aW9uID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignI2NoZWNrb3V0X3BheW1lbnQgbGkuZ2FtYmlvX2h1Yi1QYXlQYWwySHViLXBheWxhdGVyJyk7XG4gICAgICAgIGlmIChwYXlsYXRlclBheW1lbnRPcHRpb24pIHtcbiAgICAgICAgICAgIHBheWxhdGVyUGF5bWVudE9wdGlvbi5zdHlsZS5kaXNwbGF5ID0gbmV3U3R5bGU7XG4gICAgICAgIH1cbiAgICBcbiAgICAgICAgbGV0IHNlcGFQYXltZW50T3B0aW9uID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignI2NoZWNrb3V0X3BheW1lbnQgbGkuZ2FtYmlvX2h1Yi1QYXlQYWwySHViLXNlcGEnKTtcbiAgICAgICAgaWYgKHNlcGFQYXltZW50T3B0aW9uKSB7XG4gICAgICAgICAgICBzZXBhUGF5bWVudE9wdGlvbi5zdHlsZS5kaXNwbGF5ID0gbmV3U3R5bGU7XG4gICAgICAgIH1cbiAgICBcbiAgICAgICAgbGV0IGVjbVBheW1lbnRPcHRpb24gPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjY2hlY2tvdXRfcGF5bWVudCBsaS5nYW1iaW9faHViLVBheVBhbDJIdWInKTtcbiAgICAgICAgaWYgKGVjbVBheW1lbnRPcHRpb24gJiYgZGlzYWJsZSkge1xuICAgICAgICAgICAgbGV0IHBheXBhbE1vZGVJbnB1dCA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2lucHV0Jyk7XG4gICAgICAgICAgICBwYXlwYWxNb2RlSW5wdXQudHlwZSA9ICdoaWRkZW4nO1xuICAgICAgICAgICAgcGF5cGFsTW9kZUlucHV0Lm5hbWUgPSAnUGF5UGFsTW9kZSc7XG4gICAgICAgICAgICBwYXlwYWxNb2RlSW5wdXQudmFsdWUgPSAnc2RrbGVzcyc7XG4gICAgICAgICAgICBlY21QYXltZW50T3B0aW9uLmFwcGVuZENoaWxkKHBheXBhbE1vZGVJbnB1dCk7XG4gICAgICAgIH1cbiAgICB9XG4gICAgXG4gICAgbGV0IGRpc2FibGVQYXlQYWxPcHRpb25zID0gZnVuY3Rpb24oZGlzYWJsZSA9IHRydWUpIHtcbiAgICAgICAgbGV0IHBheW1lbnRPcHRpb25zID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnI2NoZWNrb3V0X3BheW1lbnQgbGkubGlzdC1ncm91cC1pdGVtJyk7XG4gICAgICAgIHBheW1lbnRPcHRpb25zLmZvckVhY2goKHBheW1lbnRPcHRpb24sIGtleSwgcGFyZW50KSA9PiB7XG4gICAgICAgICAgICBwYXltZW50T3B0aW9uLmNsYXNzTGlzdC5mb3JFYWNoKChjbGFzc05hbWUpID0+IHtcbiAgICAgICAgICAgICAgICBpZiAoY2xhc3NOYW1lLm1hdGNoKC8uKlBheVBhbDJIdWIuKi8pKSB7XG4gICAgICAgICAgICAgICAgICAgIGlmIChkaXNhYmxlKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBwYXltZW50T3B0aW9uLnF1ZXJ5U2VsZWN0b3IoJ2lucHV0W25hbWU9XCJwYXltZW50XCJdJykuc2V0QXR0cmlidXRlKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuICAgICAgICAgICAgICAgICAgICAgICAgcGF5bWVudE9wdGlvbi5zdHlsZS5vcGFjaXR5ID0gJzAuNSc7XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBwYXltZW50T3B0aW9uLnF1ZXJ5U2VsZWN0b3IoJ2lucHV0W25hbWU9XCJwYXltZW50XCJdJykucmVtb3ZlQXR0cmlidXRlKCdkaXNhYmxlZCcpO1xuICAgICAgICAgICAgICAgICAgICAgICAgcGF5bWVudE9wdGlvbi5zdHlsZS5vcGFjaXR5ID0gJzEnO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH0pO1xuICAgIH07XG4gICAgXG4gICAgbGV0IGVuYWJsZVBheVBhbE9wdGlvbnMgPSBmdW5jdGlvbigpIHtcbiAgICAgICAgZGlzYWJsZVBheVBhbE9wdGlvbnMoZmFsc2UpO1xuICAgIH1cbiAgICBcbiAgICBcblx0bGV0IHJlYWR5Q2FsbGJhY2sgPSBmdW5jdGlvbigpIHtcbiAgICAgICAgY29uc29sZUluZm8oJ3JlYWR5Q2FsbGJhY2sgc3RhcnQnKTtcblx0XHRsZXQganNzcmMgPSAnJztcbiAgICAgICAgaWYgKHR5cGVvZiAocGF5UGFsQnV0dG9uU2V0dGluZ3MpICE9PSAndW5kZWZpbmVkJyAmJiBwYXlQYWxCdXR0b25TZXR0aW5ncy5qc3NyYykge1xuICAgICAgICAgICAganNzcmMgPSBwYXlQYWxCdXR0b25TZXR0aW5ncy5qc3NyYztcbiAgICAgICAgfSBlbHNlIGlmICh0eXBlb2YgKHBheVBhbEJhbm5lclNldHRpbmdzKSAhPT0gJ3VuZGVmaW5lZCcgJiYgcGF5UGFsQmFubmVyU2V0dGluZ3MuanNzcmMpIHtcbiAgICAgICAgICAgIGpzc3JjID0gcGF5UGFsQmFubmVyU2V0dGluZ3MuanNzcmM7XG4gICAgICAgIH1cbiAgICAgICAgbGV0IHBhZ2VIYXNCYW5uZXJzID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnLnBheXBhbC1pbnN0YWxsbWVudHMnKS5sZW5ndGggPiAwO1xuICAgICAgICBsZXQgaXNDaGVja291dFBhZ2UgPSB3aW5kb3cubG9jYXRpb24ucGF0aG5hbWUubWF0Y2goL3Nob3BwaW5nX2NhcnQucGhwLykgfHxcbiAgICAgICAgICAgIHdpbmRvdy5sb2NhdGlvbi5wYXRobmFtZS5tYXRjaCgvY2hlY2tvdXRfcGF5bWVudC5waHAvKTtcbiAgICAgICAgXG4gICAgICAgIGxldCBjb25zZW50R2l2ZW4gPSB1bmRlZmluZWQ7XG4gICAgICAgIGxldCBwdXJwb3NlSWQgPSBudWxsO1xuICAgICAgICBsZXQgY29uc2VudERhdGFFbGVtZW50ID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3BheXBhbGNvbnNlbnQnKTtcbiAgICAgICAgaWYgKGNvbnNlbnREYXRhRWxlbWVudCAhPT0gbnVsbCkge1xuICAgICAgICAgICAgbGV0IGNvbnNlbnREYXRhID0gSlNPTi5wYXJzZShjb25zZW50RGF0YUVsZW1lbnQudGV4dENvbnRlbnQpO1xuICAgICAgICAgICAgcHVycG9zZUlkID0gY29uc2VudERhdGEucHVycG9zZV9pZDtcbiAgICAgICAgICAgIGxldCBneENvbnNlbnRzU3RyaW5nID0gZG9jdW1lbnQuY29va2llLnNwbGl0KCc7ICcpLmZpbmQoKHJvdykgPT4gcm93LnN0YXJ0c1dpdGgoJ0dYQ29uc2VudHM9JykpO1xuICAgICAgICAgICAgbGV0IGd4Q29uc2VudHMgPSBneENvbnNlbnRzU3RyaW5nID8gZ3hDb25zZW50c1N0cmluZy5zcGxpdCgnPScpWzFdIDogbnVsbDtcbiAgICAgICAgICAgIGlmIChneENvbnNlbnRzKSB7XG4gICAgICAgICAgICAgICAgbGV0IGd4Q29uc2VudHNEYXRhID0gSlNPTi5wYXJzZShneENvbnNlbnRzKTtcbiAgICAgICAgICAgICAgICBjb25zZW50R2l2ZW4gPSBneENvbnNlbnRzRGF0YS5wdXJwb3NlQ29uc2VudHNbJycrcHVycG9zZUlkXTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICAgICBcbiAgICAgICAgaWYgKGpzc3JjICYmIChwYWdlSGFzQmFubmVycyB8fCBpc0NoZWNrb3V0UGFnZSkgJiYgdHlwZW9mKHdpbmRvdy5wYXlwYWwpID09PSAndW5kZWZpbmVkJykge1xuICAgICAgICAgICAgbGV0IGxvYWRUaW1lb3V0ID0gbnVsbDtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgbGV0IG9uUGF5UGFsU2RrTG9hZCA9IGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIGNsZWFyVGltZW91dChsb2FkVGltZW91dCk7XG4gICAgICAgICAgICAgICAgcGF5cGFsU2RrTG9hZGVkKCk7XG4gICAgICAgICAgICAgICAgZGlzYWJsZUphdmFzY3JpcHRGZWF0dXJlcyhmYWxzZSk7XG4gICAgICAgICAgICAgICAgZW5hYmxlUGF5UGFsT3B0aW9ucygpO1xuICAgICAgICAgICAgfTtcbiAgICAgICAgICAgIGxldCBvblBheVBhbFNka0Vycm9yID0gZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgY29uc29sZUluZm8oJ0VSUk9SIGxvYWRpbmcgUGF5UGFsIEphdmFzY3JpcHQgLSBibG9ja2VkPycpO1xuICAgICAgICAgICAgICAgIGNvbnNvbGVJbmZvKCdQYXlQYWwgSlNTREsgdW5hdmFpbGFibGUsIGRpc2FibGluZyBmZWF0dXJlcycpO1xuICAgICAgICAgICAgICAgIGRpc2FibGVKYXZhc2NyaXB0RmVhdHVyZXMoKTtcbiAgICAgICAgICAgICAgICBlbmFibGVQYXlQYWxPcHRpb25zKCk7XG4gICAgICAgICAgICB9O1xuICAgICAgICAgICAgd2luZG93LmluaXRQYXlQYWxKU1NESyA9IGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIGlmICh0eXBlb2Yod2luZG93LnBheXBhbCkgIT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgbGV0IGpzc2RrID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnc2NyaXB0Jyk7XG4gICAgICAgICAgICAgICAganNzZGsuc3JjID0gcGF5UGFsQnV0dG9uU2V0dGluZ3MuanNzcmMgPyBwYXlQYWxCdXR0b25TZXR0aW5ncy5qc3NyYyA6IHBheVBhbEJhbm5lclNldHRpbmdzLmpzc3JjO1xuICAgIFxuICAgICAgICAgICAgICAgIGxldCBhY2RjSWRlbnRpdHkgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjcGF5cGFsX2NjX2lkZW50aXR5Jyk7XG4gICAgICAgICAgICAgICAgaWYgKGFjZGNJZGVudGl0eSAhPT0gbnVsbCkge1xuICAgICAgICAgICAgICAgICAgICBsZXQgY2xpZW50SWRlbnRpdHkgPSBKU09OLnBhcnNlKGFjZGNJZGVudGl0eS50ZXh0Q29udGVudCk7XG4gICAgICAgICAgICAgICAgICAgIGlmICh0eXBlb2YgY2xpZW50SWRlbnRpdHkuY2xpZW50X3Rva2VuICE9PSAndW5kZWZpbmVkJykge1xuICAgICAgICAgICAgICAgICAgICAgICAganNzZGsuZGF0YXNldC5jbGllbnRUb2tlbiA9IGNsaWVudElkZW50aXR5LmNsaWVudF90b2tlbjtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBqc3Nkay5vbmVycm9yID0gb25QYXlQYWxTZGtFcnJvcjtcbiAgICAgICAgICAgICAgICBqc3Nkay5vbmxvYWQgPSBvblBheVBhbFNka0xvYWQ7XG4gICAgXG4gICAgICAgICAgICAgICAgaWYoZG9jdW1lbnQuY3VycmVudFNjcmlwdCkge1xuICAgICAgICAgICAgICAgICAgICBkb2N1bWVudC5jdXJyZW50U2NyaXB0LnBhcmVudE5vZGUuaW5zZXJ0QmVmb3JlKGpzc2RrLCBkb2N1bWVudC5jdXJyZW50U2NyaXB0KTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCdzY3JpcHQ6bGFzdC1vZi10eXBlJykucGFyZW50Tm9kZS5hcHBlbmRDaGlsZChqc3Nkayk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICAgICAgXG4gICAgICAgICAgICBkaXNhYmxlUGF5UGFsT3B0aW9ucygpO1xuICAgICAgICAgICAgaWYgKHB1cnBvc2VJZCAhPT0gbnVsbCkge1xuICAgICAgICAgICAgICAgIGNvbnNvbGVJbmZvKFwiRXh0ZXJuYWwgUGF5UGFsIEphdmFzY3JpcHQgaXMgbWFuYWdlZCBieSBDb29raWVDb25zZW50LCBwdXJwb3NlIFwiICsgcHVycG9zZUlkKTtcbiAgICAgICAgICAgICAgICBpZiAod2luZG93LlBheVBhbEFzT2lsQ2xlYXJUb0xvYWQpIHtcbiAgICAgICAgICAgICAgICAgICAgY29uc29sZUluZm8oJ09JTCBoYXMgY2xlYXJlZCBQYXlQYWwgdG8gbG9hZCcpO1xuICAgICAgICAgICAgICAgICAgICB3aW5kb3cuaW5pdFBheVBhbEpTU0RLKCk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgY29uc29sZUluZm8oJ09JTCBoYXMgbm90IHlldCBjbGVhcmVkIFBheVBhbCB0byBsb2FkJyk7XG4gICAgICAgICAgICAgICAgICAgIGRpc2FibGVKYXZhc2NyaXB0RmVhdHVyZXMoKTtcbiAgICAgICAgICAgICAgICAgICAgd2luZG93LmFkZEV2ZW50TGlzdGVuZXIoJ1BheVBhbENsZWFyZWRUb0xvYWQnLCBldnQgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgY29uc29sZUluZm8oJ09JTCBoYXMgY2xlYXJlZCBQYXlQYWwgdG8gbG9hZCAoRXZlbnQpJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICB3aW5kb3cuaW5pdFBheVBhbEpTU0RLKCk7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICBpZiAoaXNDaGVja291dFBhZ2UpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnNvbGVJbmZvKCdvbiBjaGVja291dCBwYWdlOyB3YWl0aW5nIDVzIGZvciBPSUwnKVxuICAgICAgICAgICAgICAgICAgICAgICAgbG9hZFRpbWVvdXQgPSBzZXRUaW1lb3V0KG9uUGF5UGFsU2RrRXJyb3IsIDUwMDApO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICBjb25zb2xlLmluZm8oXCJOb3QgdXNpbmcgQ29va2llQ29uc2VudCBpbnRlZ3JhdGlvbiBmb3IgUGF5UGFsLCBsb2FkaW5nIGRpcmVjdGx5XCIpO1xuICAgICAgICAgICAgICAgIHdpbmRvdy5pbml0UGF5UGFsSlNTREsoKTtcbiAgICAgICAgICAgIH1cblx0XHR9IGVsc2Uge1xuXHRcdFx0Y29uc29sZUluZm8oJ1BheVBhbCBKU1NESyB1bmF2YWlsYWJsZSBvciBub3QgcmVxdWlyZWQsIGRpc2FibGluZyBmZWF0dXJlcycpO1xuICAgICAgICAgICAgZGlzYWJsZUphdmFzY3JpcHRGZWF0dXJlcygpO1xuXHRcdH1cbiAgICAgICAgY29uc29sZUluZm8oJ3JlYWR5Q2FsbGJhY2sgZW5kJyk7XG5cdH07XG5cblx0bGV0IG1haW5DYWxsYmFjayA9IChcImZldGNoXCIgaW4gd2luZG93KSA/IHJlYWR5Q2FsbGJhY2sgOiBoYW5kbGVBbnRpcXVlQnJvd3Nlcjtcblx0XG5cdGlmKGRvY3VtZW50LnJlYWR5U3RhdGUgPT09ICdjb21wbGV0ZScgfHwgKGRvY3VtZW50LnJlYWR5U3RhdGUgIT09ICdsb2FkaW5nJyAmJiAhZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50LmRvU2Nyb2xsKSkge1xuICAgICAgICBjb25zb2xlSW5mbygnUmVhZHlzdGF0ZSBjb21wbGV0ZScpO1xuXHRcdG1haW5DYWxsYmFjaygpO1xuXHR9IGVsc2Uge1xuICAgICAgICBjb25zb2xlSW5mbygnQWRkaW5nIEV2ZW50TGlzdGVuZXIgZm9yIERPTUNvbnRlbnRMb2FkZWQnKTtcblx0XHRkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCdET01Db250ZW50TG9hZGVkJywgbWFpbkNhbGxiYWNrKTtcblx0fVxuXHRcbn0oKSk7XG4iXX0=
