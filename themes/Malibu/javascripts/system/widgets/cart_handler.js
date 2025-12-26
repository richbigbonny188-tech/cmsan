'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/* --------------------------------------------------------------
 cart_handler.js 2023-11-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Component for handling the add to cart and wishlist features
 * at the product details and the category listing pages. It cares
 * for attributes, properties, quantity and all other
 * relevant data for adding an item to the basket or wishlist
 */
gambio.widgets.module('cart_handler', ['hooks', 'form', 'xhr', 'loading_spinner', gambio.source + '/libs/events', gambio.source + '/libs/modal.ext-magnific', gambio.source + '/libs/modal'], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    $body = $('body'),
	    $window = $(window),
	    busy = false,
	    ajax = null,
	    timeout = 0,
	    previousModifiers = {},
	    defaults = {
		// AJAX "add to cart" URL
		addCartUrl: 'shop.php?do=Cart/BuyProduct',
		// AJAX "add to cart" URL for customizer products
		addCartCustomizerUrl: 'shop.php?do=Cart/Add',
		// AJAX URL to perform a value check
		checkUrl: 'shop.php?do=CheckStatus',
		// AJAX URL to perform the add to wishlist
		wishlistUrl: 'shop.php?do=WishList/Add',
		// Submit URL for price offer button
		priceOfferUrl: 'gm_price_offer.php',
		// Submit method for price offer
		priceOfferMethod: 'get',
		// Selector for the cart dropdown
		dropdown: '#head_shopping_cart',
		// "Add to cart" buttons selectors
		cartButtons: '.js-btn-add-to-cart',
		// "Wishlist" buttons selectors
		wishlistButtons: '.btn-wishlist',
		// "Price offer" buttons selectors
		priceOfferButtons: '.btn-price-offer',
		// Selector for the attribute fields
		attributes: '.js-calculate',
		// Selector for product property
		productOptions: '.modifier-group .modifier-content .modifier-item',
		productOptionField: '.hidden-input',
		// Selector for the quantity
		quantity: '.js-calculate-qty',
		// URL where to get the theme for the dropdown
		tpl: null,
		// Show attribute images in product images swiper (if possible)
		// -- this feature is not supported yet --
		attributImagesSwiper: false,
		// Trigger the attribute images to this selectors
		triggerAttrImagesTo: '#product_image_swiper, #product_thumbnail_swiper, ' + '#product_thumbnail_swiper_mobile',
		// Class that gets added to the button on processing
		processingClass: 'loading',
		// Duration for that the success or fail class gets added to the button
		processingDuration: 2000,
		// AJAX response content selectors
		selectorMapping: {
			buttons: '.shopping-cart-button',
			giftContent: '.gift-cart-content-wrapper',
			giftLayer: '.gift-cart-layer',
			shareContent: '.share-cart-content-wrapper',
			shareLayer: '.share-cart-layer',
			hiddenOptions: '#cart_quantity .hidden-options',
			message: '.global-error-messages',
			messageCart: '.cart-error-msg',
			messageHelp: '.help-block',
			modelNumber: '.model-number',
			modelNumberText: '.model-number-text',
			price: '.current-price-container',
			modifiersForm: '.modifiers-selection',
			quantity: '.products-quantity-value',
			quantityInfo: '.products-quantity',
			ribbonSpecial: '.ribbon-special',
			shippingInformation: '#shipping-information-layer',
			shippingTime: '.products-shipping-time-value',
			shippingTimeImage: '.img-shipping-time img',
			totals: '#cart_quantity .total-box',
			weight: '.products-details-weight-container span',
			abroadShippingInfo: '.abroad-shipping-info'
		},
		page: 'product-listing'
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {},
	    mobile = $(window).width() <= 767;

	// ########## HELPER FUNCTIONS ##########

	/**
  * Helper function that updates the button
  * state with an error or success class for
  * a specified duration
  * @param   {object}        $target         jQuery selection of the target button
  * @param   {string}        state           The state string that gets added to the loading class
  * @private
  */
	var _addButtonState = function _addButtonState($target, state) {
		var timer = setTimeout(function () {
			$target.removeClass(options.processingClass + ' ' + options.processingClass + state);
		}, options.processingDuration);

		$target.data('timer', timer).addClass(options.processingClass + state);
	};

	/**
  * Helper function to set the messages and the
  * button state.
  * @param       {object}    data                Result form the ajax request
  * @param       {object}    $form               jQuery selecion of the form
  * @param       {boolean}   disableButtons      If true, the button state gets set to (in)active
  * @param       {boolean}   showNoCombiMesssage If true, the error message for missing property combination selection will be displayed
  * @private
  */
	var _stateManager = function _stateManager(data, $form, disableButtons, showNoCombiSelectedMesssage) {

		// Remove the attribute images from the common content
		// so that it doesn't get rendered anymore. Then trigger
		// an event to the given selectors and deliver the
		// attrImages object
		if (options.attributImagesSwiper && data.attrImages && data.attrImages.length) {
			delete data.content.images;
			$(options.triggerAttrImagesTo).trigger(jse.libs.theme.events.SLIDES_UPDATE(), { attributes: data.attrImages });
		}

		// Set the messages given inside the data.content object
		$.each(data.content, function (i, v) {
			var $element = $body.hasClass('page-product-info') ? $this.find(options.selectorMapping[v.selector]) : $form.parent().find(options.selectorMapping[v.selector]);

			if ((!showNoCombiSelectedMesssage || v.value === '') && i === 'messageNoCombiSelected') {
				return true;
			}

			switch (v.type) {
				case 'hide':
					if (v.value === 'true') {
						$element.hide();
					} else {
						$element.show();
					}
					break;
				case 'html':
					$element.html(v.value);
					break;
				case 'attribute':
					$element.attr(v.key, v.value);
					break;
				case 'replace':
					if (v.value) {
						$element.replaceWith(v.value);
					} else {
						$element.addClass('hidden').empty();
					}
					break;
				default:
					$element.text(v.value);
					break;
			}
		});

		// Dis- / Enable the buttons
		if (disableButtons) {
			var $buttons = $form.find(options.cartButtons);
			if (data.success) {
				$buttons.removeClass('inactive');
				$buttons.removeClass('btn-inactive');
				$buttons.prop("disabled", false);
			} else {
				$buttons.addClass('inactive');
				$buttons.addClass('btn-inactive');
				$buttons.prop("disabled", true);
			}
		}

		if (data.content.message) {
			var $errorField = $form.find(options.selectorMapping[data.content.message.selector]);
			if (data.content.message.value) {
				$errorField.removeClass('hidden').show();
			} else {
				$errorField.addClass('hidden').hide();

				if (showNoCombiSelectedMesssage && data.content.messageNoCombiSelected !== undefined && data.content.messageNoCombiSelected) {
					if (data.content.messageNoCombiSelected.value) {
						$errorField.removeClass('hidden').show();
					} else {
						$errorField.addClass('hidden').hide();
					}
				}
			}
		}

		$window.trigger(jse.libs.theme.events.STICKYBOX_CONTENT_CHANGE());
	};

	/**
  * Helper function to send the ajax
  * On success redirect to a given url, open a layer with
  * a message or add the item to the cart-dropdown directly
  * (by triggering an event to the body)
  * @param       {object}      data      Form data
  * @param       {object}      $form     The form to fill
  * @param       {string}      url       The URL for the AJAX request
  * @private
  */
	var _addToSomewhere = function _addToSomewhere(data, $form, url, $button) {
		function callback() {
			jse.libs.xhr.post({ url: url, data: data }, true).done(function (result) {
				try {
					// Fill the page with the result from the ajax
					_stateManager(result, $form, false);

					// If the AJAX was successful execute
					// a custom functionality
					if (result.success) {
						switch (result.type) {
							case 'url':
								if (result.url.substr(0, 4) !== 'http') {
									location.href = jse.core.config.get('appUrl') + '/' + result.url;
								} else {
									location.href = result.url;
								}

								break;
							case 'dropdown':
								$body.trigger(jse.libs.theme.events.CART_UPDATE(), [true]);
								break;
							case 'layer':
								jse.libs.theme.modal.info({ title: result.title, content: result.msg });
								break;
							default:
								break;
						}
					}
				} catch (ignore) {}
				_addButtonState($button, '-success');
			}).fail(function () {
				_addButtonState($button, '-fail');
			}).always(function () {
				// Reset the busy flag to be able to perform
				// further AJAX requests
				busy = false;
			});
		}

		if (!busy) {
			// only execute the ajax
			// if there is no pending ajax call
			busy = true;

			jse.libs.hooks.execute(jse.libs.hooks.keys.shop.cart.add, data, 500).then(callback).catch(callback);
		}
	};

	// ########## EVENT HANDLER ##########

	/**
  * Handler for the submit form / click
  * on "add to cart" & "wishlist" button.
  * It performs a check on the availability
  * of the combination and quantity. If
  * successful it performs the add to cart
  * or wishlist action, if it's not a
  * "check" call
  * @param       {object}    e      jQuery event object
  * @private
  */
	var _submitHandler = function _submitHandler(e) {
		if (e) {
			e.preventDefault();
		}

		var $self = $(this),
		    $form = $self.is('form') ? $self : $self.closest('form'),
		    customizer = $form.hasClass('customizer'),
		    properties = !!$form.find('.properties-selection-form').length,
		    module = properties ? '' : '/Attributes',
		    showNoCombiSelectedMesssage = e && e.data && e.data.target && e.data.target !== 'check';

		if ($form.length) {

			// Show properties overlay
			// to disable user interaction
			// before markup replace
			if (properties) {
				$this.addClass('loading');
			}

			if ($self.is('select')) {
				var price = $self.find(":selected").attr('data-price');
				$self.parents('.modifier-group').find('.selected-value-price').text(price);
			}

			var getGalleryHash = $('#current-gallery-hash').val();
			$form.find('#update-gallery-hash').val(getGalleryHash);

			var formdata = jse.libs.form.getData($form, null, true);
			formdata.target = e && e.data && e.data.target ? e.data.target : 'check';
			formdata.isProductInfo = $form.hasClass('product-info') ? 1 : 0;

			// Abort previous check ajax if
			// there is one in progress
			if (ajax && e) {
				ajax.abort();
			}

			// Add processing-class to the button
			// and remove old timed events
			if (formdata.target !== 'check') {
				var timer = $self.data('timer');
				if (timer) {
					clearTimeout(timer);
				}

				$self.removeClass(options.processingClass + '-success ' + options.processingClass + '-fail').addClass(options.processingClass);
			}

			formdata.previousModifiers = previousModifiers;

			ajax = jse.libs.xhr.get({
				url: options.checkUrl + module,
				data: formdata
			}, true).done(function (result) {
				_stateManager(result, $form, true, showNoCombiSelectedMesssage);
				$this.removeClass('loading');

				// Check if the gallery images changed
				if (formdata.target === 'check' && result.content.imageGallery.trim() !== '' && result.content.replaceGallery === true && formdata.isProductInfo === 1) {
					var loadingSpinner = jse.libs.loading_spinner.show($('.product-info-stage'), 9999);

					var swipers = [$('#product_image_swiper'), $('#product_thumbnail_swiper'), $('#product_thumbnail_swiper_mobile')];

					var _iteratorNormalCompletion = true;
					var _didIteratorError = false;
					var _iteratorError = undefined;

					try {
						for (var _iterator = swipers[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
							var element = _step.value;

							var instance = element.swiper();

							if ((typeof instance === 'undefined' ? 'undefined' : _typeof(instance)) !== 'object') {
								continue;
							}

							instance.destroy(true, true);
							element.off().remove();
						}
					} catch (err) {
						_didIteratorError = true;
						_iteratorError = err;
					} finally {
						try {
							if (!_iteratorNormalCompletion && _iterator.return) {
								_iterator.return();
							}
						} finally {
							if (_didIteratorError) {
								throw _iteratorError;
							}
						}
					}

					$('#image-collection-container').html(result.content.imageGallery);
					$('#product_image_layer').html(result.content.imageModal);

					gambio.widgets.init($('.product-info-content')).done(function () {
						jse.libs.loading_spinner.hide(loadingSpinner);
					});
				} else if (formdata.target === 'check' && result.content.imageGallery.trim() === '' && result.content.replaceGallery === true) {
					$('#image-collection-container').html(result.content.imageGallery);
					$('#product_image_layer').html(result.content.imageModal);
				}

				if (result.success) {
					var event = null,
					    url = null;

					switch (formdata.target) {
						case 'wishlist':
							if (customizer) {
								event = jse.libs.theme.events.ADD_CUSTOMIZER_WISHLIST();
							}
							url = options.wishlistUrl;
							break;
						case 'cart':
							if (customizer) {
								event = jse.libs.theme.events.ADD_CUSTOMIZER_CART();
								url = options.addCartCustomizerUrl;
							} else {
								url = options.addCartUrl;
							}
							break;
						case 'price_offer':
							$form.attr('action', options.priceOfferUrl).attr('method', options.priceOfferMethod);
							$form.off('submit');
							$form.submit();

							return;
						default:
							setTimeout(function () {
								$window.trigger(jse.libs.theme.events.STICKYBOX_CONTENT_CHANGE());
							}, 250);
							break;
					}

					if (event) {
						var deferred = $.Deferred();
						deferred.done(function (customizerRandom) {
							formdata[customizerRandom] = 0;
							_addToSomewhere(formdata, $form, url, $self);
						}).fail(function () {
							_addButtonState($self, '-fail');
						});
						$body.trigger(event, [{ 'deferred': deferred, 'dataset': formdata }]);
					} else if (url) {
						_addToSomewhere(formdata, $form, url, $self);
					}
				} else {
					var $btnFake = $this.find(".btn-add-to-cart-fake");
					if ($btnFake) {
						$btnFake.hide().prop("disabled", false).find('.throbbler').remove();
					}

					var $buttons = $form.find(options.cartButtons);
					if ($buttons) {
						$buttons.removeClass('btn-inactive inactive').prop("disabled", false).show();
					}
				}

				if (formdata.target === 'check') {
					previousModifiers = formdata.modifiers;
				}
			}).fail(function () {
				_addButtonState($self, '-fail');
			});
		}
	};

	/**
  * Handler for the change property option
  * */
	var _changeProductOptions = function _changeProductOptions(e) {
		var option = e.currentTarget;
		var optionValue = $(option).data('value');
		var optionContainer = $(option).parents('.modifier-group');

		$(optionContainer).find('li.active').removeClass('active');
		$(optionContainer).find('.modifier-item.active-modifier').removeClass('active-modifier');
		$(optionContainer).find('input.hidden-input').val(optionValue);
		$(optionContainer).find('input.hidden-input').trigger('blur', []);

		$(option).parents('li').addClass('active');
		$(option).addClass('active-modifier');
	};

	var _selectSelectedModifierInfo = function _selectSelectedModifierInfo(e) {
		var option = e.currentTarget;
		var price = $(option).attr('data-price');
		var label = $(option).attr('data-label');
		$(option).parents('.modifier-group').find('.selected-value-price').removeClass('temporary-value').attr('data-default-price', price);
		$(option).parents('.modifier-group').find('.selected-value').attr('data-default-value', label);
	};

	var _setSelectedModifierInfo = function _setSelectedModifierInfo(e) {
		var option = e.currentTarget;
		if (!$(option).parent().hasClass('active') && !$(option).is('select') && !$(option).hasClass('active-modifier')) {
			var price = $(option).attr('data-price');
			var label = $(option).attr('data-label');
			$(option).parents('.modifier-group').find('.selected-value-price').addClass('temporary-value').text(price);
			$(option).parents('.modifier-group').find('.selected-value').text(label);
		}
	};

	var _resetSelectedModifierInfo = function _resetSelectedModifierInfo(e) {
		var option = $(this);
		if (!$(option).parent().hasClass('active') && !$(option).is('select') && !$(option).hasClass('active-modifier')) {
			var priceHolder = $(option).parents('.modifier-group').find('.selected-value-price');
			var labelHolder = $(option).parents('.modifier-group').find('.selected-value');
			$(priceHolder).removeClass('temporary-value').text($(priceHolder).attr('data-default-price'));
			$(labelHolder).text($(labelHolder).attr('data-default-value'));
		}
	};

	/**
  * Keyup handler for quantity input field
  *
  * @param e
  * @private
  */
	var _keyupHandler = function _keyupHandler(e) {
		clearTimeout(timeout);

		timeout = setTimeout(function () {
			_submitHandler.call(this, e);
		}.bind(this), 300);
	};

	/**
  * Event handler for the add to cart button, that shows or hides the throbber.
  */
	var _addToCartThrobberHandler = function _addToCartThrobberHandler(e) {
		var $btn = $(this);
		var $btnFake = $this.find(".btn-add-to-cart-fake");
		var formReady = true;

		$(".properties-selection-form select").each(function () {
			var val = $(this).val();
			if (!val || val < 1) {
				formReady = false;
			}
		});

		if (formReady) {
			$btn.hide();
			$btnFake.show().prop("disabled", true).prepend('<span class="throbbler"></span>');
		}
	};

	/**
  * Cart dropdown oben event handler for the body.
  */
	var _cartDropdownOpenHandler = function _cartDropdownOpenHandler(e) {
		var $btn = $this.find("[name=btn-add-to-cart]");
		var $btnFake = $this.find(".btn-add-to-cart-fake");
		var fakeOrigLabel = $btnFake.html();
		var productCount = $(".cart-products-count").html();

		var textPhrases = JSON.parse($('#product-details-text-phrases').html());
		console.log(textPhrases['productsInCartSuffix']);

		$btnFake.html("<i class=\"fa fa-check\"></i> " + parseInt(productCount) + textPhrases['productsInCartSuffix']).prop("disabled", true).addClass("btn-buy-complete");

		setTimeout(function () {
			$btnFake.html(fakeOrigLabel).removeClass("btn-buy-complete").hide().prop("disabled", false);
			$(".throbbler", $btn).remove();
			$btn.show();
		}, 5000);
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		var $forms = $this.find('form');

		if (options.page === 'product-info') {
			$forms.find("[name=btn-add-to-cart]").on('touchstart touchmove touchend touchcancel', function () {
				return $forms.find("[name=btn-add-to-cart]").click();
			});
			$forms.find("[name=btn-add-to-cart]").on('mouseup', _addToCartThrobberHandler);
			$("body").on('CART_DROPDOWN_OPEN', _cartDropdownOpenHandler);
		}

		$forms.on('submit', { 'target': 'cart' }, _submitHandler).on('click', options.wishlistButtons, { 'target': 'wishlist' }, _submitHandler).on('click', options.priceOfferButtons, { 'target': 'price_offer' }, _submitHandler).on('change', options.attributes, { 'target': 'check' }, _submitHandler).on('mouseover', options.attributes, _setSelectedModifierInfo).on('mouseout', options.attributes, _resetSelectedModifierInfo).on('blur', options.productOptionField, { 'target': 'check' }, _submitHandler).on('click', options.productOptions, { 'target': 'check' }, function (e) {
			_selectSelectedModifierInfo(e);
			_changeProductOptions(e);
		}).on('mouseover', options.productOptions, _setSelectedModifierInfo).on('mouseout', options.productOptions, _resetSelectedModifierInfo).on('blur', options.quantity, { 'target': 'check' }, function (e) {
			_submitHandler(e);
		}).on('keyup', options.quantity, { 'target': 'check' }, _keyupHandler);

		// Fallback if the backend renders incorrect data
		// on initial page call
		$forms.not('.no-status-check').not('.product-info').each(function () {
			_submitHandler.call($(this));
		});
		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvY2FydF9oYW5kbGVyLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJzb3VyY2UiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJGJvZHkiLCIkd2luZG93Iiwid2luZG93IiwiYnVzeSIsImFqYXgiLCJ0aW1lb3V0IiwicHJldmlvdXNNb2RpZmllcnMiLCJkZWZhdWx0cyIsImFkZENhcnRVcmwiLCJhZGRDYXJ0Q3VzdG9taXplclVybCIsImNoZWNrVXJsIiwid2lzaGxpc3RVcmwiLCJwcmljZU9mZmVyVXJsIiwicHJpY2VPZmZlck1ldGhvZCIsImRyb3Bkb3duIiwiY2FydEJ1dHRvbnMiLCJ3aXNobGlzdEJ1dHRvbnMiLCJwcmljZU9mZmVyQnV0dG9ucyIsImF0dHJpYnV0ZXMiLCJwcm9kdWN0T3B0aW9ucyIsInByb2R1Y3RPcHRpb25GaWVsZCIsInF1YW50aXR5IiwidHBsIiwiYXR0cmlidXRJbWFnZXNTd2lwZXIiLCJ0cmlnZ2VyQXR0ckltYWdlc1RvIiwicHJvY2Vzc2luZ0NsYXNzIiwicHJvY2Vzc2luZ0R1cmF0aW9uIiwic2VsZWN0b3JNYXBwaW5nIiwiYnV0dG9ucyIsImdpZnRDb250ZW50IiwiZ2lmdExheWVyIiwic2hhcmVDb250ZW50Iiwic2hhcmVMYXllciIsImhpZGRlbk9wdGlvbnMiLCJtZXNzYWdlIiwibWVzc2FnZUNhcnQiLCJtZXNzYWdlSGVscCIsIm1vZGVsTnVtYmVyIiwibW9kZWxOdW1iZXJUZXh0IiwicHJpY2UiLCJtb2RpZmllcnNGb3JtIiwicXVhbnRpdHlJbmZvIiwicmliYm9uU3BlY2lhbCIsInNoaXBwaW5nSW5mb3JtYXRpb24iLCJzaGlwcGluZ1RpbWUiLCJzaGlwcGluZ1RpbWVJbWFnZSIsInRvdGFscyIsIndlaWdodCIsImFicm9hZFNoaXBwaW5nSW5mbyIsInBhZ2UiLCJvcHRpb25zIiwiZXh0ZW5kIiwibW9iaWxlIiwid2lkdGgiLCJfYWRkQnV0dG9uU3RhdGUiLCIkdGFyZ2V0Iiwic3RhdGUiLCJ0aW1lciIsInNldFRpbWVvdXQiLCJyZW1vdmVDbGFzcyIsImFkZENsYXNzIiwiX3N0YXRlTWFuYWdlciIsIiRmb3JtIiwiZGlzYWJsZUJ1dHRvbnMiLCJzaG93Tm9Db21iaVNlbGVjdGVkTWVzc3NhZ2UiLCJhdHRySW1hZ2VzIiwibGVuZ3RoIiwiY29udGVudCIsImltYWdlcyIsInRyaWdnZXIiLCJqc2UiLCJsaWJzIiwidGhlbWUiLCJldmVudHMiLCJTTElERVNfVVBEQVRFIiwiZWFjaCIsImkiLCJ2IiwiJGVsZW1lbnQiLCJoYXNDbGFzcyIsImZpbmQiLCJzZWxlY3RvciIsInBhcmVudCIsInZhbHVlIiwidHlwZSIsImhpZGUiLCJzaG93IiwiaHRtbCIsImF0dHIiLCJrZXkiLCJyZXBsYWNlV2l0aCIsImVtcHR5IiwidGV4dCIsIiRidXR0b25zIiwic3VjY2VzcyIsInByb3AiLCIkZXJyb3JGaWVsZCIsIm1lc3NhZ2VOb0NvbWJpU2VsZWN0ZWQiLCJ1bmRlZmluZWQiLCJTVElDS1lCT1hfQ09OVEVOVF9DSEFOR0UiLCJfYWRkVG9Tb21ld2hlcmUiLCJ1cmwiLCIkYnV0dG9uIiwiY2FsbGJhY2siLCJ4aHIiLCJwb3N0IiwiZG9uZSIsInJlc3VsdCIsInN1YnN0ciIsImxvY2F0aW9uIiwiaHJlZiIsImNvcmUiLCJjb25maWciLCJnZXQiLCJDQVJUX1VQREFURSIsIm1vZGFsIiwiaW5mbyIsInRpdGxlIiwibXNnIiwiaWdub3JlIiwiZmFpbCIsImFsd2F5cyIsImhvb2tzIiwiZXhlY3V0ZSIsImtleXMiLCJzaG9wIiwiY2FydCIsImFkZCIsInRoZW4iLCJjYXRjaCIsIl9zdWJtaXRIYW5kbGVyIiwiZSIsInByZXZlbnREZWZhdWx0IiwiJHNlbGYiLCJpcyIsImNsb3Nlc3QiLCJjdXN0b21pemVyIiwicHJvcGVydGllcyIsInRhcmdldCIsInBhcmVudHMiLCJnZXRHYWxsZXJ5SGFzaCIsInZhbCIsImZvcm1kYXRhIiwiZm9ybSIsImdldERhdGEiLCJpc1Byb2R1Y3RJbmZvIiwiYWJvcnQiLCJjbGVhclRpbWVvdXQiLCJpbWFnZUdhbGxlcnkiLCJ0cmltIiwicmVwbGFjZUdhbGxlcnkiLCJsb2FkaW5nU3Bpbm5lciIsImxvYWRpbmdfc3Bpbm5lciIsInN3aXBlcnMiLCJlbGVtZW50IiwiaW5zdGFuY2UiLCJzd2lwZXIiLCJkZXN0cm95Iiwib2ZmIiwicmVtb3ZlIiwiaW1hZ2VNb2RhbCIsImluaXQiLCJldmVudCIsIkFERF9DVVNUT01JWkVSX1dJU0hMSVNUIiwiQUREX0NVU1RPTUlaRVJfQ0FSVCIsInN1Ym1pdCIsImRlZmVycmVkIiwiRGVmZXJyZWQiLCJjdXN0b21pemVyUmFuZG9tIiwiJGJ0bkZha2UiLCJtb2RpZmllcnMiLCJfY2hhbmdlUHJvZHVjdE9wdGlvbnMiLCJvcHRpb24iLCJjdXJyZW50VGFyZ2V0Iiwib3B0aW9uVmFsdWUiLCJvcHRpb25Db250YWluZXIiLCJfc2VsZWN0U2VsZWN0ZWRNb2RpZmllckluZm8iLCJsYWJlbCIsIl9zZXRTZWxlY3RlZE1vZGlmaWVySW5mbyIsIl9yZXNldFNlbGVjdGVkTW9kaWZpZXJJbmZvIiwicHJpY2VIb2xkZXIiLCJsYWJlbEhvbGRlciIsIl9rZXl1cEhhbmRsZXIiLCJjYWxsIiwiYmluZCIsIl9hZGRUb0NhcnRUaHJvYmJlckhhbmRsZXIiLCIkYnRuIiwiZm9ybVJlYWR5IiwicHJlcGVuZCIsIl9jYXJ0RHJvcGRvd25PcGVuSGFuZGxlciIsImZha2VPcmlnTGFiZWwiLCJwcm9kdWN0Q291bnQiLCJ0ZXh0UGhyYXNlcyIsIkpTT04iLCJwYXJzZSIsImNvbnNvbGUiLCJsb2ciLCJwYXJzZUludCIsIiRmb3JtcyIsIm9uIiwiY2xpY2siLCJub3QiXSwibWFwcGluZ3MiOiI7Ozs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7QUFNQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQ0MsY0FERCxFQUdDLENBQ0MsT0FERCxFQUVDLE1BRkQsRUFHQyxLQUhELEVBSUMsaUJBSkQsRUFLQ0YsT0FBT0csTUFBUCxHQUFnQixjQUxqQixFQU1DSCxPQUFP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QnJEOztBQW1CQTtBQUNBO0FBQ0FnQixTQUFPRyxHQUFQLENBQVcsa0JBQVgsRUFBK0JBLEdBQS9CLENBQW1DLGVBQW5DLEVBQW9EOUgsSUFBcEQsQ0FBeUQsWUFBVztBQUNuRXVELGtCQUFlcUQsSUFBZixDQUFvQnhMLEVBQUUsSUFBRixDQUFwQjtBQUNBLEdBRkQ7QUFHQTJHO0FBQ0EsRUFyQ0Q7O0FBdUNBO0FBQ0EsUUFBTy9HLE1BQVA7QUFDQSxDQXRuQkYiLCJmaWxlIjoid2lkZ2V0cy9jYXJ0X2hhbmRsZXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGNhcnRfaGFuZGxlci5qcyAyMDIzLTExLTE0XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAyMyBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBDb21wb25lbnQgZm9yIGhhbmRsaW5nIHRoZSBhZGQgdG8gY2FydCBhbmQgd2lzaGxpc3QgZmVhdHVyZXNcbiAqIGF0IHRoZSBwcm9kdWN0IGRldGFpbHMgYW5kIHRoZSBjYXRlZ29yeSBsaXN0aW5nIHBhZ2VzLiBJdCBjYXJlc1xuICogZm9yIGF0dHJpYnV0ZXMsIHByb3BlcnRpZXMsIHF1YW50aXR5IGFuZCBhbGwgb3RoZXJcbiAqIHJlbGV2YW50IGRhdGEgZm9yIGFkZGluZyBhbiBpdGVtIHRvIHRoZSBiYXNrZXQgb3Igd2lzaGxpc3RcbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuXHQnY2FydF9oYW5kbGVyJyxcblx0XG5cdFtcblx0XHQnaG9va3MnLFxuXHRcdCdmb3JtJyxcblx0XHQneGhyJyxcblx0XHQnbG9hZGluZ19zcGlubmVyJyxcblx0XHRnYW1iaW8uc291cmNlICsgJy9saWJzL2V2ZW50cycsXG5cdFx0Z2FtYmlvLnNvdXJjZSArICcvbGlicy9tb2RhbC5leHQtbWFnbmlmaWMnLFxuXHRcdGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvbW9kYWwnXG5cdF0sXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vICMjIyMjIyMjIyMgVkFSSUFCTEUgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXHRcdFxuXHRcdHZhciAkdGhpcyA9ICQodGhpcyksXG5cdFx0XHQkYm9keSA9ICQoJ2JvZHknKSxcblx0XHRcdCR3aW5kb3cgPSAkKHdpbmRvdyksXG5cdFx0XHRidXN5ID0gZmFsc2UsXG5cdFx0XHRhamF4ID0gbnVsbCxcblx0XHRcdHRpbWVvdXQgPSAwLFxuXHRcdFx0cHJldmlvdXNNb2RpZmllcnMgPSB7fSxcblx0XHRcdGRlZmF1bHRzID0ge1xuXHRcdFx0XHQvLyBBSkFYIFwiYWRkIHRvIGNhcnRcIiBVUkxcblx0XHRcdFx0YWRkQ2FydFVybDogJ3Nob3AucGhwP2RvPUNhcnQvQnV5UHJvZHVjdCcsXG5cdFx0XHRcdC8vIEFKQVggXCJhZGQgdG8gY2FydFwiIFVSTCBmb3IgY3VzdG9taXplciBwcm9kdWN0c1xuXHRcdFx0XHRhZGRDYXJ0Q3VzdG9taXplclVybDogJ3Nob3AucGhwP2RvPUNhcnQvQWRkJyxcblx0XHRcdFx0Ly8gQUpBWCBVUkwgdG8gcGVyZm9ybSBhIHZhbHVlIGNoZWNrXG5cdFx0XHRcdGNoZWNrVXJsOiAnc2hvcC5waHA/ZG89Q2hlY2tTdGF0dXMnLFxuXHRcdFx0XHQvLyBBSkFYIFVSTCB0byBwZXJmb3JtIHRoZSBhZGQgdG8gd2lzaGxpc3Rcblx0XHRcdFx0d2lzaGxpc3RVcmw6ICdzaG9wLnBocD9kbz1XaXNoTGlzdC9BZGQnLFxuXHRcdFx0XHQvLyBTdWJtaXQgVVJMIGZvciBwcmljZSBvZmZlciBidXR0b25cblx0XHRcdFx0cHJpY2VPZmZlclVybDogJ2dtX3ByaWNlX29mZmVyLnBocCcsXG5cdFx0XHRcdC8vIFN1Ym1pdCBtZXRob2QgZm9yIHByaWNlIG9mZmVyXG5cdFx0XHRcdHByaWNlT2ZmZXJNZXRob2Q6ICdnZXQnLFxuXHRcdFx0XHQvLyBTZWxlY3RvciBmb3IgdGhlIGNhcnQgZHJvcGRvd25cblx0XHRcdFx0ZHJvcGRvd246ICcjaGVhZF9zaG9wcGluZ19jYXJ0Jyxcblx0XHRcdFx0Ly8gXCJBZGQgdG8gY2FydFwiIGJ1dHRvbnMgc2VsZWN0b3JzXG5cdFx0XHRcdGNhcnRCdXR0b25zOiAnLmpzLWJ0bi1hZGQtdG8tY2FydCcsXG5cdFx0XHRcdC8vIFwiV2lzaGxpc3RcIiBidXR0b25zIHNlbGVjdG9yc1xuXHRcdFx0XHR3aXNobGlzdEJ1dHRvbnM6ICcuYnRuLXdpc2hsaXN0Jyxcblx0XHRcdFx0Ly8gXCJQcmljZSBvZmZlclwiIGJ1dHRvbnMgc2VsZWN0b3JzXG5cdFx0XHRcdHByaWNlT2ZmZXJCdXR0b25zOiAnLmJ0bi1wcmljZS1vZmZlcicsXG5cdFx0XHRcdC8vIFNlbGVjdG9yIGZvciB0aGUgYXR0cmlidXRlIGZpZWxkc1xuXHRcdFx0XHRhdHRyaWJ1dGVzOiAnLmpzLWNhbGN1bGF0ZScsXG5cdFx0XHRcdC8vIFNlbGVjdG9yIGZvciBwcm9kdWN0IHByb3BlcnR5XG5cdFx0XHRcdHByb2R1Y3RPcHRpb25zOiAnLm1vZGlmaWVyLWdyb3VwIC5tb2RpZmllci1jb250ZW50IC5tb2RpZmllci1pdGVtJyxcblx0XHRcdFx0cHJvZHVjdE9wdGlvbkZpZWxkOiAnLmhpZGRlbi1pbnB1dCcsXG5cdFx0XHRcdC8vIFNlbGVjdG9yIGZvciB0aGUgcXVhbnRpdHlcblx0XHRcdFx0cXVhbnRpdHk6ICcuanMtY2FsY3VsYXRlLXF0eScsXG5cdFx0XHRcdC8vIFVSTCB3aGVyZSB0byBnZXQgdGhlIHRoZW1lIGZvciB0aGUgZHJvcGRvd25cblx0XHRcdFx0dHBsOiBudWxsLFxuXHRcdFx0XHQvLyBTaG93IGF0dHJpYnV0ZSBpbWFnZXMgaW4gcHJvZHVjdCBpbWFnZXMgc3dpcGVyIChpZiBwb3NzaWJsZSlcblx0XHRcdFx0Ly8gLS0gdGhpcyBmZWF0dXJlIGlzIG5vdCBzdXBwb3J0ZWQgeWV0IC0tXG5cdFx0XHRcdGF0dHJpYnV0SW1hZ2VzU3dpcGVyOiBmYWxzZSxcblx0XHRcdFx0Ly8gVHJpZ2dlciB0aGUgYXR0cmlidXRlIGltYWdlcyB0byB0aGlzIHNlbGVjdG9yc1xuXHRcdFx0XHR0cmlnZ2VyQXR0ckltYWdlc1RvOiAnI3Byb2R1Y3RfaW1hZ2Vfc3dpcGVyLCAjcHJvZHVjdF90aHVtYm5haWxfc3dpcGVyLCAnXG5cdFx0XHRcdFx0KyAnI3Byb2R1Y3RfdGh1bWJuYWlsX3N3aXBlcl9tb2JpbGUnLFxuXHRcdFx0XHQvLyBDbGFzcyB0aGF0IGdldHMgYWRkZWQgdG8gdGhlIGJ1dHRvbiBvbiBwcm9jZXNzaW5nXG5cdFx0XHRcdHByb2Nlc3NpbmdDbGFzczogJ2xvYWRpbmcnLFxuXHRcdFx0XHQvLyBEdXJhdGlvbiBmb3IgdGhhdCB0aGUgc3VjY2VzcyBvciBmYWlsIGNsYXNzIGdldHMgYWRkZWQgdG8gdGhlIGJ1dHRvblxuXHRcdFx0XHRwcm9jZXNzaW5nRHVyYXRpb246IDIwMDAsXG5cdFx0XHRcdC8vIEFKQVggcmVzcG9uc2UgY29udGVudCBzZWxlY3RvcnNcblx0XHRcdFx0c2VsZWN0b3JNYXBwaW5nOiB7XG5cdFx0XHRcdFx0YnV0dG9uczogJy5zaG9wcGluZy1jYXJ0LWJ1dHRvbicsXG5cdFx0XHRcdFx0Z2lmdENvbnRlbnQ6ICcuZ2lmdC1jYXJ0LWNvbnRlbnQtd3JhcHBlcicsXG5cdFx0XHRcdFx0Z2lmdExheWVyOiAnLmdpZnQtY2FydC1sYXllcicsXG5cdFx0XHRcdFx0c2hhcmVDb250ZW50OiAnLnNoYXJlLWNhcnQtY29udGVudC13cmFwcGVyJyxcblx0XHRcdFx0XHRzaGFyZUxheWVyOiAnLnNoYXJlLWNhcnQtbGF5ZXInLFxuXHRcdFx0XHRcdGhpZGRlbk9wdGlvbnM6ICcjY2FydF9xdWFudGl0eSAuaGlkZGVuLW9wdGlvbnMnLFxuXHRcdFx0XHRcdG1lc3NhZ2U6ICcuZ2xvYmFsLWVycm9yLW1lc3NhZ2VzJyxcblx0XHRcdFx0XHRtZXNzYWdlQ2FydDogJy5jYXJ0LWVycm9yLW1zZycsXG5cdFx0XHRcdFx0bWVzc2FnZUhlbHA6ICcuaGVscC1ibG9jaycsXG5cdFx0XHRcdFx0bW9kZWxOdW1iZXI6ICcubW9kZWwtbnVtYmVyJyxcblx0XHRcdFx0XHRtb2RlbE51bWJlclRleHQ6ICcubW9kZWwtbnVtYmVyLXRleHQnLFxuXHRcdFx0XHRcdHByaWNlOiAnLmN1cnJlbnQtcHJpY2UtY29udGFpbmVyJyxcblx0XHRcdFx0XHRtb2RpZmllcnNGb3JtOiAnLm1vZGlmaWVycy1zZWxlY3Rpb24nLFxuXHRcdFx0XHRcdHF1YW50aXR5OiAnLnByb2R1Y3RzLXF1YW50aXR5LXZhbHVlJyxcblx0XHRcdFx0XHRxdWFudGl0eUluZm86ICcucHJvZHVjdHMtcXVhbnRpdHknLFxuXHRcdFx0XHRcdHJpYmJvblNwZWNpYWw6ICcucmliYm9uLXNwZWNpYWwnLFxuXHRcdFx0XHRcdHNoaXBwaW5nSW5mb3JtYXRpb246ICcjc2hpcHBpbmctaW5mb3JtYXRpb24tbGF5ZXInLFxuXHRcdFx0XHRcdHNoaXBwaW5nVGltZTogJy5wcm9kdWN0cy1zaGlwcGluZy10aW1lLXZhbHVlJyxcblx0XHRcdFx0XHRzaGlwcGluZ1RpbWVJbWFnZTogJy5pbWctc2hpcHBpbmctdGltZSBpbWcnLFxuXHRcdFx0XHRcdHRvdGFsczogJyNjYXJ0X3F1YW50aXR5IC50b3RhbC1ib3gnLFxuXHRcdFx0XHRcdHdlaWdodDogJy5wcm9kdWN0cy1kZXRhaWxzLXdlaWdodC1jb250YWluZXIgc3BhbicsXG5cdFx0XHRcdFx0YWJyb2FkU2hpcHBpbmdJbmZvOiAnLmFicm9hZC1zaGlwcGluZy1pbmZvJ1xuXHRcdFx0XHR9LFxuXHRcdFx0XHRwYWdlOiAncHJvZHVjdC1saXN0aW5nJ1xuXHRcdFx0fSxcblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0bW9kdWxlID0ge30sXG5cdFx0XHRtb2JpbGUgPSAkKHdpbmRvdykud2lkdGgoKSA8PSA3Njc7XG5cdFx0XG5cdFx0XG5cdFx0Ly8gIyMjIyMjIyMjIyBIRUxQRVIgRlVOQ1RJT05TICMjIyMjIyMjIyNcblx0XHRcblx0XHQvKipcblx0XHQgKiBIZWxwZXIgZnVuY3Rpb24gdGhhdCB1cGRhdGVzIHRoZSBidXR0b25cblx0XHQgKiBzdGF0ZSB3aXRoIGFuIGVycm9yIG9yIHN1Y2Nlc3MgY2xhc3MgZm9yXG5cdFx0ICogYSBzcGVjaWZpZWQgZHVyYXRpb25cblx0XHQgKiBAcGFyYW0gICB7b2JqZWN0fSAgICAgICAgJHRhcmdldCAgICAgICAgIGpRdWVyeSBzZWxlY3Rpb24gb2YgdGhlIHRhcmdldCBidXR0b25cblx0XHQgKiBAcGFyYW0gICB7c3RyaW5nfSAgICAgICAgc3RhdGUgICAgICAgICAgIFRoZSBzdGF0ZSBzdHJpbmcgdGhhdCBnZXRzIGFkZGVkIHRvIHRoZSBsb2FkaW5nIGNsYXNzXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2FkZEJ1dHRvblN0YXRlID0gZnVuY3Rpb24oJHRhcmdldCwgc3RhdGUpIHtcblx0XHRcdHZhciB0aW1lciA9IHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdCR0YXJnZXQucmVtb3ZlQ2xhc3Mob3B0aW9ucy5wcm9jZXNzaW5nQ2xhc3MgKyAnICcgKyBvcHRpb25zLnByb2Nlc3NpbmdDbGFzcyArIHN0YXRlKTtcblx0XHRcdH0sIG9wdGlvbnMucHJvY2Vzc2luZ0R1cmF0aW9uKTtcblx0XHRcdFxuXHRcdFx0JHRhcmdldFxuXHRcdFx0XHQuZGF0YSgndGltZXInLCB0aW1lcilcblx0XHRcdFx0LmFkZENsYXNzKG9wdGlvbnMucHJvY2Vzc2luZ0NsYXNzICsgc3RhdGUpO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSGVscGVyIGZ1bmN0aW9uIHRvIHNldCB0aGUgbWVzc2FnZXMgYW5kIHRoZVxuXHRcdCAqIGJ1dHRvbiBzdGF0ZS5cblx0XHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgZGF0YSAgICAgICAgICAgICAgICBSZXN1bHQgZm9ybSB0aGUgYWpheCByZXF1ZXN0XG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICRmb3JtICAgICAgICAgICAgICAgalF1ZXJ5IHNlbGVjaW9uIG9mIHRoZSBmb3JtXG5cdFx0ICogQHBhcmFtICAgICAgIHtib29sZWFufSAgIGRpc2FibGVCdXR0b25zICAgICAgSWYgdHJ1ZSwgdGhlIGJ1dHRvbiBzdGF0ZSBnZXRzIHNldCB0byAoaW4pYWN0aXZlXG5cdFx0ICogQHBhcmFtICAgICAgIHtib29sZWFufSAgIHNob3dOb0NvbWJpTWVzc3NhZ2UgSWYgdHJ1ZSwgdGhlIGVycm9yIG1lc3NhZ2UgZm9yIG1pc3NpbmcgcHJvcGVydHkgY29tYmluYXRpb24gc2VsZWN0aW9uIHdpbGwgYmUgZGlzcGxheWVkXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3N0YXRlTWFuYWdlciA9IGZ1bmN0aW9uKGRhdGEsICRmb3JtLCBkaXNhYmxlQnV0dG9ucywgc2hvd05vQ29tYmlTZWxlY3RlZE1lc3NzYWdlKSB7XG5cdFx0XHRcblx0XHRcdC8vIFJlbW92ZSB0aGUgYXR0cmlidXRlIGltYWdlcyBmcm9tIHRoZSBjb21tb24gY29udGVudFxuXHRcdFx0Ly8gc28gdGhhdCBpdCBkb2Vzbid0IGdldCByZW5kZXJlZCBhbnltb3JlLiBUaGVuIHRyaWdnZXJcblx0XHRcdC8vIGFuIGV2ZW50IHRvIHRoZSBnaXZlbiBzZWxlY3RvcnMgYW5kIGRlbGl2ZXIgdGhlXG5cdFx0XHQvLyBhdHRySW1hZ2VzIG9iamVjdFxuXHRcdFx0aWYgKG9wdGlvbnMuYXR0cmlidXRJbWFnZXNTd2lwZXIgJiYgZGF0YS5hdHRySW1hZ2VzICYmIGRhdGEuYXR0ckltYWdlcy5sZW5ndGgpIHtcblx0XHRcdFx0ZGVsZXRlIGRhdGEuY29udGVudC5pbWFnZXM7XG5cdFx0XHRcdCQob3B0aW9ucy50cmlnZ2VyQXR0ckltYWdlc1RvKVxuXHRcdFx0XHRcdC50cmlnZ2VyKGpzZS5saWJzLnRoZW1lLmV2ZW50cy5TTElERVNfVVBEQVRFKCksIHthdHRyaWJ1dGVzOiBkYXRhLmF0dHJJbWFnZXN9KTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0Ly8gU2V0IHRoZSBtZXNzYWdlcyBnaXZlbiBpbnNpZGUgdGhlIGRhdGEuY29udGVudCBvYmplY3Rcblx0XHRcdCQuZWFjaChkYXRhLmNvbnRlbnQsIGZ1bmN0aW9uKGksIHYpIHtcblx0XHRcdFx0dmFyICRlbGVtZW50ID0gJGJvZHkuaGFzQ2xhc3MoJ3BhZ2UtcHJvZHVjdC1pbmZvJykgPyAkdGhpcy5maW5kKG9wdGlvbnMuc2VsZWN0b3JNYXBwaW5nW3Yuc2VsZWN0b3JdKSA6ICRmb3JtLnBhcmVudCgpLmZpbmQob3B0aW9ucy5zZWxlY3Rvck1hcHBpbmdbdi5zZWxlY3Rvcl0pO1xuXHRcdFx0XHRcblx0XHRcdFx0aWYgKCghc2hvd05vQ29tYmlTZWxlY3RlZE1lc3NzYWdlIHx8IHYudmFsdWUgPT09ICcnKSAmJiBpID09PSAnbWVzc2FnZU5vQ29tYmlTZWxlY3RlZCcpIHtcblx0XHRcdFx0XHRyZXR1cm4gdHJ1ZTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0c3dpdGNoICh2LnR5cGUpIHtcblx0XHRcdFx0XHRjYXNlICdoaWRlJzpcblx0XHRcdFx0XHRcdGlmICh2LnZhbHVlID09PSAndHJ1ZScpIHtcblx0XHRcdFx0XHRcdFx0JGVsZW1lbnQuaGlkZSgpO1xuXHRcdFx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRcdFx0JGVsZW1lbnQuc2hvdygpO1xuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdFx0Y2FzZSAnaHRtbCc6XG5cdFx0XHRcdFx0XHQkZWxlbWVudC5odG1sKHYudmFsdWUpO1xuXHRcdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdFx0Y2FzZSAnYXR0cmlidXRlJzpcblx0XHRcdFx0XHRcdCRlbGVtZW50LmF0dHIodi5rZXksIHYudmFsdWUpO1xuXHRcdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdFx0Y2FzZSAncmVwbGFjZSc6XG5cdFx0XHRcdFx0XHRpZiAodi52YWx1ZSkge1xuXHRcdFx0XHRcdFx0XHQkZWxlbWVudC5yZXBsYWNlV2l0aCh2LnZhbHVlKTtcblx0XHRcdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0XHRcdCRlbGVtZW50XG5cdFx0XHRcdFx0XHRcdFx0LmFkZENsYXNzKCdoaWRkZW4nKVxuXHRcdFx0XHRcdFx0XHRcdC5lbXB0eSgpO1xuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdFx0ZGVmYXVsdDpcblx0XHRcdFx0XHRcdCRlbGVtZW50LnRleHQodi52YWx1ZSk7XG5cdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdC8vIERpcy0gLyBFbmFibGUgdGhlIGJ1dHRvbnNcblx0XHRcdGlmIChkaXNhYmxlQnV0dG9ucykge1xuXHRcdFx0XHR2YXIgJGJ1dHRvbnMgPSAkZm9ybS5maW5kKG9wdGlvbnMuY2FydEJ1dHRvbnMpO1xuXHRcdFx0XHRpZiAoZGF0YS5zdWNjZXNzKSB7XG5cdFx0XHRcdFx0JGJ1dHRvbnMucmVtb3ZlQ2xhc3MoJ2luYWN0aXZlJyk7XG5cdFx0XHRcdFx0JGJ1dHRvbnMucmVtb3ZlQ2xhc3MoJ2J0bi1pbmFjdGl2ZScpO1xuXHRcdFx0XHRcdCRidXR0b25zLnByb3AoXCJkaXNhYmxlZFwiLCBmYWxzZSk7XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0JGJ1dHRvbnMuYWRkQ2xhc3MoJ2luYWN0aXZlJyk7XG5cdFx0XHRcdFx0JGJ1dHRvbnMuYWRkQ2xhc3MoJ2J0bi1pbmFjdGl2ZScpO1xuXHRcdFx0XHRcdCRidXR0b25zLnByb3AoXCJkaXNhYmxlZFwiLCB0cnVlKTtcblx0XHRcdFx0fVxuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRpZiAoZGF0YS5jb250ZW50Lm1lc3NhZ2UpIHtcblx0XHRcdFx0dmFyICRlcnJvckZpZWxkID0gJGZvcm0uZmluZChvcHRpb25zLnNlbGVjdG9yTWFwcGluZ1tkYXRhLmNvbnRlbnQubWVzc2FnZS5zZWxlY3Rvcl0pO1xuXHRcdFx0XHRpZiAoZGF0YS5jb250ZW50Lm1lc3NhZ2UudmFsdWUpIHtcblx0XHRcdFx0XHQkZXJyb3JGaWVsZFxuXHRcdFx0XHRcdFx0LnJlbW92ZUNsYXNzKCdoaWRkZW4nKVxuXHRcdFx0XHRcdFx0LnNob3coKTtcblx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHQkZXJyb3JGaWVsZFxuXHRcdFx0XHRcdFx0LmFkZENsYXNzKCdoaWRkZW4nKVxuXHRcdFx0XHRcdFx0LmhpZGUoKTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHRpZiAoc2hvd05vQ29tYmlTZWxlY3RlZE1lc3NzYWdlXG5cdFx0XHRcdFx0XHQmJiBkYXRhLmNvbnRlbnQubWVzc2FnZU5vQ29tYmlTZWxlY3RlZCAhPT0gdW5kZWZpbmVkXG5cdFx0XHRcdFx0XHQmJiBkYXRhLmNvbnRlbnQubWVzc2FnZU5vQ29tYmlTZWxlY3RlZCkge1xuXHRcdFx0XHRcdFx0aWYgKGRhdGEuY29udGVudC5tZXNzYWdlTm9Db21iaVNlbGVjdGVkLnZhbHVlKSB7XG5cdFx0XHRcdFx0XHRcdCRlcnJvckZpZWxkXG5cdFx0XHRcdFx0XHRcdFx0LnJlbW92ZUNsYXNzKCdoaWRkZW4nKVxuXHRcdFx0XHRcdFx0XHRcdC5zaG93KCk7XG5cdFx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0XHQkZXJyb3JGaWVsZFxuXHRcdFx0XHRcdFx0XHRcdC5hZGRDbGFzcygnaGlkZGVuJylcblx0XHRcdFx0XHRcdFx0XHQuaGlkZSgpO1xuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdH1cblx0XHRcdFx0fVxuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQkd2luZG93LnRyaWdnZXIoanNlLmxpYnMudGhlbWUuZXZlbnRzLlNUSUNLWUJPWF9DT05URU5UX0NIQU5HRSgpKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEhlbHBlciBmdW5jdGlvbiB0byBzZW5kIHRoZSBhamF4XG5cdFx0ICogT24gc3VjY2VzcyByZWRpcmVjdCB0byBhIGdpdmVuIHVybCwgb3BlbiBhIGxheWVyIHdpdGhcblx0XHQgKiBhIG1lc3NhZ2Ugb3IgYWRkIHRoZSBpdGVtIHRvIHRoZSBjYXJ0LWRyb3Bkb3duIGRpcmVjdGx5XG5cdFx0ICogKGJ5IHRyaWdnZXJpbmcgYW4gZXZlbnQgdG8gdGhlIGJvZHkpXG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgZGF0YSAgICAgIEZvcm0gZGF0YVxuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICRmb3JtICAgICBUaGUgZm9ybSB0byBmaWxsXG5cdFx0ICogQHBhcmFtICAgICAgIHtzdHJpbmd9ICAgICAgdXJsICAgICAgIFRoZSBVUkwgZm9yIHRoZSBBSkFYIHJlcXVlc3Rcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfYWRkVG9Tb21ld2hlcmUgPSBmdW5jdGlvbihkYXRhLCAkZm9ybSwgdXJsLCAkYnV0dG9uKSB7XG5cdFx0XHRmdW5jdGlvbiBjYWxsYmFjaygpIHtcblx0XHRcdFx0anNlLmxpYnMueGhyLnBvc3Qoe3VybDogdXJsLCBkYXRhOiBkYXRhfSwgdHJ1ZSkuZG9uZShmdW5jdGlvbihyZXN1bHQpIHtcblx0XHRcdFx0XHR0cnkge1xuXHRcdFx0XHRcdFx0Ly8gRmlsbCB0aGUgcGFnZSB3aXRoIHRoZSByZXN1bHQgZnJvbSB0aGUgYWpheFxuXHRcdFx0XHRcdFx0X3N0YXRlTWFuYWdlcihyZXN1bHQsICRmb3JtLCBmYWxzZSk7XG5cdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdC8vIElmIHRoZSBBSkFYIHdhcyBzdWNjZXNzZnVsIGV4ZWN1dGVcblx0XHRcdFx0XHRcdC8vIGEgY3VzdG9tIGZ1bmN0aW9uYWxpdHlcblx0XHRcdFx0XHRcdGlmIChyZXN1bHQuc3VjY2Vzcykge1xuXHRcdFx0XHRcdFx0XHRzd2l0Y2ggKHJlc3VsdC50eXBlKSB7XG5cdFx0XHRcdFx0XHRcdFx0Y2FzZSAndXJsJzpcblx0XHRcdFx0XHRcdFx0XHRcdGlmIChyZXN1bHQudXJsLnN1YnN0cigwLCA0KSAhPT0gJ2h0dHAnKSB7XG5cdFx0XHRcdFx0XHRcdFx0XHRcdGxvY2F0aW9uLmhyZWYgPSBqc2UuY29yZS5jb25maWcuZ2V0KCdhcHBVcmwnKSArICcvJyArIHJlc3VsdC51cmw7XG5cdFx0XHRcdFx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0XHRcdFx0XHRsb2NhdGlvbi5ocmVmID0gcmVzdWx0LnVybDtcblx0XHRcdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdFx0XHRcdFx0Y2FzZSAnZHJvcGRvd24nOlxuXHRcdFx0XHRcdFx0XHRcdFx0JGJvZHkudHJpZ2dlcihqc2UubGlicy50aGVtZS5ldmVudHMuQ0FSVF9VUERBVEUoKSwgW3RydWVdKTtcblx0XHRcdFx0XHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRcdFx0XHRcdGNhc2UgJ2xheWVyJzpcblx0XHRcdFx0XHRcdFx0XHRcdGpzZS5saWJzLnRoZW1lLm1vZGFsLmluZm8oe3RpdGxlOiByZXN1bHQudGl0bGUsIGNvbnRlbnQ6IHJlc3VsdC5tc2d9KTtcblx0XHRcdFx0XHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRcdFx0XHRcdGRlZmF1bHQ6XG5cdFx0XHRcdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdH0gY2F0Y2ggKGlnbm9yZSkge1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0XHRfYWRkQnV0dG9uU3RhdGUoJGJ1dHRvbiwgJy1zdWNjZXNzJyk7XG5cdFx0XHRcdH0pLmZhaWwoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0X2FkZEJ1dHRvblN0YXRlKCRidXR0b24sICctZmFpbCcpO1xuXHRcdFx0XHR9KS5hbHdheXMoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0Ly8gUmVzZXQgdGhlIGJ1c3kgZmxhZyB0byBiZSBhYmxlIHRvIHBlcmZvcm1cblx0XHRcdFx0XHQvLyBmdXJ0aGVyIEFKQVggcmVxdWVzdHNcblx0XHRcdFx0XHRidXN5ID0gZmFsc2U7XG5cdFx0XHRcdH0pO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRpZiAoIWJ1c3kpIHtcblx0XHRcdFx0Ly8gb25seSBleGVjdXRlIHRoZSBhamF4XG5cdFx0XHRcdC8vIGlmIHRoZXJlIGlzIG5vIHBlbmRpbmcgYWpheCBjYWxsXG5cdFx0XHRcdGJ1c3kgPSB0cnVlO1xuXHRcdFx0XHRcblx0XHRcdFx0anNlLmxpYnMuaG9va3MuZXhlY3V0ZShqc2UubGlicy5ob29rcy5rZXlzLnNob3AuY2FydC5hZGQsIGRhdGEsIDUwMClcblx0XHRcdFx0XHQudGhlbihjYWxsYmFjaylcblx0XHRcdFx0XHQuY2F0Y2goY2FsbGJhY2spO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0fTtcblx0XHRcblx0XHRcblx0XHQvLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEhhbmRsZXIgZm9yIHRoZSBzdWJtaXQgZm9ybSAvIGNsaWNrXG5cdFx0ICogb24gXCJhZGQgdG8gY2FydFwiICYgXCJ3aXNobGlzdFwiIGJ1dHRvbi5cblx0XHQgKiBJdCBwZXJmb3JtcyBhIGNoZWNrIG9uIHRoZSBhdmFpbGFiaWxpdHlcblx0XHQgKiBvZiB0aGUgY29tYmluYXRpb24gYW5kIHF1YW50aXR5LiBJZlxuXHRcdCAqIHN1Y2Nlc3NmdWwgaXQgcGVyZm9ybXMgdGhlIGFkZCB0byBjYXJ0XG5cdFx0ICogb3Igd2lzaGxpc3QgYWN0aW9uLCBpZiBpdCdzIG5vdCBhXG5cdFx0ICogXCJjaGVja1wiIGNhbGxcblx0XHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgZSAgICAgIGpRdWVyeSBldmVudCBvYmplY3Rcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfc3VibWl0SGFuZGxlciA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdGlmIChlKSB7XG5cdFx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdFx0JGZvcm0gPSAoJHNlbGYuaXMoJ2Zvcm0nKSkgPyAkc2VsZiA6ICRzZWxmLmNsb3Nlc3QoJ2Zvcm0nKSxcblx0XHRcdFx0Y3VzdG9taXplciA9ICRmb3JtLmhhc0NsYXNzKCdjdXN0b21pemVyJyksXG5cdFx0XHRcdHByb3BlcnRpZXMgPSAhISRmb3JtLmZpbmQoJy5wcm9wZXJ0aWVzLXNlbGVjdGlvbi1mb3JtJykubGVuZ3RoLFxuXHRcdFx0XHRtb2R1bGUgPSBwcm9wZXJ0aWVzID8gJycgOiAnL0F0dHJpYnV0ZXMnLFxuXHRcdFx0XHRzaG93Tm9Db21iaVNlbGVjdGVkTWVzc3NhZ2UgPSBlICYmIGUuZGF0YSAmJiBlLmRhdGEudGFyZ2V0ICYmIGUuZGF0YS50YXJnZXQgIT09ICdjaGVjayc7XG5cdFx0XHRcblx0XHRcdGlmICgkZm9ybS5sZW5ndGgpIHtcblx0XHRcdFx0XG5cdFx0XHRcdC8vIFNob3cgcHJvcGVydGllcyBvdmVybGF5XG5cdFx0XHRcdC8vIHRvIGRpc2FibGUgdXNlciBpbnRlcmFjdGlvblxuXHRcdFx0XHQvLyBiZWZvcmUgbWFya3VwIHJlcGxhY2Vcblx0XHRcdFx0aWYgKHByb3BlcnRpZXMpIHtcblx0XHRcdFx0XHQkdGhpcy5hZGRDbGFzcygnbG9hZGluZycpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdFxuXHRcdFx0XHRpZiAoJHNlbGYuaXMoJ3NlbGVjdCcpKSB7XG5cdFx0XHRcdFx0bGV0IHByaWNlID0gJHNlbGYuZmluZChcIjpzZWxlY3RlZFwiKS5hdHRyKCdkYXRhLXByaWNlJyk7XG5cdFx0XHRcdFx0JHNlbGYucGFyZW50cygnLm1vZGlmaWVyLWdyb3VwJykuZmluZCgnLnNlbGVjdGVkLXZhbHVlLXByaWNlJykudGV4dChwcmljZSk7XG5cdFx0XHRcdH1cblx0XHRcdFx0XG5cdFx0XHRcdGxldCBnZXRHYWxsZXJ5SGFzaCA9ICQoJyNjdXJyZW50LWdhbGxlcnktaGFzaCcpLnZhbCgpO1xuXHRcdFx0XHQkZm9ybS5maW5kKCcjdXBkYXRlLWdhbGxlcnktaGFzaCcpLnZhbChnZXRHYWxsZXJ5SGFzaCk7XG5cdFx0XHRcdFxuXHRcdFx0XHR2YXIgZm9ybWRhdGEgPSBqc2UubGlicy5mb3JtLmdldERhdGEoJGZvcm0sIG51bGwsIHRydWUpO1xuXHRcdFx0XHRmb3JtZGF0YS50YXJnZXQgPSAoZSAmJiBlLmRhdGEgJiYgZS5kYXRhLnRhcmdldCkgPyBlLmRhdGEudGFyZ2V0IDogJ2NoZWNrJztcblx0XHRcdFx0Zm9ybWRhdGEuaXNQcm9kdWN0SW5mbyA9ICRmb3JtLmhhc0NsYXNzKCdwcm9kdWN0LWluZm8nKSA/IDEgOiAwO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gQWJvcnQgcHJldmlvdXMgY2hlY2sgYWpheCBpZlxuXHRcdFx0XHQvLyB0aGVyZSBpcyBvbmUgaW4gcHJvZ3Jlc3Ncblx0XHRcdFx0aWYgKGFqYXggJiYgZSkge1xuXHRcdFx0XHRcdGFqYXguYWJvcnQoKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0Ly8gQWRkIHByb2Nlc3NpbmctY2xhc3MgdG8gdGhlIGJ1dHRvblxuXHRcdFx0XHQvLyBhbmQgcmVtb3ZlIG9sZCB0aW1lZCBldmVudHNcblx0XHRcdFx0aWYgKGZvcm1kYXRhLnRhcmdldCAhPT0gJ2NoZWNrJykge1xuXHRcdFx0XHRcdHZhciB0aW1lciA9ICRzZWxmLmRhdGEoJ3RpbWVyJyk7XG5cdFx0XHRcdFx0aWYgKHRpbWVyKSB7XG5cdFx0XHRcdFx0XHRjbGVhclRpbWVvdXQodGltZXIpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcblx0XHRcdFx0XHQkc2VsZlxuXHRcdFx0XHRcdFx0LnJlbW92ZUNsYXNzKG9wdGlvbnMucHJvY2Vzc2luZ0NsYXNzICsgJy1zdWNjZXNzICcgKyBvcHRpb25zLnByb2Nlc3NpbmdDbGFzcyArICctZmFpbCcpXG5cdFx0XHRcdFx0XHQuYWRkQ2xhc3Mob3B0aW9ucy5wcm9jZXNzaW5nQ2xhc3MpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdFxuXHRcdFx0XHRmb3JtZGF0YS5wcmV2aW91c01vZGlmaWVycyA9IHByZXZpb3VzTW9kaWZpZXJzO1xuXHRcdFx0XHRcblx0XHRcdFx0YWpheCA9IGpzZS5saWJzLnhoci5nZXQoe1xuXHRcdFx0XHRcdHVybDogb3B0aW9ucy5jaGVja1VybCArIG1vZHVsZSxcblx0XHRcdFx0XHRkYXRhOiBmb3JtZGF0YVxuXHRcdFx0XHR9LCB0cnVlKS5kb25lKGZ1bmN0aW9uKHJlc3VsdCkge1xuXHRcdFx0XHRcdF9zdGF0ZU1hbmFnZXIocmVzdWx0LCAkZm9ybSwgdHJ1ZSwgc2hvd05vQ29tYmlTZWxlY3RlZE1lc3NzYWdlKTtcblx0XHRcdFx0XHQkdGhpcy5yZW1vdmVDbGFzcygnbG9hZGluZycpO1xuICAgICAgICAgICAgICAgICAgICBcbiAgICAgICAgICAgICAgICAgICAgLy8gQ2hlY2sgaWYgdGhlIGdhbGxlcnkgaW1hZ2VzIGNoYW5nZWRcbiAgICAgICAgICAgICAgICAgICAgaWYgKGZvcm1kYXRhLnRhcmdldCA9PT0gJ2NoZWNrJyAmJiByZXN1bHQuY29udGVudC5pbWFnZUdhbGxlcnkudHJpbSgpICE9PSAnJ1xuICAgICAgICAgICAgICAgICAgICAgICAgJiYgcmVzdWx0LmNvbnRlbnQucmVwbGFjZUdhbGxlcnkgPT09IHRydWUgJiYgZm9ybWRhdGEuaXNQcm9kdWN0SW5mbyA9PT0gMSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgY29uc3QgbG9hZGluZ1NwaW5uZXIgPSBqc2UubGlicy5sb2FkaW5nX3NwaW5uZXIuc2hvdygkKCcucHJvZHVjdC1pbmZvLXN0YWdlJyksIDk5OTkpO1xuICAgICAgICAgICAgICAgICAgICAgICAgXG4gICAgICAgICAgICAgICAgICAgICAgICBjb25zdCBzd2lwZXJzID0gW1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICQoJyNwcm9kdWN0X2ltYWdlX3N3aXBlcicpLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICQoJyNwcm9kdWN0X3RodW1ibmFpbF9zd2lwZXInKSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKCcjcHJvZHVjdF90aHVtYm5haWxfc3dpcGVyX21vYmlsZScpXG4gICAgICAgICAgICAgICAgICAgICAgICBdO1xuICAgICAgICAgICAgICAgICAgICAgICAgXG4gICAgICAgICAgICAgICAgICAgICAgICBmb3IgKGNvbnN0IGVsZW1lbnQgb2Ygc3dpcGVycykge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNvbnN0IGluc3RhbmNlID0gZWxlbWVudC5zd2lwZXIoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAodHlwZW9mIGluc3RhbmNlICE9PSAnb2JqZWN0Jykge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb250aW51ZTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaW5zdGFuY2UuZGVzdHJveSh0cnVlLCB0cnVlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBlbGVtZW50Lm9mZigpLnJlbW92ZSgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgXG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjaW1hZ2UtY29sbGVjdGlvbi1jb250YWluZXInKS5odG1sKHJlc3VsdC5jb250ZW50LmltYWdlR2FsbGVyeSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjcHJvZHVjdF9pbWFnZV9sYXllcicpLmh0bWwocmVzdWx0LmNvbnRlbnQuaW1hZ2VNb2RhbCk7XG4gICAgICAgICAgICAgICAgICAgICAgICBcbiAgICAgICAgICAgICAgICAgICAgICAgIGdhbWJpby53aWRnZXRzLmluaXQoJCgnLnByb2R1Y3QtaW5mby1jb250ZW50JykpLmRvbmUoZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAganNlLmxpYnMubG9hZGluZ19zcGlubmVyLmhpZGUobG9hZGluZ1NwaW5uZXIpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAoZm9ybWRhdGEudGFyZ2V0ID09PSAnY2hlY2snICYmIHJlc3VsdC5jb250ZW50LmltYWdlR2FsbGVyeS50cmltKCkgPT09ICcnXG4gICAgICAgICAgICAgICAgICAgICAgICAmJiByZXN1bHQuY29udGVudC5yZXBsYWNlR2FsbGVyeSA9PT0gdHJ1ZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJCgnI2ltYWdlLWNvbGxlY3Rpb24tY29udGFpbmVyJykuaHRtbChyZXN1bHQuY29udGVudC5pbWFnZUdhbGxlcnkpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJCgnI3Byb2R1Y3RfaW1hZ2VfbGF5ZXInKS5odG1sKHJlc3VsdC5jb250ZW50LmltYWdlTW9kYWwpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIFxuXHRcdFx0XHRcdGlmIChyZXN1bHQuc3VjY2Vzcykge1xuXHRcdFx0XHRcdFx0dmFyIGV2ZW50ID0gbnVsbCxcblx0XHRcdFx0XHRcdFx0dXJsID0gbnVsbDtcblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0c3dpdGNoIChmb3JtZGF0YS50YXJnZXQpIHtcblx0XHRcdFx0XHRcdFx0Y2FzZSAnd2lzaGxpc3QnOlxuXHRcdFx0XHRcdFx0XHRcdGlmIChjdXN0b21pemVyKSB7XG5cdFx0XHRcdFx0XHRcdFx0XHRldmVudCA9IGpzZS5saWJzLnRoZW1lLmV2ZW50cy5BRERfQ1VTVE9NSVpFUl9XSVNITElTVCgpO1xuXHRcdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdFx0XHR1cmwgPSBvcHRpb25zLndpc2hsaXN0VXJsO1xuXHRcdFx0XHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRcdFx0XHRjYXNlICdjYXJ0Jzpcblx0XHRcdFx0XHRcdFx0XHRpZiAoY3VzdG9taXplcikge1xuXHRcdFx0XHRcdFx0XHRcdFx0ZXZlbnQgPSBqc2UubGlicy50aGVtZS5ldmVudHMuQUREX0NVU1RPTUlaRVJfQ0FSVCgpO1xuXHRcdFx0XHRcdFx0XHRcdFx0dXJsID0gb3B0aW9ucy5hZGRDYXJ0Q3VzdG9taXplclVybDtcblx0XHRcdFx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0XHRcdFx0dXJsID0gb3B0aW9ucy5hZGRDYXJ0VXJsO1xuXHRcdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHRcdFx0Y2FzZSAncHJpY2Vfb2ZmZXInOlxuXHRcdFx0XHRcdFx0XHRcdCRmb3JtLmF0dHIoJ2FjdGlvbicsIG9wdGlvbnMucHJpY2VPZmZlclVybCkuYXR0cignbWV0aG9kJywgb3B0aW9ucy5wcmljZU9mZmVyTWV0aG9kKTtcblx0XHRcdFx0XHRcdFx0XHQkZm9ybS5vZmYoJ3N1Ym1pdCcpO1xuXHRcdFx0XHRcdFx0XHRcdCRmb3JtLnN1Ym1pdCgpO1xuXHRcdFx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0XHRcdHJldHVybjtcblx0XHRcdFx0XHRcdFx0ZGVmYXVsdDpcblx0XHRcdFx0XHRcdFx0XHRzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHRcdFx0JHdpbmRvdy50cmlnZ2VyKGpzZS5saWJzLnRoZW1lLmV2ZW50cy5TVElDS1lCT1hfQ09OVEVOVF9DSEFOR0UoKSk7XG5cdFx0XHRcdFx0XHRcdFx0fSwgMjUwKTtcblx0XHRcdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0aWYgKGV2ZW50KSB7XG5cdFx0XHRcdFx0XHRcdHZhciBkZWZlcnJlZCA9ICQuRGVmZXJyZWQoKTtcblx0XHRcdFx0XHRcdFx0ZGVmZXJyZWQuZG9uZShmdW5jdGlvbihjdXN0b21pemVyUmFuZG9tKSB7XG5cdFx0XHRcdFx0XHRcdFx0Zm9ybWRhdGFbY3VzdG9taXplclJhbmRvbV0gPSAwO1xuXHRcdFx0XHRcdFx0XHRcdF9hZGRUb1NvbWV3aGVyZShmb3JtZGF0YSwgJGZvcm0sIHVybCwgJHNlbGYpO1xuXHRcdFx0XHRcdFx0XHR9KS5mYWlsKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHRcdF9hZGRCdXR0b25TdGF0ZSgkc2VsZiwgJy1mYWlsJyk7XG5cdFx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdFx0XHQkYm9keS50cmlnZ2VyKGV2ZW50LCBbeydkZWZlcnJlZCc6IGRlZmVycmVkLCAnZGF0YXNldCc6IGZvcm1kYXRhfV0pO1xuXHRcdFx0XHRcdFx0fSBlbHNlIGlmICh1cmwpIHtcblx0XHRcdFx0XHRcdFx0X2FkZFRvU29tZXdoZXJlKGZvcm1kYXRhLCAkZm9ybSwgdXJsLCAkc2VsZik7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnN0ICRidG5GYWtlID0gJHRoaXMuZmluZChcIi5idG4tYWRkLXRvLWNhcnQtZmFrZVwiKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmICgkYnRuRmFrZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRidG5GYWtlLmhpZGUoKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAucHJvcChcImRpc2FibGVkXCIsIGZhbHNlKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAuZmluZCgnLnRocm9iYmxlcicpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5yZW1vdmUoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIFxuICAgICAgICAgICAgICAgICAgICAgICAgY29uc3QgJGJ1dHRvbnMgPSAkZm9ybS5maW5kKG9wdGlvbnMuY2FydEJ1dHRvbnMpO1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKCRidXR0b25zKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJGJ1dHRvbnMucmVtb3ZlQ2xhc3MoJ2J0bi1pbmFjdGl2ZSBpbmFjdGl2ZScpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5wcm9wKFwiZGlzYWJsZWRcIiwgZmFsc2UpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5zaG93KCk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH1cblx0XHRcdFx0XHRcblx0XHRcdFx0XHRpZiAoZm9ybWRhdGEudGFyZ2V0ID09PSAnY2hlY2snKSB7XG5cdFx0XHRcdFx0XHRwcmV2aW91c01vZGlmaWVycyA9IGZvcm1kYXRhLm1vZGlmaWVycztcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH0pLmZhaWwoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0X2FkZEJ1dHRvblN0YXRlKCRzZWxmLCAnLWZhaWwnKTtcblx0XHRcdFx0fSk7XG5cdFx0XHR9XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBIYW5kbGVyIGZvciB0aGUgY2hhbmdlIHByb3BlcnR5IG9wdGlvblxuXHRcdCAqICovXG5cdFx0dmFyIF9jaGFuZ2VQcm9kdWN0T3B0aW9ucyA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdGxldCBvcHRpb24gPSBlLmN1cnJlbnRUYXJnZXQ7XG5cdFx0XHRsZXQgb3B0aW9uVmFsdWUgPSAkKG9wdGlvbikuZGF0YSgndmFsdWUnKTtcblx0XHRcdGxldCBvcHRpb25Db250YWluZXIgPSAkKG9wdGlvbikucGFyZW50cygnLm1vZGlmaWVyLWdyb3VwJyk7XG5cdFx0XHRcblx0XHRcdCQob3B0aW9uQ29udGFpbmVyKS5maW5kKCdsaS5hY3RpdmUnKS5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XG5cdFx0XHQkKG9wdGlvbkNvbnRhaW5lcikuZmluZCgnLm1vZGlmaWVyLWl0ZW0uYWN0aXZlLW1vZGlmaWVyJykucmVtb3ZlQ2xhc3MoJ2FjdGl2ZS1tb2RpZmllcicpO1xuXHRcdFx0JChvcHRpb25Db250YWluZXIpLmZpbmQoJ2lucHV0LmhpZGRlbi1pbnB1dCcpLnZhbChvcHRpb25WYWx1ZSk7XG5cdFx0XHQkKG9wdGlvbkNvbnRhaW5lcikuZmluZCgnaW5wdXQuaGlkZGVuLWlucHV0JykudHJpZ2dlcignYmx1cicsIFtdKTtcblx0XHRcdFxuXHRcdFx0JChvcHRpb24pLnBhcmVudHMoJ2xpJykuYWRkQ2xhc3MoJ2FjdGl2ZScpO1xuXHRcdFx0JChvcHRpb24pLmFkZENsYXNzKCdhY3RpdmUtbW9kaWZpZXInKTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfc2VsZWN0U2VsZWN0ZWRNb2RpZmllckluZm8gPSBmdW5jdGlvbihlKSB7XG5cdFx0XHRsZXQgb3B0aW9uID0gZS5jdXJyZW50VGFyZ2V0O1xuXHRcdFx0bGV0IHByaWNlID0gJChvcHRpb24pLmF0dHIoJ2RhdGEtcHJpY2UnKTtcblx0XHRcdGxldCBsYWJlbCA9ICQob3B0aW9uKS5hdHRyKCdkYXRhLWxhYmVsJyk7XG5cdFx0XHQkKG9wdGlvbilcblx0XHRcdFx0LnBhcmVudHMoJy5tb2RpZmllci1ncm91cCcpXG5cdFx0XHRcdC5maW5kKCcuc2VsZWN0ZWQtdmFsdWUtcHJpY2UnKVxuXHRcdFx0XHQucmVtb3ZlQ2xhc3MoJ3RlbXBvcmFyeS12YWx1ZScpXG5cdFx0XHRcdC5hdHRyKCdkYXRhLWRlZmF1bHQtcHJpY2UnLCBwcmljZSk7XG5cdFx0XHQkKG9wdGlvbikucGFyZW50cygnLm1vZGlmaWVyLWdyb3VwJykuZmluZCgnLnNlbGVjdGVkLXZhbHVlJykuYXR0cignZGF0YS1kZWZhdWx0LXZhbHVlJywgbGFiZWwpO1xuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9zZXRTZWxlY3RlZE1vZGlmaWVySW5mbyA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdGxldCBvcHRpb24gPSBlLmN1cnJlbnRUYXJnZXQ7XG5cdFx0XHRpZiAoISQob3B0aW9uKS5wYXJlbnQoKS5oYXNDbGFzcygnYWN0aXZlJykgJiYgISQob3B0aW9uKS5pcygnc2VsZWN0JykgJiYgISQob3B0aW9uKVxuXHRcdFx0XHQuaGFzQ2xhc3MoJ2FjdGl2ZS1tb2RpZmllcicpKSB7XG5cdFx0XHRcdGxldCBwcmljZSA9ICQob3B0aW9uKS5hdHRyKCdkYXRhLXByaWNlJyk7XG5cdFx0XHRcdGxldCBsYWJlbCA9ICQob3B0aW9uKS5hdHRyKCdkYXRhLWxhYmVsJyk7XG5cdFx0XHRcdCQob3B0aW9uKVxuXHRcdFx0XHRcdC5wYXJlbnRzKCcubW9kaWZpZXItZ3JvdXAnKVxuXHRcdFx0XHRcdC5maW5kKCcuc2VsZWN0ZWQtdmFsdWUtcHJpY2UnKVxuXHRcdFx0XHRcdC5hZGRDbGFzcygndGVtcG9yYXJ5LXZhbHVlJylcblx0XHRcdFx0XHQudGV4dChwcmljZSk7XG5cdFx0XHRcdCQob3B0aW9uKS5wYXJlbnRzKCcubW9kaWZpZXItZ3JvdXAnKS5maW5kKCcuc2VsZWN0ZWQtdmFsdWUnKS50ZXh0KGxhYmVsKTtcblx0XHRcdH1cblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfcmVzZXRTZWxlY3RlZE1vZGlmaWVySW5mbyA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdGxldCBvcHRpb24gPSAkKHRoaXMpO1xuXHRcdFx0aWYgKCEkKG9wdGlvbikucGFyZW50KCkuaGFzQ2xhc3MoJ2FjdGl2ZScpICYmICEkKG9wdGlvbikuaXMoJ3NlbGVjdCcpICYmICEkKG9wdGlvbilcblx0XHRcdFx0Lmhhc0NsYXNzKCdhY3RpdmUtbW9kaWZpZXInKSkge1xuXHRcdFx0XHRsZXQgcHJpY2VIb2xkZXIgPSAkKG9wdGlvbikucGFyZW50cygnLm1vZGlmaWVyLWdyb3VwJykuZmluZCgnLnNlbGVjdGVkLXZhbHVlLXByaWNlJyk7XG5cdFx0XHRcdGxldCBsYWJlbEhvbGRlciA9ICQob3B0aW9uKS5wYXJlbnRzKCcubW9kaWZpZXItZ3JvdXAnKS5maW5kKCcuc2VsZWN0ZWQtdmFsdWUnKTtcblx0XHRcdFx0JChwcmljZUhvbGRlcikucmVtb3ZlQ2xhc3MoJ3RlbXBvcmFyeS12YWx1ZScpLnRleHQoJChwcmljZUhvbGRlcikuYXR0cignZGF0YS1kZWZhdWx0LXByaWNlJykpO1xuXHRcdFx0XHQkKGxhYmVsSG9sZGVyKS50ZXh0KCQobGFiZWxIb2xkZXIpLmF0dHIoJ2RhdGEtZGVmYXVsdC12YWx1ZScpKTtcblx0XHRcdH1cblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEtleXVwIGhhbmRsZXIgZm9yIHF1YW50aXR5IGlucHV0IGZpZWxkXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0gZVxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9rZXl1cEhhbmRsZXIgPSBmdW5jdGlvbihlKSB7XG5cdFx0XHRjbGVhclRpbWVvdXQodGltZW91dCk7XG5cdFx0XHRcblx0XHRcdHRpbWVvdXQgPSBzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRfc3VibWl0SGFuZGxlci5jYWxsKHRoaXMsIGUpXG5cdFx0XHR9LmJpbmQodGhpcyksIDMwMCk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBFdmVudCBoYW5kbGVyIGZvciB0aGUgYWRkIHRvIGNhcnQgYnV0dG9uLCB0aGF0IHNob3dzIG9yIGhpZGVzIHRoZSB0aHJvYmJlci5cblx0XHQgKi9cblx0XHRjb25zdCBfYWRkVG9DYXJ0VGhyb2JiZXJIYW5kbGVyID0gZnVuY3Rpb24oZSkge1xuXHRcdFx0Y29uc3QgJGJ0biA9ICQodGhpcyk7XG5cdFx0XHRjb25zdCAkYnRuRmFrZSA9ICR0aGlzLmZpbmQoXCIuYnRuLWFkZC10by1jYXJ0LWZha2VcIik7XG5cdFx0XHRsZXQgZm9ybVJlYWR5ID0gdHJ1ZTtcblx0XHRcdFxuXHRcdFx0JChcIi5wcm9wZXJ0aWVzLXNlbGVjdGlvbi1mb3JtIHNlbGVjdFwiKS5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRjb25zdCB2YWwgPSAkKHRoaXMpLnZhbCgpO1xuXHRcdFx0XHRpZiAoIXZhbCB8fCB2YWwgPCAxKSB7XG5cdFx0XHRcdFx0Zm9ybVJlYWR5ID0gZmFsc2U7XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHRpZiAoZm9ybVJlYWR5KSB7XG5cdFx0XHRcdCRidG4uaGlkZSgpO1xuXHRcdFx0XHQkYnRuRmFrZS5zaG93KClcblx0XHRcdFx0XHQucHJvcChcImRpc2FibGVkXCIsIHRydWUpXG5cdFx0XHRcdFx0LnByZXBlbmQoJzxzcGFuIGNsYXNzPVwidGhyb2JibGVyXCI+PC9zcGFuPicpO1xuXHRcdFx0fVxuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogQ2FydCBkcm9wZG93biBvYmVuIGV2ZW50IGhhbmRsZXIgZm9yIHRoZSBib2R5LlxuXHRcdCAqL1xuXHRcdGNvbnN0IF9jYXJ0RHJvcGRvd25PcGVuSGFuZGxlciA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdGNvbnN0ICRidG4gPSAkdGhpcy5maW5kKFwiW25hbWU9YnRuLWFkZC10by1jYXJ0XVwiKTtcblx0XHRcdGNvbnN0ICRidG5GYWtlID0gJHRoaXMuZmluZChcIi5idG4tYWRkLXRvLWNhcnQtZmFrZVwiKTtcblx0XHRcdGNvbnN0IGZha2VPcmlnTGFiZWwgPSAkYnRuRmFrZS5odG1sKCk7XG5cdFx0XHRjb25zdCBwcm9kdWN0Q291bnQgPSAkKFwiLmNhcnQtcHJvZHVjdHMtY291bnRcIikuaHRtbCgpO1xuXHRcdFx0XG5cdFx0XHRjb25zdCB0ZXh0UGhyYXNlcyA9IEpTT04ucGFyc2UoJCgnI3Byb2R1Y3QtZGV0YWlscy10ZXh0LXBocmFzZXMnKS5odG1sKCkpO1xuXHRcdFx0Y29uc29sZS5sb2codGV4dFBocmFzZXNbJ3Byb2R1Y3RzSW5DYXJ0U3VmZml4J10pO1xuXHRcdFx0XG5cdFx0XHQkYnRuRmFrZS5odG1sKFwiPGkgY2xhc3M9XFxcImZhIGZhLWNoZWNrXFxcIj48L2k+IFwiICsgcGFyc2VJbnQocHJvZHVjdENvdW50KVxuXHRcdFx0XHQrIHRleHRQaHJhc2VzWydwcm9kdWN0c0luQ2FydFN1ZmZpeCddKVxuXHRcdFx0XHQucHJvcChcImRpc2FibGVkXCIsIHRydWUpXG5cdFx0XHRcdC5hZGRDbGFzcyhcImJ0bi1idXktY29tcGxldGVcIik7XG5cdFx0XHRcblx0XHRcdHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdCRidG5GYWtlLmh0bWwoZmFrZU9yaWdMYWJlbClcblx0XHRcdFx0XHQucmVtb3ZlQ2xhc3MoXCJidG4tYnV5LWNvbXBsZXRlXCIpXG5cdFx0XHRcdFx0LmhpZGUoKVxuXHRcdFx0XHRcdC5wcm9wKFwiZGlzYWJsZWRcIiwgZmFsc2UpO1xuXHRcdFx0XHQkKFwiLnRocm9iYmxlclwiLCAkYnRuKS5yZW1vdmUoKTtcblx0XHRcdFx0JGJ0bi5zaG93KCk7XG5cdFx0XHR9LCA1MDAwKTtcblx0XHRcdFxuXHRcdH07XG5cdFx0XG5cdFx0XG5cdFx0Ly8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG5cdFx0ICogQGNvbnN0cnVjdG9yXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHRcblx0XHRcdHZhciAkZm9ybXMgPSAkdGhpcy5maW5kKCdmb3JtJyk7XG5cdFx0XHRcblx0XHRcdGlmIChvcHRpb25zLnBhZ2UgPT09ICdwcm9kdWN0LWluZm8nKSB7XG5cdFx0XHRcdCRmb3Jtcy5maW5kKFwiW25hbWU9YnRuLWFkZC10by1jYXJ0XVwiKVxuXHRcdFx0XHRcdC5vbigndG91Y2hzdGFydCB0b3VjaG1vdmUgdG91Y2hlbmQgdG91Y2hjYW5jZWwnLCAoKSA9PiAkZm9ybXMuZmluZChcIltuYW1lPWJ0bi1hZGQtdG8tY2FydF1cIilcblx0XHRcdFx0XHRcdC5jbGljaygpKTtcblx0XHRcdFx0JGZvcm1zLmZpbmQoXCJbbmFtZT1idG4tYWRkLXRvLWNhcnRdXCIpLm9uKCdtb3VzZXVwJywgX2FkZFRvQ2FydFRocm9iYmVySGFuZGxlcik7XG5cdFx0XHRcdCQoXCJib2R5XCIpLm9uKCdDQVJUX0RST1BET1dOX09QRU4nLCBfY2FydERyb3Bkb3duT3BlbkhhbmRsZXIpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQkZm9ybXNcblx0XHRcdFx0Lm9uKCdzdWJtaXQnLCB7J3RhcmdldCc6ICdjYXJ0J30sIF9zdWJtaXRIYW5kbGVyKVxuXHRcdFx0XHQub24oJ2NsaWNrJywgb3B0aW9ucy53aXNobGlzdEJ1dHRvbnMsIHsndGFyZ2V0JzogJ3dpc2hsaXN0J30sIF9zdWJtaXRIYW5kbGVyKVxuXHRcdFx0XHQub24oJ2NsaWNrJywgb3B0aW9ucy5wcmljZU9mZmVyQnV0dG9ucywgeyd0YXJnZXQnOiAncHJpY2Vfb2ZmZXInfSwgX3N1Ym1pdEhhbmRsZXIpXG5cdFx0XHRcdC5vbignY2hhbmdlJywgb3B0aW9ucy5hdHRyaWJ1dGVzLCB7J3RhcmdldCc6ICdjaGVjayd9LCBfc3VibWl0SGFuZGxlcilcblx0XHRcdFx0Lm9uKCdtb3VzZW92ZXInLCBvcHRpb25zLmF0dHJpYnV0ZXMsIF9zZXRTZWxlY3RlZE1vZGlmaWVySW5mbylcblx0XHRcdFx0Lm9uKCdtb3VzZW91dCcsIG9wdGlvbnMuYXR0cmlidXRlcywgX3Jlc2V0U2VsZWN0ZWRNb2RpZmllckluZm8pXG5cdFx0XHRcdC5vbignYmx1cicsIG9wdGlvbnMucHJvZHVjdE9wdGlvbkZpZWxkLCB7J3RhcmdldCc6ICdjaGVjayd9LCBfc3VibWl0SGFuZGxlcilcblx0XHRcdFx0Lm9uKCdjbGljaycsIG9wdGlvbnMucHJvZHVjdE9wdGlvbnMsIHsndGFyZ2V0JzogJ2NoZWNrJ30sIGZ1bmN0aW9uKGUpIHtcblx0XHRcdFx0XHRfc2VsZWN0U2VsZWN0ZWRNb2RpZmllckluZm8oZSk7XG5cdFx0XHRcdFx0X2NoYW5nZVByb2R1Y3RPcHRpb25zKGUpO1xuXHRcdFx0XHR9KVxuXHRcdFx0XHQub24oJ21vdXNlb3ZlcicsIG9wdGlvbnMucHJvZHVjdE9wdGlvbnMsIF9zZXRTZWxlY3RlZE1vZGlmaWVySW5mbylcblx0XHRcdFx0Lm9uKCdtb3VzZW91dCcsIG9wdGlvbnMucHJvZHVjdE9wdGlvbnMsIF9yZXNldFNlbGVjdGVkTW9kaWZpZXJJbmZvKVxuXHRcdFx0XHQub24oJ2JsdXInLCBvcHRpb25zLnF1YW50aXR5LCB7J3RhcmdldCc6ICdjaGVjayd9LCBmdW5jdGlvbihlKSB7XG5cdFx0XHRcdFx0X3N1Ym1pdEhhbmRsZXIoZSk7XG5cdFx0XHRcdH0pXG5cdFx0XHRcdC5vbigna2V5dXAnLCBvcHRpb25zLnF1YW50aXR5LCB7J3RhcmdldCc6ICdjaGVjayd9LCBfa2V5dXBIYW5kbGVyKTtcblx0XHRcdFxuXHRcdFx0Ly8gRmFsbGJhY2sgaWYgdGhlIGJhY2tlbmQgcmVuZGVycyBpbmNvcnJlY3QgZGF0YVxuXHRcdFx0Ly8gb24gaW5pdGlhbCBwYWdlIGNhbGxcblx0XHRcdCRmb3Jtcy5ub3QoJy5uby1zdGF0dXMtY2hlY2snKS5ub3QoJy5wcm9kdWN0LWluZm8nKS5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRfc3VibWl0SGFuZGxlci5jYWxsKCQodGhpcykpO1xuXHRcdFx0fSk7XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
