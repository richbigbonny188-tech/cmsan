'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/* --------------------------------------------------------------
 product_cart_handler.js 2019-05-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Component that includes the functionality for
 * the add-to-cart, refresh and delete buttons
 * on the wishlist and cart
 */
gambio.widgets.module('product_cart_handler', ['form', 'xhr', gambio.source + '/libs/events', gambio.source + '/libs/modal.ext-magnific', gambio.source + '/libs/modal'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        $window = $(window),
        $body = $('body'),
        $form = null,
        $updateTarget = null,
        $deleteField = null,
        $cartEmpty = null,
        $cartNotEmpty = null,
        deleteFieldName = null,
        action = null,
        busy = null,
        updateList = false,
        transition = null,
        active = {},
        isChanged = false,
        defaults = {
        // Use an AJAX to update the form
        ajax: true,
        // Show an confirm-layer on deletion of an item
        confirmDelete: false,
        // Selector of the hidden field for the deletion entries
        deleteInput: '#field_cart_delete_products_id',
        // Trigger an event to that item on an successfull ajax (e.g. the shipping costs element)
        updateTarget: '.shipping-calculation',
        // The URL for the quantity check of the item
        checkUrl: 'shop.php?do=CheckQuantity',
        // If an URL is set, this one will be requests for status updates on tab focus
        updateUrl: 'shop.php?do=Cart',

        changeClass: 'has-changed', // Class that gets added if an input has changed
        errorClass: 'error', // Class that gets added to the row if an error has occured
        cartEmpty: '.cart-empty', // Show this selection if the cart is empty or hide it else
        cartNotEmpty: '.cart-not-empty', // Show this selection if the cart is not empty or hide it else
        classLoading: 'loading', // The class that gets added to an currently updating row
        actions: { // The actions that getting appended to the submit url on the different type of updates
            add: 'wishlist_to_cart',
            delete: 'update_product',
            refresh: 'update_wishlist'
        },
        ajaxActions: { // URLs for the ajax updates on the different actions
            add: 'shop.php?do=WishList/AddToCart',
            delete: 'shop.php?do=Cart/Delete',
            refresh: 'shop.php?do=Cart/Update'
        },
        selectorMapping: {
            buttons: '.shopping-cart-button',
            giftContent: '.gift-cart-content-wrapper',
            giftLayer: '.gift-cart-layer',
            shareContent: '.share-cart-content-wrapper',
            shareLayer: '.share-cart-layer',
            hiddenOptions: '#cart_quantity .hidden-options',
            message: '.global-error-messages',
            infoMessage: '.info-message',
            shippingInformation: '#shipping-information-layer',
            totals: '#cart_quantity .total-box',
            errorMsg: '.error-msg',
            submit: '.button-submit'
        }
    },
        options = $.extend(false, {}, defaults, data),
        module = {};

    // ########## HELPER FUNCTIONS ##########

    /**
     * Updates the form action to the type given
     * in the options.actions object
     * @param       {string}        type        The action name
     * @private
     */
    var _setAction = function _setAction(type) {
        if (options.ajax) {
            action = options.ajaxActions[type];
        } else if (options.actions && options.actions[type]) {
            action = action.replace(/(action=)[^\&]+/, '$1' + options.actions[type]);
            $form.attr('action', action);
        }
    };

    /**
     * Helper function that updates the
     * hidden data attributes with the current
     * values of the input fields
     * @param       {object}        $target     jQuery selection of the topmost container
     * @private
     */
    var _updateDataValues = function _updateDataValues($target) {
        $target.find('input[type="text"]').each(function () {
            var $self = $(this),
                value = $self.val();

            $self.data('oldValue', value);
        });
    };

    /**
     * Helper function that restores the values
     * stored by the _updateDataValues function
     * @param       {object}        dataset     The data object of all targets that needs to be reset
     * @private
     */
    var _restoreDataValues = function _restoreDataValues(dataset) {
        // Reset each changed field given
        // by the dataset target
        $.each(dataset, function () {
            var value = this;

            value.target.find('.' + options.changeClass).each(function () {
                var $self = $(this),
                    name = $self.attr('name').replace('[]', ''),
                    val = $self.data().oldValue;

                value[name][0] = val;
                $self.val(val).removeClass(options.changeClass);
            });
        });
    };

    /**
     * Helper function that generates an array of  datasets from the form. Each array item
     * contains the data of one row (inclusive the attributes data from the form head belonging
     * to the row). Additionally it adds the target-parameter to each dataset which contains
     * the selection of the row,the current dataset belongs to.
     *
     * @param {object} $row The optional row selection the data gets from. If no selection is given, the form
     * gets selected.
     * @return {Array} The array with the datasets of each row
     *
     * @private
     */
    var _generateFormdataObject = function _generateFormdataObject($row) {
        var $target = $row && $row.length ? $row : $form,
            $rows = $row && $row.length ? $row : $form.find('.order-wishlist .item:gt(0)'),
            $hiddens = $form.find('.hidden-options input[type="hidden"]'),
            dataset = jse.libs.form.getData($target),
            result = [],
            tmpResult = null;

        $.each(dataset.products_id, function (i, v) {
            tmpResult = {};
            tmpResult.target = $rows.eq(i);

            // Store the data from the current row as a json
            $.each(dataset, function (key, value) {
                if ((typeof value === 'undefined' ? 'undefined' : _typeof(value)) === 'object' && value[i] !== undefined) {
                    // Store the value as an array to be compliant with the old API
                    tmpResult[key] = [value[i]];
                }
            });

            // Get the hidden fields for the attributes
            // belonging to this row from the form head
            $hiddens.filter('[name^="id[' + v + '"], .force').each(function () {
                var $self = $(this),
                    name = $self.attr('name');

                tmpResult[name] = $self.val();
            });

            // Push the generated json to the final result array
            result.push(tmpResult);
        });

        return result;
    };

    /**
     * Function that checks the form / the row if the combination
     * and quantity is valid. It returns an promise which gets rejected
     * if in the scope was an invalid value. In other cases it gets
     * resolved. If it is detecting changes inside the form it can
     * show an info layer to the user and / or revert the changes
     * (depending on the caller parameters)
     * @param       {boolean}       showChanges         Show an info-layer if changes would be refused
     * @param       {boolean}       revertChanges       Resets the form values with the one from the data attributes if true
     * @param       {object}        formdata            Json that contains the data to check
     * @return     {*}                                 Returns a promise
     * @private
     */
    var _checkForm = function _checkForm(showChanges, revertChanges, formdata) {

        var promises = [],
            hasChanged = false;

        // Get the complete form data if no row data is given
        formdata = formdata || _generateFormdataObject();

        // Check the formdata for changed values
        $.each(formdata, function () {
            var $changed = this.target.find('.' + options.changeClass);
            hasChanged = hasChanged || !!$changed.length;
            return !hasChanged;
        });

        return $.when.apply(undefined, promises).promise();
    };

    /**
     * Helper function that cleans up the process state
     * (Needed especially after ajax requests, to be able
     * to make further requests)
     * @param       {string}        id              The product id that needs to be reseted
     * @return     {Array.<T>}                     Returns an array without empty fields
     * @private
     */
    var _cleanupArray = function _cleanupArray(id, $row) {
        delete active['product_' + id];
        $row.removeClass('loading');
        return active;
    };

    /**
     * Helper function that does the general form update
     * after an ajax request
     * @param       {object}    $target         The jQuery selection of the target elements.
     * @param       {object}    result          The result of the ajax request.
     * @param       {string}    type            The executed action type.
     * @private
     */
    var _updateForm = function _updateForm($target, result, type) {
        // Update the rest of the page
        jse.libs.theme.helpers.fill(result.content, $body, options.selectorMapping);

        // Toggle info-messages visibility.
        $('.info-message').toggleClass('hidden', $('.info-message').text() === '');

        // Inform other widgets about the update
        $updateTarget.trigger(jse.libs.theme.events.CART_UPDATED(), []);
        $body.trigger(jse.libs.theme.events.CART_UPDATE(), type === 'add');

        // Update the hidden data attributes of that row
        _updateDataValues($target);

        if ($.isEmptyObject(result.products)) {
            // Hide the table if no products are at the list
            $cartNotEmpty.addClass('hidden');
            $cartEmpty.removeClass('hidden');
        } else {
            // Show the table if there are products at it
            $cartEmpty.addClass('hidden');
            $cartNotEmpty.removeClass('hidden');
        }

        // reinitialize widgets in updated DOM
        window.gambio.widgets.init($this);
    };

    /**
     * Helper function to update product specific messages.
     * Therefor it calls AJAX-requests (in case ajax is
     * enabled) to the server to get the updated information
     * about the table state. If ajax isn't enabled, it simply
     * submits the form.
     * @param       {object}        messages            Object with articleId => message configuration object
     * @private
     */
    var _updateArticlesMessage = function _updateArticlesMessage(messages) {

        //update all messages og the request
        $.each(messages, function (product, message) {
            var messageCfg = { 'message': message };
            //let productId = product.match(/\d+/)[0];
            // Find the related product tr
            var item = $('input[value^="' + product + '"]').closest('tr');

            jse.libs.theme.helpers.fill(messageCfg, item, options.selectorMapping);
            if (item.find('.error-msg').text() !== '') {
                item.find('.error-msg').show();
            }
        });
    };

    /**
     * Helper function that processes the list updates.
     * Therefor it calls AJAX-requests (in case ajax is
     * enabled) to the server to get the updated information
     * about the table state. If ajax isn't enabled, it simply
     * submits the form.
     * @param       {object}        $target            The jQuery selection of the row that gets updated
     * @param       {object}        dataset            The data collected from the target row in JSON format
     * @param       {article}       article            The products id of the article in that row
     * @param       {article}       type               The operation type can either be "add", "delete" or "refresh".
     * @private
     */
    var _executeAction = function _executeAction($row, $target, dataset, article, type) {
        if (options.ajax) {
            // Delete the target element because ajax requests
            // will fail with a jQuery selection in the data json
            delete dataset.target;

            $row.trigger(jse.libs.theme.events.TRANSITION(), transition);

            // Perform an ajax if the data is valid and the options for ajax is set
            jse.libs.xhr.ajax({ url: action, data: dataset }, true).done(function (result) {
                // Perform hooks
                jse.libs.hooks.execute(jse.libs.hooks.keys.shop.cart.change, {
                    $target: $target,
                    dataset: dataset,
                    article: article,
                    type: type,
                    result: result
                }, 500);

                // Update the product row
                var $markup = $(result.products['product_' + article] || '');

                // Toggle error-messages visibility.
                $markup.removeClass(options.classLoading);
                $target.replaceWith($markup);

                _updateArticlesMessage(result.content.errorMessageList);
                delete result.content.errorMessageList;

                _updateForm($target, result, type);

                var productNumber = article.match(/\d+/)[0];

                // Find all items with the same product number
                var $items = $('input[value^="' + productNumber + '"]').parent('td');

                // Apply the new markup foreach item which has the same product number.
                $items.each(function () {
                    if (!$(this).find('input[value="' + article + '"]').length) {
                        var number = $(this).find('input[id="products_id[]"]').attr('value');
                        $markup = $(result.products['product_' + number] || '');
                        $target = $(this).parent('tr');
                        $target.replaceWith($markup);
                    }
                });
            }).always(function () {
                _cleanupArray(article, $row);
            });
        } else {
            // Cleanup the active array on fail / success
            // of the following submit. This is a fallback
            // if an other component would prevent the submit
            // in some cases, so that this script can perform
            // actions again
            var deferred = $.Deferred();
            deferred.always(function () {
                _cleanupArray(article, $row);
            });

            // Submit the form
            $form.trigger('submit', deferred);
        }
    };

    // ########## EVENT HANDLER ##########

    /**
     * Adds an class to the changed input
     * field, so that it's styling shows
     * that it wasn't refreshed till now
     * @private
     */
    var _inputHandler = function _inputHandler() {
        var $self = $(this),
            value = $self.val(),
            oldValue = $self.data().oldValue,
            hasNewValue = value !== oldValue;

        if (hasNewValue) {
            isChanged = hasNewValue;
            $self.addClass(options.changeClass);
        } else {
            $self.removeClass(options.changeClass);
        }

        _updateChangeState();
    };

    /**
     * Handle the blur event
     * @private
     */
    var _blurHandler = function _blurHandler() {
        var $self = $(this),
            value = $self.val(),
            oldValue = $self.data().oldValue,
            hasNewValue = value !== oldValue;

        if (hasNewValue) {
            $self.closest('.item').find('.button-refresh').first().trigger('click');
        }
    };

    /**
     * Handler that listens on click events on the
     * buttons "refresh", "delete" & "add to cart".
     * It validates the form / row and passes the
     * the data to an submit execute funciton if valid
     * @param       {object}    e       jQuery event object
     * @private
     */
    var _clickHandler = function _clickHandler(e) {
        e.preventDefault();
        e.stopPropagation();

        var $self = $(this),
            $row = $self.closest('.item'),
            type = e.data.type,
            rowdata = _generateFormdataObject($row)[0],
            article = rowdata.products_id[0],
            $target = rowdata.target,
            title = $target.find('.product-title').text();

        // Add loading class
        $row.addClass('loading');

        // Check if there is no current process for this article
        // or in case it's no ajax call there is NO other process
        if ($.isEmptyObject(active) || options.ajax && !active['product_' + article]) {
            active['product_' + article] = true;
            _setAction(type);

            switch (type) {
                case 'delete':
                    // Update the form and the dataset with
                    // the article id to delete
                    $deleteField.val(article);
                    rowdata[deleteFieldName] = [article];

                    if (options.confirmDelete) {
                        // Open a modal layer to confirm the deletion
                        var modalTitle = jse.core.lang.translate('CART_WISHLIST_DELETE_TITLE', 'general'),
                            modalMessage = jse.core.lang.translate('CART_WISHLIST_DELETE', 'general');

                        jse.libs.theme.modal.confirm({
                            content: modalMessage,
                            title: modalTitle
                        }).done(function () {
                            var deferred = $.Deferred();

                            deferred.done(function () {
                                _executeAction($row, $target, rowdata, article, type);
                            });

                            $body.trigger(jse.libs.theme.events.WISHLIST_CART_DELETE(), [{
                                'deferred': deferred,
                                'dataset': rowdata
                            }]);
                        }).fail(function () {
                            _cleanupArray(article, $row);
                        });
                    } else {
                        var deferred = $.Deferred();

                        deferred.done(function () {
                            _executeAction($row, $target, rowdata, article, type);
                        });

                        $body.trigger(jse.libs.theme.events.WISHLIST_CART_DELETE(), [{
                            'deferred': deferred,
                            'dataset': rowdata
                        }]);
                    }
                    break;

                default:
                    // In all other cases check if the form
                    // has valid values and continue with the
                    // done callback if valid
                    _checkForm(false, false, [$.extend(true, {}, rowdata)]).done(function () {
                        // Empty the delete hidden field in case it was set before
                        $deleteField.val('');

                        var event = null;

                        if (type === 'add') {
                            event = jse.libs.theme.events.WISHLIST_TO_CART();
                        }

                        if (event) {
                            var deferred = $.Deferred();

                            deferred.done(function () {
                                _executeAction($row, $target, rowdata, article, type);
                            });

                            $body.trigger(event, [{ 'deferred': deferred, 'dataset': rowdata }]);
                        } else {
                            _executeAction($row, $target, rowdata, article, type);
                        }
                    }).fail(function () {
                        _cleanupArray(article, $row);
                    });
                    break;
            }
        }
    };

    /**
     * Prevent the submit event that was triggerd
     * by user or by script. If it was triggered
     * by the user, check if it was an "Enter"-key
     * submit from an input field. If so, execute
     * the refresh functionality for that row.
     * If the event was triggered by the script
     * (identified by the data flag "d") check the
     * whole form for errors. Only in case of valid
     * data proceed the submit
     * @param       {object}        e       jQuery event object
     * @param       {boolean}       d       A flag that identifies that the submit was triggered by this script
     * @private
     */
    var _submitHandler = function _submitHandler(e, d) {

        // Prevent the default behaviour
        // in both cases
        e.preventDefault();
        e.stopPropagation();

        if (!d && e.originalEvent) {

            // Check if an input field has triggerd the submit event
            // and call the refresh handler
            var $source = $(e.originalEvent.explicitOriginalTarget);
            if ($source.length && $source.is('input[type="text"]')) {
                $source.closest('.item').find('.button-refresh').first().trigger('click');
            }
        } else if (d) {

            // Check the whole form and only submit
            // it if it's valid
            _checkForm().done(function () {
                // Remove the submit event handler
                // on a successful validation and
                // trigger a submit again, so that the
                // browser executes it's default behavior
                $form.off('submit').trigger('submit');

                // Resolve the deferred if given
                if ((typeof d === 'undefined' ? 'undefined' : _typeof(d)) === 'object') {
                    d.resolve();
                }
            }).fail(function () {
                // Reject the deferred if given
                if ((typeof d === 'undefined' ? 'undefined' : _typeof(d)) === 'object') {
                    d.reject();
                }
            });
        }
    };

    /**
     * Event handler for clicking on the proceed
     * button to get to the checkout process. It
     * checks all items again if they contain valid
     * data. Only if so, proceed
     * @param       {object}        e       jQuery event object
     * @private
     */
    var _submitButtonHandler = function _submitButtonHandler(e) {
        e.preventDefault();
        e.stopPropagation();

        if (isChanged) {
            // Get the complete form data if no row data is given
            var formdata = _generateFormdataObject();

            // Check the formdata for changed values
            $.each(formdata, function () {
                var $changedInput = this.target.find('.' + options.changeClass);

                if ($changedInput) {
                    $changedInput.closest('.item').find('.button-refresh').first().trigger('click');
                }
            });

            isChanged = false;
            _updateChangeState();
            return;
        }

        var $self = $(this),
            destination = $self.attr('href');

        // Check if there is any other process running
        if ($.isEmptyObject(active) && !busy && !updateList) {
            busy = true;

            _checkForm(true, true).done(function () {
                function callback() {
                    location.href = destination;
                }

                jse.libs.hooks.execute(jse.libs.hooks.keys.shop.cart.checkout, { event: e }, 500).then(callback).catch(callback);
            }).always(function () {
                busy = false;
            });
        }
    };

    /**
     * Event handler that checks the form and
     * resolves or rejects the delivered deferred
     * (Used for external payment modules to
     * check if the form is valid)
     * @param       {object}    e               jQuery event object
     * @param       {object}    d               JSON object with the event settings
     * @private
     */
    var _checkFormHandler = function _checkFormHandler(e, d) {
        e.stopPropagation();

        d = d || {};

        _checkForm(d.showChanges, d.revertChanges).done(function () {
            if (d.deferred) {
                d.deferred.resolve();
            }
        }).fail(function () {
            if (d.deferred) {
                d.deferred.reject();
            }
        });
    };

    /**
     * Function that updates the list on focus of
     * the window
     * @private
     */
    var _updateList = function _updateList() {
        updateList = true;
        jse.libs.xhr.ajax({ url: options.updateUrl }, true).done(function (result) {
            // Init with he first line since this ist the heading
            var $lastScanned = $form.find('.order-wishlist .item').first(),
                $target = $();

            // Iterate through the products object and search for the
            // products inside the markup. If the product was found,
            // update the values, if not add the product row at the
            // correct position
            $.each(result.products, function (key, value) {
                var articleId = key.replace('product_', ''),
                    $article = $form.find('input[name="products_id[]"][value="' + articleId + '"]'),
                    $row = null;

                if (!$article.length) {
                    // The article wasn't found on page
                    // -> add it
                    $row = $(value);
                    $row.insertAfter($lastScanned);
                } else {
                    // The article was found on page
                    // -> update it
                    $row = $article.closest('.item');

                    var $qty = $row.find('input[name="cart_quantity[]"]'),
                        oldQty = parseFloat($qty.data().oldValue),
                        currentQty = parseFloat($qty.val()),
                        newQty = parseFloat($(value).find('input[name="cart_quantity[]"]').val());

                    $qty.data('oldValue', newQty);

                    // Add or remove the changed classes depending on
                    // the quantity changes and the on page stored values
                    if (oldQty === currentQty && currentQty !== newQty) {
                        $qty.addClass(options.changeClass);
                    } else if (oldQty !== currentQty && currentQty === newQty) {
                        $qty.removeClass(options.changeClass);
                    }
                }

                $target.add($row);
                $lastScanned = $row;
            });

            // Update the rest of the form
            _updateForm($target, result);
        }).always(function () {
            updateList = false;
        });
    };

    /**
     * Update the input change state
     * @private
     */
    var _updateChangeState = function _updateChangeState() {
        $form.find(options.selectorMapping.submit).text(jse.core.lang.translate(isChanged ? 'refresh' : 'checkout', 'buttons'));
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        $updateTarget = $(options.updateTarget);
        $cartEmpty = $(options.cartEmpty);
        $cartNotEmpty = $(options.cartNotEmpty);
        $deleteField = $(options.deleteInput);
        $form = $this.find('form').first();
        deleteFieldName = $deleteField.attr('name');
        action = $form.attr('action');
        transition = { open: true, classOpen: options.classLoading };

        // Sets the current value of the input
        // to an hidden data attribute
        _updateDataValues($form);

        $form.on('input', 'input[type="text"]:not(.gift-coupon-code-input)', _inputHandler).on('blur', 'input[type="text"]:not(.gift-coupon-code-input)', _blurHandler).on('click.delete', '.button-delete', { 'type': 'delete' }, _clickHandler).on('click.refresh', '.button-refresh', { 'type': 'refresh' }, _clickHandler).on('click.addtocart', '.button-to-cart', { 'type': 'add' }, _clickHandler).on('click.submit', '.button-submit', { 'type': 'submit' }, _submitButtonHandler).on('submit', _submitHandler).on(jse.libs.theme.events.CHECK_CART(), _checkFormHandler);

        $('a.toggleusebalance input[name="gv_use_balance"]').on('click', function () {
            console.info('click');location = $(this).parent('a').get(0).href;
        });

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcHJvZHVjdF9jYXJ0X2hhbmRsZXIuanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsInNvdXJjZSIsImRhdGEiLCIkdGhpcyIsIiQiLCIkd2luZG93Iiwid2luZG93IiwiJGJvZHkiLCIkZm9ybSIsIiR1cGRhdGVUYXJnZXQiLCIkZGVsZXRlRmllbGQiLCIkY2FydEVtcHR5IiwiJGNhcnROb3RFbXB0eSIsImRlbGV0ZUZpZWxkTmFtZSIsImFjdGlvbiIsImJ1c3kiLCJ1cGRhdGVMaXN0IiwidHJhbnNpdGlvbiIsImFjdGl2ZSIsImlzQ2hhbmdlZCIsImRlZmF1bHRzIiwiYWpheCIsImNvbmZpcm1EZWxldGUiLCJkZWxldGVJbnB1dCIsInVwZGF0ZVRhcmdldCIsImNoZWNrVXJsIiwidXBkYXRlVXJsIiwiY2hhbmdlQ2xhc3MiLCJlcnJvckNsYXNzIiwiY2FydEVtcHR5IiwiY2FydE5vdEVtcHR5IiwiY2xhc3NMb2FkaW5nIiwiYWN0aW9ucyIsImFkZCIsImRlbGV0ZSIsInJlZnJlc2giLCJhamF4QWN0aW9ucyIsInNlbGVjdG9yTWFwcGluZyIsImJ1dHRvbnMiLCJnaWZ0Q29udGVudCIsImdpZnRMYXllciIsInNoYXJlQ29udGVudCIsInNoYXJlTGF5ZXIiLCJoaWRkZW5PcHRpb25zIiwibWVzc2FnZSIsImluZm9NZXNzYWdlIiwic2hpcHBpbmdJbmZvcm1hdGlvbiIsInRvdGFscyIsImVycm9yTXNnIiwic3VibWl0Iiwib3B0aW9ucyIsImV4dGVuZCIsIl9zZXRBY3Rpb24iLCJ0eXBlIiwicmVwbGFjZSIsImF0dHIiLCJfdXBkYXRlRGF0YVZhbHVlcyIsIiR0YXJnZXQiLCJmaW5kIiwiZWFjaCIsIiRzZWxmIiwidmFsdWUiLCJ2YWwiLCJfcmVzdG9yZURhdGFWYWx1ZXMiLCJkYXRhc2V0IiwidGFyZ2V0IiwibmFtZSIsIm9sZFZhbHVlIiwicmVtb3ZlQ2xhc3MiLCJfZ2VuZXJhdGVGb3JtZGF0YU9iamVjdCIsIiRyb3ciLCJsZW5ndGgiLCIkcm93cyIsIiRoaWRkZW5zIiwianNlIiwibGlicyIsImZvcm0iLCJnZXREYXRhIiwicmVzdWx0IiwidG1wUmVzdWx0IiwicHJvZHVjdHNfaWQiLCJpIiwidiIsImVxIiwia2V5IiwidW5kZWZpbmVkIiwiZmlsdGVyIiwicHVzaCIsIl9jaGVja0Zvcm0iLCJzaG93Q2hhbmdlcyIsInJldmVydENoYW5nZXMiLCJmb3JtZGF0YSIsInByb21pc2VzIiwiaGFzQ2hhbmdlZCIsIiRjaGFuZ2VkIiwid2hlbiIsImFwcGx5IiwicHJvbWlzZSIsIl9jbGVhbnVwQXJyYXkiLCJpZCIsIl91cGRhdGVGb3JtIiwidGhlbWUiLCJoZWxwZXJzIiwiZmlsbCIsImNvbnRlbnQiLCJ0b2dnbGVDbGFzcyIsInRleHQiLCJ0cmlnZ2VyIiwiZXZlbnRzIiwiQ0FSVF9VUERBVEVEIiwiQ0FSVF9VUERBVEUiLCJpc0VtcHR5T2JqZWN0IiwicHJvZHVjdHMiLCJhZGRDbGFzcyIsImluaXQiLCJfdXBkYXRlQXJ0aWNsZXNNZXNzYWdlIiwibWVzc2FnZXMiLCJwcm9kdWN0IiwibWVzc2FnZUNmZyIsIml0ZW0iLCJjbG9zZXN0Iiwic2hvdyIsIl9leGVjdXRlQWN0aW9uIiwiYXJ0aWNsZSIsIlRSQU5TSVRJT04iLCJ4aHIiLCJ1cmwiLCJkb25lIiwiaG9va3MiLCJleGVjdXRlIiwia2V5cyIsInNob3AiLCJjYXJ0IiwiY2hhbmdlIiwiJG1hcmt1cCIsInJlcGxhY2VXaXRoIiwiZXJyb3JNZXNzYWdlTGlzdCIsInByb2R1Y3ROdW1iZXIiLCJtYXRjaCIsIiRpdGVtcyIsInBhcmVudCIsIm51bWJlciIsImFsd2F5cyIsImRlZmVycmVkIiwiRGVmZXJyZWQiLCJfaW5wdXRIYW5kbGVyIiwiaGFzTmV3VmFsdWUiLCJfdXBkYXRlQ2hhbmdlU3RhdGUiLCJfYmx1ckhhbmRsZXIiLCJmaXJzdCIsIl9jbGlja0hhbmRsZXIiLCJlIiwicHJldmVudERlZmF1bHQiLCJzdG9wUHJvcGFnYXRpb24iLCJyb3dkYXRhIiwidGl0bGUiLCJtb2RhbFRpdGxlIiwiY29yZSIsImxhbmciLCJ0cmFuc2xhdGUiLCJtb2RhbE1lc3NhZ2UiLCJtb2RhbCIsImNvbmZpcm0iLCJXSVNITElTVF9DQVJUX0RFTEVURSIsImZhaWwiLCJldmVudCIsIldJU0hMSVNUX1RPX0NBUlQiLCJfc3VibWl0SGFuZGxlciIsImQiLCJvcmlnaW5hbEV2ZW50IiwiJHNvdXJjZSIsImV4cGxpY2l0T3JpZ2luYWxUYXJnZXQiLCJpcyIsIm9mZiIsInJlc29sdmUiLCJyZWplY3QiLCJfc3VibWl0QnV0dG9uSGFuZGxlciIsIiRjaGFuZ2VkSW5wdXQiLCJkZXN0aW5hdGlvbiIsImNhbGxiYWNrIiwibG9jYXRpb24iLCJocmVmIiwiY2hlY2tvdXQiLCJ0aGVuIiwiY2F0Y2giLCJfY2hlY2tGb3JtSGFuZGxlciIsIl91cGRhdGVMaXN0IiwiJGxhc3RTY2FubmVkIiwiYXJ0aWNsZUlkIiwiJGFydGljbGUiLCJpbnNlcnRBZnRlciIsIiRxdHkiLCJvbGRRdHkiLCJwYXJzZUZsb2F0IiwiY3VycmVudFF0eSIsIm5ld1F0eSIsIm9wZW4iLCJjbGFzc09wZW4iLCJvbiIsIkNIRUNLX0NBUlQiLCJjb25zb2xlIiwiaW5mbyIsImdldCJdLCJtYXBwaW5ncyI6Ijs7OztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7O0FBS0FBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNJLHNCQURKLEVBR0ksQ0FDSSxNQURKLEVBRUksS0FGSixFQUdJRixPQUFPRyxNQUFQLEdBQWdCLGNBSHBCLEVBSUlILE9BQU9HLE1BQVAsR0FBZ0IsMEJBSnBCLEVBS0lILE9BQU9HLE1BQVAsR0FBZ0IsYUFMcEIsQ0FISixFQVdJLFVBQVVDLElBQVYsRUFBZ0I7O0FBRVo7O0FBRVI7O0FBRVEsUUFBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxRQUNJQyxVQUFVRCxFQUFFRSxNQUFGLENBRGQ7QUFBQSxRQUVJQyxRQUFRSCxFQUFFLE1BQUYsQ0FGWjtBQUFBLFFBR0lJLFFBQVEsSUFIWjtBQUFBLFFBSUlD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TCxDQUVRLE1BRlIsRUFFZ0IsaURBRmhCLEVBRW1FbEQsWUFGbkUsRUFHS2tELEVBSEwsQ0FHUSxjQUhSLEVBR3dCLGdCQUh4QixFQUcwQyxFQUFDLFFBQVEsUUFBVCxFQUgxQyxFQUc4RGhELGFBSDlELEVBSUtnRCxFQUpMLENBSVEsZUFKUixFQUl5QixpQkFKekIsRUFJNEMsRUFBQyxRQUFRLFNBQVQsRUFKNUMsRUFJaUVoRCxhQUpqRSxFQUtLZ0QsRUFMTCxDQUtRLGlCQUxSLEVBSzJCLGlCQUwzQixFQUs4QyxFQUFDLFFBQVEsS0FBVCxFQUw5QyxFQUsrRGhELGFBTC9ELEVBTUtnRCxFQU5MLENBTVEsY0FOUixFQU13QixnQkFOeEIsRUFNMEMsRUFBQyxRQUFRLFFBQVQsRUFOMUMsRUFNOER0QixvQkFOOUQsRUFPS3NCLEVBUEwsQ0FPUSxRQVBSLEVBT2tCL0IsY0FQbEIsRUFRSytCLEVBUkwsQ0FRUTVILElBQUlDLElBQUosQ0FBUzBCLEtBQVQsQ0FBZU8sTUFBZixDQUFzQjJGLFVBQXRCLEVBUlIsRUFRNENkLGlCQVI1Qzs7QUFVQXJMLFVBQUUsaURBQUYsRUFBcURrTSxFQUFyRCxDQUF3RCxPQUF4RCxFQUFpRSxZQUFXO0FBQzNFRSxvQkFBUUMsSUFBUixDQUFhLE9BQWIsRUFBdUJyQixXQUFXaEwsRUFBRSxJQUFGLEVBQVF3SSxNQUFSLENBQWUsR0FBZixFQUFvQjhELEdBQXBCLENBQXdCLENBQXhCLEVBQTJCckIsSUFBdEM7QUFDdkIsU0FGRDs7QUFJQXREO0FBQ0gsS0E5QkQ7O0FBZ0NBO0FBQ0EsV0FBTy9ILE1BQVA7QUFDSCxDQTl3QkwiLCJmaWxlIjoid2lkZ2V0cy9wcm9kdWN0X2NhcnRfaGFuZGxlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gcHJvZHVjdF9jYXJ0X2hhbmRsZXIuanMgMjAxOS0wNS0yM1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTggR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogQ29tcG9uZW50IHRoYXQgaW5jbHVkZXMgdGhlIGZ1bmN0aW9uYWxpdHkgZm9yXG4gKiB0aGUgYWRkLXRvLWNhcnQsIHJlZnJlc2ggYW5kIGRlbGV0ZSBidXR0b25zXG4gKiBvbiB0aGUgd2lzaGxpc3QgYW5kIGNhcnRcbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuICAgICdwcm9kdWN0X2NhcnRfaGFuZGxlcicsXG5cbiAgICBbXG4gICAgICAgICdmb3JtJyxcbiAgICAgICAgJ3hocicsXG4gICAgICAgIGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvZXZlbnRzJyxcbiAgICAgICAgZ2FtYmlvLnNvdXJjZSArICcvbGlicy9tb2RhbC5leHQtbWFnbmlmaWMnLFxuICAgICAgICBnYW1iaW8uc291cmNlICsgJy9saWJzL21vZGFsJ1xuICAgIF0sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgdmFyICR0aGlzID0gJCh0aGlzKSxcbiAgICAgICAgICAgICR3aW5kb3cgPSAkKHdpbmRvdyksXG4gICAgICAgICAgICAkYm9keSA9ICQoJ2JvZHknKSxcbiAgICAgICAgICAgICRmb3JtID0gbnVsbCxcbiAgICAgICAgICAgICR1cGRhdGVUYXJnZXQgPSBudWxsLFxuICAgICAgICAgICAgJGRlbGV0ZUZpZWxkID0gbnVsbCxcbiAgICAgICAgICAgICRjYXJ0RW1wdHkgPSBudWxsLFxuICAgICAgICAgICAgJGNhcnROb3RFbXB0eSA9IG51bGwsXG4gICAgICAgICAgICBkZWxldGVGaWVsZE5hbWUgPSBudWxsLFxuICAgICAgICAgICAgYWN0aW9uID0gbnVsbCxcbiAgICAgICAgICAgIGJ1c3kgPSBudWxsLFxuICAgICAgICAgICAgdXBkYXRlTGlzdCA9IGZhbHNlLFxuICAgICAgICAgICAgdHJhbnNpdGlvbiA9IG51bGwsXG4gICAgICAgICAgICBhY3RpdmUgPSB7fSxcbiAgICAgICAgICAgIGlzQ2hhbmdlZCA9IGZhbHNlLFxuICAgICAgICAgICAgZGVmYXVsdHMgPSB7XG4gICAgICAgICAgICAgICAgLy8gVXNlIGFuIEFKQVggdG8gdXBkYXRlIHRoZSBmb3JtXG4gICAgICAgICAgICAgICAgYWpheDogdHJ1ZSxcbiAgICAgICAgICAgICAgICAvLyBTaG93IGFuIGNvbmZpcm0tbGF5ZXIgb24gZGVsZXRpb24gb2YgYW4gaXRlbVxuICAgICAgICAgICAgICAgIGNvbmZpcm1EZWxldGU6IGZhbHNlLFxuICAgICAgICAgICAgICAgIC8vIFNlbGVjdG9yIG9mIHRoZSBoaWRkZW4gZmllbGQgZm9yIHRoZSBkZWxldGlvbiBlbnRyaWVzXG4gICAgICAgICAgICAgICAgZGVsZXRlSW5wdXQ6ICcjZmllbGRfY2FydF9kZWxldGVfcHJvZHVjdHNfaWQnLFxuICAgICAgICAgICAgICAgIC8vIFRyaWdnZXIgYW4gZXZlbnQgdG8gdGhhdCBpdGVtIG9uIGFuIHN1Y2Nlc3NmdWxsIGFqYXggKGUuZy4gdGhlIHNoaXBwaW5nIGNvc3RzIGVsZW1lbnQpXG4gICAgICAgICAgICAgICAgdXBkYXRlVGFyZ2V0OiAnLnNoaXBwaW5nLWNhbGN1bGF0aW9uJyxcbiAgICAgICAgICAgICAgICAvLyBUaGUgVVJMIGZvciB0aGUgcXVhbnRpdHkgY2hlY2sgb2YgdGhlIGl0ZW1cbiAgICAgICAgICAgICAgICBjaGVja1VybDogJ3Nob3AucGhwP2RvPUNoZWNrUXVhbnRpdHknLFxuICAgICAgICAgICAgICAgIC8vIElmIGFuIFVSTCBpcyBzZXQsIHRoaXMgb25lIHdpbGwgYmUgcmVxdWVzdHMgZm9yIHN0YXR1cyB1cGRhdGVzIG9uIHRhYiBmb2N1c1xuICAgICAgICAgICAgICAgIHVwZGF0ZVVybDogJ3Nob3AucGhwP2RvPUNhcnQnLFxuXG4gICAgICAgICAgICAgICAgY2hhbmdlQ2xhc3M6ICdoYXMtY2hhbmdlZCcsIC8vIENsYXNzIHRoYXQgZ2V0cyBhZGRlZCBpZiBhbiBpbnB1dCBoYXMgY2hhbmdlZFxuICAgICAgICAgICAgICAgIGVycm9yQ2xhc3M6ICdlcnJvcicsIC8vIENsYXNzIHRoYXQgZ2V0cyBhZGRlZCB0byB0aGUgcm93IGlmIGFuIGVycm9yIGhhcyBvY2N1cmVkXG4gICAgICAgICAgICAgICAgY2FydEVtcHR5OiAnLmNhcnQtZW1wdHknLCAvLyBTaG93IHRoaXMgc2VsZWN0aW9uIGlmIHRoZSBjYXJ0IGlzIGVtcHR5IG9yIGhpZGUgaXQgZWxzZVxuICAgICAgICAgICAgICAgIGNhcnROb3RFbXB0eTogJy5jYXJ0LW5vdC1lbXB0eScsIC8vIFNob3cgdGhpcyBzZWxlY3Rpb24gaWYgdGhlIGNhcnQgaXMgbm90IGVtcHR5IG9yIGhpZGUgaXQgZWxzZVxuICAgICAgICAgICAgICAgIGNsYXNzTG9hZGluZzogJ2xvYWRpbmcnLCAvLyBUaGUgY2xhc3MgdGhhdCBnZXRzIGFkZGVkIHRvIGFuIGN1cnJlbnRseSB1cGRhdGluZyByb3dcbiAgICAgICAgICAgICAgICBhY3Rpb25zOiB7IC8vIFRoZSBhY3Rpb25zIHRoYXQgZ2V0dGluZyBhcHBlbmRlZCB0byB0aGUgc3VibWl0IHVybCBvbiB0aGUgZGlmZmVyZW50IHR5cGUgb2YgdXBkYXRlc1xuICAgICAgICAgICAgICAgICAgICBhZGQ6ICd3aXNobGlzdF90b19jYXJ0JyxcbiAgICAgICAgICAgICAgICAgICAgZGVsZXRlOiAndXBkYXRlX3Byb2R1Y3QnLFxuICAgICAgICAgICAgICAgICAgICByZWZyZXNoOiAndXBkYXRlX3dpc2hsaXN0J1xuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgYWpheEFjdGlvbnM6IHsgLy8gVVJMcyBmb3IgdGhlIGFqYXggdXBkYXRlcyBvbiB0aGUgZGlmZmVyZW50IGFjdGlvbnNcbiAgICAgICAgICAgICAgICAgICAgYWRkOiAnc2hvcC5waHA/ZG89V2lzaExpc3QvQWRkVG9DYXJ0JyxcbiAgICAgICAgICAgICAgICAgICAgZGVsZXRlOiAnc2hvcC5waHA/ZG89Q2FydC9EZWxldGUnLFxuICAgICAgICAgICAgICAgICAgICByZWZyZXNoOiAnc2hvcC5waHA/ZG89Q2FydC9VcGRhdGUnXG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICBzZWxlY3Rvck1hcHBpbmc6IHtcbiAgICAgICAgICAgICAgICAgICAgYnV0dG9uczogJy5zaG9wcGluZy1jYXJ0LWJ1dHRvbicsXG4gICAgICAgICAgICAgICAgICAgIGdpZnRDb250ZW50OiAnLmdpZnQtY2FydC1jb250ZW50LXdyYXBwZXInLFxuICAgICAgICAgICAgICAgICAgICBnaWZ0TGF5ZXI6ICcuZ2lmdC1jYXJ0LWxheWVyJyxcbiAgICAgICAgICAgICAgICAgICAgc2hhcmVDb250ZW50OiAnLnNoYXJlLWNhcnQtY29udGVudC13cmFwcGVyJyxcbiAgICAgICAgICAgICAgICAgICAgc2hhcmVMYXllcjogJy5zaGFyZS1jYXJ0LWxheWVyJyxcbiAgICAgICAgICAgICAgICAgICAgaGlkZGVuT3B0aW9uczogJyNjYXJ0X3F1YW50aXR5IC5oaWRkZW4tb3B0aW9ucycsXG4gICAgICAgICAgICAgICAgICAgIG1lc3NhZ2U6ICcuZ2xvYmFsLWVycm9yLW1lc3NhZ2VzJyxcbiAgICAgICAgICAgICAgICAgICAgaW5mb01lc3NhZ2U6ICcuaW5mby1tZXNzYWdlJyxcbiAgICAgICAgICAgICAgICAgICAgc2hpcHBpbmdJbmZvcm1hdGlvbjogJyNzaGlwcGluZy1pbmZvcm1hdGlvbi1sYXllcicsXG4gICAgICAgICAgICAgICAgICAgIHRvdGFsczogJyNjYXJ0X3F1YW50aXR5IC50b3RhbC1ib3gnLFxuICAgICAgICAgICAgICAgICAgICBlcnJvck1zZzogJy5lcnJvci1tc2cnLFxuICAgICAgICAgICAgICAgICAgICBzdWJtaXQ6ICcuYnV0dG9uLXN1Ym1pdCdcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgb3B0aW9ucyA9ICQuZXh0ZW5kKGZhbHNlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuICAgICAgICAgICAgbW9kdWxlID0ge307XG5cbi8vICMjIyMjIyMjIyMgSEVMUEVSIEZVTkNUSU9OUyAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFVwZGF0ZXMgdGhlIGZvcm0gYWN0aW9uIHRvIHRoZSB0eXBlIGdpdmVuXG4gICAgICAgICAqIGluIHRoZSBvcHRpb25zLmFjdGlvbnMgb2JqZWN0XG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7c3RyaW5nfSAgICAgICAgdHlwZSAgICAgICAgVGhlIGFjdGlvbiBuYW1lXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3NldEFjdGlvbiA9IGZ1bmN0aW9uICh0eXBlKSB7XG4gICAgICAgICAgICBpZiAob3B0aW9ucy5hamF4KSB7XG4gICAgICAgICAgICAgICAgYWN0aW9uID0gb3B0aW9ucy5hamF4QWN0aW9uc1t0eXBlXTtcbiAgICAgICAgICAgIH0gZWxzZSBpZiAob3B0aW9ucy5hY3Rpb25zICYmIG9wdGlvbnMuYWN0aW9uc1t0eXBlXSkge1xuICAgICAgICAgICAgICAgIGFjdGlvbiA9IGFjdGlvbi5yZXBsYWNlKC8oYWN0aW9uPSlbXlxcJl0rLywgJyQxJyArIG9wdGlvbnMuYWN0aW9uc1t0eXBlXSk7XG4gICAgICAgICAgICAgICAgJGZvcm0uYXR0cignYWN0aW9uJywgYWN0aW9uKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGVscGVyIGZ1bmN0aW9uIHRoYXQgdXBkYXRlcyB0aGVcbiAgICAgICAgICogaGlkZGVuIGRhdGEgYXR0cmlidXRlcyB3aXRoIHRoZSBjdXJyZW50XG4gICAgICAgICAqIHZhbHVlcyBvZiB0aGUgaW5wdXQgZmllbGRzXG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgJHRhcmdldCAgICAgalF1ZXJ5IHNlbGVjdGlvbiBvZiB0aGUgdG9wbW9zdCBjb250YWluZXJcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfdXBkYXRlRGF0YVZhbHVlcyA9IGZ1bmN0aW9uICgkdGFyZ2V0KSB7XG4gICAgICAgICAgICAkdGFyZ2V0XG4gICAgICAgICAgICAgICAgLmZpbmQoJ2lucHV0W3R5cGU9XCJ0ZXh0XCJdJylcbiAgICAgICAgICAgICAgICAuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgIHZhciAkc2VsZiA9ICQodGhpcyksXG4gICAgICAgICAgICAgICAgICAgICAgICB2YWx1ZSA9ICRzZWxmLnZhbCgpO1xuXG4gICAgICAgICAgICAgICAgICAgICRzZWxmLmRhdGEoJ29sZFZhbHVlJywgdmFsdWUpO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBIZWxwZXIgZnVuY3Rpb24gdGhhdCByZXN0b3JlcyB0aGUgdmFsdWVzXG4gICAgICAgICAqIHN0b3JlZCBieSB0aGUgX3VwZGF0ZURhdGFWYWx1ZXMgZnVuY3Rpb25cbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICBkYXRhc2V0ICAgICBUaGUgZGF0YSBvYmplY3Qgb2YgYWxsIHRhcmdldHMgdGhhdCBuZWVkcyB0byBiZSByZXNldFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9yZXN0b3JlRGF0YVZhbHVlcyA9IGZ1bmN0aW9uIChkYXRhc2V0KSB7XG4gICAgICAgICAgICAvLyBSZXNldCBlYWNoIGNoYW5nZWQgZmllbGQgZ2l2ZW5cbiAgICAgICAgICAgIC8vIGJ5IHRoZSBkYXRhc2V0IHRhcmdldFxuICAgICAgICAgICAgJC5lYWNoKGRhdGFzZXQsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICB2YXIgdmFsdWUgPSB0aGlzO1xuXG4gICAgICAgICAgICAgICAgdmFsdWVcbiAgICAgICAgICAgICAgICAgICAgLnRhcmdldFxuICAgICAgICAgICAgICAgICAgICAuZmluZCgnLicgKyBvcHRpb25zLmNoYW5nZUNsYXNzKVxuICAgICAgICAgICAgICAgICAgICAuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgJHNlbGYgPSAkKHRoaXMpLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG5hbWUgPSAkc2VsZi5hdHRyKCduYW1lJykucmVwbGFjZSgnW10nLCAnJyksXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFsID0gJHNlbGYuZGF0YSgpLm9sZFZhbHVlO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YWx1ZVtuYW1lXVswXSA9IHZhbDtcbiAgICAgICAgICAgICAgICAgICAgICAgICRzZWxmXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLnZhbCh2YWwpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLnJlbW92ZUNsYXNzKG9wdGlvbnMuY2hhbmdlQ2xhc3MpO1xuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBIZWxwZXIgZnVuY3Rpb24gdGhhdCBnZW5lcmF0ZXMgYW4gYXJyYXkgb2YgIGRhdGFzZXRzIGZyb20gdGhlIGZvcm0uIEVhY2ggYXJyYXkgaXRlbVxuICAgICAgICAgKiBjb250YWlucyB0aGUgZGF0YSBvZiBvbmUgcm93IChpbmNsdXNpdmUgdGhlIGF0dHJpYnV0ZXMgZGF0YSBmcm9tIHRoZSBmb3JtIGhlYWQgYmVsb25naW5nXG4gICAgICAgICAqIHRvIHRoZSByb3cpLiBBZGRpdGlvbmFsbHkgaXQgYWRkcyB0aGUgdGFyZ2V0LXBhcmFtZXRlciB0byBlYWNoIGRhdGFzZXQgd2hpY2ggY29udGFpbnNcbiAgICAgICAgICogdGhlIHNlbGVjdGlvbiBvZiB0aGUgcm93LHRoZSBjdXJyZW50IGRhdGFzZXQgYmVsb25ncyB0by5cbiAgICAgICAgICpcbiAgICAgICAgICogQHBhcmFtIHtvYmplY3R9ICRyb3cgVGhlIG9wdGlvbmFsIHJvdyBzZWxlY3Rpb24gdGhlIGRhdGEgZ2V0cyBmcm9tLiBJZiBubyBzZWxlY3Rpb24gaXMgZ2l2ZW4sIHRoZSBmb3JtXG4gICAgICAgICAqIGdldHMgc2VsZWN0ZWQuXG4gICAgICAgICAqIEByZXR1cm4ge0FycmF5fSBUaGUgYXJyYXkgd2l0aCB0aGUgZGF0YXNldHMgb2YgZWFjaCByb3dcbiAgICAgICAgICpcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfZ2VuZXJhdGVGb3JtZGF0YU9iamVjdCA9IGZ1bmN0aW9uICgkcm93KSB7XG4gICAgICAgICAgICB2YXIgJHRhcmdldCA9ICgkcm93ICYmICRyb3cubGVuZ3RoKSA/ICRyb3cgOiAkZm9ybSxcbiAgICAgICAgICAgICAgICAkcm93cyA9ICgkcm93ICYmICRyb3cubGVuZ3RoKSA/ICRyb3cgOiAkZm9ybS5maW5kKCcub3JkZXItd2lzaGxpc3QgLml0ZW06Z3QoMCknKSxcbiAgICAgICAgICAgICAgICAkaGlkZGVucyA9ICRmb3JtLmZpbmQoJy5oaWRkZW4tb3B0aW9ucyBpbnB1dFt0eXBlPVwiaGlkZGVuXCJdJyksXG4gICAgICAgICAgICAgICAgZGF0YXNldCA9IGpzZS5saWJzLmZvcm0uZ2V0RGF0YSgkdGFyZ2V0KSxcbiAgICAgICAgICAgICAgICByZXN1bHQgPSBbXSxcbiAgICAgICAgICAgICAgICB0bXBSZXN1bHQgPSBudWxsO1xuXG4gICAgICAgICAgICAkLmVhY2goZGF0YXNldC5wcm9kdWN0c19pZCwgZnVuY3Rpb24gKGksIHYpIHtcbiAgICAgICAgICAgICAgICB0bXBSZXN1bHQgPSB7fTtcbiAgICAgICAgICAgICAgICB0bXBSZXN1bHQudGFyZ2V0ID0gJHJvd3MuZXEoaSk7XG5cbiAgICAgICAgICAgICAgICAvLyBTdG9yZSB0aGUgZGF0YSBmcm9tIHRoZSBjdXJyZW50IHJvdyBhcyBhIGpzb25cbiAgICAgICAgICAgICAgICAkLmVhY2goZGF0YXNldCwgZnVuY3Rpb24gKGtleSwgdmFsdWUpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKHR5cGVvZiB2YWx1ZSA9PT0gJ29iamVjdCcgJiYgdmFsdWVbaV0gIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgLy8gU3RvcmUgdGhlIHZhbHVlIGFzIGFuIGFycmF5IHRvIGJlIGNvbXBsaWFudCB3aXRoIHRoZSBvbGQgQVBJXG4gICAgICAgICAgICAgICAgICAgICAgICB0bXBSZXN1bHRba2V5XSA9IFt2YWx1ZVtpXV07XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIC8vIEdldCB0aGUgaGlkZGVuIGZpZWxkcyBmb3IgdGhlIGF0dHJpYnV0ZXNcbiAgICAgICAgICAgICAgICAvLyBiZWxvbmdpbmcgdG8gdGhpcyByb3cgZnJvbSB0aGUgZm9ybSBoZWFkXG4gICAgICAgICAgICAgICAgJGhpZGRlbnNcbiAgICAgICAgICAgICAgICAgICAgLmZpbHRlcignW25hbWVePVwiaWRbJyArIHYgKyAnXCJdLCAuZm9yY2UnKVxuICAgICAgICAgICAgICAgICAgICAuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgJHNlbGYgPSAkKHRoaXMpLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG5hbWUgPSAkc2VsZi5hdHRyKCduYW1lJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHRtcFJlc3VsdFtuYW1lXSA9ICRzZWxmLnZhbCgpO1xuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIC8vIFB1c2ggdGhlIGdlbmVyYXRlZCBqc29uIHRvIHRoZSBmaW5hbCByZXN1bHQgYXJyYXlcbiAgICAgICAgICAgICAgICByZXN1bHQucHVzaCh0bXBSZXN1bHQpO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIHJldHVybiByZXN1bHQ7XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEZ1bmN0aW9uIHRoYXQgY2hlY2tzIHRoZSBmb3JtIC8gdGhlIHJvdyBpZiB0aGUgY29tYmluYXRpb25cbiAgICAgICAgICogYW5kIHF1YW50aXR5IGlzIHZhbGlkLiBJdCByZXR1cm5zIGFuIHByb21pc2Ugd2hpY2ggZ2V0cyByZWplY3RlZFxuICAgICAgICAgKiBpZiBpbiB0aGUgc2NvcGUgd2FzIGFuIGludmFsaWQgdmFsdWUuIEluIG90aGVyIGNhc2VzIGl0IGdldHNcbiAgICAgICAgICogcmVzb2x2ZWQuIElmIGl0IGlzIGRldGVjdGluZyBjaGFuZ2VzIGluc2lkZSB0aGUgZm9ybSBpdCBjYW5cbiAgICAgICAgICogc2hvdyBhbiBpbmZvIGxheWVyIHRvIHRoZSB1c2VyIGFuZCAvIG9yIHJldmVydCB0aGUgY2hhbmdlc1xuICAgICAgICAgKiAoZGVwZW5kaW5nIG9uIHRoZSBjYWxsZXIgcGFyYW1ldGVycylcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtib29sZWFufSAgICAgICBzaG93Q2hhbmdlcyAgICAgICAgIFNob3cgYW4gaW5mby1sYXllciBpZiBjaGFuZ2VzIHdvdWxkIGJlIHJlZnVzZWRcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtib29sZWFufSAgICAgICByZXZlcnRDaGFuZ2VzICAgICAgIFJlc2V0cyB0aGUgZm9ybSB2YWx1ZXMgd2l0aCB0aGUgb25lIGZyb20gdGhlIGRhdGEgYXR0cmlidXRlcyBpZiB0cnVlXG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgZm9ybWRhdGEgICAgICAgICAgICBKc29uIHRoYXQgY29udGFpbnMgdGhlIGRhdGEgdG8gY2hlY2tcbiAgICAgICAgICogQHJldHVybiAgICAgeyp9ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgUmV0dXJucyBhIHByb21pc2VcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfY2hlY2tGb3JtID0gZnVuY3Rpb24gKHNob3dDaGFuZ2VzLCByZXZlcnRDaGFuZ2VzLCBmb3JtZGF0YSkge1xuXG4gICAgICAgICAgICB2YXIgcHJvbWlzZXMgPSBbXSxcbiAgICAgICAgICAgICAgICBoYXNDaGFuZ2VkID0gZmFsc2U7XG5cbiAgICAgICAgICAgIC8vIEdldCB0aGUgY29tcGxldGUgZm9ybSBkYXRhIGlmIG5vIHJvdyBkYXRhIGlzIGdpdmVuXG4gICAgICAgICAgICBmb3JtZGF0YSA9IGZvcm1kYXRhIHx8IF9nZW5lcmF0ZUZvcm1kYXRhT2JqZWN0KCk7XG5cbiAgICAgICAgICAgIC8vIENoZWNrIHRoZSBmb3JtZGF0YSBmb3IgY2hhbmdlZCB2YWx1ZXNcbiAgICAgICAgICAgICQuZWFjaChmb3JtZGF0YSwgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIHZhciAkY2hhbmdlZCA9IHRoaXMudGFyZ2V0LmZpbmQoJy4nICsgb3B0aW9ucy5jaGFuZ2VDbGFzcyk7XG4gICAgICAgICAgICAgICAgaGFzQ2hhbmdlZCA9IGhhc0NoYW5nZWQgfHwgISEkY2hhbmdlZC5sZW5ndGg7XG4gICAgICAgICAgICAgICAgcmV0dXJuICFoYXNDaGFuZ2VkO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIHJldHVybiAkLndoZW4uYXBwbHkodW5kZWZpbmVkLCBwcm9taXNlcykucHJvbWlzZSgpO1xuXG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEhlbHBlciBmdW5jdGlvbiB0aGF0IGNsZWFucyB1cCB0aGUgcHJvY2VzcyBzdGF0ZVxuICAgICAgICAgKiAoTmVlZGVkIGVzcGVjaWFsbHkgYWZ0ZXIgYWpheCByZXF1ZXN0cywgdG8gYmUgYWJsZVxuICAgICAgICAgKiB0byBtYWtlIGZ1cnRoZXIgcmVxdWVzdHMpXG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7c3RyaW5nfSAgICAgICAgaWQgICAgICAgICAgICAgIFRoZSBwcm9kdWN0IGlkIHRoYXQgbmVlZHMgdG8gYmUgcmVzZXRlZFxuICAgICAgICAgKiBAcmV0dXJuICAgICB7QXJyYXkuPFQ+fSAgICAgICAgICAgICAgICAgICAgIFJldHVybnMgYW4gYXJyYXkgd2l0aG91dCBlbXB0eSBmaWVsZHNcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfY2xlYW51cEFycmF5ID0gZnVuY3Rpb24gKGlkLCAkcm93KSB7XG4gICAgICAgICAgICBkZWxldGUgYWN0aXZlWydwcm9kdWN0XycgKyBpZF07XG4gICAgICAgICAgICAkcm93LnJlbW92ZUNsYXNzKCdsb2FkaW5nJyk7XG4gICAgICAgICAgICByZXR1cm4gYWN0aXZlO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBIZWxwZXIgZnVuY3Rpb24gdGhhdCBkb2VzIHRoZSBnZW5lcmFsIGZvcm0gdXBkYXRlXG4gICAgICAgICAqIGFmdGVyIGFuIGFqYXggcmVxdWVzdFxuICAgICAgICAgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgJHRhcmdldCAgICAgICAgIFRoZSBqUXVlcnkgc2VsZWN0aW9uIG9mIHRoZSB0YXJnZXQgZWxlbWVudHMuXG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICByZXN1bHQgICAgICAgICAgVGhlIHJlc3VsdCBvZiB0aGUgYWpheCByZXF1ZXN0LlxuICAgICAgICAgKiBAcGFyYW0gICAgICAge3N0cmluZ30gICAgdHlwZSAgICAgICAgICAgIFRoZSBleGVjdXRlZCBhY3Rpb24gdHlwZS5cbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfdXBkYXRlRm9ybSA9IGZ1bmN0aW9uICgkdGFyZ2V0LCByZXN1bHQsIHR5cGUpIHtcbiAgICAgICAgICAgIC8vIFVwZGF0ZSB0aGUgcmVzdCBvZiB0aGUgcGFnZVxuICAgICAgICAgICAganNlLmxpYnMudGhlbWUuaGVscGVycy5maWxsKHJlc3VsdC5jb250ZW50LCAkYm9keSwgb3B0aW9ucy5zZWxlY3Rvck1hcHBpbmcpO1xuXG4gICAgICAgICAgICAvLyBUb2dnbGUgaW5mby1tZXNzYWdlcyB2aXNpYmlsaXR5LlxuICAgICAgICAgICAgJCgnLmluZm8tbWVzc2FnZScpLnRvZ2dsZUNsYXNzKCdoaWRkZW4nLCAkKCcuaW5mby1tZXNzYWdlJykudGV4dCgpID09PSAnJyk7XG5cbiAgICAgICAgICAgIC8vIEluZm9ybSBvdGhlciB3aWRnZXRzIGFib3V0IHRoZSB1cGRhdGVcbiAgICAgICAgICAgICR1cGRhdGVUYXJnZXQudHJpZ2dlcihqc2UubGlicy50aGVtZS5ldmVudHMuQ0FSVF9VUERBVEVEKCksIFtdKTtcbiAgICAgICAgICAgICRib2R5LnRyaWdnZXIoanNlLmxpYnMudGhlbWUuZXZlbnRzLkNBUlRfVVBEQVRFKCksICh0eXBlID09PSAnYWRkJykpO1xuXG4gICAgICAgICAgICAvLyBVcGRhdGUgdGhlIGhpZGRlbiBkYXRhIGF0dHJpYnV0ZXMgb2YgdGhhdCByb3dcbiAgICAgICAgICAgIF91cGRhdGVEYXRhVmFsdWVzKCR0YXJnZXQpO1xuXG4gICAgICAgICAgICBpZiAoJC5pc0VtcHR5T2JqZWN0KHJlc3VsdC5wcm9kdWN0cykpIHtcbiAgICAgICAgICAgICAgICAvLyBIaWRlIHRoZSB0YWJsZSBpZiBubyBwcm9kdWN0cyBhcmUgYXQgdGhlIGxpc3RcbiAgICAgICAgICAgICAgICAkY2FydE5vdEVtcHR5LmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAgICAgICAgICAgICAkY2FydEVtcHR5LnJlbW92ZUNsYXNzKCdoaWRkZW4nKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgLy8gU2hvdyB0aGUgdGFibGUgaWYgdGhlcmUgYXJlIHByb2R1Y3RzIGF0IGl0XG4gICAgICAgICAgICAgICAgJGNhcnRFbXB0eS5hZGRDbGFzcygnaGlkZGVuJyk7XG4gICAgICAgICAgICAgICAgJGNhcnROb3RFbXB0eS5yZW1vdmVDbGFzcygnaGlkZGVuJyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vIHJlaW5pdGlhbGl6ZSB3aWRnZXRzIGluIHVwZGF0ZWQgRE9NXG4gICAgICAgICAgICB3aW5kb3cuZ2FtYmlvLndpZGdldHMuaW5pdCgkdGhpcyk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEhlbHBlciBmdW5jdGlvbiB0byB1cGRhdGUgcHJvZHVjdCBzcGVjaWZpYyBtZXNzYWdlcy5cbiAgICAgICAgICogVGhlcmVmb3IgaXQgY2FsbHMgQUpBWC1yZXF1ZXN0cyAoaW4gY2FzZSBhamF4IGlzXG4gICAgICAgICAqIGVuYWJsZWQpIHRvIHRoZSBzZXJ2ZXIgdG8gZ2V0IHRoZSB1cGRhdGVkIGluZm9ybWF0aW9uXG4gICAgICAgICAqIGFib3V0IHRoZSB0YWJsZSBzdGF0ZS4gSWYgYWpheCBpc24ndCBlbmFibGVkLCBpdCBzaW1wbHlcbiAgICAgICAgICogc3VibWl0cyB0aGUgZm9ybS5cbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICBtZXNzYWdlcyAgICAgICAgICAgIE9iamVjdCB3aXRoIGFydGljbGVJZCA9PiBtZXNzYWdlIGNvbmZpZ3VyYXRpb24gb2JqZWN0XG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3VwZGF0ZUFydGljbGVzTWVzc2FnZSA9IGZ1bmN0aW9uIChtZXNzYWdlcykge1xuXG4gICAgICAgICAgICAvL3VwZGF0ZSBhbGwgbWVzc2FnZXMgb2cgdGhlIHJlcXVlc3RcbiAgICAgICAgICAgICQuZWFjaChtZXNzYWdlcywgZnVuY3Rpb24gKHByb2R1Y3QsIG1lc3NhZ2UpIHtcbiAgICAgICAgICAgICAgICBsZXQgbWVzc2FnZUNmZyA9IHsnbWVzc2FnZSc6IG1lc3NhZ2V9O1xuICAgICAgICAgICAgICAgIC8vbGV0IHByb2R1Y3RJZCA9IHByb2R1Y3QubWF0Y2goL1xcZCsvKVswXTtcbiAgICAgICAgICAgICAgICAvLyBGaW5kIHRoZSByZWxhdGVkIHByb2R1Y3QgdHJcbiAgICAgICAgICAgICAgICBsZXQgaXRlbSA9ICQoJ2lucHV0W3ZhbHVlXj1cIicgKyBwcm9kdWN0ICsgJ1wiXScpLmNsb3Nlc3QoJ3RyJyk7XG5cbiAgICAgICAgICAgICAgICBqc2UubGlicy50aGVtZS5oZWxwZXJzLmZpbGwobWVzc2FnZUNmZywgaXRlbSwgb3B0aW9ucy5zZWxlY3Rvck1hcHBpbmcpO1xuICAgICAgICAgICAgICAgIGlmIChpdGVtLmZpbmQoJy5lcnJvci1tc2cnKS50ZXh0KCkgIT09ICcnKSB7XG4gICAgICAgICAgICAgICAgICAgIGl0ZW0uZmluZCgnLmVycm9yLW1zZycpLnNob3coKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICB9XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEhlbHBlciBmdW5jdGlvbiB0aGF0IHByb2Nlc3NlcyB0aGUgbGlzdCB1cGRhdGVzLlxuICAgICAgICAgKiBUaGVyZWZvciBpdCBjYWxscyBBSkFYLXJlcXVlc3RzIChpbiBjYXNlIGFqYXggaXNcbiAgICAgICAgICogZW5hYmxlZCkgdG8gdGhlIHNlcnZlciB0byBnZXQgdGhlIHVwZGF0ZWQgaW5mb3JtYXRpb25cbiAgICAgICAgICogYWJvdXQgdGhlIHRhYmxlIHN0YXRlLiBJZiBhamF4IGlzbid0IGVuYWJsZWQsIGl0IHNpbXBseVxuICAgICAgICAgKiBzdWJtaXRzIHRoZSBmb3JtLlxuICAgICAgICAgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgICAgICR0YXJnZXQgICAgICAgICAgICBUaGUgalF1ZXJ5IHNlbGVjdGlvbiBvZiB0aGUgcm93IHRoYXQgZ2V0cyB1cGRhdGVkXG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgZGF0YXNldCAgICAgICAgICAgIFRoZSBkYXRhIGNvbGxlY3RlZCBmcm9tIHRoZSB0YXJnZXQgcm93IGluIEpTT04gZm9ybWF0XG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7YXJ0aWNsZX0gICAgICAgYXJ0aWNsZSAgICAgICAgICAgIFRoZSBwcm9kdWN0cyBpZCBvZiB0aGUgYXJ0aWNsZSBpbiB0aGF0IHJvd1xuICAgICAgICAgKiBAcGFyYW0gICAgICAge2FydGljbGV9ICAgICAgIHR5cGUgICAgICAgICAgICAgICBUaGUgb3BlcmF0aW9uIHR5cGUgY2FuIGVpdGhlciBiZSBcImFkZFwiLCBcImRlbGV0ZVwiIG9yIFwicmVmcmVzaFwiLlxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9leGVjdXRlQWN0aW9uID0gZnVuY3Rpb24gKCRyb3csICR0YXJnZXQsIGRhdGFzZXQsIGFydGljbGUsIHR5cGUpIHtcbiAgICAgICAgICAgIGlmIChvcHRpb25zLmFqYXgpIHtcbiAgICAgICAgICAgICAgICAvLyBEZWxldGUgdGhlIHRhcmdldCBlbGVtZW50IGJlY2F1c2UgYWpheCByZXF1ZXN0c1xuICAgICAgICAgICAgICAgIC8vIHdpbGwgZmFpbCB3aXRoIGEgalF1ZXJ5IHNlbGVjdGlvbiBpbiB0aGUgZGF0YSBqc29uXG4gICAgICAgICAgICAgICAgZGVsZXRlIGRhdGFzZXQudGFyZ2V0O1xuXG4gICAgICAgICAgICAgICAgJHJvdy50cmlnZ2VyKGpzZS5saWJzLnRoZW1lLmV2ZW50cy5UUkFOU0lUSU9OKCksIHRyYW5zaXRpb24pO1xuXG4gICAgICAgICAgICAgICAgLy8gUGVyZm9ybSBhbiBhamF4IGlmIHRoZSBkYXRhIGlzIHZhbGlkIGFuZCB0aGUgb3B0aW9ucyBmb3IgYWpheCBpcyBzZXRcbiAgICAgICAgICAgICAgICBqc2UubGlicy54aHIuYWpheCh7dXJsOiBhY3Rpb24sIGRhdGE6IGRhdGFzZXR9LCB0cnVlKS5kb25lKGZ1bmN0aW9uIChyZXN1bHQpIHtcbiAgICAgICAgICAgICAgICAgICAgLy8gUGVyZm9ybSBob29rc1xuICAgICAgICAgICAgICAgICAgICBqc2UubGlicy5ob29rcy5leGVjdXRlKGpzZS5saWJzLmhvb2tzLmtleXMuc2hvcC5jYXJ0LmNoYW5nZSwge1xuICAgICAgICAgICAgICAgICAgICAgICAgJHRhcmdldCxcbiAgICAgICAgICAgICAgICAgICAgICAgIGRhdGFzZXQsXG4gICAgICAgICAgICAgICAgICAgICAgICBhcnRpY2xlLFxuICAgICAgICAgICAgICAgICAgICAgICAgdHlwZSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdCxcbiAgICAgICAgICAgICAgICAgICAgfSwgNTAwKTtcblxuICAgICAgICAgICAgICAgICAgICAvLyBVcGRhdGUgdGhlIHByb2R1Y3Qgcm93XG4gICAgICAgICAgICAgICAgICAgIHZhciAkbWFya3VwID0gJChyZXN1bHQucHJvZHVjdHNbJ3Byb2R1Y3RfJyArIGFydGljbGVdIHx8ICcnKTtcblxuICAgICAgICAgICAgICAgICAgICAvLyBUb2dnbGUgZXJyb3ItbWVzc2FnZXMgdmlzaWJpbGl0eS5cbiAgICAgICAgICAgICAgICAgICAgJG1hcmt1cC5yZW1vdmVDbGFzcyhvcHRpb25zLmNsYXNzTG9hZGluZyk7XG4gICAgICAgICAgICAgICAgICAgICR0YXJnZXQucmVwbGFjZVdpdGgoJG1hcmt1cCk7XG5cbiAgICAgICAgICAgICAgICAgICAgX3VwZGF0ZUFydGljbGVzTWVzc2FnZShyZXN1bHQuY29udGVudC5lcnJvck1lc3NhZ2VMaXN0KTtcbiAgICAgICAgICAgICAgICAgICAgZGVsZXRlIHJlc3VsdC5jb250ZW50LmVycm9yTWVzc2FnZUxpc3Q7XG5cbiAgICAgICAgICAgICAgICAgICAgX3VwZGF0ZUZvcm0oJHRhcmdldCwgcmVzdWx0LCB0eXBlKTtcblxuICAgICAgICAgICAgICAgICAgICB2YXIgcHJvZHVjdE51bWJlciA9IGFydGljbGUubWF0Y2goL1xcZCsvKVswXTtcblxuICAgICAgICAgICAgICAgICAgICAvLyBGaW5kIGFsbCBpdGVtcyB3aXRoIHRoZSBzYW1lIHByb2R1Y3QgbnVtYmVyXG4gICAgICAgICAgICAgICAgICAgIHZhciAkaXRlbXMgPSAkKCdpbnB1dFt2YWx1ZV49XCInICsgcHJvZHVjdE51bWJlciArICdcIl0nKS5wYXJlbnQoJ3RkJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy8gQXBwbHkgdGhlIG5ldyBtYXJrdXAgZm9yZWFjaCBpdGVtIHdoaWNoIGhhcyB0aGUgc2FtZSBwcm9kdWN0IG51bWJlci5cbiAgICAgICAgICAgICAgICAgICAgJGl0ZW1zLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKCEkKHRoaXMpLmZpbmQoJ2lucHV0W3ZhbHVlPVwiJyArIGFydGljbGUgKyAnXCJdJykubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIG51bWJlciA9ICQodGhpcykuZmluZCgnaW5wdXRbaWQ9XCJwcm9kdWN0c19pZFtdXCJdJykuYXR0cigndmFsdWUnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkbWFya3VwID0gJChyZXN1bHQucHJvZHVjdHNbJ3Byb2R1Y3RfJyArIG51bWJlcl0gfHwgJycpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICR0YXJnZXQgPSAkKHRoaXMpLnBhcmVudCgndHInKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkdGFyZ2V0LnJlcGxhY2VXaXRoKCRtYXJrdXApO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIH0pLmFsd2F5cyhmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgIF9jbGVhbnVwQXJyYXkoYXJ0aWNsZSwgJHJvdyk7XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIC8vIENsZWFudXAgdGhlIGFjdGl2ZSBhcnJheSBvbiBmYWlsIC8gc3VjY2Vzc1xuICAgICAgICAgICAgICAgIC8vIG9mIHRoZSBmb2xsb3dpbmcgc3VibWl0LiBUaGlzIGlzIGEgZmFsbGJhY2tcbiAgICAgICAgICAgICAgICAvLyBpZiBhbiBvdGhlciBjb21wb25lbnQgd291bGQgcHJldmVudCB0aGUgc3VibWl0XG4gICAgICAgICAgICAgICAgLy8gaW4gc29tZSBjYXNlcywgc28gdGhhdCB0aGlzIHNjcmlwdCBjYW4gcGVyZm9ybVxuICAgICAgICAgICAgICAgIC8vIGFjdGlvbnMgYWdhaW5cbiAgICAgICAgICAgICAgICB2YXIgZGVmZXJyZWQgPSAkLkRlZmVycmVkKCk7XG4gICAgICAgICAgICAgICAgZGVmZXJyZWQuYWx3YXlzKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgX2NsZWFudXBBcnJheShhcnRpY2xlLCAkcm93KTtcbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIC8vIFN1Ym1pdCB0aGUgZm9ybVxuICAgICAgICAgICAgICAgICRmb3JtLnRyaWdnZXIoJ3N1Ym1pdCcsIGRlZmVycmVkKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuXG4vLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBBZGRzIGFuIGNsYXNzIHRvIHRoZSBjaGFuZ2VkIGlucHV0XG4gICAgICAgICAqIGZpZWxkLCBzbyB0aGF0IGl0J3Mgc3R5bGluZyBzaG93c1xuICAgICAgICAgKiB0aGF0IGl0IHdhc24ndCByZWZyZXNoZWQgdGlsbCBub3dcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfaW5wdXRIYW5kbGVyID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgdmFyICRzZWxmID0gJCh0aGlzKSxcbiAgICAgICAgICAgICAgICB2YWx1ZSA9ICRzZWxmLnZhbCgpLFxuICAgICAgICAgICAgICAgIG9sZFZhbHVlID0gJHNlbGYuZGF0YSgpLm9sZFZhbHVlLFxuICAgICAgICAgICAgICAgIGhhc05ld1ZhbHVlID0gdmFsdWUgIT09IG9sZFZhbHVlO1xuXG4gICAgICAgICAgICBpZiAoaGFzTmV3VmFsdWUpIHtcbiAgICAgICAgICAgICAgICBpc0NoYW5nZWQgPSBoYXNOZXdWYWx1ZTtcbiAgICAgICAgICAgICAgICAkc2VsZi5hZGRDbGFzcyhvcHRpb25zLmNoYW5nZUNsYXNzKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgJHNlbGYucmVtb3ZlQ2xhc3Mob3B0aW9ucy5jaGFuZ2VDbGFzcyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIF91cGRhdGVDaGFuZ2VTdGF0ZSgpO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBIYW5kbGUgdGhlIGJsdXIgZXZlbnRcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfYmx1ckhhbmRsZXIgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB2YXIgJHNlbGYgPSAkKHRoaXMpLFxuICAgICAgICAgICAgICAgIHZhbHVlID0gJHNlbGYudmFsKCksXG4gICAgICAgICAgICAgICAgb2xkVmFsdWUgPSAkc2VsZi5kYXRhKCkub2xkVmFsdWUsXG4gICAgICAgICAgICAgICAgaGFzTmV3VmFsdWUgPSB2YWx1ZSAhPT0gb2xkVmFsdWU7XG5cbiAgICAgICAgICAgIGlmIChoYXNOZXdWYWx1ZSkge1xuICAgICAgICAgICAgICAgICRzZWxmXG4gICAgICAgICAgICAgICAgICAgIC5jbG9zZXN0KCcuaXRlbScpXG4gICAgICAgICAgICAgICAgICAgIC5maW5kKCcuYnV0dG9uLXJlZnJlc2gnKVxuICAgICAgICAgICAgICAgICAgICAuZmlyc3QoKVxuICAgICAgICAgICAgICAgICAgICAudHJpZ2dlcignY2xpY2snKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGFuZGxlciB0aGF0IGxpc3RlbnMgb24gY2xpY2sgZXZlbnRzIG9uIHRoZVxuICAgICAgICAgKiBidXR0b25zIFwicmVmcmVzaFwiLCBcImRlbGV0ZVwiICYgXCJhZGQgdG8gY2FydFwiLlxuICAgICAgICAgKiBJdCB2YWxpZGF0ZXMgdGhlIGZvcm0gLyByb3cgYW5kIHBhc3NlcyB0aGVcbiAgICAgICAgICogdGhlIGRhdGEgdG8gYW4gc3VibWl0IGV4ZWN1dGUgZnVuY2l0b24gaWYgdmFsaWRcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9jbGlja0hhbmRsZXIgPSBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcblxuICAgICAgICAgICAgdmFyICRzZWxmID0gJCh0aGlzKSxcbiAgICAgICAgICAgICAgICAkcm93ID0gJHNlbGYuY2xvc2VzdCgnLml0ZW0nKSxcbiAgICAgICAgICAgICAgICB0eXBlID0gZS5kYXRhLnR5cGUsXG4gICAgICAgICAgICAgICAgcm93ZGF0YSA9IF9nZW5lcmF0ZUZvcm1kYXRhT2JqZWN0KCRyb3cpWzBdLFxuICAgICAgICAgICAgICAgIGFydGljbGUgPSByb3dkYXRhLnByb2R1Y3RzX2lkWzBdLFxuICAgICAgICAgICAgICAgICR0YXJnZXQgPSByb3dkYXRhLnRhcmdldCxcbiAgICAgICAgICAgICAgICB0aXRsZSA9ICR0YXJnZXQuZmluZCgnLnByb2R1Y3QtdGl0bGUnKS50ZXh0KCk7XG5cbiAgICAgICAgICAgIC8vIEFkZCBsb2FkaW5nIGNsYXNzXG4gICAgICAgICAgICAkcm93LmFkZENsYXNzKCdsb2FkaW5nJyk7XG5cbiAgICAgICAgICAgIC8vIENoZWNrIGlmIHRoZXJlIGlzIG5vIGN1cnJlbnQgcHJvY2VzcyBmb3IgdGhpcyBhcnRpY2xlXG4gICAgICAgICAgICAvLyBvciBpbiBjYXNlIGl0J3Mgbm8gYWpheCBjYWxsIHRoZXJlIGlzIE5PIG90aGVyIHByb2Nlc3NcbiAgICAgICAgICAgIGlmICgkLmlzRW1wdHlPYmplY3QoYWN0aXZlKSB8fCAob3B0aW9ucy5hamF4ICYmICFhY3RpdmVbJ3Byb2R1Y3RfJyArIGFydGljbGVdKSkge1xuICAgICAgICAgICAgICAgIGFjdGl2ZVsncHJvZHVjdF8nICsgYXJ0aWNsZV0gPSB0cnVlO1xuICAgICAgICAgICAgICAgIF9zZXRBY3Rpb24odHlwZSk7XG5cbiAgICAgICAgICAgICAgICBzd2l0Y2ggKHR5cGUpIHtcbiAgICAgICAgICAgICAgICAgICAgY2FzZSAnZGVsZXRlJzpcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIFVwZGF0ZSB0aGUgZm9ybSBhbmQgdGhlIGRhdGFzZXQgd2l0aFxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gdGhlIGFydGljbGUgaWQgdG8gZGVsZXRlXG4gICAgICAgICAgICAgICAgICAgICAgICAkZGVsZXRlRmllbGQudmFsKGFydGljbGUpO1xuICAgICAgICAgICAgICAgICAgICAgICAgcm93ZGF0YVtkZWxldGVGaWVsZE5hbWVdID0gW2FydGljbGVdO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAob3B0aW9ucy5jb25maXJtRGVsZXRlKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gT3BlbiBhIG1vZGFsIGxheWVyIHRvIGNvbmZpcm0gdGhlIGRlbGV0aW9uXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIG1vZGFsVGl0bGUgPSBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnQ0FSVF9XSVNITElTVF9ERUxFVEVfVElUTEUnLCAnZ2VuZXJhbCcpLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBtb2RhbE1lc3NhZ2UgPSBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnQ0FSVF9XSVNITElTVF9ERUxFVEUnLCAnZ2VuZXJhbCcpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAganNlLmxpYnMudGhlbWUubW9kYWwuY29uZmlybSh7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNvbnRlbnQ6IG1vZGFsTWVzc2FnZSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdGl0bGU6IG1vZGFsVGl0bGVcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KS5kb25lKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIGRlZmVycmVkID0gJC5EZWZlcnJlZCgpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRlZmVycmVkLmRvbmUoZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgX2V4ZWN1dGVBY3Rpb24oJHJvdywgJHRhcmdldCwgcm93ZGF0YSwgYXJ0aWNsZSwgdHlwZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICRib2R5LnRyaWdnZXIoanNlLmxpYnMudGhlbWUuZXZlbnRzLldJU0hMSVNUX0NBUlRfREVMRVRFKCksIFtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnZGVmZXJyZWQnOiBkZWZlcnJlZCxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnZGF0YXNldCc6IHJvd2RhdGFcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgXSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSkuZmFpbChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIF9jbGVhbnVwQXJyYXkoYXJ0aWNsZSwgJHJvdyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhciBkZWZlcnJlZCA9ICQuRGVmZXJyZWQoKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRlZmVycmVkLmRvbmUoZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBfZXhlY3V0ZUFjdGlvbigkcm93LCAkdGFyZ2V0LCByb3dkYXRhLCBhcnRpY2xlLCB0eXBlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRib2R5LnRyaWdnZXIoanNlLmxpYnMudGhlbWUuZXZlbnRzLldJU0hMSVNUX0NBUlRfREVMRVRFKCksIFtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ2RlZmVycmVkJzogZGVmZXJyZWQsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnZGF0YXNldCc6IHJvd2RhdGFcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIF0pO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG5cbiAgICAgICAgICAgICAgICAgICAgZGVmYXVsdDpcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIEluIGFsbCBvdGhlciBjYXNlcyBjaGVjayBpZiB0aGUgZm9ybVxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gaGFzIHZhbGlkIHZhbHVlcyBhbmQgY29udGludWUgd2l0aCB0aGVcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIGRvbmUgY2FsbGJhY2sgaWYgdmFsaWRcbiAgICAgICAgICAgICAgICAgICAgICAgIF9jaGVja0Zvcm0oZmFsc2UsIGZhbHNlLCBbJC5leHRlbmQodHJ1ZSwge30sIHJvd2RhdGEpXSlcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuZG9uZShmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIEVtcHR5IHRoZSBkZWxldGUgaGlkZGVuIGZpZWxkIGluIGNhc2UgaXQgd2FzIHNldCBiZWZvcmVcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJGRlbGV0ZUZpZWxkLnZhbCgnJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIGV2ZW50ID0gbnVsbDtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAodHlwZSA9PT0gJ2FkZCcpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGV2ZW50ID0ganNlLmxpYnMudGhlbWUuZXZlbnRzLldJU0hMSVNUX1RPX0NBUlQoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmIChldmVudCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIGRlZmVycmVkID0gJC5EZWZlcnJlZCgpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBkZWZlcnJlZC5kb25lKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBfZXhlY3V0ZUFjdGlvbigkcm93LCAkdGFyZ2V0LCByb3dkYXRhLCBhcnRpY2xlLCB0eXBlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAkYm9keS50cmlnZ2VyKGV2ZW50LCBbeydkZWZlcnJlZCc6IGRlZmVycmVkLCAnZGF0YXNldCc6IHJvd2RhdGF9XSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBfZXhlY3V0ZUFjdGlvbigkcm93LCAkdGFyZ2V0LCByb3dkYXRhLCBhcnRpY2xlLCB0eXBlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSkuZmFpbChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgX2NsZWFudXBBcnJheShhcnRpY2xlLCAkcm93KTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBQcmV2ZW50IHRoZSBzdWJtaXQgZXZlbnQgdGhhdCB3YXMgdHJpZ2dlcmRcbiAgICAgICAgICogYnkgdXNlciBvciBieSBzY3JpcHQuIElmIGl0IHdhcyB0cmlnZ2VyZWRcbiAgICAgICAgICogYnkgdGhlIHVzZXIsIGNoZWNrIGlmIGl0IHdhcyBhbiBcIkVudGVyXCIta2V5XG4gICAgICAgICAqIHN1Ym1pdCBmcm9tIGFuIGlucHV0IGZpZWxkLiBJZiBzbywgZXhlY3V0ZVxuICAgICAgICAgKiB0aGUgcmVmcmVzaCBmdW5jdGlvbmFsaXR5IGZvciB0aGF0IHJvdy5cbiAgICAgICAgICogSWYgdGhlIGV2ZW50IHdhcyB0cmlnZ2VyZWQgYnkgdGhlIHNjcmlwdFxuICAgICAgICAgKiAoaWRlbnRpZmllZCBieSB0aGUgZGF0YSBmbGFnIFwiZFwiKSBjaGVjayB0aGVcbiAgICAgICAgICogd2hvbGUgZm9ybSBmb3IgZXJyb3JzLiBPbmx5IGluIGNhc2Ugb2YgdmFsaWRcbiAgICAgICAgICogZGF0YSBwcm9jZWVkIHRoZSBzdWJtaXRcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3RcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtib29sZWFufSAgICAgICBkICAgICAgIEEgZmxhZyB0aGF0IGlkZW50aWZpZXMgdGhhdCB0aGUgc3VibWl0IHdhcyB0cmlnZ2VyZWQgYnkgdGhpcyBzY3JpcHRcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfc3VibWl0SGFuZGxlciA9IGZ1bmN0aW9uIChlLCBkKSB7XG5cbiAgICAgICAgICAgIC8vIFByZXZlbnQgdGhlIGRlZmF1bHQgYmVoYXZpb3VyXG4gICAgICAgICAgICAvLyBpbiBib3RoIGNhc2VzXG4gICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICBlLnN0b3BQcm9wYWdhdGlvbigpO1xuXG4gICAgICAgICAgICBpZiAoIWQgJiYgZS5vcmlnaW5hbEV2ZW50KSB7XG5cbiAgICAgICAgICAgICAgICAvLyBDaGVjayBpZiBhbiBpbnB1dCBmaWVsZCBoYXMgdHJpZ2dlcmQgdGhlIHN1Ym1pdCBldmVudFxuICAgICAgICAgICAgICAgIC8vIGFuZCBjYWxsIHRoZSByZWZyZXNoIGhhbmRsZXJcbiAgICAgICAgICAgICAgICB2YXIgJHNvdXJjZSA9ICQoZS5vcmlnaW5hbEV2ZW50LmV4cGxpY2l0T3JpZ2luYWxUYXJnZXQpO1xuICAgICAgICAgICAgICAgIGlmICgkc291cmNlLmxlbmd0aCAmJiAkc291cmNlLmlzKCdpbnB1dFt0eXBlPVwidGV4dFwiXScpKSB7XG4gICAgICAgICAgICAgICAgICAgICRzb3VyY2VcbiAgICAgICAgICAgICAgICAgICAgICAgIC5jbG9zZXN0KCcuaXRlbScpXG4gICAgICAgICAgICAgICAgICAgICAgICAuZmluZCgnLmJ1dHRvbi1yZWZyZXNoJylcbiAgICAgICAgICAgICAgICAgICAgICAgIC5maXJzdCgpXG4gICAgICAgICAgICAgICAgICAgICAgICAudHJpZ2dlcignY2xpY2snKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH0gZWxzZSBpZiAoZCkge1xuXG4gICAgICAgICAgICAgICAgLy8gQ2hlY2sgdGhlIHdob2xlIGZvcm0gYW5kIG9ubHkgc3VibWl0XG4gICAgICAgICAgICAgICAgLy8gaXQgaWYgaXQncyB2YWxpZFxuICAgICAgICAgICAgICAgIF9jaGVja0Zvcm0oKS5kb25lKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgLy8gUmVtb3ZlIHRoZSBzdWJtaXQgZXZlbnQgaGFuZGxlclxuICAgICAgICAgICAgICAgICAgICAvLyBvbiBhIHN1Y2Nlc3NmdWwgdmFsaWRhdGlvbiBhbmRcbiAgICAgICAgICAgICAgICAgICAgLy8gdHJpZ2dlciBhIHN1Ym1pdCBhZ2Fpbiwgc28gdGhhdCB0aGVcbiAgICAgICAgICAgICAgICAgICAgLy8gYnJvd3NlciBleGVjdXRlcyBpdCdzIGRlZmF1bHQgYmVoYXZpb3JcbiAgICAgICAgICAgICAgICAgICAgJGZvcm1cbiAgICAgICAgICAgICAgICAgICAgICAgIC5vZmYoJ3N1Ym1pdCcpXG4gICAgICAgICAgICAgICAgICAgICAgICAudHJpZ2dlcignc3VibWl0Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy8gUmVzb2x2ZSB0aGUgZGVmZXJyZWQgaWYgZ2l2ZW5cbiAgICAgICAgICAgICAgICAgICAgaWYgKHR5cGVvZiBkID09PSAnb2JqZWN0Jykge1xuICAgICAgICAgICAgICAgICAgICAgICAgZC5yZXNvbHZlKCk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KS5mYWlsKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgLy8gUmVqZWN0IHRoZSBkZWZlcnJlZCBpZiBnaXZlblxuICAgICAgICAgICAgICAgICAgICBpZiAodHlwZW9mIGQgPT09ICdvYmplY3QnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBkLnJlamVjdCgpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogRXZlbnQgaGFuZGxlciBmb3IgY2xpY2tpbmcgb24gdGhlIHByb2NlZWRcbiAgICAgICAgICogYnV0dG9uIHRvIGdldCB0byB0aGUgY2hlY2tvdXQgcHJvY2Vzcy4gSXRcbiAgICAgICAgICogY2hlY2tzIGFsbCBpdGVtcyBhZ2FpbiBpZiB0aGV5IGNvbnRhaW4gdmFsaWRcbiAgICAgICAgICogZGF0YS4gT25seSBpZiBzbywgcHJvY2VlZFxuICAgICAgICAgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9zdWJtaXRCdXR0b25IYW5kbGVyID0gZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cbiAgICAgICAgICAgIGlmIChpc0NoYW5nZWQpIHtcbiAgICAgICAgICAgICAgICAvLyBHZXQgdGhlIGNvbXBsZXRlIGZvcm0gZGF0YSBpZiBubyByb3cgZGF0YSBpcyBnaXZlblxuICAgICAgICAgICAgICAgIHZhciBmb3JtZGF0YSA9IF9nZW5lcmF0ZUZvcm1kYXRhT2JqZWN0KCk7XG5cbiAgICAgICAgICAgICAgICAvLyBDaGVjayB0aGUgZm9ybWRhdGEgZm9yIGNoYW5nZWQgdmFsdWVzXG4gICAgICAgICAgICAgICAgJC5lYWNoKGZvcm1kYXRhLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgIHZhciAkY2hhbmdlZElucHV0ID0gdGhpcy50YXJnZXQuZmluZCgnLicgKyBvcHRpb25zLmNoYW5nZUNsYXNzKTtcblxuICAgICAgICAgICAgICAgICAgICBpZiAoJGNoYW5nZWRJbnB1dCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJGNoYW5nZWRJbnB1dFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5jbG9zZXN0KCcuaXRlbScpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmZpbmQoJy5idXR0b24tcmVmcmVzaCcpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmZpcnN0KClcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAudHJpZ2dlcignY2xpY2snKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgaXNDaGFuZ2VkID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgX3VwZGF0ZUNoYW5nZVN0YXRlKCk7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB2YXIgJHNlbGYgPSAkKHRoaXMpLFxuICAgICAgICAgICAgICAgIGRlc3RpbmF0aW9uID0gJHNlbGYuYXR0cignaHJlZicpO1xuXG4gICAgICAgICAgICAvLyBDaGVjayBpZiB0aGVyZSBpcyBhbnkgb3RoZXIgcHJvY2VzcyBydW5uaW5nXG4gICAgICAgICAgICBpZiAoJC5pc0VtcHR5T2JqZWN0KGFjdGl2ZSkgJiYgIWJ1c3kgJiYgIXVwZGF0ZUxpc3QpIHtcbiAgICAgICAgICAgICAgICBidXN5ID0gdHJ1ZTtcblxuICAgICAgICAgICAgICAgIF9jaGVja0Zvcm0odHJ1ZSwgdHJ1ZSkuZG9uZShmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgIGZ1bmN0aW9uIGNhbGxiYWNrKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgbG9jYXRpb24uaHJlZiA9IGRlc3RpbmF0aW9uO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAganNlLmxpYnMuaG9va3MuZXhlY3V0ZShqc2UubGlicy5ob29rcy5rZXlzLnNob3AuY2FydC5jaGVja291dCwge2V2ZW50OiBlfSwgNTAwKVxuICAgICAgICAgICAgICAgICAgICAgICAgLnRoZW4oY2FsbGJhY2spXG4gICAgICAgICAgICAgICAgICAgICAgICAuY2F0Y2goY2FsbGJhY2spO1xuICAgICAgICAgICAgICAgIH0pLmFsd2F5cyhmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgIGJ1c3kgPSBmYWxzZTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogRXZlbnQgaGFuZGxlciB0aGF0IGNoZWNrcyB0aGUgZm9ybSBhbmRcbiAgICAgICAgICogcmVzb2x2ZXMgb3IgcmVqZWN0cyB0aGUgZGVsaXZlcmVkIGRlZmVycmVkXG4gICAgICAgICAqIChVc2VkIGZvciBleHRlcm5hbCBwYXltZW50IG1vZHVsZXMgdG9cbiAgICAgICAgICogY2hlY2sgaWYgdGhlIGZvcm0gaXMgdmFsaWQpXG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICBlICAgICAgICAgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAgICAgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgZCAgICAgICAgICAgICAgIEpTT04gb2JqZWN0IHdpdGggdGhlIGV2ZW50IHNldHRpbmdzXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX2NoZWNrRm9ybUhhbmRsZXIgPSBmdW5jdGlvbiAoZSwgZCkge1xuICAgICAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcblxuICAgICAgICAgICAgZCA9IGQgfHwge307XG5cbiAgICAgICAgICAgIF9jaGVja0Zvcm0oZC5zaG93Q2hhbmdlcywgZC5yZXZlcnRDaGFuZ2VzKS5kb25lKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICBpZiAoZC5kZWZlcnJlZCkge1xuICAgICAgICAgICAgICAgICAgICBkLmRlZmVycmVkLnJlc29sdmUoKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KS5mYWlsKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICBpZiAoZC5kZWZlcnJlZCkge1xuICAgICAgICAgICAgICAgICAgICBkLmRlZmVycmVkLnJlamVjdCgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBGdW5jdGlvbiB0aGF0IHVwZGF0ZXMgdGhlIGxpc3Qgb24gZm9jdXMgb2ZcbiAgICAgICAgICogdGhlIHdpbmRvd1xuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF91cGRhdGVMaXN0ID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgdXBkYXRlTGlzdCA9IHRydWU7XG4gICAgICAgICAgICBqc2UubGlicy54aHIuYWpheCh7dXJsOiBvcHRpb25zLnVwZGF0ZVVybH0sIHRydWUpLmRvbmUoZnVuY3Rpb24gKHJlc3VsdCkge1xuICAgICAgICAgICAgICAgIC8vIEluaXQgd2l0aCBoZSBmaXJzdCBsaW5lIHNpbmNlIHRoaXMgaXN0IHRoZSBoZWFkaW5nXG4gICAgICAgICAgICAgICAgdmFyICRsYXN0U2Nhbm5lZCA9ICRmb3JtLmZpbmQoJy5vcmRlci13aXNobGlzdCAuaXRlbScpLmZpcnN0KCksXG4gICAgICAgICAgICAgICAgICAgICR0YXJnZXQgPSAkKCk7XG5cbiAgICAgICAgICAgICAgICAvLyBJdGVyYXRlIHRocm91Z2ggdGhlIHByb2R1Y3RzIG9iamVjdCBhbmQgc2VhcmNoIGZvciB0aGVcbiAgICAgICAgICAgICAgICAvLyBwcm9kdWN0cyBpbnNpZGUgdGhlIG1hcmt1cC4gSWYgdGhlIHByb2R1Y3Qgd2FzIGZvdW5kLFxuICAgICAgICAgICAgICAgIC8vIHVwZGF0ZSB0aGUgdmFsdWVzLCBpZiBub3QgYWRkIHRoZSBwcm9kdWN0IHJvdyBhdCB0aGVcbiAgICAgICAgICAgICAgICAvLyBjb3JyZWN0IHBvc2l0aW9uXG4gICAgICAgICAgICAgICAgJC5lYWNoKHJlc3VsdC5wcm9kdWN0cywgZnVuY3Rpb24gKGtleSwgdmFsdWUpIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyIGFydGljbGVJZCA9IGtleS5yZXBsYWNlKCdwcm9kdWN0XycsICcnKSxcbiAgICAgICAgICAgICAgICAgICAgICAgICRhcnRpY2xlID0gJGZvcm0uZmluZCgnaW5wdXRbbmFtZT1cInByb2R1Y3RzX2lkW11cIl1bdmFsdWU9XCInICsgYXJ0aWNsZUlkICsgJ1wiXScpLFxuICAgICAgICAgICAgICAgICAgICAgICAgJHJvdyA9IG51bGw7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKCEkYXJ0aWNsZS5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIFRoZSBhcnRpY2xlIHdhc24ndCBmb3VuZCBvbiBwYWdlXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyAtPiBhZGQgaXRcbiAgICAgICAgICAgICAgICAgICAgICAgICRyb3cgPSAkKHZhbHVlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICRyb3cuaW5zZXJ0QWZ0ZXIoJGxhc3RTY2FubmVkKTtcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIFRoZSBhcnRpY2xlIHdhcyBmb3VuZCBvbiBwYWdlXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyAtPiB1cGRhdGUgaXRcbiAgICAgICAgICAgICAgICAgICAgICAgICRyb3cgPSAkYXJ0aWNsZS5jbG9zZXN0KCcuaXRlbScpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgJHF0eSA9ICRyb3cuZmluZCgnaW5wdXRbbmFtZT1cImNhcnRfcXVhbnRpdHlbXVwiXScpLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG9sZFF0eSA9IHBhcnNlRmxvYXQoJHF0eS5kYXRhKCkub2xkVmFsdWUpLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGN1cnJlbnRRdHkgPSBwYXJzZUZsb2F0KCRxdHkudmFsKCkpLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG5ld1F0eSA9IHBhcnNlRmxvYXQoJCh2YWx1ZSkuZmluZCgnaW5wdXRbbmFtZT1cImNhcnRfcXVhbnRpdHlbXVwiXScpLnZhbCgpKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJHF0eS5kYXRhKCdvbGRWYWx1ZScsIG5ld1F0eSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIEFkZCBvciByZW1vdmUgdGhlIGNoYW5nZWQgY2xhc3NlcyBkZXBlbmRpbmcgb25cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIHRoZSBxdWFudGl0eSBjaGFuZ2VzIGFuZCB0aGUgb24gcGFnZSBzdG9yZWQgdmFsdWVzXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAob2xkUXR5ID09PSBjdXJyZW50UXR5ICYmIGN1cnJlbnRRdHkgIT09IG5ld1F0eSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRxdHkuYWRkQ2xhc3Mob3B0aW9ucy5jaGFuZ2VDbGFzcyk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKG9sZFF0eSAhPT0gY3VycmVudFF0eSAmJiBjdXJyZW50UXR5ID09PSBuZXdRdHkpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkcXR5LnJlbW92ZUNsYXNzKG9wdGlvbnMuY2hhbmdlQ2xhc3MpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgJHRhcmdldC5hZGQoJHJvdyk7XG4gICAgICAgICAgICAgICAgICAgICRsYXN0U2Nhbm5lZCA9ICRyb3c7XG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAvLyBVcGRhdGUgdGhlIHJlc3Qgb2YgdGhlIGZvcm1cbiAgICAgICAgICAgICAgICBfdXBkYXRlRm9ybSgkdGFyZ2V0LCByZXN1bHQpO1xuICAgICAgICAgICAgfSkuYWx3YXlzKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICB1cGRhdGVMaXN0ID0gZmFsc2U7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogVXBkYXRlIHRoZSBpbnB1dCBjaGFuZ2Ugc3RhdGVcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfdXBkYXRlQ2hhbmdlU3RhdGUgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAkZm9ybVxuICAgICAgICAgICAgICAgIC5maW5kKG9wdGlvbnMuc2VsZWN0b3JNYXBwaW5nLnN1Ym1pdClcbiAgICAgICAgICAgICAgICAudGV4dChqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZShpc0NoYW5nZWQgPyAncmVmcmVzaCcgOiAnY2hlY2tvdXQnLCAnYnV0dG9ucycpKVxuICAgICAgICB9O1xuXG5cbi8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcbiAgICAgICAgICogQGNvbnN0cnVjdG9yXG4gICAgICAgICAqL1xuICAgICAgICBtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uIChkb25lKSB7XG5cbiAgICAgICAgICAgICR1cGRhdGVUYXJnZXQgPSAkKG9wdGlvbnMudXBkYXRlVGFyZ2V0KTtcbiAgICAgICAgICAgICRjYXJ0RW1wdHkgPSAkKG9wdGlvbnMuY2FydEVtcHR5KTtcbiAgICAgICAgICAgICRjYXJ0Tm90RW1wdHkgPSAkKG9wdGlvbnMuY2FydE5vdEVtcHR5KTtcbiAgICAgICAgICAgICRkZWxldGVGaWVsZCA9ICQob3B0aW9ucy5kZWxldGVJbnB1dCk7XG4gICAgICAgICAgICAkZm9ybSA9ICR0aGlzLmZpbmQoJ2Zvcm0nKS5maXJzdCgpO1xuICAgICAgICAgICAgZGVsZXRlRmllbGROYW1lID0gJGRlbGV0ZUZpZWxkLmF0dHIoJ25hbWUnKTtcbiAgICAgICAgICAgIGFjdGlvbiA9ICRmb3JtLmF0dHIoJ2FjdGlvbicpO1xuICAgICAgICAgICAgdHJhbnNpdGlvbiA9IHtvcGVuOiB0cnVlLCBjbGFzc09wZW46IG9wdGlvbnMuY2xhc3NMb2FkaW5nfTtcblxuICAgICAgICAgICAgLy8gU2V0cyB0aGUgY3VycmVudCB2YWx1ZSBvZiB0aGUgaW5wdXRcbiAgICAgICAgICAgIC8vIHRvIGFuIGhpZGRlbiBkYXRhIGF0dHJpYnV0ZVxuICAgICAgICAgICAgX3VwZGF0ZURhdGFWYWx1ZXMoJGZvcm0pO1xuXG4gICAgICAgICAgICAkZm9ybVxuXHRcdFx0XHQub24oJ2lucHV0JywgJ2lucHV0W3R5cGU9XCJ0ZXh0XCJdOm5vdCguZ2lmdC1jb3Vwb24tY29kZS1pbnB1dCknLCBfaW5wdXRIYW5kbGVyKVxuICAgICAgICAgICAgICAgIC5vbignYmx1cicsICdpbnB1dFt0eXBlPVwidGV4dFwiXTpub3QoLmdpZnQtY291cG9uLWNvZGUtaW5wdXQpJywgX2JsdXJIYW5kbGVyKVxuICAgICAgICAgICAgICAgIC5vbignY2xpY2suZGVsZXRlJywgJy5idXR0b24tZGVsZXRlJywgeyd0eXBlJzogJ2RlbGV0ZSd9LCBfY2xpY2tIYW5kbGVyKVxuICAgICAgICAgICAgICAgIC5vbignY2xpY2sucmVmcmVzaCcsICcuYnV0dG9uLXJlZnJlc2gnLCB7J3R5cGUnOiAncmVmcmVzaCd9LCBfY2xpY2tIYW5kbGVyKVxuICAgICAgICAgICAgICAgIC5vbignY2xpY2suYWRkdG9jYXJ0JywgJy5idXR0b24tdG8tY2FydCcsIHsndHlwZSc6ICdhZGQnfSwgX2NsaWNrSGFuZGxlcilcbiAgICAgICAgICAgICAgICAub24oJ2NsaWNrLnN1Ym1pdCcsICcuYnV0dG9uLXN1Ym1pdCcsIHsndHlwZSc6ICdzdWJtaXQnfSwgX3N1Ym1pdEJ1dHRvbkhhbmRsZXIpXG4gICAgICAgICAgICAgICAgLm9uKCdzdWJtaXQnLCBfc3VibWl0SGFuZGxlcilcbiAgICAgICAgICAgICAgICAub24oanNlLmxpYnMudGhlbWUuZXZlbnRzLkNIRUNLX0NBUlQoKSwgX2NoZWNrRm9ybUhhbmRsZXIpO1xuXG4gICAgICAgICAgICAkKCdhLnRvZ2dsZXVzZWJhbGFuY2UgaW5wdXRbbmFtZT1cImd2X3VzZV9iYWxhbmNlXCJdJykub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICBcdGNvbnNvbGUuaW5mbygnY2xpY2snKTsgbG9jYXRpb24gPSAkKHRoaXMpLnBhcmVudCgnYScpLmdldCgwKS5ocmVmO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICBcbiAgICAgICAgICAgIGRvbmUoKTtcbiAgICAgICAgfTtcblxuICAgICAgICAvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG4gICAgICAgIHJldHVybiBtb2R1bGU7XG4gICAgfSk7XG4iXX0=
