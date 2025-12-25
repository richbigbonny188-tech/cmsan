'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/* --------------------------------------------------------------
 filter.js 2022-06-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gambio.widgets.module('filter', ['form', 'xhr'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        $body = $('body'),
        $preloader = null,
        $contentWrapper = null,
        errorTimer = null,
        updateTimer = null,
        filterAjax = null,
        productsAjax = null,
        historyAvailable = false,
        reset = false,
        historyPopstateEventBinded = false,
        defaults = {
        // The url the ajax request execute against
        requestUrl: 'shop.php?do=Filter',
        // If autoUpdate is false, and this is true the product listing filter will be set to default 
        // on page reload
        resetProductlistingFilter: false,
        // If true, the product list gets updated dynamically
        autoUpdate: true,
        // The delay after a change event before an ajax gets executed
        updateDelay: 200,
        // The maximum number of retries after failures
        retries: 2,
        // After which delay the nex try will be done
        retryDelay: 500,

        selectorMapping: {
            filterForm: '.filter-box-form-wrapper',
            productsContainer: '.product-filter-target',
            filterSelectionContainer: '.filter-selection-container',
            listingPagination: '.productlisting-filter-container .panel-pagination',
            filterHiddenContainer: '.productlisting-filter-container .productlisting-filter-hiddens',
            paginationInfo: '.pagination-info'
        }
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    /*
     var v_selected_values_group = new Array();
     $("#menubox_body_shadow").find("span").live("click", function()
     {		
     $("#menubox_body_shadow").removeClass("error").html("");
      get_selected_values();
     get_available_values(0);
     });
      $("#menubox_filter .filter_features_link.link_list").live("click", function(){
     var t_feature_value_id = $(this).attr("rel");
     $( "#"+t_feature_value_id ).trigger("click");
     return false;
     */

    // ########## HELPER FUNCTIONS ##########

    /**
     * Helper function that updates the product list
     * and the pagination for the filter.
     * @param filterResult
     * @private
     */
    var _updateProducts = function _updateProducts(historyChange) {
        var resetParam = '';

        if (productsAjax) {
            productsAjax.abort();
        }

        if (reset) {
            resetParam = '&reset=true';
        }

        // Call the request ajax and fill the page with the delivered data
        productsAjax = $.ajax({
            url: options.requestUrl + '/GetListing&' + $this.serialize() + resetParam,
            method: 'GET',
            dataType: 'json'
        }).done(function (result) {

            // redirect if filter has been reset
            if (typeof result.redirect !== 'undefined') {
                location.href = result.redirect;
                return;
            }

            // bind _historyHandler function on popstate event not earlier than first paged content change to 
            // prevent endless popstate event triggering bug on mobile devices
            if (!historyPopstateEventBinded && options.autoUpdate) {
                $(window).on('popstate', _historyHandler);
                historyPopstateEventBinded = true;
            }

            jse.libs.theme.helpers.fill(result.content, $contentWrapper, options.selectorMapping);

            var $productsContainer = $(options.selectorMapping.productsContainer);

            $productsContainer.attr('data-gambio-widget', 'cart_handler');
            gambio.widgets.init($productsContainer);

            var $productsContainerWrapper = $(options.selectorMapping.productsContainer).parent('div');

            $productsContainerWrapper.attr('data-gambio-widget', 'product_hover');
            $productsContainerWrapper.attr('data-product_hover-scope', '.productlist-viewmode-grid');
            gambio.widgets.init($productsContainerWrapper);

            if (historyAvailable && historyChange) {
                var urlParameter = decodeURIComponent($this.serialize());

                history.pushState({}, 'filter', location.origin + location.pathname + '?' + urlParameter + location.hash);
                $this.trigger('pushstate', []);
            } else {
                $this.trigger('pushstate_no_history', []);
            }
        });
    };

    /**
     * Helper function that transforms the filter
     * settings to a format that is readable by
     * the backend
     * @param       {object}        dataset             The formdata that contains the filter settings
     * @return     {*}                                 The transformed form data
     * @private
     */
    var _transform = function _transform(dataset, join) {
        var result = [];
        $.each(dataset.filter_fv_id, function (key, value) {
            if (value !== undefined && value !== false) {

                if ((typeof value === 'undefined' ? 'undefined' : _typeof(value)) === 'object') {
                    var valid = [];
                    $.each(value, function (k, v) {
                        if (v !== false) {
                            valid.push(v);
                        }
                    });
                    if (join) {
                        result.push(key + ':' + valid.join('|'));
                    } else {
                        result[key] = result[key] || [];
                        result[key] = valid;
                    }
                } else {
                    result.push(key + ':' + value);
                }
            }
        });

        dataset.filter_fv_id = join ? result.join('&') : result;

        // value_conjunction is not needed for do=Filter-request and should be deleted because its length can be too
        // large for POST-data
        delete dataset.value_conjunction;

        return dataset;
    };

    /**
     * Helper function that calls the update
     * ajax and replaces the filter box with
     * the new form
     * @param       {integer}       tryCount        The count how often the ajax has failed
     * @param       {object}        formdata        The ready to use data from the form
     * @param       {boolean}       historyChange   If true, the history will be updted after the list update (if possible)
     * @private
     */
    var _update = function _update(tryCount, formdata, historyChange) {

        $preloader.removeClass('error').show();

        if (filterAjax) {
            filterAjax.abort();
        }

        filterAjax = jse.libs.xhr.ajax({
            url: options.requestUrl,
            data: formdata
        }, true).done(function (result) {
            // Update the filterbox and check if the products need to be updated automatically.
            // The elements will need to be converted again to checkbox widgets, so we will first
            // store them in a hidden div, convert them and then append them to the filter box 
            // (dirty fix because it is not otherwise possible without major refactoring ...)
            var checkboxes = $(result.content.filter.selector).find('input:checkbox').length,
                $targets = $(result.content.filter.selector);

            if (checkboxes) {

                var $hiddenContainer = $('<div/>').appendTo('body').hide();
                // Copy the elements but leave a clone to the filter box element.
                $this.children().appendTo($hiddenContainer).clone().appendTo($this);

                jse.libs.theme.helpers.fill(result.content, $hiddenContainer, options.selectorMapping);
                gambio.widgets.init($hiddenContainer);

                var intv = setInterval(function () {
                    if ($hiddenContainer.find('.single-checkbox').length > 0) {
                        $this.children().remove();
                        $hiddenContainer.children().appendTo($this);
                        $hiddenContainer.remove();

                        $preloader.hide();
                        if (options.autoUpdate) {
                            _updateProducts(historyChange);
                        }

                        clearInterval(intv);
                    }
                }, 300);
            } else {
                jse.libs.theme.helpers.fill(result.content, $body, options.selectorMapping);
                gambio.widgets.init($targets);
                $preloader.hide();

                if (options.autoUpdate) {
                    _updateProducts(historyChange);
                }
            }

            if (location.href.search(/advanced_search_result\.php/g) !== -1) {
                $('h1').css('visibility', 'hidden');
            }

            // reinitialize widgets in updated DOM
            window.gambio.widgets.init($this);
        }).fail(function () {
            if (tryCount < options.retries) {
                // Restart the update process if the
                // tryCount hasn't reached the maximum
                errorTimer = setTimeout(function () {
                    _update(tryCount + 1, formdata, historyChange);
                }, options.retryDelay);
            } else {
                $preloader.addClass('error');
            }
        });
    };

    /**
     * Helper function that starts the filter
     * and page update process
     * @private
     */
    var _updateStart = function _updateStart(historyChange) {
        var dataset = jse.libs.form.getData($this);

        historyChange = historyChange !== undefined ? !!historyChange : true;

        _update(0, _transform(dataset, true), historyChange);
    };

    // ########## EVENT HANDLER #########

    /**
     * The submit event gets aborted
     * if the live update is set to true. Else
     * if the productlisiting filter shall be
     * kept, get the parameters from it and store
     * them in hidden input fields before submit
     * @param       {object}        e           jQuery event object
     * @private
     */
    var _submitHandler = function _submitHandler(e) {
        reset = false;

        if (options.autoUpdate) {
            e.preventDefault();
            e.stopPropagation();
            $.magnificPopup.close();
        } else if (!options.resetProductlistingFilter) {
            jse.libs.form.addHiddenByUrl($this);
            // remove old filter_fv_id values
            $('input[name^="filter_fv_id"][type="hidden"]').remove();
        }
    };

    /**
     * Event handler that gets triggered
     * on every change of an input field
     * inside the filter box. It starts the
     * update process after a short delay
     * @param       {object}        e           jQuery event object
     * @private
     */
    var _changeHandler = function _changeHandler(e) {
        e.preventDefault();
        e.stopPropagation();

        clearTimeout(updateTimer);
        clearTimeout(errorTimer);

        updateTimer = setTimeout(_updateStart, options.updateDelay);
    };

    /**
     * Event handler that reacts on the reset
     * button / event. Depending on the autoUpdate
     * setting the page gets reloaded or the form
     * / products gets updated
     * @param       {object}        e           jQuery event object
     * @private
     */
    var _resetHandler = function _resetHandler(e) {
        e.preventDefault();
        e.stopPropagation();

        jse.libs.form.reset($this);
        jse.libs.form.addHiddenByUrl($this);

        reset = true;

        if (options.autoUpdate) {
            _updateStart();
        } else {
            // reset hidden input values
            var formData = $this.serialize().replace(/(value_conjunction|filter_price_min|filter_price_max)([^=]*=)[^&]*&/g, '$1$2&');
            location.href = location.pathname + '?' + formData;
        }
    };

    /**
     * Handler that listens on the popstate event.
     * In a case of a popstate, the filter will change
     * to it's previous state and will update the page
     * @private
     */
    var _historyHandler = function _historyHandler() {
        jse.libs.form.reset($this);
        jse.libs.form.prefillForm($this, jse.libs.theme.helpers.getUrlParams());
        _updateStart(false);
    };

    /**
     * Handler that listens on the click event
     * of a "more" button to show all filter options
     * @private
     */
    var _clickHandler = function _clickHandler() {
        $(this).parent().removeClass('collapsed');
        $(this).hide();
    };

    /**
     * Handler that listens on the click event
     * of a filter option link to trigger the
     * change event of the belonging hidden checkbox
     *
     * @param e
     * @private
     */
    var _filterClickHandler = function _filterClickHandler(e) {
        var id = $(this).attr('rel');

        e.preventDefault();
        e.stopPropagation();

        $('#' + id).prop('checked', true).trigger('change');
    };

    // ########## INITIALIZATION ##########


    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {
        $preloader = $this.find('.preloader, .preloader-message');
        $contentWrapper = $('.main-inside');
        historyAvailable = jse.core.config.get('history');

        // no auto update on start page
        if ($(options.selectorMapping.productsContainer).length === 0) {
            options.autoUpdate = false;
        }

        $this.on('change', 'select, input[type="checkbox"], input[type="text"]', _changeHandler).on('click', '.btn-link', _filterClickHandler).on('reset', _resetHandler).on('submit', _submitHandler).on('click', '.show-more', _clickHandler);

        $body.addClass('filterbox-enabled');

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvZmlsdGVyLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJGJvZHkiLCIkcHJlbG9hZGVyIiwiJGNvbnRlbnRXcmFwcGVyIiwiZXJyb3JUaW1lciIsInVwZGF0ZVRpbWVyIiwiZmlsdGVyQWpheCIsInByb2R1Y3RzQWpheCIsImhpc3RvcnlBdmFpbGFibGUiLCJyZXNldCIsImhpc3RvcnlQb3BzdGF0ZUV2ZW50QmluZGVkIiwiZGVmYXVsdHMiLCJyZXF1ZXN0VXJsIiwicmVzZXRQcm9kdWN0bGlzdGluZ0ZpbHRlciIsImF1dG9VcGRhdGUiLCJ1cGRhdGVEZWxheSIsInJldHJpZXMiLCJyZXRyeURlbGF5Iiwic2VsZWN0b3JNYXBwaW5nIiwiZmlsdGVyRm9ybSIsInByb2R1Y3RzQ29udGFpbmVyIiwiZmlsdGVyU2VsZWN0aW9uQ29udGFpbmVyIiwibGlzdGluZ1BhZ2luYXRpb24iLCJmaWx0ZXJIaWRkZW5Db250YWluZXIiLCJwYWdpbmF0aW9uSW5mbyIsIm9wdGlvbnMiLCJleHRlbmQiLCJfdXBkYXRlUHJvZHVjdHMiLCJoaXN0b3J5Q2hhbmdlIiwicmVzZXRQYXJhbSIsImFib3J0IiwiYWpheCIsInVybCIsInNlcmlhbGl6ZSIsIm1ldGhvZCIsImRhdGFUeXBlIiwiZG9uZSIsInJlc3VsdCIsInJlZGlyZWN0IiwibG9jYXRpb24iLCJocmVmIiwid2luZG93Iiwib24iLCJfaGlzdG9yeUhhbmRsZXIiLCJqc2UiLCJsaWJzIiwidGhlbWUiLCJoZWxwZXJzIiwiZmlsbCIsImNvbnRlbnQiLCIkcHJvZHVjdHNDb250YWluZXIiLCJhdHRyIiwiaW5pdCIsIiRwcm9kdWN0c0NvbnRhaW5lcldyYXBwZXIiLCJwYXJlbnQiLCJ1cmxQYXJhbWV0ZXIiLCJkZWNvZGVVUklDb21wb25lbnQiLCJoaXN0b3J5IiwicHVzaFN0YXRlIiwib3JpZ2luIiwicGF0aG5hbWUiLCJoYXNoIiwidHJpZ2dlciIsIl90cmFuc2Zvcm0iLCJkYXRhc2V0Iiwiam9pbiIsImVhY2giLCJmaWx0ZXJfZnZfaWQiLCJrZXkiLCJ2YWx1ZSIsInVuZGVmaW5lZCIsInZhbGlkIiwiayIsInYiLCJwdXNoIiwidmFsdWVfY29uanVuY3Rpb24iLCJfdXBkYXRlIiwidHJ5Q291bnQiLCJmb3JtZGF0YSIsInJlbW92ZUNsYXNzIiwic2hvdyIsInhociIsImNoZWNrYm94ZXMiLCJmaWx0ZXIiLCJzZWxlY3RvciIsImZpbmQiLCJsZW5ndGgiLCIkdGFyZ2V0cyIsIiRoaWRkZW5Db250YWluZXIiLCJhcHBlbmRUbyIsImhpZGUiLCJjaGlsZHJlbiIsImNsb25lIiwiaW50diIsInNldEludGVydmFsIiwicmVtb3ZlIiwiY2xlYXJJbnRlcnZhbCIsInNlYXJjaCIsImNzcyIsImZhaWwiLCJzZXRUaW1lb3V0IiwiYWRkQ2xhc3MiLCJfdXBkYXRlU3RhcnQiLCJmb3JtIiwiZ2V0RGF0YSIsIl9zdWJtaXRIYW5kbGVyIiwiZSIsInByZXZlbnREZWZhdWx0Iiwic3RvcFByb3BhZ2F0aW9uIiwibWFnbmlmaWNQb3B1cCIsImNsb3NlIiwiYWRkSGlkZGVuQnlVcmwiLCJfY2hhbmdlSGFuZGxlciIsImNsZWFyVGltZW91dCIsIl9yZXNldEhhbmRsZXIiLCJmb3JtRGF0YSIsInJlcGxhY2UiLCJwcmVmaWxsRm9ybSIsImdldFVybFBhcmFtcyIsIl9jbGlja0hhbmRsZXIiLCJfZmlsdGVyQ2xpY2tIYW5kbGVyIiwiaWQiLCJwcm9wIiwiY29yZSIsImNvbmZpZyIsImdldCJdLCJtYXBwaW5ncyI6Ijs7OztBQUFBOzs7Ozs7Ozs7O0FBVUFBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNJLFFBREosRUFHSSxDQUFDLE1BQUQsRUFBUyxLQUFULENBSEosRUFLSSxVQUFVQyxJQUFWLEVBQWdCOztBQUVaOztBQUVSOztBQUVRLFFBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsUUFDSUMsUUFBUUQsRUFBRSxNQUFGLENBRFo7QUFBQSxRQUVJRSxh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S0svRCxFQUxMLENBS1EsT0FMUixFQUtpQixZQUxqQixFQUsrQjZFLGFBTC9COztBQU9BdEgsY0FBTW9HLFFBQU4sQ0FBZSxtQkFBZjs7QUFFQWpFO0FBQ0gsS0FwQkQ7O0FBc0JBO0FBQ0EsV0FBT3ZDLE1BQVA7QUFDSCxDQTVaTCIsImZpbGUiOiJ3aWRnZXRzL2ZpbHRlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gZmlsdGVyLmpzIDIwMjItMDYtMTRcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDIyIEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG4gICAgJ2ZpbHRlcicsXG5cbiAgICBbJ2Zvcm0nLCAneGhyJ10sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgdmFyICR0aGlzID0gJCh0aGlzKSxcbiAgICAgICAgICAgICRib2R5ID0gJCgnYm9keScpLFxuICAgICAgICAgICAgJHByZWxvYWRlciA9IG51bGwsXG4gICAgICAgICAgICAkY29udGVudFdyYXBwZXIgPSBudWxsLFxuICAgICAgICAgICAgZXJyb3JUaW1lciA9IG51bGwsXG4gICAgICAgICAgICB1cGRhdGVUaW1lciA9IG51bGwsXG4gICAgICAgICAgICBmaWx0ZXJBamF4ID0gbnVsbCxcbiAgICAgICAgICAgIHByb2R1Y3RzQWpheCA9IG51bGwsXG4gICAgICAgICAgICBoaXN0b3J5QXZhaWxhYmxlID0gZmFsc2UsXG4gICAgICAgICAgICByZXNldCA9IGZhbHNlLFxuICAgICAgICAgICAgaGlzdG9yeVBvcHN0YXRlRXZlbnRCaW5kZWQgPSBmYWxzZSxcbiAgICAgICAgICAgIGRlZmF1bHRzID0ge1xuICAgICAgICAgICAgICAgIC8vIFRoZSB1cmwgdGhlIGFqYXggcmVxdWVzdCBleGVjdXRlIGFnYWluc3RcbiAgICAgICAgICAgICAgICByZXF1ZXN0VXJsOiAnc2hvcC5waHA/ZG89RmlsdGVyJyxcbiAgICAgICAgICAgICAgICAvLyBJZiBhdXRvVXBkYXRlIGlzIGZhbHNlLCBhbmQgdGhpcyBpcyB0cnVlIHRoZSBwcm9kdWN0IGxpc3RpbmcgZmlsdGVyIHdpbGwgYmUgc2V0IHRvIGRlZmF1bHQgXG4gICAgICAgICAgICAgICAgLy8gb24gcGFnZSByZWxvYWRcbiAgICAgICAgICAgICAgICByZXNldFByb2R1Y3RsaXN0aW5nRmlsdGVyOiBmYWxzZSxcbiAgICAgICAgICAgICAgICAvLyBJZiB0cnVlLCB0aGUgcHJvZHVjdCBsaXN0IGdldHMgdXBkYXRlZCBkeW5hbWljYWxseVxuICAgICAgICAgICAgICAgIGF1dG9VcGRhdGU6IHRydWUsXG4gICAgICAgICAgICAgICAgLy8gVGhlIGRlbGF5IGFmdGVyIGEgY2hhbmdlIGV2ZW50IGJlZm9yZSBhbiBhamF4IGdldHMgZXhlY3V0ZWRcbiAgICAgICAgICAgICAgICB1cGRhdGVEZWxheTogMjAwLFxuICAgICAgICAgICAgICAgIC8vIFRoZSBtYXhpbXVtIG51bWJlciBvZiByZXRyaWVzIGFmdGVyIGZhaWx1cmVzXG4gICAgICAgICAgICAgICAgcmV0cmllczogMixcbiAgICAgICAgICAgICAgICAvLyBBZnRlciB3aGljaCBkZWxheSB0aGUgbmV4IHRyeSB3aWxsIGJlIGRvbmVcbiAgICAgICAgICAgICAgICByZXRyeURlbGF5OiA1MDAsXG5cbiAgICAgICAgICAgICAgICBzZWxlY3Rvck1hcHBpbmc6IHtcbiAgICAgICAgICAgICAgICAgICAgZmlsdGVyRm9ybTogJy5maWx0ZXItYm94LWZvcm0td3JhcHBlcicsXG4gICAgICAgICAgICAgICAgICAgIHByb2R1Y3RzQ29udGFpbmVyOiAnLnByb2R1Y3QtZmlsdGVyLXRhcmdldCcsXG4gICAgICAgICAgICAgICAgICAgIGZpbHRlclNlbGVjdGlvbkNvbnRhaW5lcjogJy5maWx0ZXItc2VsZWN0aW9uLWNvbnRhaW5lcicsXG4gICAgICAgICAgICAgICAgICAgIGxpc3RpbmdQYWdpbmF0aW9uOiAnLnByb2R1Y3RsaXN0aW5nLWZpbHRlci1jb250YWluZXIgLnBhbmVsLXBhZ2luYXRpb24nLFxuICAgICAgICAgICAgICAgICAgICBmaWx0ZXJIaWRkZW5Db250YWluZXI6ICcucHJvZHVjdGxpc3RpbmctZmlsdGVyLWNvbnRhaW5lciAucHJvZHVjdGxpc3RpbmctZmlsdGVyLWhpZGRlbnMnLFxuICAgICAgICAgICAgICAgICAgICBwYWdpbmF0aW9uSW5mbzogJy5wYWdpbmF0aW9uLWluZm8nXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuICAgICAgICAgICAgbW9kdWxlID0ge307XG5cblxuICAgICAgICAvKlxuICAgICAgICAgdmFyIHZfc2VsZWN0ZWRfdmFsdWVzX2dyb3VwID0gbmV3IEFycmF5KCk7XG4gICAgICAgICAkKFwiI21lbnVib3hfYm9keV9zaGFkb3dcIikuZmluZChcInNwYW5cIikubGl2ZShcImNsaWNrXCIsIGZ1bmN0aW9uKClcbiAgICAgICAgIHtcdFx0XG4gICAgICAgICAkKFwiI21lbnVib3hfYm9keV9zaGFkb3dcIikucmVtb3ZlQ2xhc3MoXCJlcnJvclwiKS5odG1sKFwiXCIpO1xuXG4gICAgICAgICBnZXRfc2VsZWN0ZWRfdmFsdWVzKCk7XG4gICAgICAgICBnZXRfYXZhaWxhYmxlX3ZhbHVlcygwKTtcbiAgICAgICAgIH0pO1xuXG4gICAgICAgICAkKFwiI21lbnVib3hfZmlsdGVyIC5maWx0ZXJfZmVhdHVyZXNfbGluay5saW5rX2xpc3RcIikubGl2ZShcImNsaWNrXCIsIGZ1bmN0aW9uKCl7XG4gICAgICAgICB2YXIgdF9mZWF0dXJlX3ZhbHVlX2lkID0gJCh0aGlzKS5hdHRyKFwicmVsXCIpO1xuICAgICAgICAgJCggXCIjXCIrdF9mZWF0dXJlX3ZhbHVlX2lkICkudHJpZ2dlcihcImNsaWNrXCIpO1xuICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgKi9cblxuLy8gIyMjIyMjIyMjIyBIRUxQRVIgRlVOQ1RJT05TICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGVscGVyIGZ1bmN0aW9uIHRoYXQgdXBkYXRlcyB0aGUgcHJvZHVjdCBsaXN0XG4gICAgICAgICAqIGFuZCB0aGUgcGFnaW5hdGlvbiBmb3IgdGhlIGZpbHRlci5cbiAgICAgICAgICogQHBhcmFtIGZpbHRlclJlc3VsdFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF91cGRhdGVQcm9kdWN0cyA9IGZ1bmN0aW9uIChoaXN0b3J5Q2hhbmdlKSB7XG4gICAgICAgICAgICB2YXIgcmVzZXRQYXJhbSA9ICcnO1xuXG4gICAgICAgICAgICBpZiAocHJvZHVjdHNBamF4KSB7XG4gICAgICAgICAgICAgICAgcHJvZHVjdHNBamF4LmFib3J0KCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmIChyZXNldCkge1xuICAgICAgICAgICAgICAgIHJlc2V0UGFyYW0gPSAnJnJlc2V0PXRydWUnO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvLyBDYWxsIHRoZSByZXF1ZXN0IGFqYXggYW5kIGZpbGwgdGhlIHBhZ2Ugd2l0aCB0aGUgZGVsaXZlcmVkIGRhdGFcbiAgICAgICAgICAgIHByb2R1Y3RzQWpheCA9ICQuYWpheCh7XG4gICAgICAgICAgICAgICAgdXJsOiBvcHRpb25zLnJlcXVlc3RVcmwgKyAnL0dldExpc3RpbmcmJyArICR0aGlzLnNlcmlhbGl6ZSgpICsgcmVzZXRQYXJhbSxcbiAgICAgICAgICAgICAgICBtZXRob2Q6ICdHRVQnLFxuICAgICAgICAgICAgICAgIGRhdGFUeXBlOiAnanNvbidcbiAgICAgICAgICAgIH0pLmRvbmUoZnVuY3Rpb24gKHJlc3VsdCkge1xuXG4gICAgICAgICAgICAgICAgLy8gcmVkaXJlY3QgaWYgZmlsdGVyIGhhcyBiZWVuIHJlc2V0XG4gICAgICAgICAgICAgICAgaWYgKHR5cGVvZiByZXN1bHQucmVkaXJlY3QgIT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgICAgICAgICAgICAgIGxvY2F0aW9uLmhyZWYgPSByZXN1bHQucmVkaXJlY3Q7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAvLyBiaW5kIF9oaXN0b3J5SGFuZGxlciBmdW5jdGlvbiBvbiBwb3BzdGF0ZSBldmVudCBub3QgZWFybGllciB0aGFuIGZpcnN0IHBhZ2VkIGNvbnRlbnQgY2hhbmdlIHRvIFxuICAgICAgICAgICAgICAgIC8vIHByZXZlbnQgZW5kbGVzcyBwb3BzdGF0ZSBldmVudCB0cmlnZ2VyaW5nIGJ1ZyBvbiBtb2JpbGUgZGV2aWNlc1xuICAgICAgICAgICAgICAgIGlmICghaGlzdG9yeVBvcHN0YXRlRXZlbnRCaW5kZWQgJiYgb3B0aW9ucy5hdXRvVXBkYXRlKSB7XG4gICAgICAgICAgICAgICAgICAgICQod2luZG93KS5vbigncG9wc3RhdGUnLCBfaGlzdG9yeUhhbmRsZXIpO1xuICAgICAgICAgICAgICAgICAgICBoaXN0b3J5UG9wc3RhdGVFdmVudEJpbmRlZCA9IHRydWU7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAganNlLmxpYnMudGhlbWUuaGVscGVycy5maWxsKHJlc3VsdC5jb250ZW50LCAkY29udGVudFdyYXBwZXIsIG9wdGlvbnMuc2VsZWN0b3JNYXBwaW5nKTtcblxuICAgICAgICAgICAgICAgIHZhciAkcHJvZHVjdHNDb250YWluZXIgPSAkKG9wdGlvbnMuc2VsZWN0b3JNYXBwaW5nLnByb2R1Y3RzQ29udGFpbmVyKTtcblxuICAgICAgICAgICAgICAgICRwcm9kdWN0c0NvbnRhaW5lci5hdHRyKCdkYXRhLWdhbWJpby13aWRnZXQnLCAnY2FydF9oYW5kbGVyJyk7XG4gICAgICAgICAgICAgICAgZ2FtYmlvLndpZGdldHMuaW5pdCgkcHJvZHVjdHNDb250YWluZXIpO1xuXG4gICAgICAgICAgICAgICAgdmFyICRwcm9kdWN0c0NvbnRhaW5lcldyYXBwZXIgPSAkKG9wdGlvbnMuc2VsZWN0b3JNYXBwaW5nLnByb2R1Y3RzQ29udGFpbmVyKS5wYXJlbnQoJ2RpdicpO1xuXG4gICAgICAgICAgICAgICAgJHByb2R1Y3RzQ29udGFpbmVyV3JhcHBlci5hdHRyKCdkYXRhLWdhbWJpby13aWRnZXQnLCAncHJvZHVjdF9ob3ZlcicpO1xuICAgICAgICAgICAgICAgICRwcm9kdWN0c0NvbnRhaW5lcldyYXBwZXIuYXR0cignZGF0YS1wcm9kdWN0X2hvdmVyLXNjb3BlJywgJy5wcm9kdWN0bGlzdC12aWV3bW9kZS1ncmlkJyk7XG4gICAgICAgICAgICAgICAgZ2FtYmlvLndpZGdldHMuaW5pdCgkcHJvZHVjdHNDb250YWluZXJXcmFwcGVyKTtcblxuICAgICAgICAgICAgICAgIGlmIChoaXN0b3J5QXZhaWxhYmxlICYmIGhpc3RvcnlDaGFuZ2UpIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyIHVybFBhcmFtZXRlciA9IGRlY29kZVVSSUNvbXBvbmVudCgkdGhpcy5zZXJpYWxpemUoKSk7XG5cbiAgICAgICAgICAgICAgICAgICAgaGlzdG9yeS5wdXNoU3RhdGUoe30sICdmaWx0ZXInLCBsb2NhdGlvbi5vcmlnaW4gKyBsb2NhdGlvbi5wYXRobmFtZSArICc/JyArIHVybFBhcmFtZXRlclxuICAgICAgICAgICAgICAgICAgICAgICAgKyBsb2NhdGlvbi5oYXNoKTtcbiAgICAgICAgICAgICAgICAgICAgJHRoaXMudHJpZ2dlcigncHVzaHN0YXRlJywgW10pO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICR0aGlzLnRyaWdnZXIoJ3B1c2hzdGF0ZV9ub19oaXN0b3J5JywgW10pO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBIZWxwZXIgZnVuY3Rpb24gdGhhdCB0cmFuc2Zvcm1zIHRoZSBmaWx0ZXJcbiAgICAgICAgICogc2V0dGluZ3MgdG8gYSBmb3JtYXQgdGhhdCBpcyByZWFkYWJsZSBieVxuICAgICAgICAgKiB0aGUgYmFja2VuZFxuICAgICAgICAgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgICAgIGRhdGFzZXQgICAgICAgICAgICAgVGhlIGZvcm1kYXRhIHRoYXQgY29udGFpbnMgdGhlIGZpbHRlciBzZXR0aW5nc1xuICAgICAgICAgKiBAcmV0dXJuICAgICB7Kn0gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBUaGUgdHJhbnNmb3JtZWQgZm9ybSBkYXRhXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3RyYW5zZm9ybSA9IGZ1bmN0aW9uIChkYXRhc2V0LCBqb2luKSB7XG4gICAgICAgICAgICB2YXIgcmVzdWx0ID0gW107XG4gICAgICAgICAgICAkLmVhY2goZGF0YXNldC5maWx0ZXJfZnZfaWQsIGZ1bmN0aW9uIChrZXksIHZhbHVlKSB7XG4gICAgICAgICAgICAgICAgaWYgKHZhbHVlICE9PSB1bmRlZmluZWQgJiYgdmFsdWUgIT09IGZhbHNlKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKHR5cGVvZiB2YWx1ZSA9PT0gJ29iamVjdCcpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciB2YWxpZCA9IFtdO1xuICAgICAgICAgICAgICAgICAgICAgICAgJC5lYWNoKHZhbHVlLCBmdW5jdGlvbiAoaywgdikge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmICh2ICE9PSBmYWxzZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YWxpZC5wdXNoKHYpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGpvaW4pIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXN1bHQucHVzaChrZXkgKyAnOicgKyB2YWxpZC5qb2luKCd8JykpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXN1bHRba2V5XSA9IHJlc3VsdFtrZXldIHx8IFtdO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdFtrZXldID0gdmFsaWQ7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXN1bHQucHVzaChrZXkgKyAnOicgKyB2YWx1ZSk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgZGF0YXNldC5maWx0ZXJfZnZfaWQgPSAoam9pbikgPyByZXN1bHQuam9pbignJicpIDogcmVzdWx0O1xuXG4gICAgICAgICAgICAvLyB2YWx1ZV9jb25qdW5jdGlvbiBpcyBub3QgbmVlZGVkIGZvciBkbz1GaWx0ZXItcmVxdWVzdCBhbmQgc2hvdWxkIGJlIGRlbGV0ZWQgYmVjYXVzZSBpdHMgbGVuZ3RoIGNhbiBiZSB0b29cbiAgICAgICAgICAgIC8vIGxhcmdlIGZvciBQT1NULWRhdGFcbiAgICAgICAgICAgIGRlbGV0ZSBkYXRhc2V0LnZhbHVlX2Nvbmp1bmN0aW9uO1xuXG4gICAgICAgICAgICByZXR1cm4gZGF0YXNldDtcbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGVscGVyIGZ1bmN0aW9uIHRoYXQgY2FsbHMgdGhlIHVwZGF0ZVxuICAgICAgICAgKiBhamF4IGFuZCByZXBsYWNlcyB0aGUgZmlsdGVyIGJveCB3aXRoXG4gICAgICAgICAqIHRoZSBuZXcgZm9ybVxuICAgICAgICAgKiBAcGFyYW0gICAgICAge2ludGVnZXJ9ICAgICAgIHRyeUNvdW50ICAgICAgICBUaGUgY291bnQgaG93IG9mdGVuIHRoZSBhamF4IGhhcyBmYWlsZWRcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICBmb3JtZGF0YSAgICAgICAgVGhlIHJlYWR5IHRvIHVzZSBkYXRhIGZyb20gdGhlIGZvcm1cbiAgICAgICAgICogQHBhcmFtICAgICAgIHtib29sZWFufSAgICAgICBoaXN0b3J5Q2hhbmdlICAgSWYgdHJ1ZSwgdGhlIGhpc3Rvcnkgd2lsbCBiZSB1cGR0ZWQgYWZ0ZXIgdGhlIGxpc3QgdXBkYXRlIChpZiBwb3NzaWJsZSlcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfdXBkYXRlID0gZnVuY3Rpb24gKHRyeUNvdW50LCBmb3JtZGF0YSwgaGlzdG9yeUNoYW5nZSkge1xuXG4gICAgICAgICAgICAkcHJlbG9hZGVyXG4gICAgICAgICAgICAgICAgLnJlbW92ZUNsYXNzKCdlcnJvcicpXG4gICAgICAgICAgICAgICAgLnNob3coKTtcblxuICAgICAgICAgICAgaWYgKGZpbHRlckFqYXgpIHtcbiAgICAgICAgICAgICAgICBmaWx0ZXJBamF4LmFib3J0KCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGZpbHRlckFqYXggPSBqc2UubGlicy54aHIuYWpheCh7XG4gICAgICAgICAgICAgICAgdXJsOiBvcHRpb25zLnJlcXVlc3RVcmwsXG4gICAgICAgICAgICAgICAgZGF0YTogZm9ybWRhdGFcbiAgICAgICAgICAgIH0sIHRydWUpLmRvbmUoZnVuY3Rpb24gKHJlc3VsdCkge1xuICAgICAgICAgICAgICAgIC8vIFVwZGF0ZSB0aGUgZmlsdGVyYm94IGFuZCBjaGVjayBpZiB0aGUgcHJvZHVjdHMgbmVlZCB0byBiZSB1cGRhdGVkIGF1dG9tYXRpY2FsbHkuXG4gICAgICAgICAgICAgICAgLy8gVGhlIGVsZW1lbnRzIHdpbGwgbmVlZCB0byBiZSBjb252ZXJ0ZWQgYWdhaW4gdG8gY2hlY2tib3ggd2lkZ2V0cywgc28gd2Ugd2lsbCBmaXJzdFxuICAgICAgICAgICAgICAgIC8vIHN0b3JlIHRoZW0gaW4gYSBoaWRkZW4gZGl2LCBjb252ZXJ0IHRoZW0gYW5kIHRoZW4gYXBwZW5kIHRoZW0gdG8gdGhlIGZpbHRlciBib3ggXG4gICAgICAgICAgICAgICAgLy8gKGRpcnR5IGZpeCBiZWNhdXNlIGl0IGlzIG5vdCBvdGhlcndpc2UgcG9zc2libGUgd2l0aG91dCBtYWpvciByZWZhY3RvcmluZyAuLi4pXG4gICAgICAgICAgICAgICAgdmFyIGNoZWNrYm94ZXMgPSAkKHJlc3VsdC5jb250ZW50LmZpbHRlci5zZWxlY3RvcilcbiAgICAgICAgICAgICAgICAgICAgICAgIC5maW5kKCdpbnB1dDpjaGVja2JveCcpXG4gICAgICAgICAgICAgICAgICAgICAgICAubGVuZ3RoLFxuICAgICAgICAgICAgICAgICAgICAkdGFyZ2V0cyA9ICQocmVzdWx0LmNvbnRlbnQuZmlsdGVyLnNlbGVjdG9yKTtcblxuICAgICAgICAgICAgICAgIGlmIChjaGVja2JveGVzKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgdmFyICRoaWRkZW5Db250YWluZXIgPSAkKCc8ZGl2Lz4nKS5hcHBlbmRUbygnYm9keScpLmhpZGUoKTtcbiAgICAgICAgICAgICAgICAgICAgLy8gQ29weSB0aGUgZWxlbWVudHMgYnV0IGxlYXZlIGEgY2xvbmUgdG8gdGhlIGZpbHRlciBib3ggZWxlbWVudC5cbiAgICAgICAgICAgICAgICAgICAgJHRoaXMuY2hpbGRyZW4oKS5hcHBlbmRUbygkaGlkZGVuQ29udGFpbmVyKS5jbG9uZSgpLmFwcGVuZFRvKCR0aGlzKTtcblxuICAgICAgICAgICAgICAgICAgICBqc2UubGlicy50aGVtZS5oZWxwZXJzLmZpbGwocmVzdWx0LmNvbnRlbnQsICRoaWRkZW5Db250YWluZXIsIG9wdGlvbnMuc2VsZWN0b3JNYXBwaW5nKTtcbiAgICAgICAgICAgICAgICAgICAgZ2FtYmlvLndpZGdldHMuaW5pdCgkaGlkZGVuQ29udGFpbmVyKTtcblxuICAgICAgICAgICAgICAgICAgICB2YXIgaW50diA9IHNldEludGVydmFsKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmICgkaGlkZGVuQ29udGFpbmVyLmZpbmQoJy5zaW5nbGUtY2hlY2tib3gnKS5sZW5ndGggPiAwKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJHRoaXMuY2hpbGRyZW4oKS5yZW1vdmUoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkaGlkZGVuQ29udGFpbmVyLmNoaWxkcmVuKCkuYXBwZW5kVG8oJHRoaXMpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRoaWRkZW5Db250YWluZXIucmVtb3ZlKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkcHJlbG9hZGVyLmhpZGUoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAob3B0aW9ucy5hdXRvVXBkYXRlKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIF91cGRhdGVQcm9kdWN0cyhoaXN0b3J5Q2hhbmdlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjbGVhckludGVydmFsKGludHYpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIH0sIDMwMCk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBqc2UubGlicy50aGVtZS5oZWxwZXJzLmZpbGwocmVzdWx0LmNvbnRlbnQsICRib2R5LCBvcHRpb25zLnNlbGVjdG9yTWFwcGluZyk7XG4gICAgICAgICAgICAgICAgICAgIGdhbWJpby53aWRnZXRzLmluaXQoJHRhcmdldHMpO1xuICAgICAgICAgICAgICAgICAgICAkcHJlbG9hZGVyLmhpZGUoKTtcblxuICAgICAgICAgICAgICAgICAgICBpZiAob3B0aW9ucy5hdXRvVXBkYXRlKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBfdXBkYXRlUHJvZHVjdHMoaGlzdG9yeUNoYW5nZSk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBpZiAobG9jYXRpb24uaHJlZi5zZWFyY2goL2FkdmFuY2VkX3NlYXJjaF9yZXN1bHRcXC5waHAvZykgIT09IC0xKSB7XG4gICAgICAgICAgICAgICAgICAgICQoJ2gxJykuY3NzKCd2aXNpYmlsaXR5JywgJ2hpZGRlbicpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIC8vIHJlaW5pdGlhbGl6ZSB3aWRnZXRzIGluIHVwZGF0ZWQgRE9NXG4gICAgICAgICAgICAgICAgd2luZG93LmdhbWJpby53aWRnZXRzLmluaXQoJHRoaXMpO1xuXG4gICAgICAgICAgICB9KS5mYWlsKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICBpZiAodHJ5Q291bnQgPCBvcHRpb25zLnJldHJpZXMpIHtcbiAgICAgICAgICAgICAgICAgICAgLy8gUmVzdGFydCB0aGUgdXBkYXRlIHByb2Nlc3MgaWYgdGhlXG4gICAgICAgICAgICAgICAgICAgIC8vIHRyeUNvdW50IGhhc24ndCByZWFjaGVkIHRoZSBtYXhpbXVtXG4gICAgICAgICAgICAgICAgICAgIGVycm9yVGltZXIgPSBzZXRUaW1lb3V0KGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIF91cGRhdGUodHJ5Q291bnQgKyAxLCBmb3JtZGF0YSwgaGlzdG9yeUNoYW5nZSk7XG4gICAgICAgICAgICAgICAgICAgIH0sIG9wdGlvbnMucmV0cnlEZWxheSk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgJHByZWxvYWRlci5hZGRDbGFzcygnZXJyb3InKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBIZWxwZXIgZnVuY3Rpb24gdGhhdCBzdGFydHMgdGhlIGZpbHRlclxuICAgICAgICAgKiBhbmQgcGFnZSB1cGRhdGUgcHJvY2Vzc1xuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF91cGRhdGVTdGFydCA9IGZ1bmN0aW9uIChoaXN0b3J5Q2hhbmdlKSB7XG4gICAgICAgICAgICB2YXIgZGF0YXNldCA9IGpzZS5saWJzLmZvcm0uZ2V0RGF0YSgkdGhpcyk7XG5cbiAgICAgICAgICAgIGhpc3RvcnlDaGFuZ2UgPSAoaGlzdG9yeUNoYW5nZSAhPT0gdW5kZWZpbmVkKSA/ICEhaGlzdG9yeUNoYW5nZSA6IHRydWU7XG5cbiAgICAgICAgICAgIF91cGRhdGUoMCwgX3RyYW5zZm9ybShkYXRhc2V0LCB0cnVlKSwgaGlzdG9yeUNoYW5nZSk7XG4gICAgICAgIH07XG5cblxuLy8gIyMjIyMjIyMjIyBFVkVOVCBIQU5ETEVSICMjIyMjIyMjI1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBUaGUgc3VibWl0IGV2ZW50IGdldHMgYWJvcnRlZFxuICAgICAgICAgKiBpZiB0aGUgbGl2ZSB1cGRhdGUgaXMgc2V0IHRvIHRydWUuIEVsc2VcbiAgICAgICAgICogaWYgdGhlIHByb2R1Y3RsaXNpdGluZyBmaWx0ZXIgc2hhbGwgYmVcbiAgICAgICAgICoga2VwdCwgZ2V0IHRoZSBwYXJhbWV0ZXJzIGZyb20gaXQgYW5kIHN0b3JlXG4gICAgICAgICAqIHRoZW0gaW4gaGlkZGVuIGlucHV0IGZpZWxkcyBiZWZvcmUgc3VibWl0XG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgZSAgICAgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9zdWJtaXRIYW5kbGVyID0gZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIHJlc2V0ID0gZmFsc2U7XG5cbiAgICAgICAgICAgIGlmIChvcHRpb25zLmF1dG9VcGRhdGUpIHtcbiAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcbiAgICAgICAgICAgICAgICAkLm1hZ25pZmljUG9wdXAuY2xvc2UoKVxuICAgICAgICAgICAgfSBlbHNlIGlmICghb3B0aW9ucy5yZXNldFByb2R1Y3RsaXN0aW5nRmlsdGVyKSB7XG4gICAgICAgICAgICAgICAganNlLmxpYnMuZm9ybS5hZGRIaWRkZW5CeVVybCgkdGhpcyk7XG4gICAgICAgICAgICAgICAgLy8gcmVtb3ZlIG9sZCBmaWx0ZXJfZnZfaWQgdmFsdWVzXG4gICAgICAgICAgICAgICAgJCgnaW5wdXRbbmFtZV49XCJmaWx0ZXJfZnZfaWRcIl1bdHlwZT1cImhpZGRlblwiXScpLnJlbW92ZSgpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBFdmVudCBoYW5kbGVyIHRoYXQgZ2V0cyB0cmlnZ2VyZWRcbiAgICAgICAgICogb24gZXZlcnkgY2hhbmdlIG9mIGFuIGlucHV0IGZpZWxkXG4gICAgICAgICAqIGluc2lkZSB0aGUgZmlsdGVyIGJveC4gSXQgc3RhcnRzIHRoZVxuICAgICAgICAgKiB1cGRhdGUgcHJvY2VzcyBhZnRlciBhIHNob3J0IGRlbGF5XG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgZSAgICAgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9jaGFuZ2VIYW5kbGVyID0gZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cbiAgICAgICAgICAgIGNsZWFyVGltZW91dCh1cGRhdGVUaW1lcik7XG4gICAgICAgICAgICBjbGVhclRpbWVvdXQoZXJyb3JUaW1lcik7XG5cbiAgICAgICAgICAgIHVwZGF0ZVRpbWVyID0gc2V0VGltZW91dChfdXBkYXRlU3RhcnQsIG9wdGlvbnMudXBkYXRlRGVsYXkpO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBFdmVudCBoYW5kbGVyIHRoYXQgcmVhY3RzIG9uIHRoZSByZXNldFxuICAgICAgICAgKiBidXR0b24gLyBldmVudC4gRGVwZW5kaW5nIG9uIHRoZSBhdXRvVXBkYXRlXG4gICAgICAgICAqIHNldHRpbmcgdGhlIHBhZ2UgZ2V0cyByZWxvYWRlZCBvciB0aGUgZm9ybVxuICAgICAgICAgKiAvIHByb2R1Y3RzIGdldHMgdXBkYXRlZFxuICAgICAgICAgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgICAgIGUgICAgICAgICAgIGpRdWVyeSBldmVudCBvYmplY3RcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfcmVzZXRIYW5kbGVyID0gZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cbiAgICAgICAgICAgIGpzZS5saWJzLmZvcm0ucmVzZXQoJHRoaXMpO1xuICAgICAgICAgICAganNlLmxpYnMuZm9ybS5hZGRIaWRkZW5CeVVybCgkdGhpcyk7XG5cbiAgICAgICAgICAgIHJlc2V0ID0gdHJ1ZTtcblxuICAgICAgICAgICAgaWYgKG9wdGlvbnMuYXV0b1VwZGF0ZSkge1xuICAgICAgICAgICAgICAgIF91cGRhdGVTdGFydCgpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAvLyByZXNldCBoaWRkZW4gaW5wdXQgdmFsdWVzXG4gICAgICAgICAgICAgICAgbGV0IGZvcm1EYXRhID0gJHRoaXMuc2VyaWFsaXplKCkucmVwbGFjZSgvKHZhbHVlX2Nvbmp1bmN0aW9ufGZpbHRlcl9wcmljZV9taW58ZmlsdGVyX3ByaWNlX21heCkoW149XSo9KVteJl0qJi9nLCAnJDEkMiYnKTtcbiAgICAgICAgICAgICAgICBsb2NhdGlvbi5ocmVmID0gbG9jYXRpb24ucGF0aG5hbWUgKyAnPycgKyBmb3JtRGF0YTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGFuZGxlciB0aGF0IGxpc3RlbnMgb24gdGhlIHBvcHN0YXRlIGV2ZW50LlxuICAgICAgICAgKiBJbiBhIGNhc2Ugb2YgYSBwb3BzdGF0ZSwgdGhlIGZpbHRlciB3aWxsIGNoYW5nZVxuICAgICAgICAgKiB0byBpdCdzIHByZXZpb3VzIHN0YXRlIGFuZCB3aWxsIHVwZGF0ZSB0aGUgcGFnZVxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9oaXN0b3J5SGFuZGxlciA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIGpzZS5saWJzLmZvcm0ucmVzZXQoJHRoaXMpO1xuICAgICAgICAgICAganNlLmxpYnMuZm9ybS5wcmVmaWxsRm9ybSgkdGhpcywganNlLmxpYnMudGhlbWUuaGVscGVycy5nZXRVcmxQYXJhbXMoKSk7XG4gICAgICAgICAgICBfdXBkYXRlU3RhcnQoZmFsc2UpO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBIYW5kbGVyIHRoYXQgbGlzdGVucyBvbiB0aGUgY2xpY2sgZXZlbnRcbiAgICAgICAgICogb2YgYSBcIm1vcmVcIiBidXR0b24gdG8gc2hvdyBhbGwgZmlsdGVyIG9wdGlvbnNcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfY2xpY2tIYW5kbGVyID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgJCh0aGlzKS5wYXJlbnQoKS5yZW1vdmVDbGFzcygnY29sbGFwc2VkJyk7XG4gICAgICAgICAgICAkKHRoaXMpLmhpZGUoKTtcbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGFuZGxlciB0aGF0IGxpc3RlbnMgb24gdGhlIGNsaWNrIGV2ZW50XG4gICAgICAgICAqIG9mIGEgZmlsdGVyIG9wdGlvbiBsaW5rIHRvIHRyaWdnZXIgdGhlXG4gICAgICAgICAqIGNoYW5nZSBldmVudCBvZiB0aGUgYmVsb25naW5nIGhpZGRlbiBjaGVja2JveFxuICAgICAgICAgKlxuICAgICAgICAgKiBAcGFyYW0gZVxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9maWx0ZXJDbGlja0hhbmRsZXIgPSBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgdmFyIGlkID0gJCh0aGlzKS5hdHRyKCdyZWwnKTtcblxuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcblxuICAgICAgICAgICAgJCgnIycgKyBpZCkucHJvcCgnY2hlY2tlZCcsIHRydWUpLnRyaWdnZXIoJ2NoYW5nZScpO1xuICAgICAgICB9O1xuXG4vLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcbiAgICAgICAgICogQGNvbnN0cnVjdG9yXG4gICAgICAgICAqL1xuICAgICAgICBtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uIChkb25lKSB7XG4gICAgICAgICAgICAkcHJlbG9hZGVyID0gJHRoaXMuZmluZCgnLnByZWxvYWRlciwgLnByZWxvYWRlci1tZXNzYWdlJyk7XG4gICAgICAgICAgICAkY29udGVudFdyYXBwZXIgPSAkKCcubWFpbi1pbnNpZGUnKTtcbiAgICAgICAgICAgIGhpc3RvcnlBdmFpbGFibGUgPSBqc2UuY29yZS5jb25maWcuZ2V0KCdoaXN0b3J5Jyk7XG5cbiAgICAgICAgICAgIC8vIG5vIGF1dG8gdXBkYXRlIG9uIHN0YXJ0IHBhZ2VcbiAgICAgICAgICAgIGlmICgkKG9wdGlvbnMuc2VsZWN0b3JNYXBwaW5nLnByb2R1Y3RzQ29udGFpbmVyKS5sZW5ndGggPT09IDApIHtcbiAgICAgICAgICAgICAgICBvcHRpb25zLmF1dG9VcGRhdGUgPSBmYWxzZTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgJHRoaXNcbiAgICAgICAgICAgICAgICAub24oJ2NoYW5nZScsICdzZWxlY3QsIGlucHV0W3R5cGU9XCJjaGVja2JveFwiXSwgaW5wdXRbdHlwZT1cInRleHRcIl0nLCBfY2hhbmdlSGFuZGxlcilcbiAgICAgICAgICAgICAgICAub24oJ2NsaWNrJywgJy5idG4tbGluaycsIF9maWx0ZXJDbGlja0hhbmRsZXIpXG4gICAgICAgICAgICAgICAgLm9uKCdyZXNldCcsIF9yZXNldEhhbmRsZXIpXG4gICAgICAgICAgICAgICAgLm9uKCdzdWJtaXQnLCBfc3VibWl0SGFuZGxlcilcbiAgICAgICAgICAgICAgICAub24oJ2NsaWNrJywgJy5zaG93LW1vcmUnLCBfY2xpY2tIYW5kbGVyKTtcblxuICAgICAgICAgICAgJGJvZHkuYWRkQ2xhc3MoJ2ZpbHRlcmJveC1lbmFibGVkJyk7XG5cbiAgICAgICAgICAgIGRvbmUoKTtcbiAgICAgICAgfTtcblxuICAgICAgICAvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG4gICAgICAgIHJldHVybiBtb2R1bGU7XG4gICAgfSk7Il19
