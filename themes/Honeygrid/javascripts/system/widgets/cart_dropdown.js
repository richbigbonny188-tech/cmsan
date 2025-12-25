'use strict';

/* --------------------------------------------------------------
 cart_dropdown.js 2018-06-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Enables the functionality of the cart dropdown, to open
 * via an event. This is needed to open the flyout after
 * an item is added to the cart
 */
gambio.widgets.module('cart_dropdown', ['xhr', gambio.source + '/libs/events'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        $window = $(window),
        $body = $('body'),
        $item = null,
        $target = null,
        isCartDropdownSticky = false,
        timer = null,
        animateTimer = null,
        ajax = null,
        ajaxData = {
        part1: 'header',
        part2: 'dropdown'
    },
        defaults = {
        // Default delay (in ms) after which the flyout closes
        delay: 5000,
        // Update request url
        url: 'shop.php?do=CartDropdown',
        // Selection of the container the result gets filled in
        fillTarget: 'header',
        // Duration that the count badge gets resized after adding an item to the basket
        countAnimation: 2000,
        // AJAX response content selectors
        selectorMapping: {
            cartDropdown: '.cart-dropdown',
            cartDropdownProducts: '.products',
            cartDropdownProductsCount: '.cart-products-count'
        }
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## EVENT HANDLER ##########

    /**
     * Helper function that scroll the list
     * down to the end
     * @private
     */
    var _scrollDown = function _scrollDown() {
        var $list = $this.find('.products-list'),
            height = $list.outerHeight() * 2; // Multiply with 2 to be sure that it gets scrolled to the bottom

        $list.animate({ 'scrollTop': height + 'px' }, 0);
    };

    /**
     * Triggers the mouseenter event
     * on the cartdropdown link
     * @param       {object}        e       jQuery event object
     * @private
     */
    var _open = function _open(e) {
        e.stopPropagation();

        if ($(defaults.selectorMapping.cartDropdownProductsCount).text() !== '0') {
            $(defaults.selectorMapping.cartDropdownProductsCount).removeClass('hidden');
        }

        $item.trigger('mouseenter', { prog: true });
    };

    /**
     * Triggers the mouseleave event
     * on the cartdropdown link
     * @param       {object}        e       jQuery event object
     * @private
     */
    var _close = function _close(e) {
        e.stopPropagation();
        $item.trigger('mouseleave', { prog: true });
    };

    /**
     * Helper function that resizes the count badge
     * after the add of an item to the basket for
     * a specific duration
     * @param       {string}    selector        Text value of the old badge (the count)
     * @param       {object}    config          The config for the badges from the ajax result content
     * @private
     */
    var _resizeCountBadge = function _resizeCountBadge(currentCount, config) {
        if (options.selectorMapping[config.selector] === undefined) {
            jse.core.debug.warn('The selector mapping "' + config.selector + '" doesn\'t exist.');
            return true;
        }

        var count = $(config.value).text(),
            $counts = $target.find(options.selectorMapping[config.selector]);

        if (currentCount !== count) {
            if (animateTimer) {
                clearTimeout(animateTimer);
            }
            if (config.value > 99) {
                $counts.addClass('over99');
            } else {
                $counts.removeClass('over99');
            }

            $counts.addClass('big');
            animateTimer = setTimeout(function () {
                $counts.removeClass('big');
            }, options.countAnimation);
        }
    };

    /**
     * Updates the dropdown with data from
     * the server and opens the layer for a
     * certain time
     * @param       {object}        e               jQuery event object
     * @param       {boolean}       openDropdown    Defines if the dropdown shall be opened after update
     * @private
     */
    var _update = function _update(e, openDropdown) {
        if (ajax) {
            ajax.abort();
        }

        ajax = jse.libs.xhr.ajax({ url: options.url, data: ajaxData }).done(function (result) {
            if (options.selectorMapping[result.content.count.selector] === undefined) {
                jse.core.debug.warn('The selector mapping "' + result.content.count.selector + '" doesn\'t exist.');
                return true;
            }

            var count = $(options.selectorMapping[result.content.count.selector]).first().text();
            jse.libs.theme.helpers.fill(result.content, $target, options.selectorMapping);
            _resizeCountBadge(count, result.content.count);

            _scrollDown();

            if (openDropdown) {
                $this.trigger(jse.libs.theme.events.CART_OPEN(), []);
                timer = setTimeout(function () {
                    $this.trigger(jse.libs.theme.events.CART_CLOSE(), []);
                }, options.delay);
            }
        });
    };

    /**
     * Event handler that listens on the
     * mouseenter / leave events. If these
     * events are not triggered by this script
     * stop the timer, because the user has
     * moved the mouse cursor over the object
     * @param       {object}        e       jQuery event object
     * @param       {object}        d       JSON which contains the status if the program triggered the event
     * @private
     */
    var _preventExec = function _preventExec(e, d) {
        if ((!d || !d.prog) && timer) {
            clearTimeout(timer);
        }
    };

    /**
     * Sticky Cart Dropdown
     *
     * There are cases when the user adds something to the cart and this pops out but it cannot be seen cause
     * it is out of the viewport (e.g. user has scrolled to bottom). This method will make sure that the cart
     * dropdown is always visible by applying a "sticky" positioning to respective elements.
     *
     * @private
     */
    var _stickyCartDropdown = function _stickyCartDropdown() {
        // If the cart dropdown is not visible wait until the transition completes (see menu.js). 
        if (!$item.hasClass('open')) {
            var interval = setInterval(function () {
                if ($item.hasClass('open')) {
                    _stickyCartDropdown();
                    clearInterval(interval);
                }
            }, 100);

            isCartDropdownSticky = false;
            return;
        }

        var $cartDropdown = $(options.selectorMapping.cartDropdown);
        var cartDropdownOffset = $cartDropdown.offset();

        // Enable "sticky" position in order to make the cart dropdown visible to the user.
        if (!isCartDropdownSticky && cartDropdownOffset.top < $(window).scrollTop()) {
            $cartDropdown.css({
                position: 'fixed',
                top: 20,
                left: cartDropdownOffset.left
            });

            isCartDropdownSticky = true;
        }

        // Reset sticky position once the user has scrolled to top. 
        if (isCartDropdownSticky && cartDropdownOffset.top < $item.offset().top) {
            $cartDropdown.css({
                position: '',
                top: '',
                left: ''
            });

            isCartDropdownSticky = false;
        }
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     *
     * @constructor
     */
    module.init = function (done) {

        $item = $this.find('> ul > li');
        $target = options.fillTarget ? $(options.fillTarget) : $this;

        $window.on('scroll', _stickyCartDropdown);

        $body.on(jse.libs.theme.events.CART_OPEN(), _open).on(jse.libs.theme.events.CART_CLOSE(), _close).on(jse.libs.theme.events.CART_UPDATE(), _update);

        $item.on('mouseenter mouseleave', _preventExec).on('mouseenter', _stickyCartDropdown);

        _scrollDown();

        if (location.search.search('open_cart_dropdown=1') !== -1) {
            $body.trigger(jse.libs.theme.events.CART_OPEN());
        }

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvY2FydF9kcm9wZG93bi5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwic291cmNlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiR3aW5kb3ciLCJ3aW5kb3ciLCIkYm9keSIsIiRpdGVtIiwiJHRhcmdldCIsImlzQ2FydERyb3Bkb3duU3RpY2t5IiwidGltZXIiLCJhbmltYXRlVGltZXIiLCJhamF4IiwiYWpheERhdGEiLCJwYXJ0MSIsInBhcnQyIiwiZGVmYXVsdHMiLCJkZWxheSIsInVybCIsImZpbGxUYXJnZXQiLCJjb3VudEFuaW1hdGlvbiIsInNlbGVjdG9yTWFwcGluZyIsImNhcnREcm9wZG93biIsImNhcnREcm9wZG93blByb2R1Y3RzIiwiY2FydERyb3Bkb3duUHJvZHVjdHNDb3VudCIsIm9wdGlvbnMiLCJleHRlbmQiLCJfc2Nyb2xsRG93biIsIiRsaXN0IiwiZmluZCIsImhlaWdodCIsIm91dGVySGVpZ2h0IiwiYW5pbWF0ZSIsIl9vcGVuIiwiZSIsInN0b3BQcm9wYWdhdGlvbiIsInRleHQiLCJyZW1vdmVDbGFzcyIsInRyaWdnZXIiLCJwcm9nIiwiX2Nsb3NlIiwiX3Jlc2l6ZUNvdW50QmFkZ2UiLCJjdXJyZW50Q291bnQiLCJjb25maWciLCJzZWxlY3RvciIsInVuZGVmaW5lZCIsImpzZSIsImNvcmUiLCJkZWJ1ZyIsIndhcm4iLCJjb3VudCIsInZhbHVlIiwiJGNvdW50cyIsImNsZWFyVGltZW91dCIsImFkZENsYXNzIiwic2V0VGltZW91dCIsIl91cGRhdGUiLCJvcGVuRHJvcGRvd24iLCJhYm9ydCIsImxpYnMiLCJ4aHIiLCJkb25lIiwicmVzdWx0IiwiY29udGVudCIsImZpcnN0IiwidGhlbWUiLCJoZWxwZXJzIiwiZmlsbCIsImV2ZW50cyIsIkNBUlRfT1BFTiIsIkNBUlRfQ0xPU0UiLCJfcHJldmVudEV4ZWMiLCJkIiwiX3N0aWNreUNhcnREcm9wZG93biIsImhhc0NsYXNzIiwiaW50ZXJ2YWwiLCJzZXRJbnRlcnZhbCIsImNsZWFySW50ZXJ2YWwiLCIkY2FydERyb3Bkb3duIiwiY2FydERyb3Bkb3duT2Zmc2V0Iiwib2Zmc2V0IiwidG9wIiwic2Nyb2xsVG9wIiwiY3NzIiwicG9zaXRpb24iLCJsZWZ0IiwiaW5pdCIsIm9uIiwiQ0FSVF9VUERBVEUiLCJsb2NhdGlvbiIsInNlYXJjaCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7OztBQUtBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FDSSxlQURKLEVBR0ksQ0FDSSxLQURKLEVBRUlGLE9BQU9HLE1BQVAsR0FBZ0IsY0FGcEIsQ0FISixFQVFJLFVBQVVDLElBQVYsRUFBZ0I7O0FBRVo7O0FBRVI7O0FBRVEsUUFBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxRQUNJQyxVQUFVRCxFQUFFRSxNQUFGLENBRGQ7QUFBQSxRQUVJQyxRQUFRSCxFQUFFLE1BQUYsQ0FGWjtBQUFBLFFBR0lJLFFBQVEsSUFIWjtBQUFBLFFBSUlDLFVBQVUsSUFKZDtBQUFBLFFBS0lDLHVCQUF1QixLQUwzQjtBQUFBLFFBTUlDLFFBQVEsSUFOWjtBQUFBLFFBT0lDLGVBQWUsSUFQbkI7QUFBQSxRQVFJQyxPQUFPLElBUlg7QUFBQSxRQVNJQyxXQUFXO0FBQ1BDLGVBQU8sUUFEQTtBQUVQQyxlQUFPO0FBRkEsS0FUZjtBQUFBLFFBYUlDLFdBQVc7QUFDUDtBQUNBQyxlQUFPLElBRkE7QUFHUDtBQUNBQyxhQUFLLDBCQUpFO0FBS1A7QUFDQUMsb0JBQVksUUFOTDtBQU9QO0FBQ0FDLHdCQUFnQixJQVJUO0FBU1A7QUFDQUMseUJBQWlCO0FBQ2JDLDBCQUFjLGdCQUREO0FBRWJDLGtDQUFzQixXQUZUO0FBR2JDLHVDQUEyQjtBQUhkO0FBVlYsS0FiZjtBQUFBLFFBNkJJQyxVQUFVdEIsRUFBRXVCLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQlYsUUFBbkIsRUFBNkJmLElBQTdCLENBN0JkO0FBQUEsUUE4QklGLFNBQVMsRUE5QmI7O0FB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LElBQUlhLElBQUosQ0FBU00sS0FBVCxDQUFlRyxNQUFmLENBQXNCQyxTQUF0QixFQURSLEVBQzJDcEMsS0FEM0MsRUFFS3NELEVBRkwsQ0FFUXpDLElBQUlhLElBQUosQ0FBU00sS0FBVCxDQUFlRyxNQUFmLENBQXNCRSxVQUF0QixFQUZSLEVBRTRDOUIsTUFGNUMsRUFHSytDLEVBSEwsQ0FHUXpDLElBQUlhLElBQUosQ0FBU00sS0FBVCxDQUFlRyxNQUFmLENBQXNCb0IsV0FBdEIsRUFIUixFQUc2Q2hDLE9BSDdDOztBQUtBakQsY0FDS2dGLEVBREwsQ0FDUSx1QkFEUixFQUNpQ2hCLFlBRGpDLEVBRUtnQixFQUZMLENBRVEsWUFGUixFQUVzQmQsbUJBRnRCOztBQUlBOUM7O0FBRUEsWUFBSThELFNBQVNDLE1BQVQsQ0FBZ0JBLE1BQWhCLENBQXVCLHNCQUF2QixNQUFtRCxDQUFDLENBQXhELEVBQTJEO0FBQ3ZEcEYsa0JBQU1nQyxPQUFOLENBQWNRLElBQUlhLElBQUosQ0FBU00sS0FBVCxDQUFlRyxNQUFmLENBQXNCQyxTQUF0QixFQUFkO0FBQ0g7O0FBRURSO0FBQ0gsS0F4QkQ7O0FBMEJBO0FBQ0EsV0FBTzlELE1BQVA7QUFDSCxDQWpRTCIsImZpbGUiOiJ3aWRnZXRzL2NhcnRfZHJvcGRvd24uanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGNhcnRfZHJvcGRvd24uanMgMjAxOC0wNi0xM1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTggR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogRW5hYmxlcyB0aGUgZnVuY3Rpb25hbGl0eSBvZiB0aGUgY2FydCBkcm9wZG93biwgdG8gb3BlblxuICogdmlhIGFuIGV2ZW50LiBUaGlzIGlzIG5lZWRlZCB0byBvcGVuIHRoZSBmbHlvdXQgYWZ0ZXJcbiAqIGFuIGl0ZW0gaXMgYWRkZWQgdG8gdGhlIGNhcnRcbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuICAgICdjYXJ0X2Ryb3Bkb3duJyxcblxuICAgIFtcbiAgICAgICAgJ3hocicsXG4gICAgICAgIGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvZXZlbnRzJ1xuICAgIF0sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgdmFyICR0aGlzID0gJCh0aGlzKSxcbiAgICAgICAgICAgICR3aW5kb3cgPSAkKHdpbmRvdyksXG4gICAgICAgICAgICAkYm9keSA9ICQoJ2JvZHknKSxcbiAgICAgICAgICAgICRpdGVtID0gbnVsbCxcbiAgICAgICAgICAgICR0YXJnZXQgPSBudWxsLFxuICAgICAgICAgICAgaXNDYXJ0RHJvcGRvd25TdGlja3kgPSBmYWxzZSxcbiAgICAgICAgICAgIHRpbWVyID0gbnVsbCxcbiAgICAgICAgICAgIGFuaW1hdGVUaW1lciA9IG51bGwsXG4gICAgICAgICAgICBhamF4ID0gbnVsbCxcbiAgICAgICAgICAgIGFqYXhEYXRhID0ge1xuICAgICAgICAgICAgICAgIHBhcnQxOiAnaGVhZGVyJyxcbiAgICAgICAgICAgICAgICBwYXJ0MjogJ2Ryb3Bkb3duJ1xuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGRlZmF1bHRzID0ge1xuICAgICAgICAgICAgICAgIC8vIERlZmF1bHQgZGVsYXkgKGluIG1zKSBhZnRlciB3aGljaCB0aGUgZmx5b3V0IGNsb3Nlc1xuICAgICAgICAgICAgICAgIGRlbGF5OiA1MDAwLFxuICAgICAgICAgICAgICAgIC8vIFVwZGF0ZSByZXF1ZXN0IHVybFxuICAgICAgICAgICAgICAgIHVybDogJ3Nob3AucGhwP2RvPUNhcnREcm9wZG93bicsXG4gICAgICAgICAgICAgICAgLy8gU2VsZWN0aW9uIG9mIHRoZSBjb250YWluZXIgdGhlIHJlc3VsdCBnZXRzIGZpbGxlZCBpblxuICAgICAgICAgICAgICAgIGZpbGxUYXJnZXQ6ICdoZWFkZXInLFxuICAgICAgICAgICAgICAgIC8vIER1cmF0aW9uIHRoYXQgdGhlIGNvdW50IGJhZGdlIGdldHMgcmVzaXplZCBhZnRlciBhZGRpbmcgYW4gaXRlbSB0byB0aGUgYmFza2V0XG4gICAgICAgICAgICAgICAgY291bnRBbmltYXRpb246IDIwMDAsXG4gICAgICAgICAgICAgICAgLy8gQUpBWCByZXNwb25zZSBjb250ZW50IHNlbGVjdG9yc1xuICAgICAgICAgICAgICAgIHNlbGVjdG9yTWFwcGluZzoge1xuICAgICAgICAgICAgICAgICAgICBjYXJ0RHJvcGRvd246ICcuY2FydC1kcm9wZG93bicsXG4gICAgICAgICAgICAgICAgICAgIGNhcnREcm9wZG93blByb2R1Y3RzOiAnLnByb2R1Y3RzJyxcbiAgICAgICAgICAgICAgICAgICAgY2FydERyb3Bkb3duUHJvZHVjdHNDb3VudDogJy5jYXJ0LXByb2R1Y3RzLWNvdW50J1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcbiAgICAgICAgICAgIG1vZHVsZSA9IHt9O1xuXG5cbi8vICMjIyMjIyMjIyMgRVZFTlQgSEFORExFUiAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEhlbHBlciBmdW5jdGlvbiB0aGF0IHNjcm9sbCB0aGUgbGlzdFxuICAgICAgICAgKiBkb3duIHRvIHRoZSBlbmRcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfc2Nyb2xsRG93biA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHZhciAkbGlzdCA9ICR0aGlzLmZpbmQoJy5wcm9kdWN0cy1saXN0JyksXG4gICAgICAgICAgICAgICAgaGVpZ2h0ID0gJGxpc3Qub3V0ZXJIZWlnaHQoKSAqIDI7ICAgIC8vIE11bHRpcGx5IHdpdGggMiB0byBiZSBzdXJlIHRoYXQgaXQgZ2V0cyBzY3JvbGxlZCB0byB0aGUgYm90dG9tXG5cbiAgICAgICAgICAgICRsaXN0LmFuaW1hdGUoeydzY3JvbGxUb3AnOiBoZWlnaHQgKyAncHgnfSwgMCk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFRyaWdnZXJzIHRoZSBtb3VzZWVudGVyIGV2ZW50XG4gICAgICAgICAqIG9uIHRoZSBjYXJ0ZHJvcGRvd24gbGlua1xuICAgICAgICAgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9vcGVuID0gZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cbiAgICAgICAgICAgIGlmICgkKGRlZmF1bHRzLnNlbGVjdG9yTWFwcGluZy5jYXJ0RHJvcGRvd25Qcm9kdWN0c0NvdW50KS50ZXh0KCkgIT09ICcwJykge1xuICAgICAgICAgICAgICAgICQoZGVmYXVsdHMuc2VsZWN0b3JNYXBwaW5nLmNhcnREcm9wZG93blByb2R1Y3RzQ291bnQpLnJlbW92ZUNsYXNzKCdoaWRkZW4nKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgJGl0ZW0udHJpZ2dlcignbW91c2VlbnRlcicsIHtwcm9nOiB0cnVlfSk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFRyaWdnZXJzIHRoZSBtb3VzZWxlYXZlIGV2ZW50XG4gICAgICAgICAqIG9uIHRoZSBjYXJ0ZHJvcGRvd24gbGlua1xuICAgICAgICAgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9jbG9zZSA9IGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICBlLnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgICAgICAgJGl0ZW0udHJpZ2dlcignbW91c2VsZWF2ZScsIHtwcm9nOiB0cnVlfSk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEhlbHBlciBmdW5jdGlvbiB0aGF0IHJlc2l6ZXMgdGhlIGNvdW50IGJhZGdlXG4gICAgICAgICAqIGFmdGVyIHRoZSBhZGQgb2YgYW4gaXRlbSB0byB0aGUgYmFza2V0IGZvclxuICAgICAgICAgKiBhIHNwZWNpZmljIGR1cmF0aW9uXG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7c3RyaW5nfSAgICBzZWxlY3RvciAgICAgICAgVGV4dCB2YWx1ZSBvZiB0aGUgb2xkIGJhZGdlICh0aGUgY291bnQpXG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICBjb25maWcgICAgICAgICAgVGhlIGNvbmZpZyBmb3IgdGhlIGJhZGdlcyBmcm9tIHRoZSBhamF4IHJlc3VsdCBjb250ZW50XG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3Jlc2l6ZUNvdW50QmFkZ2UgPSBmdW5jdGlvbiAoY3VycmVudENvdW50LCBjb25maWcpIHtcbiAgICAgICAgICAgIGlmIChvcHRpb25zLnNlbGVjdG9yTWFwcGluZ1tjb25maWcuc2VsZWN0b3JdID09PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICAgICAgICBqc2UuY29yZS5kZWJ1Zy53YXJuKCdUaGUgc2VsZWN0b3IgbWFwcGluZyBcIicgKyBjb25maWcuc2VsZWN0b3IgKyAnXCIgZG9lc25cXCd0IGV4aXN0LicpO1xuICAgICAgICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB2YXIgY291bnQgPSAkKGNvbmZpZy52YWx1ZSkudGV4dCgpLFxuICAgICAgICAgICAgICAgICRjb3VudHMgPSAkdGFyZ2V0LmZpbmQob3B0aW9ucy5zZWxlY3Rvck1hcHBpbmdbY29uZmlnLnNlbGVjdG9yXSk7XG5cbiAgICAgICAgICAgIGlmIChjdXJyZW50Q291bnQgIT09IGNvdW50KSB7XG4gICAgICAgICAgICAgICAgaWYgKGFuaW1hdGVUaW1lcikge1xuICAgICAgICAgICAgICAgICAgICBjbGVhclRpbWVvdXQoYW5pbWF0ZVRpbWVyKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgaWYoY29uZmlnLnZhbHVlID4gOTkpIHtcbiAgICAgICAgICAgICAgICAgICAgJGNvdW50cy5hZGRDbGFzcygnb3Zlcjk5Jyk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgJGNvdW50cy5yZW1vdmVDbGFzcygnb3Zlcjk5Jyk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgJGNvdW50cy5hZGRDbGFzcygnYmlnJyk7XG4gICAgICAgICAgICAgICAgYW5pbWF0ZVRpbWVyID0gc2V0VGltZW91dChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICRjb3VudHMucmVtb3ZlQ2xhc3MoJ2JpZycpO1xuICAgICAgICAgICAgICAgIH0sIG9wdGlvbnMuY291bnRBbmltYXRpb24pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBVcGRhdGVzIHRoZSBkcm9wZG93biB3aXRoIGRhdGEgZnJvbVxuICAgICAgICAgKiB0aGUgc2VydmVyIGFuZCBvcGVucyB0aGUgbGF5ZXIgZm9yIGFcbiAgICAgICAgICogY2VydGFpbiB0aW1lXG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgZSAgICAgICAgICAgICAgIGpRdWVyeSBldmVudCBvYmplY3RcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtib29sZWFufSAgICAgICBvcGVuRHJvcGRvd24gICAgRGVmaW5lcyBpZiB0aGUgZHJvcGRvd24gc2hhbGwgYmUgb3BlbmVkIGFmdGVyIHVwZGF0ZVxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF91cGRhdGUgPSBmdW5jdGlvbiAoZSwgb3BlbkRyb3Bkb3duKSB7XG4gICAgICAgICAgICBpZiAoYWpheCkge1xuICAgICAgICAgICAgICAgIGFqYXguYWJvcnQoKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgYWpheCA9IGpzZS5saWJzLnhoci5hamF4KHt1cmw6IG9wdGlvbnMudXJsLCBkYXRhOiBhamF4RGF0YX0pLmRvbmUoZnVuY3Rpb24gKHJlc3VsdCkge1xuICAgICAgICAgICAgICAgIGlmIChvcHRpb25zLnNlbGVjdG9yTWFwcGluZ1tyZXN1bHQuY29udGVudC5jb3VudC5zZWxlY3Rvcl0gPT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgICAgICBqc2UuY29yZS5kZWJ1Zy53YXJuKCdUaGUgc2VsZWN0b3IgbWFwcGluZyBcIicgKyByZXN1bHQuY29udGVudC5jb3VudC5zZWxlY3RvciArICdcIiBkb2VzblxcJ3QgZXhpc3QuJyk7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHZhciBjb3VudCA9ICQob3B0aW9ucy5zZWxlY3Rvck1hcHBpbmdbcmVzdWx0LmNvbnRlbnQuY291bnQuc2VsZWN0b3JdKS5maXJzdCgpLnRleHQoKTtcbiAgICAgICAgICAgICAgICBqc2UubGlicy50aGVtZS5oZWxwZXJzLmZpbGwocmVzdWx0LmNvbnRlbnQsICR0YXJnZXQsIG9wdGlvbnMuc2VsZWN0b3JNYXBwaW5nKTtcbiAgICAgICAgICAgICAgICBfcmVzaXplQ291bnRCYWRnZShjb3VudCwgcmVzdWx0LmNvbnRlbnQuY291bnQpO1xuXG4gICAgICAgICAgICAgICAgX3Njcm9sbERvd24oKTtcblxuICAgICAgICAgICAgICAgIGlmIChvcGVuRHJvcGRvd24pIHtcbiAgICAgICAgICAgICAgICAgICAgJHRoaXMudHJpZ2dlcihqc2UubGlicy50aGVtZS5ldmVudHMuQ0FSVF9PUEVOKCksIFtdKTtcbiAgICAgICAgICAgICAgICAgICAgdGltZXIgPSBzZXRUaW1lb3V0KGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICR0aGlzLnRyaWdnZXIoanNlLmxpYnMudGhlbWUuZXZlbnRzLkNBUlRfQ0xPU0UoKSwgW10pO1xuICAgICAgICAgICAgICAgICAgICB9LCBvcHRpb25zLmRlbGF5KTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogRXZlbnQgaGFuZGxlciB0aGF0IGxpc3RlbnMgb24gdGhlXG4gICAgICAgICAqIG1vdXNlZW50ZXIgLyBsZWF2ZSBldmVudHMuIElmIHRoZXNlXG4gICAgICAgICAqIGV2ZW50cyBhcmUgbm90IHRyaWdnZXJlZCBieSB0aGlzIHNjcmlwdFxuICAgICAgICAgKiBzdG9wIHRoZSB0aW1lciwgYmVjYXVzZSB0aGUgdXNlciBoYXNcbiAgICAgICAgICogbW92ZWQgdGhlIG1vdXNlIGN1cnNvciBvdmVyIHRoZSBvYmplY3RcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3RcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICBkICAgICAgIEpTT04gd2hpY2ggY29udGFpbnMgdGhlIHN0YXR1cyBpZiB0aGUgcHJvZ3JhbSB0cmlnZ2VyZWQgdGhlIGV2ZW50XG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3ByZXZlbnRFeGVjID0gZnVuY3Rpb24gKGUsIGQpIHtcbiAgICAgICAgICAgIGlmICgoIWQgfHwgIWQucHJvZykgJiYgdGltZXIpIHtcbiAgICAgICAgICAgICAgICBjbGVhclRpbWVvdXQodGltZXIpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBTdGlja3kgQ2FydCBEcm9wZG93blxuICAgICAgICAgKlxuICAgICAgICAgKiBUaGVyZSBhcmUgY2FzZXMgd2hlbiB0aGUgdXNlciBhZGRzIHNvbWV0aGluZyB0byB0aGUgY2FydCBhbmQgdGhpcyBwb3BzIG91dCBidXQgaXQgY2Fubm90IGJlIHNlZW4gY2F1c2VcbiAgICAgICAgICogaXQgaXMgb3V0IG9mIHRoZSB2aWV3cG9ydCAoZS5nLiB1c2VyIGhhcyBzY3JvbGxlZCB0byBib3R0b20pLiBUaGlzIG1ldGhvZCB3aWxsIG1ha2Ugc3VyZSB0aGF0IHRoZSBjYXJ0XG4gICAgICAgICAqIGRyb3Bkb3duIGlzIGFsd2F5cyB2aXNpYmxlIGJ5IGFwcGx5aW5nIGEgXCJzdGlja3lcIiBwb3NpdGlvbmluZyB0byByZXNwZWN0aXZlIGVsZW1lbnRzLlxuICAgICAgICAgKlxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9zdGlja3lDYXJ0RHJvcGRvd24gPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAvLyBJZiB0aGUgY2FydCBkcm9wZG93biBpcyBub3QgdmlzaWJsZSB3YWl0IHVudGlsIHRoZSB0cmFuc2l0aW9uIGNvbXBsZXRlcyAoc2VlIG1lbnUuanMpLiBcbiAgICAgICAgICAgIGlmICghJGl0ZW0uaGFzQ2xhc3MoJ29wZW4nKSkge1xuICAgICAgICAgICAgICAgIHZhciBpbnRlcnZhbCA9IHNldEludGVydmFsKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKCRpdGVtLmhhc0NsYXNzKCdvcGVuJykpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIF9zdGlja3lDYXJ0RHJvcGRvd24oKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNsZWFySW50ZXJ2YWwoaW50ZXJ2YWwpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSwgMTAwKTtcblxuICAgICAgICAgICAgICAgIGlzQ2FydERyb3Bkb3duU3RpY2t5ID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB2YXIgJGNhcnREcm9wZG93biA9ICQob3B0aW9ucy5zZWxlY3Rvck1hcHBpbmcuY2FydERyb3Bkb3duKTtcbiAgICAgICAgICAgIHZhciBjYXJ0RHJvcGRvd25PZmZzZXQgPSAkY2FydERyb3Bkb3duLm9mZnNldCgpO1xuXG4gICAgICAgICAgICAvLyBFbmFibGUgXCJzdGlja3lcIiBwb3NpdGlvbiBpbiBvcmRlciB0byBtYWtlIHRoZSBjYXJ0IGRyb3Bkb3duIHZpc2libGUgdG8gdGhlIHVzZXIuXG4gICAgICAgICAgICBpZiAoIWlzQ2FydERyb3Bkb3duU3RpY2t5ICYmIGNhcnREcm9wZG93bk9mZnNldC50b3AgPCAkKHdpbmRvdykuc2Nyb2xsVG9wKCkpIHtcbiAgICAgICAgICAgICAgICAkY2FydERyb3Bkb3duLmNzcyh7XG4gICAgICAgICAgICAgICAgICAgIHBvc2l0aW9uOiAnZml4ZWQnLFxuICAgICAgICAgICAgICAgICAgICB0b3A6IDIwLFxuICAgICAgICAgICAgICAgICAgICBsZWZ0OiBjYXJ0RHJvcGRvd25PZmZzZXQubGVmdFxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgaXNDYXJ0RHJvcGRvd25TdGlja3kgPSB0cnVlO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvLyBSZXNldCBzdGlja3kgcG9zaXRpb24gb25jZSB0aGUgdXNlciBoYXMgc2Nyb2xsZWQgdG8gdG9wLiBcbiAgICAgICAgICAgIGlmIChpc0NhcnREcm9wZG93blN0aWNreSAmJiBjYXJ0RHJvcGRvd25PZmZzZXQudG9wIDwgJGl0ZW0ub2Zmc2V0KCkudG9wKSB7XG4gICAgICAgICAgICAgICAgJGNhcnREcm9wZG93bi5jc3Moe1xuICAgICAgICAgICAgICAgICAgICBwb3NpdGlvbjogJycsXG4gICAgICAgICAgICAgICAgICAgIHRvcDogJycsXG4gICAgICAgICAgICAgICAgICAgIGxlZnQ6ICcnXG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICBpc0NhcnREcm9wZG93blN0aWNreSA9IGZhbHNlO1xuICAgICAgICAgICAgfVxuICAgICAgICB9O1xuXG5cbi8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcbiAgICAgICAgICpcbiAgICAgICAgICogQGNvbnN0cnVjdG9yXG4gICAgICAgICAqL1xuICAgICAgICBtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uIChkb25lKSB7XG5cbiAgICAgICAgICAgICRpdGVtID0gJHRoaXMuZmluZCgnPiB1bCA+IGxpJyk7XG4gICAgICAgICAgICAkdGFyZ2V0ID0gb3B0aW9ucy5maWxsVGFyZ2V0ID8gJChvcHRpb25zLmZpbGxUYXJnZXQpIDogJHRoaXM7XG5cbiAgICAgICAgICAgICR3aW5kb3dcbiAgICAgICAgICAgICAgICAub24oJ3Njcm9sbCcsIF9zdGlja3lDYXJ0RHJvcGRvd24pO1xuXG4gICAgICAgICAgICAkYm9keVxuICAgICAgICAgICAgICAgIC5vbihqc2UubGlicy50aGVtZS5ldmVudHMuQ0FSVF9PUEVOKCksIF9vcGVuKVxuICAgICAgICAgICAgICAgIC5vbihqc2UubGlicy50aGVtZS5ldmVudHMuQ0FSVF9DTE9TRSgpLCBfY2xvc2UpXG4gICAgICAgICAgICAgICAgLm9uKGpzZS5saWJzLnRoZW1lLmV2ZW50cy5DQVJUX1VQREFURSgpLCBfdXBkYXRlKTtcblxuICAgICAgICAgICAgJGl0ZW1cbiAgICAgICAgICAgICAgICAub24oJ21vdXNlZW50ZXIgbW91c2VsZWF2ZScsIF9wcmV2ZW50RXhlYylcbiAgICAgICAgICAgICAgICAub24oJ21vdXNlZW50ZXInLCBfc3RpY2t5Q2FydERyb3Bkb3duKTtcblxuICAgICAgICAgICAgX3Njcm9sbERvd24oKTtcblxuICAgICAgICAgICAgaWYgKGxvY2F0aW9uLnNlYXJjaC5zZWFyY2goJ29wZW5fY2FydF9kcm9wZG93bj0xJykgIT09IC0xKSB7XG4gICAgICAgICAgICAgICAgJGJvZHkudHJpZ2dlcihqc2UubGlicy50aGVtZS5ldmVudHMuQ0FSVF9PUEVOKCkpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBkb25lKCk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLy8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuICAgICAgICByZXR1cm4gbW9kdWxlO1xuICAgIH0pO1xuIl19
