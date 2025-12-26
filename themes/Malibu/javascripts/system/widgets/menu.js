'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/* --------------------------------------------------------------
 menu.js 2023-10-04
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * This widget handles the horizontal menu/dropdown functionality.
 *
 * It's used for the top category navigation, the cart dropdown or the top menu (for example). It is
 * able to re-order the menu entries to a special "More" submenu to save space if the entries don't
 * fit in the current view. It's also able to work with different event types for opening/closing menu
 * items in the different view types.
 */
gambio.widgets.module('menu', [gambio.source + '/libs/events', gambio.source + '/libs/responsive', gambio.source + '/libs/interaction'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        $window = $(window),
        $body = $('body'),
        $list = null,
        $entries = null,
        $more = null,
        $moreEntries = null,
        $menuEntries = null,
        $custom = null,
        $categories = null,
        touchEvents = null,
        currentWidth = null,
        mode = null,
        mobile = false,
        enterTimer = null,
        leaveTimer = null,
        initializedPos = false,
        onEnter = false,
        toucheStartEvent = null,
        toucheEndEvent = null,
        transition = {},
        isTouchDevice = Modernizr.touchevents || navigator.userAgent.search(/Touch/i) !== -1,
        defaults = {
        // The menu type must be either 'horizontal' or 'vertical'
        menuType: 'horizontal',

        // Vertical menu options.
        unfoldLevel: 0,
        accordion: false,
        showAllLink: false,

        // Minimum breakpoint to switch to mobile view
        breakpoint: 40,
        // Delay in ms after a mouseenter the element gets shown
        enterDelay: 0,
        // Delay in ms after a mouseleave an element gets hidden
        leaveDelay: 50,
        // Tolerance in px which gets substracted from the nav-width to prevent flickering
        widthTolerance: 10,
        // Class that gets added to an opened menu list item
        openClass: 'open',
        // If true, elements get moved from/to the more menu if there isn't enough space
        switchElementPosition: true,
        // Ignore menu functionality on elements inside this selection
        ignoreClass: 'ignore-menu',
        // Tolerance in px which is allowed for a "click" event on touch
        touchMoveTolerance: 10,
        // If true, the li with the active class gets opened
        openActive: false,
        events: {
            // Event types that open the menus in desktop view.
            // Possible values: ['click']; ['hover']; ['touch', 'hover']; ['click', 'hover']
            desktop: ['touch', 'hover'],
            // Event types that open the menus in mobile view.
            // Possible values: ['click']; ['hover']; ['touch', 'hover']; ['click', 'hover']; ['touch', 'click']
            mobile: ['touch', 'click']
        }
    },
        options = $.extend({}, defaults, data),
        module = {};

    // ########## HELPER FUNCTIONS ##########

    /**
     * Helper function to calculate the tolerance
     * between the touchstart and touchend event.
     * If the max tolarance is exceeded return true
     * @param       {object}        e       jQuery event object
     * @return     {boolean}               If true it is a move event
     * @private
     */
    var _touchMoveDetect = function _touchMoveDetect() {
        toucheEndEvent = toucheEndEvent || toucheStartEvent;
        var diff = Math.abs(toucheEndEvent.event.originalEvent.pageY - toucheStartEvent.event.originalEvent.pageY);
        toucheEndEvent = null;
        return diff > options.touchMoveTolerance;
    };

    /**
     * Updates the jQuery selection, because the
     * list elements can be moved
     *
     * @private
     */
    var _getSelections = function _getSelections() {
        $list = $this.children('ul');
        // Exclude the ".navbar-topbar-item" elements because they
        // are cloned to this menu and are only shown in mobile view
        $entries = $list.children('li').not('.navbar-topbar-item');
        $more = $entries.filter('.dropdown-more');
        $moreEntries = $more.children('ul');
        $custom = $entries.filter('.custom');
        $menuEntries = $entries.not($more);
        $categories = $menuEntries.not($custom);
    };

    /**
     * Helper function that detaches an element from the
     * menu and attaches it to the correct position at
     * the target
     * @param       {object}    $item       jQuery selection of the item that gets detached / attached
     * @param       {object}    $target     jQuery selection of the target container
     * @private
     */
    var _setItem = function _setItem($item, $target) {
        var positionId = $item.data('position'),
            done = false;

        // Look for the first item that has a higher
        // positionId that the item and insert it
        // before the found entry
        $target.children().each(function () {
            var $self = $(this),
                position = $self.data('position');

            if (position > positionId) {
                $self.before($item.detach());
                done = true;
                return false;
            }
        });

        // Append the item if the positionId has
        // a higher value as the last item int the
        // target
        if (!done) {
            $target.append($item);
        }
    };

    /**
     * Helper function that checks which elements needs
     * to be added to the menu. Every element that needs
     * to be added gets passed to the function
     * "_setItem"
     * @param       {integer}       diff        Amount of pixels that were free
     * @private
     */
    var _addElement = function _addElement(diff) {

        var done = false;

        /**
         * Helper function that loops through the elements
         * and tries to add the elements to the menu if
         * it would fit.
         * @param       {object}    $elements       jQuery selection of the entries inside the more-menu
         * @private
         */
        var _showElements = function _showElements($elements) {
            $elements.each(function () {
                var $self = $(this),
                    width = $self.data().width;

                if (diff > width) {
                    // Add the item to the menu
                    _setItem($self, $list);
                    diff -= width;
                } else {
                    // The next item wouldn't fit anymore',
                    // quit the loop
                    done = true;
                    return false;
                }
            });
        };

        // Update the selection of the visible menu items.
        _getSelections();

        // Add the content manager entries to the menu first.
        // If there is still space, add the "normal" category
        // items also
        _showElements($moreEntries.children('.custom'));
        if (!done) {
            _showElements($moreEntries.children());
        }

        // Check if the items still in the more menu
        // would fit inside the main menu if the more
        // menu would get hidden
        var width = 0;
        $moreEntries.children().each(function () {
            width += $(this).data().width;
        });

        if (width === 0) {
            $more.hide();
        } else if (width < $more.data().width + diff) {
            $more.hide();
            diff += $more.data().width;
            _showElements($moreEntries.children());
        }
    };

    /**
     * Helper function that checks which elements needs
     * to be removed from the menu, so that it fits
     * inside one menu line. Every element that needs
     * to be removed gets passed to the function
     * "_setItem"
     * @param       {integer}       diff        Amount of pixels that needs to be saved
     * @private
     */
    var _removeElement = function _removeElement(diff) {

        var done = false;

        /**
         * Helper function that contains the check
         * loop for determining which elements
         * needs to be removed
         * @param           {object}    $elements       jQuery selection of the menu items
         * @private
         */
        var _hideElements = function _hideElements($elements) {
            $elements.each(function () {
                var $self = $(this),
                    width = $self.data().width;

                // Remove the possibly set open state
                $self.filter('.' + options.openClass).add($self.find('.' + options.openClass)).removeClass(options.openClass);

                // Add the entry to the more-menu
                _setItem($self, $moreEntries);

                diff -= width;

                if (diff < 0) {
                    // Enough elements are removed,
                    // quit the loop
                    done = true;
                    return false;
                }
            });
        };

        // Update the selection of the visible menu items
        _getSelections();

        // Add the width of the more entry if it's not
        // visible, because it will get shown during this
        // function call
        if ($more.is(':hidden')) {
            diff += $more.data().width;
            $more.removeClass('style');
            $more.show();
        }

        // First remove "normal" category entries. If that
        // isn't enough remove the content manager entries also
        _hideElements($($categories.get().reverse()));
        if (!done) {
            _hideElements($($custom.get().reverse()));
        }
    };

    /**
     * Sets a data attribute to the menu items
     * that contains the width of the elements.
     * This is needed because if it is display
     * none the detected with will be zero. It
     * sets position id also.
     * @private
     */
    var _initElementSizesAndPosition = function _initElementSizesAndPosition() {
        $entries.each(function (i) {
            var $self = $(this),
                width = $self.outerWidth();

            $self.data({ width: width, position: i });
        });
    };

    /**
     * Helper function to close all menu entries.
     * Needed for the desktop <-> mobile view
     * change, mostly.
     * @private
     */
    var _closeMenu = function _closeMenu(e) {
        $this.find('li.' + options.openClass).each(function () {
            if ($(this).parents('.navbar-categories-left').length > 0) {
                return true;
            }
            $(this).removeClass(options.openClass);
        });

        var isObject = (typeof e === 'undefined' ? 'undefined' : _typeof(e)) === 'object',
            isEvent = isObject ? e.hasOwnProperty('originalEvent') : false;
        if (isEvent) {
            e.stopPropagation();
            e.preventDefault();
        }
    };

    /**
     * Helper function to clear all pending
     * functions
     * @private
     */
    var _clearTimeouts = function _clearTimeouts() {
        enterTimer = enterTimer ? clearTimeout(enterTimer) : null;
        leaveTimer = leaveTimer ? clearTimeout(leaveTimer) : null;
    };

    /**
     * Helper function to reset the css of the menu.
     * This is needed to remove the overflow & height
     * settings of the menu of the css file. The
     * directives were set to prevent flickering on page
     * load
     * @private
     */
    var _resetInitialCss = function _resetInitialCss() {
        $this.css({
            'overflow': 'visible'
        });
    };

    /**
     * Helper function to set positioning classes
     * to the opend flyout. This is needed to keep
     * the flyout inside the boundaries of the navigation
     * @private
     */
    var _repositionOpenLayer = function _repositionOpenLayer() {
        var listWidth = $list.width(),
            $openLayer = $entries.filter('.' + options.openClass).children('ul');

        $openLayer.each(function () {
            var $self = $(this),
                $parent = $self.parent();

            // Reset the classes to prevent wrong calculation due to special styles
            $parent.removeClass('flyout-right flyout-left flyout-center flyout-wont-fit');

            var width = $self.outerWidth(),
                parentPosition = $parent.position().left,
                parentWidth = $parent.outerWidth();

            // Check witch class needs to be set
            if (listWidth > parentPosition + width) {
                $parent.addClass('flyout-right');
            } else if (parentPosition + parentWidth - width > 0) {
                $parent.addClass('flyout-left');
            } else if (width < listWidth) {
                $parent.addClass('flyout-center');
            } else {
                $parent.addClass('flyout-wont-fit');
            }
        });
    };

    /**
     * Helper function to calculate the difference between
     * the size of the visible elements in the menu and the
     * container size. If there is space, it calls the function
     * to activate an menu entry else it calls the function to
     * deactivate a menu entry
     * @param       {object}    e         jQuery event object
     * @param       {string}    eventName Event name parameter of the event object
     * @private
     */
    var _updateCategoryMenu = function _updateCategoryMenu(e, eventName) {
        var containerWidth = $this.innerWidth() - options.widthTolerance,
            width = 0;

        // Check if the container width has changed since last call
        if (options.menuType === 'horizontal' && (currentWidth !== containerWidth || eventName === 'switchedToDesktop')) {

            $list.children(':visible').each(function () {
                width += $(this).data('width');
            });

            // Add or remove elements depending on the size of the
            // visible elements
            if (containerWidth < width) {
                _removeElement(width - containerWidth);
            } else {
                _addElement(containerWidth - width);
            }

            _repositionOpenLayer();

            currentWidth = containerWidth;
        }
    };

    /**
     * Helper function to switch to the mobile
     * mode of the menu.
     * @private
     */
    var _switchToMobileView = function _switchToMobileView() {
        // Reset the current width so that
        // the "_updateCategoryMenu" will
        // perform correctly on the next view
        // change to desktop
        currentWidth = -1;
        _addElement(99999999);

        $('.level-1').css('padding-bottom', '200px'); // This padding corrects expand/collapse behavior of lower menu items in various mobile browsers. 

        // Use the vertical menu on mobile view.
        if (options.menuType === 'vertical') {
            // fixes display horizontal menu after a switch to mobile and back to desktop is performed
            if ($('#categories nav.navbar-default:first').not('.nav-categories-left').length > 0) {
                $('#categories nav.navbar-default:first').css({
                    opacity: 0,
                    height: 0
                }).children().hide();
            }

            // move topmenu-content items from horizontal menu to vertical menu
            $this.find('ul.level-1 li.navbar-topbar-item:first').before($('#categories nav.navbar-default li.topmenu-content').detach());

            $this.appendTo('#categories > .navbar-collapse');
            $this.addClass('navbar-default navbar-categories');
            $this.find('ul.level-1').addClass('navbar-nav');
            $this.find('.navbar-topbar-item').not('.topbar-search').show();

            _bindHorizontalEventHandlers();

            $body.trigger(jse.libs.theme.events.MENU_REPOSITIONED(), ['switchedToMobile']);
        }
    };

    /**
     * Helper function to switch to the desktop
     * mode of the menu. Additionally, in case that
     * the desktop mode is shown for the first time
     * set the position and width of the elements
     * @private
     */
    var _switchToDesktopView = function _switchToDesktopView() {
        $('.level-1').css('padding-bottom', ''); // Reset display fix for mobile browsers.

        // Revert all the changes made during the switch to mobile.
        if (options.menuType === 'vertical') {
            // fixes display horizontal menu after a switch to mobile and back to desktop is performed
            if ($('#categories nav.navbar-default:first').not('.nav-categories-left').length > 0) {
                $('#categories nav.navbar-default:first').css({
                    opacity: 1,
                    height: 'auto'
                }).children().show();
            }

            // move topmenu-content items back to horizontal menu
            var $topmenuContentElements = $this.find('li.topmenu-content').detach();
            $('#categories nav.navbar-default ul.level-1:first').append($topmenuContentElements);

            $this.appendTo('.box-categories');
            $this.removeClass('navbar-default navbar-categories');
            $this.find('ul.level-1').removeClass('navbar-nav');
            $this.find('.navbar-topbar-item').hide();
            _unbindHorizontalEventHandlers();

            $body.trigger(jse.libs.theme.events.MENU_REPOSITIONED(), ['switchedToDesktop']);
        }

        if (!initializedPos) {
            _initElementSizesAndPosition();
            initializedPos = true;
        }

        if (options.menuType === 'horizontal') {
            _updateCategoryMenu();

            if (isTouchDevice) {
                $list.find('.enter-category').show();
                $list.find('.dropdown > a').click(function (e) {
                    e.preventDefault();
                });
            }
        }
    };

    /**
     * Helper function to add the class to the li-element
     * depending on the open event. This can be a "touch"
     * or a "mouse" class
     * @param       {object}    $target         jQuery selection of the li-element
     * @param       {string}    className       Name of the class that gets added
     * @private
     */
    var _setEventTypeClass = function _setEventTypeClass($target, className) {
        $target.removeClass('touch mouse').addClass(className || '');
    };

    // ########## MAIN FUNCTIONALITY ##########

    /**
     * Function that gets called by the breakpoint trigger
     * (which is fired on browser resize). It checks for
     * CSS view changes and reconfigures the the JS behaviour
     * of the menu in that case
     * @private
     */
    var _breakpointHandler = function _breakpointHandler() {

        // Get the current viewmode
        var oldMode = mode || {},
            newMode = jse.libs.theme.responsive.breakpoint();

        // Only do something if the view was changed
        if (newMode.id !== oldMode.id) {

            // Check if a view change between mobile and desktop view was made
            var switchToMobile = newMode.id <= options.breakpoint && (!mobile || oldMode.id === undefined),
                switchToDesktop = newMode.id > options.breakpoint && (mobile || oldMode.id === undefined);

            // Store the new view settings
            mobile = newMode.id <= options.breakpoint;
            mode = $.extend({}, newMode);

            if (switchToMobile || switchToDesktop) {
                _clearTimeouts();
                if (options.menuType !== 'vertical') {
                    _closeMenu();
                }

                // Change the visibility of the menu items
                // in case of desktop <-> mobile view change
                if (options.switchElementPosition) {
                    if (switchToMobile) {
                        _switchToMobileView();
                    } else {
                        _switchToDesktopView();
                    }
                } else {
                    _repositionOpenLayer();
                }
            } else if (!mobile && options.switchElementPosition) {
                // Update the visibility of the menu items
                // if the view change was desktop to desktop only
                _updateCategoryMenu();
            } else if (!mobile) {
                _repositionOpenLayer();
            }
        }
    };

    // ######### EVENT HANDLER ##########

    /**
     * Changes the epand / collapse state of the menu,
     * if there is an submenu. In the other case it
     * will let execute the default action (most times
     * the execution of a link)
     * @param {object}  e       jQuery event object
     * @param {string}  mode    The current view mode (can be "mobile" or "desktop"
     * @param {integer} delay   Custom delay (in ms) for opening closing the menu (needed for click / touch events)
     * @private
     */
    var _openMenu = function _openMenu(e, type, delay) {

        var $self = $(this),
            $submenu = $self.children("ul.dropdown-menu"),
            length = $submenu.length,
            level = $submenu.length ? $submenu.data('level') || '0' : 99,
            validSubmenu = parseInt(level, 10) <= 2 && mode.id > options.breakpoint || mode.id <= options.breakpoint;

        if (type === 'mobile') {
            e.stopPropagation();
        }

        // Only change the state if there is
        // a submenu
        if (length && validSubmenu) {
            e.preventDefault();

            if (type === 'mobile') {
                // Simply toggle the openClass in mobile mode
                $self.toggleClass(options.openClass);
            } else {
                // Perform the else case for the desktop view

                var visible = $self.hasClass(options.openClass),
                    leave = $self.hasClass('leave'),
                    action = e.data && e.data.action ? e.data.action : visible && leave ? 'enter' : visible ? 'leave' : 'enter';

                // Prevent opening the menu if the user is searching
                var $liveSearchField = $('.navbar-search-input-group .form-control.search-input');
                if ($liveSearchField.length && $liveSearchField.is(":focus")) {
                    return;
                }

                // Depending on the visibility and the event-action-parameter
                // the submenu gets opened or closed
                switch (action) {
                    case 'enter':
                        if (!onEnter && !jse.libs.theme.interaction.isMouseDown()) {
                            onEnter = true;
                            // Set a timer for opening if the submenu (delayed opening)
                            _clearTimeouts();
                            enterTimer = setTimeout(function () {

                                // Remove all openClass-classes from the
                                // menu except the element to open and it's parents
                                $list.find('.' + options.openClass).not($self).not($self.parentsUntil($this, '.' + options.openClass)).trigger(jse.libs.theme.events.TRANSITION_STOP(), []).removeClass(options.openClass);

                                $list.find('.leave').trigger(jse.libs.theme.events.TRANSITION_STOP(), []).removeClass('leave');

                                // Open the submenu
                                transition.open = true;

                                // Set and unset the "onEnter" to prevent
                                // closing the menu immediately after opening if
                                // the cursor is at an place over the opening menu
                                // (this can happen if other components trigger the
                                // open event)
                                $self.off(jse.libs.theme.events.TRANSITION_FINISHED()).one(jse.libs.theme.events.TRANSITION_FINISHED(), function () {
                                    onEnter = false;
                                }).trigger(jse.libs.theme.events.TRANSITION(), transition).trigger(jse.libs.theme.events.OPEN_FLYOUT(), [$this]);

                                _repositionOpenLayer();
                            }, typeof delay === 'number' ? delay : options.enterDelay);
                        }

                        break;
                    case 'leave':
                        onEnter = false;
                        // Set a timer for closing if the submenu (delayed closing)
                        _clearTimeouts();
                        $self.addClass('leave');
                        leaveTimer = setTimeout(function () {
                            // Remove all openClass-classes from the
                            // menu except the elements parents
                            transition.open = false;
                            $list.find('.' + options.openClass).not($self.parentsUntil($this, '.' + options.openClass)).off(jse.libs.theme.events.TRANSITION_FINISHED()).one(jse.libs.theme.events.TRANSITION_FINISHED(), function () {
                                _setEventTypeClass($self, '');
                                $self.removeClass('leave');
                            }).trigger(jse.libs.theme.events.TRANSITION(), transition);
                        }, typeof delay === 'number' ? delay : options.leaveDelay);
                        break;
                    default:
                        break;
                }
            }
        }
    };

    /**
     * Event handler for the click / mouseenter / mouseleave event
     * on the navigation li elements. It checks if the event type
     * is supported for the current view type and calls the
     * openMenu-function if so.
     * @param       {object}    e           jQuery event object
     * @private
     */
    var _mouseHandler = function _mouseHandler(e) {
        var $self = $(this),
            viewport = mode.id <= options.breakpoint ? 'mobile' : 'desktop',
            events = options.events && options.events[viewport] ? options.events[viewport] : [];

        _setEventTypeClass($self, 'mouse');
        if ($.inArray(e.data.event, events) > -1) {
            _openMenu.call($self, e, viewport, e.data.delay);
        }

        // Perform navigation for custom links and category links on touch devices if no subcategories are found.
        if (($self.hasClass('custom') || isTouchDevice && $self.children('ul').length == 0) && e.data.event === 'click' && !$self.find('form').length) {
            e.preventDefault();
            e.stopPropagation();

            if ($self.find('a').attr('target') === '_blank') {
                window.open($self.find('a').attr('href'));
            } else {
                location.href = $self.find('a').attr('href');
            }
        }
    };

    /**
     * Event handler for the touchstart event (or "pointerdown"
     * depending on the browser). It removes the other critical
     * event handler (that would open the menu) from the list
     * element if the the mouseenter was executed before and
     * a click or touch event will be performed afterwards. This
     * is needed to prevent the browser engine workarounds which
     * will automatically perform mouse / click-events on touch
     * also.
     * @private
     */
    var _touchHandler = function _touchHandler(e) {
        e.stopPropagation();

        var $self = $(this),
            viewport = mode.id <= options.breakpoint ? 'mobile' : 'desktop',
            events = options.events && options.events[viewport] ? options.events[viewport] : [];

        $list.find('.enter-category').show();
        $list.find('.dropdown > a').on('click', function (e) {
            e.preventDefault();
        });

        if (e.data.type === 'start') {
            toucheStartEvent = { event: e, timestamp: new Date().getTime(), top: $window.scrollTop() };
            $list.off('mouseenter.menu mouseleave.menu');
        } else if ($.inArray('touch', events) > -1 && !_touchMoveDetect(e)) {
            _setEventTypeClass($self, 'touch');

            if ($.inArray('hover', events) === -1 || touchEvents.start !== 'pointerdown') {
                _openMenu.call($self, e, viewport);
            }

            $list.on('mouseleave', function () {
                $list.on('mouseenter.menu', 'li', { event: 'hover' }, _mouseHandler).on('mouseleave.menu', 'li', { event: 'hover', action: 'leave' }, _mouseHandler);
            });
        }
    };

    /**
     * Stores the last touch position on touchmove
     * @param       e       jQuery event object
     * @private
     */
    var _touchMoveHandler = function _touchMoveHandler(e) {
        toucheEndEvent = { event: e, timestamp: new Date().getTime(), top: $window.scrollTop() };
    };

    /**
     * Event handler for closing the menu if
     * the user interacts with the page
     * outside of the menu
     * @param       {object}    e       jQuery event object
     * @param       {object}    d       jQuery selection of the event emitter
     * @private
     */
    var _closeFlyout = function _closeFlyout(e, d) {
        if (d !== $this && $this.find($(e.target)).length === 0) {
            // Remove open and close timer
            _clearTimeouts();

            // Remove all state-classes from the menu
            if (options.menuType === 'horizontal') {
                $list.find('.touch, .mouse, .leave, .' + options.openClass).removeClass('touch mouse leave ' + options.openClass);
            }
        }
    };

    var _onClickAccordion = function _onClickAccordion(e) {
        e.preventDefault();
        e.stopPropagation();

        if ($(this).parents('.navbar-topbar-item').length > 0) {
            return;
        }

        if ($(this).hasClass('dropdown')) {
            if ($(this).hasClass(options.openClass)) {
                $(this).removeClass(options.openClass).find('.' + options.openClass).removeClass(options.openClass);
            } else {
                $(this).addClass(options.openClass).parentsUntil($this, 'li').addClass(options.openClass);
            }
        } else {
            location.href = $(this).find('a').attr('href');
        }
    };

    var _bindHorizontalEventHandlers = function _bindHorizontalEventHandlers() {
        $list.on(touchEvents.start + '.menu', 'li', { type: 'start' }, _touchHandler).on(touchEvents.move + '.menu', 'li', { type: 'start' }, _touchMoveHandler).on(touchEvents.end + '.menu', 'li', { type: 'end' }, _touchHandler).on('click.menu', 'li', { event: 'click', 'delay': 0 }, _mouseHandler).on('mouseenter.menu', 'li', { event: 'hover', action: 'enter' }, _mouseHandler).on('mouseleave.menu', 'li', { event: 'hover', action: 'leave' }, _mouseHandler);

        $body.on(jse.libs.theme.events.MENU_REPOSITIONED(), _updateCategoryMenu);
    };

    var _unbindHorizontalEventHandlers = function _unbindHorizontalEventHandlers() {
        $list.off(touchEvents.start + '.menu', 'li').off(touchEvents.move + '.menu', 'li').off(touchEvents.end + '.menu', 'li').off('click.menu', 'li').off('mouseenter.menu', 'li').off('mouseleave.menu', 'li');
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {
        // @todo Getting the "touchEvents" config value produces problems in tablet devices.
        touchEvents = jse.core.config.get('touch');
        transition.classOpen = options.openClass;

        _getSelections();
        _resetInitialCss();

        $body.on(jse.libs.theme.events.BREAKPOINT(), _breakpointHandler).on(jse.libs.theme.events.OPEN_FLYOUT() + ' click ' + touchEvents.end, _closeFlyout);

        $('.close-menu-container').on('touchstart touchend click', _closeMenu);

        $('.close-flyout').on('touchstart touchend click', _closeMenu);

        if (options.menuType === 'horizontal') {
            _bindHorizontalEventHandlers();
        }

        if (options.menuType === 'vertical') {
            if (options.accordion === true) {
                $this.on('click', 'li', _onClickAccordion);
            }

            // if there is no top header we must create dummy html because other modules will not work correctly
            if ($('#categories').length === 0) {
                var html = '<div id="categories"><div class="navbar-collapse collapse">' + '<nav class="navbar-default navbar-categories hidden"></nav></div></div>';
                $('#header').append(html);
            }
        }

        _breakpointHandler();

        /**
         * Stop the propagation of the events inside this container
         * (Workaround for the "more"-dropdown)
         */
        $this.find('.' + options.ignoreClass).on('mouseleave.menu mouseenter.menu click.menu ' + touchEvents.start + ' ' + touchEvents.end, 'li', function (e) {
            e.stopPropagation();
        });

        if (options.openActive) {
            var $active = $this.find('.active');
            $active.parentsUntil($this, 'li').addClass('open');
        }

        $('li.custom-entries a').on('click', function (e) {
            e.stopPropagation();
        });

        var viewport = mode.id <= options.breakpoint ? 'mobile' : 'desktop';

        if (viewport == 'mobile') {
            $('.level-1').css('padding-bottom', '200px'); // This padding corrects expand/collapse behavior of lower menu items in various mobile browsers. 
        }

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvbWVudS5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwic291cmNlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiR3aW5kb3ciLCJ3aW5kb3ciLCIkYm9keSIsIiRsaXN0IiwiJGVudHJpZXMiLCIkbW9yZSIsIiRtb3JlRW50cmllcyIsIiRtZW51RW50cmllcyIsIiRjdXN0b20iLCIkY2F0ZWdvcmllcyIsInRvdWNoRXZlbnRzIiwiY3VycmVudFdpZHRoIiwibW9kZSIsIm1vYmlsZSIsImVudGVyVGltZXIiLCJsZWF2ZVRpbWVyIiwiaW5pdGlhbGl6ZWRQb3MiLCJvbkVudGVyIiwidG91Y2hlU3RhcnRFdmVudCIsInRvdWNoZUVuZEV2ZW50IiwidHJhbnNpdGlvbiIsImlzVG91Y2hEZXZpY2UiLCJNb2Rlcm5penIiLCJ0b3VjaGV2ZW50cyIsIm5hdmlnYXRvciIsInVzZXJBZ2VudCIsInNlYXJjaCIsImRlZmF1bHRzIiwibWVudVR5cGUiLCJ1bmZvbGRMZXZlbCIsImFjY29yZGlvbiIsInNob3dBbGxMaW5rIiwiYnJlYWtwb2ludCIsImVudGVyRGVsYXkiLCJsZWF2ZURlbGF5Iiwid2lkdGhUb2xlcmFuY2UiLCJvcGVuQ2xhc3MiLCJzd2l0Y2hFbGVtZW50UG9zaXRpb24iLCJpZ25vcmVDbGFzcyIsInRvdWNoTW92ZVRvbGVyYW5jZSIsIm9wZW5BY3RpdmUiLCJldmVudHMiLCJkZXNrdG9wIiwib3B0aW9ucyIsImV4dGVuZCIsIl90b3VjaE1vdmVEZXRlY3QiLCJkaWZmIiwiTWF0aCIsImFicyIsImV2ZW50Iiwib3JpZ2luYWxFdmVudCIsInBhZ2VZIiwiX2dldFNlbGVjdGlvbnMiLCJjaGlsZHJlbiIsIm5vdCIsImZpbHRlciIsIl9zZXRJdGVtIiwiJGl0ZW0iLCIkdGFyZ2V0IiwicG9zaXRpb25JZCIsImRvbmUiLCJlYWNoIiwiJHNlbGYiLCJwb3NpdGlvbiIsImJlZm9yZSIsImRldGFjaCIsImFwcGVuZCIsIl9hZGRFbGVtZW50IiwiX3Nob3dFbGVtZW50cyIsIiRlbGVtZW50cyIsIndpZHRoIiwiaGlkZSIsIl9yZW1vdmVFbGVtZW50IiwiX2hpZGVFbGVtZW50cyIsImFkZCIsImZpbmQiLCJyZW1vdmVDbGFzcyIsImlzIiwic2hvdyIsImdldCIsInJldmVyc2UiLCJfaW5pdEVsZW1lbnRTaXplc0FuZFBvc2l0aW9uIiwiaSIsIm91dGVyV2lkdGgiLCJfY2xvc2VNZW51IiwiZSIsInBhcmVudHMiLCJsZW5ndGgiLCJpc09iamVjdCIsImlzRXZlbnQiLCJoYXNPd25Qcm9wZXJ0eSIsInN0b3BQcm9wYWdhdGlvbiIsInByZXZlbnREZWZhdWx0IiwiX2NsZWFyVGltZW91dHMiLCJjbGVhclRpbWVvdXQiLCJfcmVzZXRJbml0aWFsQ3NzIiwiY3NzIiwiX3JlcG9zaXRpb25PcGVuTGF5ZXIiLCJsaXN0V2lkdGgiLCIkb3BlbkxheWVyIiwiJHBhcmVudCIsInBhcmVudCIsInBhcmVudFBvc2l0aW9uIiwibGVmdCIsInBhcmVudFdpZHRoIiwiYWRkQ2xhc3MiLCJfdXBkYXRlQ2F0ZWdvcnlNZW51IiwiZXZlbnROYW1lIiwiY29udGFpbmVyV2lkdGgiLCJpbm5lcldpZHRoIiwiX3N3aXRjaFRvTW9iaWxlVmlldyIsIm9wYWNpdHkiLCJoZWlnaHQiLCJhcHBlbmRUbyIsIl9iaW5kSG9yaXpvbnRhbEV2ZW50SGFuZGxlcnMiLCJ0cmlnZ2VyIiwianNlIiwibGlicyIsInRoZW1lIiwiTUVOVV9SRVBPU0lUSU9ORUQiLCJfc3dpdGNoVG9EZXNrdG9wVmlldyIsIiR0b3BtZW51Q29udGVudEVsZW1lbnRzIiwiX3VuYmluZEhvcml6b250YWxFdmVudEhhbmRsZXJzIiwiY2xpY2siLCJfc2V0RXZlbnRUeXBlQ2xhc3MiLCJjbGFzc05hbWUiLCJfYnJlYWtwb2ludEhhbmRsZXIiLCJvbGRNb2RlIiwibmV3TW9kZSIsInJlc3BvbnNpdmUiLCJpZCIsInN3aXRjaFRvTW9iaWxlIiwidW5kZWZpbmVkIiwic3dpdGNoVG9EZXNrdG9wIiwiX29wZW5NZW51IiwidHlwZSIsImRlbGF5IiwiJHN1Ym1lbnUiLCJsZXZlbCIsInZhbGlkU3VibWVudSIsInBhcnNlSW50IiwidG9nZ2xlQ2xhc3MiLCJ2aXNpYmxlIiwiaGFzQ2xhc3MiLCJsZWF2ZSIsImFjdGlvbiIsIiRsaXZlU2VhcmNoRmllbGQiLCJpbnRlcmFjdGlvbiIsImlzTW91c2VEb3duIiwic2V0VGltZW91dCIsInBhcmVudHNVbnRpbCIsIlRSQU5TSVRJT05fU1RPUCIsIm9wZW4iLCJvZmYiLCJUUkFOU0lUSU9OX0ZJTklTSEVEIiwib25lIiwiVFJBTlNJVElPTiIsIk9QRU5fRkxZT1VUIiwiX21vdXNlSGFuZGxlciIsInZpZXdwb3J0IiwiaW5BcnJheSIsImNhbGwiLCJhdHRyIiwibG9jYXRpb24iLCJocmVmIiwiX3RvdWNoSGFuZGxlciIsIm9uIiwidGltZXN0YW1wIiwiRGF0ZSIsImdldFRpbWUiLCJ0b3AiLCJzY3JvbGxUb3AiLCJzdGFydCIsIl90b3VjaE1vdmVIYW5kbGVyIiwiX2Nsb3NlRmx5b3V0IiwiZCIsInRhcmdldCIsIl9vbkNsaWNrQWNjb3JkaW9uIiwibW92ZSIsImVuZCIsImluaXQiLCJjb3JlIiwiY29uZmlnIiwiY2xhc3NPcGVuIiwiQlJFQUtQT0lOVCIsImh0bWwiLCIkYWN0aXZlIl0sIm1hcHBpbmdzIjoiOzs7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7QUFRQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQ0ksTUFESixFQUdJLENBQ0lGLE9BQU9HLE1BQVAsR0FBZ0IsY0FEcEIsRUFFSUgsT0FBT0csTUFBUCxHQUFnQixrQkFGcEIsRUFHSUgsT0FBT0csTUFBUCxHQUFnQixtQkFIcEIsQ0FISixFQVNJLFVBQVVDLElBQVYsRUFBZ0I7O0FBRVo7O0FBRVI7O0FBRVEsUUFBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxRQUNJQyxVQUFVRCxFQUFFRSxNQUFGLENBRGQ7QUFBQSxRQUVJQyxRQUFRSCxFQUFFLE1BQUYsQ0FGWjtBQUFBLFFBR0lJLFFBQVEs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QUN3QjtBQUNqRDs7QUFFRHBDO0FBQ0gsS0FoRUQ7O0FBa0VBO0FBQ0EsV0FBT2pFLE1BQVA7QUFDSCxDQTU2QkwiLCJmaWxlIjoid2lkZ2V0cy9tZW51LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBtZW51LmpzIDIwMjMtMTAtMDRcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDIzIEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIFRoaXMgd2lkZ2V0IGhhbmRsZXMgdGhlIGhvcml6b250YWwgbWVudS9kcm9wZG93biBmdW5jdGlvbmFsaXR5LlxuICpcbiAqIEl0J3MgdXNlZCBmb3IgdGhlIHRvcCBjYXRlZ29yeSBuYXZpZ2F0aW9uLCB0aGUgY2FydCBkcm9wZG93biBvciB0aGUgdG9wIG1lbnUgKGZvciBleGFtcGxlKS4gSXQgaXNcbiAqIGFibGUgdG8gcmUtb3JkZXIgdGhlIG1lbnUgZW50cmllcyB0byBhIHNwZWNpYWwgXCJNb3JlXCIgc3VibWVudSB0byBzYXZlIHNwYWNlIGlmIHRoZSBlbnRyaWVzIGRvbid0XG4gKiBmaXQgaW4gdGhlIGN1cnJlbnQgdmlldy4gSXQncyBhbHNvIGFibGUgdG8gd29yayB3aXRoIGRpZmZlcmVudCBldmVudCB0eXBlcyBmb3Igb3BlbmluZy9jbG9zaW5nIG1lbnVcbiAqIGl0ZW1zIGluIHRoZSBkaWZmZXJlbnQgdmlldyB0eXBlcy5cbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuICAgICdtZW51JyxcblxuICAgIFtcbiAgICAgICAgZ2FtYmlvLnNvdXJjZSArICcvbGlicy9ldmVudHMnLFxuICAgICAgICBnYW1iaW8uc291cmNlICsgJy9saWJzL3Jlc3BvbnNpdmUnLFxuICAgICAgICBnYW1iaW8uc291cmNlICsgJy9saWJzL2ludGVyYWN0aW9uJ1xuICAgIF0sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgdmFyICR0aGlzID0gJCh0aGlzKSxcbiAgICAgICAgICAgICR3aW5kb3cgPSAkKHdpbmRvdyksXG4gICAgICAgICAgICAkYm9keSA9ICQoJ2JvZHknKSxcbiAgICAgICAgICAgICRsaXN0ID0gbnVsbCxcbiAgICAgICAgICAgICRlbnRyaWVzID0gbnVsbCxcbiAgICAgICAgICAgICRtb3JlID0gbnVsbCxcbiAgICAgICAgICAgICRtb3JlRW50cmllcyA9IG51bGwsXG4gICAgICAgICAgICAkbWVudUVudHJpZXMgPSBudWxsLFxuICAgICAgICAgICAgJGN1c3RvbSA9IG51bGwsXG4gICAgICAgICAgICAkY2F0ZWdvcmllcyA9IG51bGwsXG4gICAgICAgICAgICB0b3VjaEV2ZW50cyA9IG51bGwsXG4gICAgICAgICAgICBjdXJyZW50V2lkdGggPSBudWxsLFxuICAgICAgICAgICAgbW9kZSA9IG51bGwsXG4gICAgICAgICAgICBtb2JpbGUgPSBmYWxzZSxcbiAgICAgICAgICAgIGVudGVyVGltZXIgPSBudWxsLFxuICAgICAgICAgICAgbGVhdmVUaW1lciA9IG51bGwsXG4gICAgICAgICAgICBpbml0aWFsaXplZFBvcyA9IGZhbHNlLFxuICAgICAgICAgICAgb25FbnRlciA9IGZhbHNlLFxuICAgICAgICAgICAgdG91Y2hlU3RhcnRFdmVudCA9IG51bGwsXG4gICAgICAgICAgICB0b3VjaGVFbmRFdmVudCA9IG51bGwsXG4gICAgICAgICAgICB0cmFuc2l0aW9uID0ge30sXG4gICAgICAgICAgICBpc1RvdWNoRGV2aWNlID0gTW9kZXJuaXpyLnRvdWNoZXZlbnRzIHx8IG5hdmlnYXRvci51c2VyQWdlbnQuc2VhcmNoKC9Ub3VjaC9pKSAhPT0gLTEsXG4gICAgICAgICAgICBkZWZhdWx0cyA9IHtcbiAgICAgICAgICAgICAgICAvLyBUaGUgbWVudSB0eXBlIG11c3QgYmUgZWl0aGVyICdob3Jpem9udGFsJyBvciAndmVydGljYWwnXG4gICAgICAgICAgICAgICAgbWVudVR5cGU6ICdob3Jpem9udGFsJyxcblxuICAgICAgICAgICAgICAgIC8vIFZlcnRpY2FsIG1lbnUgb3B0aW9ucy5cbiAgICAgICAgICAgICAgICB1bmZvbGRMZXZlbDogMCxcbiAgICAgICAgICAgICAgICBhY2NvcmRpb246IGZhbHNlLFxuICAgICAgICAgICAgICAgIHNob3dBbGxMaW5rOiBmYWxzZSxcblxuICAgICAgICAgICAgICAgIC8vIE1pbmltdW0gYnJlYWtwb2ludCB0byBzd2l0Y2ggdG8gbW9iaWxlIHZpZXdcbiAgICAgICAgICAgICAgICBicmVha3BvaW50OiA0MCxcbiAgICAgICAgICAgICAgICAvLyBEZWxheSBpbiBtcyBhZnRlciBhIG1vdXNlZW50ZXIgdGhlIGVsZW1lbnQgZ2V0cyBzaG93blxuICAgICAgICAgICAgICAgIGVudGVyRGVsYXk6IDAsXG4gICAgICAgICAgICAgICAgLy8gRGVsYXkgaW4gbXMgYWZ0ZXIgYSBtb3VzZWxlYXZlIGFuIGVsZW1lbnQgZ2V0cyBoaWRkZW5cbiAgICAgICAgICAgICAgICBsZWF2ZURlbGF5OiA1MCxcbiAgICAgICAgICAgICAgICAvLyBUb2xlcmFuY2UgaW4gcHggd2hpY2ggZ2V0cyBzdWJzdHJhY3RlZCBmcm9tIHRoZSBuYXYtd2lkdGggdG8gcHJldmVudCBmbGlja2VyaW5nXG4gICAgICAgICAgICAgICAgd2lkdGhUb2xlcmFuY2U6IDEwLFxuICAgICAgICAgICAgICAgIC8vIENsYXNzIHRoYXQgZ2V0cyBhZGRlZCB0byBhbiBvcGVuZWQgbWVudSBsaXN0IGl0ZW1cbiAgICAgICAgICAgICAgICBvcGVuQ2xhc3M6ICdvcGVuJyxcbiAgICAgICAgICAgICAgICAvLyBJZiB0cnVlLCBlbGVtZW50cyBnZXQgbW92ZWQgZnJvbS90byB0aGUgbW9yZSBtZW51IGlmIHRoZXJlIGlzbid0IGVub3VnaCBzcGFjZVxuICAgICAgICAgICAgICAgIHN3aXRjaEVsZW1lbnRQb3NpdGlvbjogdHJ1ZSxcbiAgICAgICAgICAgICAgICAvLyBJZ25vcmUgbWVudSBmdW5jdGlvbmFsaXR5IG9uIGVsZW1lbnRzIGluc2lkZSB0aGlzIHNlbGVjdGlvblxuICAgICAgICAgICAgICAgIGlnbm9yZUNsYXNzOiAnaWdub3JlLW1lbnUnLFxuICAgICAgICAgICAgICAgIC8vIFRvbGVyYW5jZSBpbiBweCB3aGljaCBpcyBhbGxvd2VkIGZvciBhIFwiY2xpY2tcIiBldmVudCBvbiB0b3VjaFxuICAgICAgICAgICAgICAgIHRvdWNoTW92ZVRvbGVyYW5jZTogMTAsXG4gICAgICAgICAgICAgICAgLy8gSWYgdHJ1ZSwgdGhlIGxpIHdpdGggdGhlIGFjdGl2ZSBjbGFzcyBnZXRzIG9wZW5lZFxuICAgICAgICAgICAgICAgIG9wZW5BY3RpdmU6IGZhbHNlLFxuICAgICAgICAgICAgICAgIGV2ZW50czoge1xuICAgICAgICAgICAgICAgICAgICAvLyBFdmVudCB0eXBlcyB0aGF0IG9wZW4gdGhlIG1lbnVzIGluIGRlc2t0b3Agdmlldy5cbiAgICAgICAgICAgICAgICAgICAgLy8gUG9zc2libGUgdmFsdWVzOiBbJ2NsaWNrJ107IFsnaG92ZXInXTsgWyd0b3VjaCcsICdob3ZlciddOyBbJ2NsaWNrJywgJ2hvdmVyJ11cbiAgICAgICAgICAgICAgICAgICAgZGVza3RvcDogWyd0b3VjaCcsICdob3ZlciddLFxuICAgICAgICAgICAgICAgICAgICAvLyBFdmVudCB0eXBlcyB0aGF0IG9wZW4gdGhlIG1lbnVzIGluIG1vYmlsZSB2aWV3LlxuICAgICAgICAgICAgICAgICAgICAvLyBQb3NzaWJsZSB2YWx1ZXM6IFsnY2xpY2snXTsgWydob3ZlciddOyBbJ3RvdWNoJywgJ2hvdmVyJ107IFsnY2xpY2snLCAnaG92ZXInXTsgWyd0b3VjaCcsICdjbGljayddXG4gICAgICAgICAgICAgICAgICAgIG1vYmlsZTogWyd0b3VjaCcsICdjbGljayddXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIG9wdGlvbnMgPSAkLmV4dGVuZCh7fSwgZGVmYXVsdHMsIGRhdGEpLFxuICAgICAgICAgICAgbW9kdWxlID0ge307XG5cblxuLy8gIyMjIyMjIyMjIyBIRUxQRVIgRlVOQ1RJT05TICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGVscGVyIGZ1bmN0aW9uIHRvIGNhbGN1bGF0ZSB0aGUgdG9sZXJhbmNlXG4gICAgICAgICAqIGJldHdlZW4gdGhlIHRvdWNoc3RhcnQgYW5kIHRvdWNoZW5kIGV2ZW50LlxuICAgICAgICAgKiBJZiB0aGUgbWF4IHRvbGFyYW5jZSBpcyBleGNlZWRlZCByZXR1cm4gdHJ1ZVxuICAgICAgICAgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAgICAgKiBAcmV0dXJuICAgICB7Ym9vbGVhbn0gICAgICAgICAgICAgICBJZiB0cnVlIGl0IGlzIGEgbW92ZSBldmVudFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF90b3VjaE1vdmVEZXRlY3QgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB0b3VjaGVFbmRFdmVudCA9IHRvdWNoZUVuZEV2ZW50IHx8IHRvdWNoZVN0YXJ0RXZlbnQ7XG4gICAgICAgICAgICB2YXIgZGlmZiA9IE1hdGguYWJzKHRvdWNoZUVuZEV2ZW50LmV2ZW50Lm9yaWdpbmFsRXZlbnQucGFnZVkgLSB0b3VjaGVTdGFydEV2ZW50LmV2ZW50Lm9yaWdpbmFsRXZlbnQucGFnZVkpO1xuICAgICAgICAgICAgdG91Y2hlRW5kRXZlbnQgPSBudWxsO1xuICAgICAgICAgICAgcmV0dXJuIGRpZmYgPiBvcHRpb25zLnRvdWNoTW92ZVRvbGVyYW5jZTtcbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogVXBkYXRlcyB0aGUgalF1ZXJ5IHNlbGVjdGlvbiwgYmVjYXVzZSB0aGVcbiAgICAgICAgICogbGlzdCBlbGVtZW50cyBjYW4gYmUgbW92ZWRcbiAgICAgICAgICpcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfZ2V0U2VsZWN0aW9ucyA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICRsaXN0ID0gJHRoaXMuY2hpbGRyZW4oJ3VsJyk7XG4gICAgICAgICAgICAvLyBFeGNsdWRlIHRoZSBcIi5uYXZiYXItdG9wYmFyLWl0ZW1cIiBlbGVtZW50cyBiZWNhdXNlIHRoZXlcbiAgICAgICAgICAgIC8vIGFyZSBjbG9uZWQgdG8gdGhpcyBtZW51IGFuZCBhcmUgb25seSBzaG93biBpbiBtb2JpbGUgdmlld1xuICAgICAgICAgICAgJGVudHJpZXMgPSAkbGlzdC5jaGlsZHJlbignbGknKS5ub3QoJy5uYXZiYXItdG9wYmFyLWl0ZW0nKTtcbiAgICAgICAgICAgICRtb3JlID0gJGVudHJpZXMuZmlsdGVyKCcuZHJvcGRvd24tbW9yZScpO1xuICAgICAgICAgICAgJG1vcmVFbnRyaWVzID0gJG1vcmUuY2hpbGRyZW4oJ3VsJyk7XG4gICAgICAgICAgICAkY3VzdG9tID0gJGVudHJpZXMuZmlsdGVyKCcuY3VzdG9tJyk7XG4gICAgICAgICAgICAkbWVudUVudHJpZXMgPSAkZW50cmllcy5ub3QoJG1vcmUpO1xuICAgICAgICAgICAgJGNhdGVnb3JpZXMgPSAkbWVudUVudHJpZXMubm90KCRjdXN0b20pO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBIZWxwZXIgZnVuY3Rpb24gdGhhdCBkZXRhY2hlcyBhbiBlbGVtZW50IGZyb20gdGhlXG4gICAgICAgICAqIG1lbnUgYW5kIGF0dGFjaGVzIGl0IHRvIHRoZSBjb3JyZWN0IHBvc2l0aW9uIGF0XG4gICAgICAgICAqIHRoZSB0YXJnZXRcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICRpdGVtICAgICAgIGpRdWVyeSBzZWxlY3Rpb24gb2YgdGhlIGl0ZW0gdGhhdCBnZXRzIGRldGFjaGVkIC8gYXR0YWNoZWRcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICR0YXJnZXQgICAgIGpRdWVyeSBzZWxlY3Rpb24gb2YgdGhlIHRhcmdldCBjb250YWluZXJcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfc2V0SXRlbSA9IGZ1bmN0aW9uICgkaXRlbSwgJHRhcmdldCkge1xuICAgICAgICAgICAgdmFyIHBvc2l0aW9uSWQgPSAkaXRlbS5kYXRhKCdwb3NpdGlvbicpLFxuICAgICAgICAgICAgICAgIGRvbmUgPSBmYWxzZTtcblxuICAgICAgICAgICAgLy8gTG9vayBmb3IgdGhlIGZpcnN0IGl0ZW0gdGhhdCBoYXMgYSBoaWdoZXJcbiAgICAgICAgICAgIC8vIHBvc2l0aW9uSWQgdGhhdCB0aGUgaXRlbSBhbmQgaW5zZXJ0IGl0XG4gICAgICAgICAgICAvLyBiZWZvcmUgdGhlIGZvdW5kIGVudHJ5XG4gICAgICAgICAgICAkdGFyZ2V0XG4gICAgICAgICAgICAgICAgLmNoaWxkcmVuKClcbiAgICAgICAgICAgICAgICAuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgIHZhciAkc2VsZiA9ICQodGhpcyksXG4gICAgICAgICAgICAgICAgICAgICAgICBwb3NpdGlvbiA9ICRzZWxmLmRhdGEoJ3Bvc2l0aW9uJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKHBvc2l0aW9uID4gcG9zaXRpb25JZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJHNlbGYuYmVmb3JlKCRpdGVtLmRldGFjaCgpKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGRvbmUgPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIC8vIEFwcGVuZCB0aGUgaXRlbSBpZiB0aGUgcG9zaXRpb25JZCBoYXNcbiAgICAgICAgICAgIC8vIGEgaGlnaGVyIHZhbHVlIGFzIHRoZSBsYXN0IGl0ZW0gaW50IHRoZVxuICAgICAgICAgICAgLy8gdGFyZ2V0XG4gICAgICAgICAgICBpZiAoIWRvbmUpIHtcbiAgICAgICAgICAgICAgICAkdGFyZ2V0LmFwcGVuZCgkaXRlbSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEhlbHBlciBmdW5jdGlvbiB0aGF0IGNoZWNrcyB3aGljaCBlbGVtZW50cyBuZWVkc1xuICAgICAgICAgKiB0byBiZSBhZGRlZCB0byB0aGUgbWVudS4gRXZlcnkgZWxlbWVudCB0aGF0IG5lZWRzXG4gICAgICAgICAqIHRvIGJlIGFkZGVkIGdldHMgcGFzc2VkIHRvIHRoZSBmdW5jdGlvblxuICAgICAgICAgKiBcIl9zZXRJdGVtXCJcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtpbnRlZ2VyfSAgICAgICBkaWZmICAgICAgICBBbW91bnQgb2YgcGl4ZWxzIHRoYXQgd2VyZSBmcmVlXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX2FkZEVsZW1lbnQgPSBmdW5jdGlvbiAoZGlmZikge1xuXG4gICAgICAgICAgICB2YXIgZG9uZSA9IGZhbHNlO1xuXG4gICAgICAgICAgICAvKipcbiAgICAgICAgICAgICAqIEhlbHBlciBmdW5jdGlvbiB0aGF0IGxvb3BzIHRocm91Z2ggdGhlIGVsZW1lbnRzXG4gICAgICAgICAgICAgKiBhbmQgdHJpZXMgdG8gYWRkIHRoZSBlbGVtZW50cyB0byB0aGUgbWVudSBpZlxuICAgICAgICAgICAgICogaXQgd291bGQgZml0LlxuICAgICAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICRlbGVtZW50cyAgICAgICBqUXVlcnkgc2VsZWN0aW9uIG9mIHRoZSBlbnRyaWVzIGluc2lkZSB0aGUgbW9yZS1tZW51XG4gICAgICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgICAgICovXG4gICAgICAgICAgICB2YXIgX3Nob3dFbGVtZW50cyA9IGZ1bmN0aW9uICgkZWxlbWVudHMpIHtcbiAgICAgICAgICAgICAgICAkZWxlbWVudHMuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgIHZhciAkc2VsZiA9ICQodGhpcyksXG4gICAgICAgICAgICAgICAgICAgICAgICB3aWR0aCA9ICRzZWxmLmRhdGEoKS53aWR0aDtcblxuICAgICAgICAgICAgICAgICAgICBpZiAoZGlmZiA+IHdpZHRoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBBZGQgdGhlIGl0ZW0gdG8gdGhlIG1lbnVcbiAgICAgICAgICAgICAgICAgICAgICAgIF9zZXRJdGVtKCRzZWxmLCAkbGlzdCk7XG4gICAgICAgICAgICAgICAgICAgICAgICBkaWZmIC09IHdpZHRoO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgLy8gVGhlIG5leHQgaXRlbSB3b3VsZG4ndCBmaXQgYW55bW9yZScsXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBxdWl0IHRoZSBsb29wXG4gICAgICAgICAgICAgICAgICAgICAgICBkb25lID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgLy8gVXBkYXRlIHRoZSBzZWxlY3Rpb24gb2YgdGhlIHZpc2libGUgbWVudSBpdGVtcy5cbiAgICAgICAgICAgIF9nZXRTZWxlY3Rpb25zKCk7XG5cbiAgICAgICAgICAgIC8vIEFkZCB0aGUgY29udGVudCBtYW5hZ2VyIGVudHJpZXMgdG8gdGhlIG1lbnUgZmlyc3QuXG4gICAgICAgICAgICAvLyBJZiB0aGVyZSBpcyBzdGlsbCBzcGFjZSwgYWRkIHRoZSBcIm5vcm1hbFwiIGNhdGVnb3J5XG4gICAgICAgICAgICAvLyBpdGVtcyBhbHNvXG4gICAgICAgICAgICBfc2hvd0VsZW1lbnRzKCRtb3JlRW50cmllcy5jaGlsZHJlbignLmN1c3RvbScpKTtcbiAgICAgICAgICAgIGlmICghZG9uZSkge1xuICAgICAgICAgICAgICAgIF9zaG93RWxlbWVudHMoJG1vcmVFbnRyaWVzLmNoaWxkcmVuKCkpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvLyBDaGVjayBpZiB0aGUgaXRlbXMgc3RpbGwgaW4gdGhlIG1vcmUgbWVudVxuICAgICAgICAgICAgLy8gd291bGQgZml0IGluc2lkZSB0aGUgbWFpbiBtZW51IGlmIHRoZSBtb3JlXG4gICAgICAgICAgICAvLyBtZW51IHdvdWxkIGdldCBoaWRkZW5cbiAgICAgICAgICAgIHZhciB3aWR0aCA9IDA7XG4gICAgICAgICAgICAkbW9yZUVudHJpZXNcbiAgICAgICAgICAgICAgICAuY2hpbGRyZW4oKVxuICAgICAgICAgICAgICAgIC5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgd2lkdGggKz0gJCh0aGlzKS5kYXRhKCkud2lkdGg7XG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIGlmICh3aWR0aCA9PT0gMCkge1xuICAgICAgICAgICAgICAgICRtb3JlLmhpZGUoKTtcbiAgICAgICAgICAgIH0gZWxzZSBpZiAod2lkdGggPCAoJG1vcmUuZGF0YSgpLndpZHRoICsgZGlmZikpIHtcbiAgICAgICAgICAgICAgICAkbW9yZS5oaWRlKCk7XG4gICAgICAgICAgICAgICAgZGlmZiArPSAkbW9yZS5kYXRhKCkud2lkdGg7XG4gICAgICAgICAgICAgICAgX3Nob3dFbGVtZW50cygkbW9yZUVudHJpZXMuY2hpbGRyZW4oKSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGVscGVyIGZ1bmN0aW9uIHRoYXQgY2hlY2tzIHdoaWNoIGVsZW1lbnRzIG5lZWRzXG4gICAgICAgICAqIHRvIGJlIHJlbW92ZWQgZnJvbSB0aGUgbWVudSwgc28gdGhhdCBpdCBmaXRzXG4gICAgICAgICAqIGluc2lkZSBvbmUgbWVudSBsaW5lLiBFdmVyeSBlbGVtZW50IHRoYXQgbmVlZHNcbiAgICAgICAgICogdG8gYmUgcmVtb3ZlZCBnZXRzIHBhc3NlZCB0byB0aGUgZnVuY3Rpb25cbiAgICAgICAgICogXCJfc2V0SXRlbVwiXG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7aW50ZWdlcn0gICAgICAgZGlmZiAgICAgICAgQW1vdW50IG9mIHBpeGVscyB0aGF0IG5lZWRzIHRvIGJlIHNhdmVkXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3JlbW92ZUVsZW1lbnQgPSBmdW5jdGlvbiAoZGlmZikge1xuXG4gICAgICAgICAgICB2YXIgZG9uZSA9IGZhbHNlO1xuXG4gICAgICAgICAgICAvKipcbiAgICAgICAgICAgICAqIEhlbHBlciBmdW5jdGlvbiB0aGF0IGNvbnRhaW5zIHRoZSBjaGVja1xuICAgICAgICAgICAgICogbG9vcCBmb3IgZGV0ZXJtaW5pbmcgd2hpY2ggZWxlbWVudHNcbiAgICAgICAgICAgICAqIG5lZWRzIHRvIGJlIHJlbW92ZWRcbiAgICAgICAgICAgICAqIEBwYXJhbSAgICAgICAgICAge29iamVjdH0gICAgJGVsZW1lbnRzICAgICAgIGpRdWVyeSBzZWxlY3Rpb24gb2YgdGhlIG1lbnUgaXRlbXNcbiAgICAgICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAgICAgKi9cbiAgICAgICAgICAgIHZhciBfaGlkZUVsZW1lbnRzID0gZnVuY3Rpb24gKCRlbGVtZW50cykge1xuICAgICAgICAgICAgICAgICRlbGVtZW50cy5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyICRzZWxmID0gJCh0aGlzKSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHdpZHRoID0gJHNlbGYuZGF0YSgpLndpZHRoO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vIFJlbW92ZSB0aGUgcG9zc2libHkgc2V0IG9wZW4gc3RhdGVcbiAgICAgICAgICAgICAgICAgICAgJHNlbGZcbiAgICAgICAgICAgICAgICAgICAgICAgIC5maWx0ZXIoJy4nICsgb3B0aW9ucy5vcGVuQ2xhc3MpXG4gICAgICAgICAgICAgICAgICAgICAgICAuYWRkKCRzZWxmLmZpbmQoJy4nICsgb3B0aW9ucy5vcGVuQ2xhc3MpKVxuICAgICAgICAgICAgICAgICAgICAgICAgLnJlbW92ZUNsYXNzKG9wdGlvbnMub3BlbkNsYXNzKTtcblxuICAgICAgICAgICAgICAgICAgICAvLyBBZGQgdGhlIGVudHJ5IHRvIHRoZSBtb3JlLW1lbnVcbiAgICAgICAgICAgICAgICAgICAgX3NldEl0ZW0oJHNlbGYsICRtb3JlRW50cmllcyk7XG5cbiAgICAgICAgICAgICAgICAgICAgZGlmZiAtPSB3aWR0aDtcblxuICAgICAgICAgICAgICAgICAgICBpZiAoZGlmZiA8IDApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIEVub3VnaCBlbGVtZW50cyBhcmUgcmVtb3ZlZCxcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIHF1aXQgdGhlIGxvb3BcbiAgICAgICAgICAgICAgICAgICAgICAgIGRvbmUgPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAvLyBVcGRhdGUgdGhlIHNlbGVjdGlvbiBvZiB0aGUgdmlzaWJsZSBtZW51IGl0ZW1zXG4gICAgICAgICAgICBfZ2V0U2VsZWN0aW9ucygpO1xuXG4gICAgICAgICAgICAvLyBBZGQgdGhlIHdpZHRoIG9mIHRoZSBtb3JlIGVudHJ5IGlmIGl0J3Mgbm90XG4gICAgICAgICAgICAvLyB2aXNpYmxlLCBiZWNhdXNlIGl0IHdpbGwgZ2V0IHNob3duIGR1cmluZyB0aGlzXG4gICAgICAgICAgICAvLyBmdW5jdGlvbiBjYWxsXG4gICAgICAgICAgICBpZiAoJG1vcmUuaXMoJzpoaWRkZW4nKSkge1xuICAgICAgICAgICAgICAgIGRpZmYgKz0gJG1vcmUuZGF0YSgpLndpZHRoO1xuICAgICAgICAgICAgICAgICRtb3JlLnJlbW92ZUNsYXNzKCdzdHlsZScpO1xuICAgICAgICAgICAgICAgICRtb3JlLnNob3coKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy8gRmlyc3QgcmVtb3ZlIFwibm9ybWFsXCIgY2F0ZWdvcnkgZW50cmllcy4gSWYgdGhhdFxuICAgICAgICAgICAgLy8gaXNuJ3QgZW5vdWdoIHJlbW92ZSB0aGUgY29udGVudCBtYW5hZ2VyIGVudHJpZXMgYWxzb1xuICAgICAgICAgICAgX2hpZGVFbGVtZW50cygkKCRjYXRlZ29yaWVzLmdldCgpLnJldmVyc2UoKSkpO1xuICAgICAgICAgICAgaWYgKCFkb25lKSB7XG4gICAgICAgICAgICAgICAgX2hpZGVFbGVtZW50cygkKCRjdXN0b20uZ2V0KCkucmV2ZXJzZSgpKSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFNldHMgYSBkYXRhIGF0dHJpYnV0ZSB0byB0aGUgbWVudSBpdGVtc1xuICAgICAgICAgKiB0aGF0IGNvbnRhaW5zIHRoZSB3aWR0aCBvZiB0aGUgZWxlbWVudHMuXG4gICAgICAgICAqIFRoaXMgaXMgbmVlZGVkIGJlY2F1c2UgaWYgaXQgaXMgZGlzcGxheVxuICAgICAgICAgKiBub25lIHRoZSBkZXRlY3RlZCB3aXRoIHdpbGwgYmUgemVyby4gSXRcbiAgICAgICAgICogc2V0cyBwb3NpdGlvbiBpZCBhbHNvLlxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9pbml0RWxlbWVudFNpemVzQW5kUG9zaXRpb24gPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAkZW50cmllcy5lYWNoKGZ1bmN0aW9uIChpKSB7XG4gICAgICAgICAgICAgICAgdmFyICRzZWxmID0gJCh0aGlzKSxcbiAgICAgICAgICAgICAgICAgICAgd2lkdGggPSAkc2VsZi5vdXRlcldpZHRoKCk7XG5cbiAgICAgICAgICAgICAgICAkc2VsZi5kYXRhKHt3aWR0aDogd2lkdGgsIHBvc2l0aW9uOiBpfSk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGVscGVyIGZ1bmN0aW9uIHRvIGNsb3NlIGFsbCBtZW51IGVudHJpZXMuXG4gICAgICAgICAqIE5lZWRlZCBmb3IgdGhlIGRlc2t0b3AgPC0+IG1vYmlsZSB2aWV3XG4gICAgICAgICAqIGNoYW5nZSwgbW9zdGx5LlxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9jbG9zZU1lbnUgPSBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgJHRoaXMuZmluZCgnbGkuJyArIG9wdGlvbnMub3BlbkNsYXNzKS5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICBpZiAoJCh0aGlzKS5wYXJlbnRzKCcubmF2YmFyLWNhdGVnb3JpZXMtbGVmdCcpLmxlbmd0aCA+IDApIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlQ2xhc3Mob3B0aW9ucy5vcGVuQ2xhc3MpO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIGxldCBpc09iamVjdCA9IHR5cGVvZiBlICA9PT0nb2JqZWN0JyxcbiAgICAgICAgICAgICAgICBpc0V2ZW50ICA9IGlzT2JqZWN0ID8gZS5oYXNPd25Qcm9wZXJ0eSgnb3JpZ2luYWxFdmVudCcpIDogZmFsc2U7XG4gICAgICAgICAgICBpZihpc0V2ZW50KSB7XG4gICAgICAgICAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcbiAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEhlbHBlciBmdW5jdGlvbiB0byBjbGVhciBhbGwgcGVuZGluZ1xuICAgICAgICAgKiBmdW5jdGlvbnNcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfY2xlYXJUaW1lb3V0cyA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIGVudGVyVGltZXIgPSBlbnRlclRpbWVyID8gY2xlYXJUaW1lb3V0KGVudGVyVGltZXIpIDogbnVsbDtcbiAgICAgICAgICAgIGxlYXZlVGltZXIgPSBsZWF2ZVRpbWVyID8gY2xlYXJUaW1lb3V0KGxlYXZlVGltZXIpIDogbnVsbDtcbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGVscGVyIGZ1bmN0aW9uIHRvIHJlc2V0IHRoZSBjc3Mgb2YgdGhlIG1lbnUuXG4gICAgICAgICAqIFRoaXMgaXMgbmVlZGVkIHRvIHJlbW92ZSB0aGUgb3ZlcmZsb3cgJiBoZWlnaHRcbiAgICAgICAgICogc2V0dGluZ3Mgb2YgdGhlIG1lbnUgb2YgdGhlIGNzcyBmaWxlLiBUaGVcbiAgICAgICAgICogZGlyZWN0aXZlcyB3ZXJlIHNldCB0byBwcmV2ZW50IGZsaWNrZXJpbmcgb24gcGFnZVxuICAgICAgICAgKiBsb2FkXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3Jlc2V0SW5pdGlhbENzcyA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICR0aGlzLmNzcyh7XG4gICAgICAgICAgICAgICAgJ292ZXJmbG93JzogJ3Zpc2libGUnXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGVscGVyIGZ1bmN0aW9uIHRvIHNldCBwb3NpdGlvbmluZyBjbGFzc2VzXG4gICAgICAgICAqIHRvIHRoZSBvcGVuZCBmbHlvdXQuIFRoaXMgaXMgbmVlZGVkIHRvIGtlZXBcbiAgICAgICAgICogdGhlIGZseW91dCBpbnNpZGUgdGhlIGJvdW5kYXJpZXMgb2YgdGhlIG5hdmlnYXRpb25cbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfcmVwb3NpdGlvbk9wZW5MYXllciA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHZhciBsaXN0V2lkdGggPSAkbGlzdC53aWR0aCgpLFxuICAgICAgICAgICAgICAgICRvcGVuTGF5ZXIgPSAkZW50cmllc1xuICAgICAgICAgICAgICAgICAgICAuZmlsdGVyKCcuJyArIG9wdGlvbnMub3BlbkNsYXNzKVxuICAgICAgICAgICAgICAgICAgICAuY2hpbGRyZW4oJ3VsJyk7XG5cbiAgICAgICAgICAgICRvcGVuTGF5ZXIuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgdmFyICRzZWxmID0gJCh0aGlzKSxcbiAgICAgICAgICAgICAgICAgICAgJHBhcmVudCA9ICRzZWxmLnBhcmVudCgpO1xuXG4gICAgICAgICAgICAgICAgLy8gUmVzZXQgdGhlIGNsYXNzZXMgdG8gcHJldmVudCB3cm9uZyBjYWxjdWxhdGlvbiBkdWUgdG8gc3BlY2lhbCBzdHlsZXNcbiAgICAgICAgICAgICAgICAkcGFyZW50LnJlbW92ZUNsYXNzKCdmbHlvdXQtcmlnaHQgZmx5b3V0LWxlZnQgZmx5b3V0LWNlbnRlciBmbHlvdXQtd29udC1maXQnKTtcblxuICAgICAgICAgICAgICAgIHZhciB3aWR0aCA9ICRzZWxmLm91dGVyV2lkdGgoKSxcbiAgICAgICAgICAgICAgICAgICAgcGFyZW50UG9zaXRpb24gPSAkcGFyZW50LnBvc2l0aW9uKCkubGVmdCxcbiAgICAgICAgICAgICAgICAgICAgcGFyZW50V2lkdGggPSAkcGFyZW50Lm91dGVyV2lkdGgoKTtcblxuICAgICAgICAgICAgICAgIC8vIENoZWNrIHdpdGNoIGNsYXNzIG5lZWRzIHRvIGJlIHNldFxuICAgICAgICAgICAgICAgIGlmIChsaXN0V2lkdGggPiBwYXJlbnRQb3NpdGlvbiArIHdpZHRoKSB7XG4gICAgICAgICAgICAgICAgICAgICRwYXJlbnQuYWRkQ2xhc3MoJ2ZseW91dC1yaWdodCcpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAocGFyZW50UG9zaXRpb24gKyBwYXJlbnRXaWR0aCAtIHdpZHRoID4gMCkge1xuICAgICAgICAgICAgICAgICAgICAkcGFyZW50LmFkZENsYXNzKCdmbHlvdXQtbGVmdCcpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAod2lkdGggPCBsaXN0V2lkdGgpIHtcbiAgICAgICAgICAgICAgICAgICAgJHBhcmVudC5hZGRDbGFzcygnZmx5b3V0LWNlbnRlcicpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICRwYXJlbnQuYWRkQ2xhc3MoJ2ZseW91dC13b250LWZpdCcpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEhlbHBlciBmdW5jdGlvbiB0byBjYWxjdWxhdGUgdGhlIGRpZmZlcmVuY2UgYmV0d2VlblxuICAgICAgICAgKiB0aGUgc2l6ZSBvZiB0aGUgdmlzaWJsZSBlbGVtZW50cyBpbiB0aGUgbWVudSBhbmQgdGhlXG4gICAgICAgICAqIGNvbnRhaW5lciBzaXplLiBJZiB0aGVyZSBpcyBzcGFjZSwgaXQgY2FsbHMgdGhlIGZ1bmN0aW9uXG4gICAgICAgICAqIHRvIGFjdGl2YXRlIGFuIG1lbnUgZW50cnkgZWxzZSBpdCBjYWxscyB0aGUgZnVuY3Rpb24gdG9cbiAgICAgICAgICogZGVhY3RpdmF0ZSBhIG1lbnUgZW50cnlcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIGUgICAgICAgICBqUXVlcnkgZXZlbnQgb2JqZWN0XG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7c3RyaW5nfSAgICBldmVudE5hbWUgRXZlbnQgbmFtZSBwYXJhbWV0ZXIgb2YgdGhlIGV2ZW50IG9iamVjdFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF91cGRhdGVDYXRlZ29yeU1lbnUgPSBmdW5jdGlvbiAoZSwgZXZlbnROYW1lKSB7XG4gICAgICAgICAgICB2YXIgY29udGFpbmVyV2lkdGggPSAkdGhpcy5pbm5lcldpZHRoKCkgLSBvcHRpb25zLndpZHRoVG9sZXJhbmNlLFxuICAgICAgICAgICAgICAgIHdpZHRoID0gMDtcblxuICAgICAgICAgICAgLy8gQ2hlY2sgaWYgdGhlIGNvbnRhaW5lciB3aWR0aCBoYXMgY2hhbmdlZCBzaW5jZSBsYXN0IGNhbGxcbiAgICAgICAgICAgIGlmIChvcHRpb25zLm1lbnVUeXBlID09PSAnaG9yaXpvbnRhbCdcbiAgICAgICAgICAgICAgICAmJiAoY3VycmVudFdpZHRoICE9PSBjb250YWluZXJXaWR0aCB8fCBldmVudE5hbWUgPT09ICdzd2l0Y2hlZFRvRGVza3RvcCcpKSB7XG5cbiAgICAgICAgICAgICAgICAkbGlzdFxuICAgICAgICAgICAgICAgICAgICAuY2hpbGRyZW4oJzp2aXNpYmxlJylcbiAgICAgICAgICAgICAgICAgICAgLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgd2lkdGggKz0gJCh0aGlzKS5kYXRhKCd3aWR0aCcpO1xuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIC8vIEFkZCBvciByZW1vdmUgZWxlbWVudHMgZGVwZW5kaW5nIG9uIHRoZSBzaXplIG9mIHRoZVxuICAgICAgICAgICAgICAgIC8vIHZpc2libGUgZWxlbWVudHNcbiAgICAgICAgICAgICAgICBpZiAoY29udGFpbmVyV2lkdGggPCB3aWR0aCkge1xuICAgICAgICAgICAgICAgICAgICBfcmVtb3ZlRWxlbWVudCh3aWR0aCAtIGNvbnRhaW5lcldpZHRoKTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBfYWRkRWxlbWVudChjb250YWluZXJXaWR0aCAtIHdpZHRoKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBfcmVwb3NpdGlvbk9wZW5MYXllcigpO1xuXG4gICAgICAgICAgICAgICAgY3VycmVudFdpZHRoID0gY29udGFpbmVyV2lkdGg7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGVscGVyIGZ1bmN0aW9uIHRvIHN3aXRjaCB0byB0aGUgbW9iaWxlXG4gICAgICAgICAqIG1vZGUgb2YgdGhlIG1lbnUuXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3N3aXRjaFRvTW9iaWxlVmlldyA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIC8vIFJlc2V0IHRoZSBjdXJyZW50IHdpZHRoIHNvIHRoYXRcbiAgICAgICAgICAgIC8vIHRoZSBcIl91cGRhdGVDYXRlZ29yeU1lbnVcIiB3aWxsXG4gICAgICAgICAgICAvLyBwZXJmb3JtIGNvcnJlY3RseSBvbiB0aGUgbmV4dCB2aWV3XG4gICAgICAgICAgICAvLyBjaGFuZ2UgdG8gZGVza3RvcFxuICAgICAgICAgICAgY3VycmVudFdpZHRoID0gLTE7XG4gICAgICAgICAgICBfYWRkRWxlbWVudCg5OTk5OTk5OSk7XG5cbiAgICAgICAgICAgICQoJy5sZXZlbC0xJykuY3NzKCdwYWRkaW5nLWJvdHRvbScsICcyMDBweCcpOyAvLyBUaGlzIHBhZGRpbmcgY29ycmVjdHMgZXhwYW5kL2NvbGxhcHNlIGJlaGF2aW9yIG9mIGxvd2VyIG1lbnUgaXRlbXMgaW4gdmFyaW91cyBtb2JpbGUgYnJvd3NlcnMuIFxuXG4gICAgICAgICAgICAvLyBVc2UgdGhlIHZlcnRpY2FsIG1lbnUgb24gbW9iaWxlIHZpZXcuXG4gICAgICAgICAgICBpZiAob3B0aW9ucy5tZW51VHlwZSA9PT0gJ3ZlcnRpY2FsJykge1xuICAgICAgICAgICAgICAgIC8vIGZpeGVzIGRpc3BsYXkgaG9yaXpvbnRhbCBtZW51IGFmdGVyIGEgc3dpdGNoIHRvIG1vYmlsZSBhbmQgYmFjayB0byBkZXNrdG9wIGlzIHBlcmZvcm1lZFxuICAgICAgICAgICAgICAgIGlmICgkKCcjY2F0ZWdvcmllcyBuYXYubmF2YmFyLWRlZmF1bHQ6Zmlyc3QnKS5ub3QoJy5uYXYtY2F0ZWdvcmllcy1sZWZ0JykubGVuZ3RoID4gMCkge1xuICAgICAgICAgICAgICAgICAgICAkKCcjY2F0ZWdvcmllcyBuYXYubmF2YmFyLWRlZmF1bHQ6Zmlyc3QnKS5jc3Moe1xuICAgICAgICAgICAgICAgICAgICAgICAgb3BhY2l0eTogMCxcbiAgICAgICAgICAgICAgICAgICAgICAgIGhlaWdodDogMFxuICAgICAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgICAgICAgICAgICAgLmNoaWxkcmVuKCkuaGlkZSgpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIC8vIG1vdmUgdG9wbWVudS1jb250ZW50IGl0ZW1zIGZyb20gaG9yaXpvbnRhbCBtZW51IHRvIHZlcnRpY2FsIG1lbnVcbiAgICAgICAgICAgICAgICAkdGhpc1xuICAgICAgICAgICAgICAgICAgICAuZmluZCgndWwubGV2ZWwtMSBsaS5uYXZiYXItdG9wYmFyLWl0ZW06Zmlyc3QnKVxuICAgICAgICAgICAgICAgICAgICAuYmVmb3JlKCQoJyNjYXRlZ29yaWVzIG5hdi5uYXZiYXItZGVmYXVsdCBsaS50b3BtZW51LWNvbnRlbnQnKS5kZXRhY2goKSk7XG5cbiAgICAgICAgICAgICAgICAkdGhpcy5hcHBlbmRUbygnI2NhdGVnb3JpZXMgPiAubmF2YmFyLWNvbGxhcHNlJyk7XG4gICAgICAgICAgICAgICAgJHRoaXMuYWRkQ2xhc3MoJ25hdmJhci1kZWZhdWx0IG5hdmJhci1jYXRlZ29yaWVzJyk7XG4gICAgICAgICAgICAgICAgJHRoaXMuZmluZCgndWwubGV2ZWwtMScpLmFkZENsYXNzKCduYXZiYXItbmF2Jyk7XG4gICAgICAgICAgICAgICAgJHRoaXMuZmluZCgnLm5hdmJhci10b3BiYXItaXRlbScpLm5vdCgnLnRvcGJhci1zZWFyY2gnKS5zaG93KCk7XG5cbiAgICAgICAgICAgICAgICBfYmluZEhvcml6b250YWxFdmVudEhhbmRsZXJzKCk7XG5cbiAgICAgICAgICAgICAgICAkYm9keS50cmlnZ2VyKGpzZS5saWJzLnRoZW1lLmV2ZW50cy5NRU5VX1JFUE9TSVRJT05FRCgpLCBbJ3N3aXRjaGVkVG9Nb2JpbGUnXSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEhlbHBlciBmdW5jdGlvbiB0byBzd2l0Y2ggdG8gdGhlIGRlc2t0b3BcbiAgICAgICAgICogbW9kZSBvZiB0aGUgbWVudS4gQWRkaXRpb25hbGx5LCBpbiBjYXNlIHRoYXRcbiAgICAgICAgICogdGhlIGRlc2t0b3AgbW9kZSBpcyBzaG93biBmb3IgdGhlIGZpcnN0IHRpbWVcbiAgICAgICAgICogc2V0IHRoZSBwb3NpdGlvbiBhbmQgd2lkdGggb2YgdGhlIGVsZW1lbnRzXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3N3aXRjaFRvRGVza3RvcFZpZXcgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAkKCcubGV2ZWwtMScpLmNzcygncGFkZGluZy1ib3R0b20nLCAnJyk7IC8vIFJlc2V0IGRpc3BsYXkgZml4IGZvciBtb2JpbGUgYnJvd3NlcnMuXG5cbiAgICAgICAgICAgIC8vIFJldmVydCBhbGwgdGhlIGNoYW5nZXMgbWFkZSBkdXJpbmcgdGhlIHN3aXRjaCB0byBtb2JpbGUuXG4gICAgICAgICAgICBpZiAob3B0aW9ucy5tZW51VHlwZSA9PT0gJ3ZlcnRpY2FsJykge1xuICAgICAgICAgICAgICAgIC8vIGZpeGVzIGRpc3BsYXkgaG9yaXpvbnRhbCBtZW51IGFmdGVyIGEgc3dpdGNoIHRvIG1vYmlsZSBhbmQgYmFjayB0byBkZXNrdG9wIGlzIHBlcmZvcm1lZFxuICAgICAgICAgICAgICAgIGlmICgkKCcjY2F0ZWdvcmllcyBuYXYubmF2YmFyLWRlZmF1bHQ6Zmlyc3QnKS5ub3QoJy5uYXYtY2F0ZWdvcmllcy1sZWZ0JykubGVuZ3RoID4gMCkge1xuICAgICAgICAgICAgICAgICAgICAkKCcjY2F0ZWdvcmllcyBuYXYubmF2YmFyLWRlZmF1bHQ6Zmlyc3QnKS5jc3Moe1xuICAgICAgICAgICAgICAgICAgICAgICAgb3BhY2l0eTogMSxcbiAgICAgICAgICAgICAgICAgICAgICAgIGhlaWdodDogJ2F1dG8nXG4gICAgICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAgICAgICAgICAgICAuY2hpbGRyZW4oKS5zaG93KCk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgLy8gbW92ZSB0b3BtZW51LWNvbnRlbnQgaXRlbXMgYmFjayB0byBob3Jpem9udGFsIG1lbnVcbiAgICAgICAgICAgICAgICB2YXIgJHRvcG1lbnVDb250ZW50RWxlbWVudHMgPSAkdGhpcy5maW5kKCdsaS50b3BtZW51LWNvbnRlbnQnKS5kZXRhY2goKTtcbiAgICAgICAgICAgICAgICAkKCcjY2F0ZWdvcmllcyBuYXYubmF2YmFyLWRlZmF1bHQgdWwubGV2ZWwtMTpmaXJzdCcpLmFwcGVuZCgkdG9wbWVudUNvbnRlbnRFbGVtZW50cyk7XG5cbiAgICAgICAgICAgICAgICAkdGhpcy5hcHBlbmRUbygnLmJveC1jYXRlZ29yaWVzJyk7XG4gICAgICAgICAgICAgICAgJHRoaXMucmVtb3ZlQ2xhc3MoJ25hdmJhci1kZWZhdWx0IG5hdmJhci1jYXRlZ29yaWVzJyk7XG4gICAgICAgICAgICAgICAgJHRoaXMuZmluZCgndWwubGV2ZWwtMScpLnJlbW92ZUNsYXNzKCduYXZiYXItbmF2Jyk7XG4gICAgICAgICAgICAgICAgJHRoaXMuZmluZCgnLm5hdmJhci10b3BiYXItaXRlbScpLmhpZGUoKTtcbiAgICAgICAgICAgICAgICBfdW5iaW5kSG9yaXpvbnRhbEV2ZW50SGFuZGxlcnMoKTtcblxuICAgICAgICAgICAgICAgICRib2R5LnRyaWdnZXIoanNlLmxpYnMudGhlbWUuZXZlbnRzLk1FTlVfUkVQT1NJVElPTkVEKCksIFsnc3dpdGNoZWRUb0Rlc2t0b3AnXSk7XG4gICAgICAgICAgICB9XG5cblxuICAgICAgICAgICAgaWYgKCFpbml0aWFsaXplZFBvcykge1xuICAgICAgICAgICAgICAgIF9pbml0RWxlbWVudFNpemVzQW5kUG9zaXRpb24oKTtcbiAgICAgICAgICAgICAgICBpbml0aWFsaXplZFBvcyA9IHRydWU7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmIChvcHRpb25zLm1lbnVUeXBlID09PSAnaG9yaXpvbnRhbCcpIHtcbiAgICAgICAgICAgICAgICBfdXBkYXRlQ2F0ZWdvcnlNZW51KCk7XG5cbiAgICAgICAgICAgICAgICBpZiAoaXNUb3VjaERldmljZSkge1xuICAgICAgICAgICAgICAgICAgICAkbGlzdC5maW5kKCcuZW50ZXItY2F0ZWdvcnknKS5zaG93KCk7XG4gICAgICAgICAgICAgICAgICAgICRsaXN0LmZpbmQoJy5kcm9wZG93biA+IGEnKS5jbGljayhmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEhlbHBlciBmdW5jdGlvbiB0byBhZGQgdGhlIGNsYXNzIHRvIHRoZSBsaS1lbGVtZW50XG4gICAgICAgICAqIGRlcGVuZGluZyBvbiB0aGUgb3BlbiBldmVudC4gVGhpcyBjYW4gYmUgYSBcInRvdWNoXCJcbiAgICAgICAgICogb3IgYSBcIm1vdXNlXCIgY2xhc3NcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICR0YXJnZXQgICAgICAgICBqUXVlcnkgc2VsZWN0aW9uIG9mIHRoZSBsaS1lbGVtZW50XG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7c3RyaW5nfSAgICBjbGFzc05hbWUgICAgICAgTmFtZSBvZiB0aGUgY2xhc3MgdGhhdCBnZXRzIGFkZGVkXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3NldEV2ZW50VHlwZUNsYXNzID0gZnVuY3Rpb24gKCR0YXJnZXQsIGNsYXNzTmFtZSkge1xuICAgICAgICAgICAgJHRhcmdldFxuICAgICAgICAgICAgICAgIC5yZW1vdmVDbGFzcygndG91Y2ggbW91c2UnKVxuICAgICAgICAgICAgICAgIC5hZGRDbGFzcyhjbGFzc05hbWUgfHwgJycpO1xuICAgICAgICB9O1xuXG5cbi8vICMjIyMjIyMjIyMgTUFJTiBGVU5DVElPTkFMSVRZICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogRnVuY3Rpb24gdGhhdCBnZXRzIGNhbGxlZCBieSB0aGUgYnJlYWtwb2ludCB0cmlnZ2VyXG4gICAgICAgICAqICh3aGljaCBpcyBmaXJlZCBvbiBicm93c2VyIHJlc2l6ZSkuIEl0IGNoZWNrcyBmb3JcbiAgICAgICAgICogQ1NTIHZpZXcgY2hhbmdlcyBhbmQgcmVjb25maWd1cmVzIHRoZSB0aGUgSlMgYmVoYXZpb3VyXG4gICAgICAgICAqIG9mIHRoZSBtZW51IGluIHRoYXQgY2FzZVxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9icmVha3BvaW50SGFuZGxlciA9IGZ1bmN0aW9uICgpIHtcblxuICAgICAgICAgICAgLy8gR2V0IHRoZSBjdXJyZW50IHZpZXdtb2RlXG4gICAgICAgICAgICB2YXIgb2xkTW9kZSA9IG1vZGUgfHwge30sXG4gICAgICAgICAgICAgICAgbmV3TW9kZSA9IGpzZS5saWJzLnRoZW1lLnJlc3BvbnNpdmUuYnJlYWtwb2ludCgpO1xuXG4gICAgICAgICAgICAvLyBPbmx5IGRvIHNvbWV0aGluZyBpZiB0aGUgdmlldyB3YXMgY2hhbmdlZFxuICAgICAgICAgICAgaWYgKG5ld01vZGUuaWQgIT09IG9sZE1vZGUuaWQpIHtcblxuICAgICAgICAgICAgICAgIC8vIENoZWNrIGlmIGEgdmlldyBjaGFuZ2UgYmV0d2VlbiBtb2JpbGUgYW5kIGRlc2t0b3AgdmlldyB3YXMgbWFkZVxuICAgICAgICAgICAgICAgIHZhciBzd2l0Y2hUb01vYmlsZSA9IChuZXdNb2RlLmlkIDw9IG9wdGlvbnMuYnJlYWtwb2ludCAmJiAoIW1vYmlsZSB8fCBvbGRNb2RlLmlkID09PSB1bmRlZmluZWQpKSxcbiAgICAgICAgICAgICAgICAgICAgc3dpdGNoVG9EZXNrdG9wID0gKG5ld01vZGUuaWQgPiBvcHRpb25zLmJyZWFrcG9pbnQgJiYgKG1vYmlsZSB8fCBvbGRNb2RlLmlkID09PSB1bmRlZmluZWQpKTtcblxuICAgICAgICAgICAgICAgIC8vIFN0b3JlIHRoZSBuZXcgdmlldyBzZXR0aW5nc1xuICAgICAgICAgICAgICAgIG1vYmlsZSA9IG5ld01vZGUuaWQgPD0gb3B0aW9ucy5icmVha3BvaW50O1xuICAgICAgICAgICAgICAgIG1vZGUgPSAkLmV4dGVuZCh7fSwgbmV3TW9kZSk7XG5cbiAgICAgICAgICAgICAgICBpZiAoc3dpdGNoVG9Nb2JpbGUgfHwgc3dpdGNoVG9EZXNrdG9wKSB7XG4gICAgICAgICAgICAgICAgICAgIF9jbGVhclRpbWVvdXRzKCk7XG4gICAgICAgICAgICAgICAgICAgIGlmIChvcHRpb25zLm1lbnVUeXBlICE9PSAndmVydGljYWwnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBfY2xvc2VNZW51KCk7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAvLyBDaGFuZ2UgdGhlIHZpc2liaWxpdHkgb2YgdGhlIG1lbnUgaXRlbXNcbiAgICAgICAgICAgICAgICAgICAgLy8gaW4gY2FzZSBvZiBkZXNrdG9wIDwtPiBtb2JpbGUgdmlldyBjaGFuZ2VcbiAgICAgICAgICAgICAgICAgICAgaWYgKG9wdGlvbnMuc3dpdGNoRWxlbWVudFBvc2l0aW9uKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoc3dpdGNoVG9Nb2JpbGUpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBfc3dpdGNoVG9Nb2JpbGVWaWV3KCk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIF9zd2l0Y2hUb0Rlc2t0b3BWaWV3KCk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBfcmVwb3NpdGlvbk9wZW5MYXllcigpO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKCFtb2JpbGUgJiYgb3B0aW9ucy5zd2l0Y2hFbGVtZW50UG9zaXRpb24pIHtcbiAgICAgICAgICAgICAgICAgICAgLy8gVXBkYXRlIHRoZSB2aXNpYmlsaXR5IG9mIHRoZSBtZW51IGl0ZW1zXG4gICAgICAgICAgICAgICAgICAgIC8vIGlmIHRoZSB2aWV3IGNoYW5nZSB3YXMgZGVza3RvcCB0byBkZXNrdG9wIG9ubHlcbiAgICAgICAgICAgICAgICAgICAgX3VwZGF0ZUNhdGVnb3J5TWVudSgpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAoIW1vYmlsZSkge1xuICAgICAgICAgICAgICAgICAgICBfcmVwb3NpdGlvbk9wZW5MYXllcigpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cblxuLy8gIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBDaGFuZ2VzIHRoZSBlcGFuZCAvIGNvbGxhcHNlIHN0YXRlIG9mIHRoZSBtZW51LFxuICAgICAgICAgKiBpZiB0aGVyZSBpcyBhbiBzdWJtZW51LiBJbiB0aGUgb3RoZXIgY2FzZSBpdFxuICAgICAgICAgKiB3aWxsIGxldCBleGVjdXRlIHRoZSBkZWZhdWx0IGFjdGlvbiAobW9zdCB0aW1lc1xuICAgICAgICAgKiB0aGUgZXhlY3V0aW9uIG9mIGEgbGluaylcbiAgICAgICAgICogQHBhcmFtIHtvYmplY3R9ICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3RcbiAgICAgICAgICogQHBhcmFtIHtzdHJpbmd9ICBtb2RlICAgIFRoZSBjdXJyZW50IHZpZXcgbW9kZSAoY2FuIGJlIFwibW9iaWxlXCIgb3IgXCJkZXNrdG9wXCJcbiAgICAgICAgICogQHBhcmFtIHtpbnRlZ2VyfSBkZWxheSAgIEN1c3RvbSBkZWxheSAoaW4gbXMpIGZvciBvcGVuaW5nIGNsb3NpbmcgdGhlIG1lbnUgKG5lZWRlZCBmb3IgY2xpY2sgLyB0b3VjaCBldmVudHMpXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX29wZW5NZW51ID0gZnVuY3Rpb24gKGUsIHR5cGUsIGRlbGF5KSB7XG5cbiAgICAgICAgICAgIHZhciAkc2VsZiA9ICQodGhpcyksXG4gICAgICAgICAgICAgICAgJHN1Ym1lbnUgPSAkc2VsZi5jaGlsZHJlbihcInVsLmRyb3Bkb3duLW1lbnVcIiksXG4gICAgICAgICAgICAgICAgbGVuZ3RoID0gJHN1Ym1lbnUubGVuZ3RoLFxuICAgICAgICAgICAgICAgIGxldmVsID0gKCRzdWJtZW51Lmxlbmd0aCkgPyAoJHN1Ym1lbnUuZGF0YSgnbGV2ZWwnKSB8fCAnMCcpIDogOTksXG4gICAgICAgICAgICAgICAgdmFsaWRTdWJtZW51ID0gKHBhcnNlSW50KGxldmVsLCAxMCkgPD0gMiAmJiBtb2RlLmlkID4gb3B0aW9ucy5icmVha3BvaW50KSB8fCBtb2RlLmlkXG4gICAgICAgICAgICAgICAgICAgIDw9IG9wdGlvbnMuYnJlYWtwb2ludDtcblxuICAgICAgICAgICAgaWYgKHR5cGUgPT09ICdtb2JpbGUnKSB7XG4gICAgICAgICAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy8gT25seSBjaGFuZ2UgdGhlIHN0YXRlIGlmIHRoZXJlIGlzXG4gICAgICAgICAgICAvLyBhIHN1Ym1lbnVcbiAgICAgICAgICAgIGlmIChsZW5ndGggJiYgdmFsaWRTdWJtZW51KSB7XG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgICAgICAgICAgaWYgKHR5cGUgPT09ICdtb2JpbGUnKSB7XG4gICAgICAgICAgICAgICAgICAgIC8vIFNpbXBseSB0b2dnbGUgdGhlIG9wZW5DbGFzcyBpbiBtb2JpbGUgbW9kZVxuICAgICAgICAgICAgICAgICAgICAkc2VsZi50b2dnbGVDbGFzcyhvcHRpb25zLm9wZW5DbGFzcyk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgLy8gUGVyZm9ybSB0aGUgZWxzZSBjYXNlIGZvciB0aGUgZGVza3RvcCB2aWV3XG5cbiAgICAgICAgICAgICAgICAgICAgdmFyIHZpc2libGUgPSAkc2VsZi5oYXNDbGFzcyhvcHRpb25zLm9wZW5DbGFzcyksXG4gICAgICAgICAgICAgICAgICAgICAgICBsZWF2ZSA9ICRzZWxmLmhhc0NsYXNzKCdsZWF2ZScpLFxuICAgICAgICAgICAgICAgICAgICAgICAgYWN0aW9uID0gKGUuZGF0YSAmJiBlLmRhdGEuYWN0aW9uKSA/IGUuZGF0YS5hY3Rpb24gOlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICh2aXNpYmxlICYmIGxlYXZlKSA/ICdlbnRlcicgOlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB2aXNpYmxlID8gJ2xlYXZlJyA6ICdlbnRlcic7XG5cbiAgICAgICAgICAgICAgICAgICAgLy8gUHJldmVudCBvcGVuaW5nIHRoZSBtZW51IGlmIHRoZSB1c2VyIGlzIHNlYXJjaGluZ1xuICAgICAgICAgICAgICAgICAgICBjb25zdCAkbGl2ZVNlYXJjaEZpZWxkID0gJCgnLm5hdmJhci1zZWFyY2gtaW5wdXQtZ3JvdXAgLmZvcm0tY29udHJvbC5zZWFyY2gtaW5wdXQnKTtcbiAgICAgICAgICAgICAgICAgICAgaWYoJGxpdmVTZWFyY2hGaWVsZC5sZW5ndGggJiYgJGxpdmVTZWFyY2hGaWVsZC5pcyhcIjpmb2N1c1wiKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIFxuICAgICAgICAgICAgICAgICAgICAvLyBEZXBlbmRpbmcgb24gdGhlIHZpc2liaWxpdHkgYW5kIHRoZSBldmVudC1hY3Rpb24tcGFyYW1ldGVyXG4gICAgICAgICAgICAgICAgICAgIC8vIHRoZSBzdWJtZW51IGdldHMgb3BlbmVkIG9yIGNsb3NlZFxuICAgICAgICAgICAgICAgICAgICBzd2l0Y2ggKGFjdGlvbikge1xuICAgICAgICAgICAgICAgICAgICAgICAgY2FzZSAnZW50ZXInOlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmICghb25FbnRlciAmJiAhanNlLmxpYnMudGhlbWUuaW50ZXJhY3Rpb24uaXNNb3VzZURvd24oKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBvbkVudGVyID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gU2V0IGEgdGltZXIgZm9yIG9wZW5pbmcgaWYgdGhlIHN1Ym1lbnUgKGRlbGF5ZWQgb3BlbmluZylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgX2NsZWFyVGltZW91dHMoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZW50ZXJUaW1lciA9IHNldFRpbWVvdXQoZnVuY3Rpb24gKCkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBSZW1vdmUgYWxsIG9wZW5DbGFzcy1jbGFzc2VzIGZyb20gdGhlXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBtZW51IGV4Y2VwdCB0aGUgZWxlbWVudCB0byBvcGVuIGFuZCBpdCdzIHBhcmVudHNcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICRsaXN0XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLmZpbmQoJy4nICsgb3B0aW9ucy5vcGVuQ2xhc3MpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLm5vdCgkc2VsZilcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAubm90KCRzZWxmLnBhcmVudHNVbnRpbCgkdGhpcywgJy4nICsgb3B0aW9ucy5vcGVuQ2xhc3MpKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC50cmlnZ2VyKGpzZS5saWJzLnRoZW1lLmV2ZW50cy5UUkFOU0lUSU9OX1NUT1AoKSwgW10pXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLnJlbW92ZUNsYXNzKG9wdGlvbnMub3BlbkNsYXNzKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJGxpc3RcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAuZmluZCgnLmxlYXZlJylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAudHJpZ2dlcihqc2UubGlicy50aGVtZS5ldmVudHMuVFJBTlNJVElPTl9TVE9QKCksIFtdKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5yZW1vdmVDbGFzcygnbGVhdmUnKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gT3BlbiB0aGUgc3VibWVudVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdHJhbnNpdGlvbi5vcGVuID0gdHJ1ZTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gU2V0IGFuZCB1bnNldCB0aGUgXCJvbkVudGVyXCIgdG8gcHJldmVudFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gY2xvc2luZyB0aGUgbWVudSBpbW1lZGlhdGVseSBhZnRlciBvcGVuaW5nIGlmXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyB0aGUgY3Vyc29yIGlzIGF0IGFuIHBsYWNlIG92ZXIgdGhlIG9wZW5pbmcgbWVudVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gKHRoaXMgY2FuIGhhcHBlbiBpZiBvdGhlciBjb21wb25lbnRzIHRyaWdnZXIgdGhlXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBvcGVuIGV2ZW50KVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJHNlbGZcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAub2ZmKGpzZS5saWJzLnRoZW1lLmV2ZW50cy5UUkFOU0lUSU9OX0ZJTklTSEVEKCkpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLm9uZShqc2UubGlicy50aGVtZS5ldmVudHMuVFJBTlNJVElPTl9GSU5JU0hFRCgpLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIG9uRW50ZXIgPSBmYWxzZTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC50cmlnZ2VyKGpzZS5saWJzLnRoZW1lLmV2ZW50cy5UUkFOU0lUSU9OKCksIHRyYW5zaXRpb24pXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLnRyaWdnZXIoanNlLmxpYnMudGhlbWUuZXZlbnRzLk9QRU5fRkxZT1VUKCksIFskdGhpc10pO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBfcmVwb3NpdGlvbk9wZW5MYXllcigpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9LCAodHlwZW9mIGRlbGF5ID09PSAnbnVtYmVyJykgPyBkZWxheSA6IG9wdGlvbnMuZW50ZXJEZWxheSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICAgICAgICAgIGNhc2UgJ2xlYXZlJzpcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBvbkVudGVyID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gU2V0IGEgdGltZXIgZm9yIGNsb3NpbmcgaWYgdGhlIHN1Ym1lbnUgKGRlbGF5ZWQgY2xvc2luZylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBfY2xlYXJUaW1lb3V0cygpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRzZWxmLmFkZENsYXNzKCdsZWF2ZScpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxlYXZlVGltZXIgPSBzZXRUaW1lb3V0KGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gUmVtb3ZlIGFsbCBvcGVuQ2xhc3MtY2xhc3NlcyBmcm9tIHRoZVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBtZW51IGV4Y2VwdCB0aGUgZWxlbWVudHMgcGFyZW50c1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0cmFuc2l0aW9uLm9wZW4gPSBmYWxzZTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJGxpc3RcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5maW5kKCcuJyArIG9wdGlvbnMub3BlbkNsYXNzKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLm5vdCgkc2VsZi5wYXJlbnRzVW50aWwoJHRoaXMsICcuJyArIG9wdGlvbnMub3BlbkNsYXNzKSlcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5vZmYoanNlLmxpYnMudGhlbWUuZXZlbnRzLlRSQU5TSVRJT05fRklOSVNIRUQoKSlcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5vbmUoanNlLmxpYnMudGhlbWUuZXZlbnRzLlRSQU5TSVRJT05fRklOSVNIRUQoKSwgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIF9zZXRFdmVudFR5cGVDbGFzcygkc2VsZiwgJycpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICRzZWxmLnJlbW92ZUNsYXNzKCdsZWF2ZScpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC50cmlnZ2VyKGpzZS5saWJzLnRoZW1lLmV2ZW50cy5UUkFOU0lUSU9OKCksIHRyYW5zaXRpb24pO1xuXG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9LCAodHlwZW9mIGRlbGF5ID09PSAnbnVtYmVyJykgPyBkZWxheSA6IG9wdGlvbnMubGVhdmVEZWxheSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgICAgICAgICBkZWZhdWx0OlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBFdmVudCBoYW5kbGVyIGZvciB0aGUgY2xpY2sgLyBtb3VzZWVudGVyIC8gbW91c2VsZWF2ZSBldmVudFxuICAgICAgICAgKiBvbiB0aGUgbmF2aWdhdGlvbiBsaSBlbGVtZW50cy4gSXQgY2hlY2tzIGlmIHRoZSBldmVudCB0eXBlXG4gICAgICAgICAqIGlzIHN1cHBvcnRlZCBmb3IgdGhlIGN1cnJlbnQgdmlldyB0eXBlIGFuZCBjYWxscyB0aGVcbiAgICAgICAgICogb3Blbk1lbnUtZnVuY3Rpb24gaWYgc28uXG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICBlICAgICAgICAgICBqUXVlcnkgZXZlbnQgb2JqZWN0XG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX21vdXNlSGFuZGxlciA9IGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICB2YXIgJHNlbGYgPSAkKHRoaXMpLFxuICAgICAgICAgICAgICAgIHZpZXdwb3J0ID0gbW9kZS5pZCA8PSBvcHRpb25zLmJyZWFrcG9pbnQgPyAnbW9iaWxlJyA6ICdkZXNrdG9wJyxcbiAgICAgICAgICAgICAgICBldmVudHMgPSAob3B0aW9ucy5ldmVudHMgJiYgb3B0aW9ucy5ldmVudHNbdmlld3BvcnRdKSA/IG9wdGlvbnMuZXZlbnRzW3ZpZXdwb3J0XSA6IFtdO1xuXG4gICAgICAgICAgICBfc2V0RXZlbnRUeXBlQ2xhc3MoJHNlbGYsICdtb3VzZScpO1xuICAgICAgICAgICAgaWYgKCQuaW5BcnJheShlLmRhdGEuZXZlbnQsIGV2ZW50cykgPiAtMSkge1xuICAgICAgICAgICAgICAgIF9vcGVuTWVudS5jYWxsKCRzZWxmLCBlLCB2aWV3cG9ydCwgZS5kYXRhLmRlbGF5KTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy8gUGVyZm9ybSBuYXZpZ2F0aW9uIGZvciBjdXN0b20gbGlua3MgYW5kIGNhdGVnb3J5IGxpbmtzIG9uIHRvdWNoIGRldmljZXMgaWYgbm8gc3ViY2F0ZWdvcmllcyBhcmUgZm91bmQuXG4gICAgICAgICAgICBpZiAoKCRzZWxmLmhhc0NsYXNzKCdjdXN0b20nKSB8fCAoaXNUb3VjaERldmljZSAmJiAkc2VsZi5jaGlsZHJlbigndWwnKS5sZW5ndGggPT0gMCkpXG4gICAgICAgICAgICAgICAgJiYgZS5kYXRhLmV2ZW50ID09PSAnY2xpY2snICYmICEkc2VsZi5maW5kKCdmb3JtJykubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cbiAgICAgICAgICAgICAgICBpZiAoJHNlbGYuZmluZCgnYScpLmF0dHIoJ3RhcmdldCcpID09PSAnX2JsYW5rJykge1xuICAgICAgICAgICAgICAgICAgICB3aW5kb3cub3Blbigkc2VsZi5maW5kKCdhJykuYXR0cignaHJlZicpKTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBsb2NhdGlvbi5ocmVmID0gJHNlbGYuZmluZCgnYScpLmF0dHIoJ2hyZWYnKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEV2ZW50IGhhbmRsZXIgZm9yIHRoZSB0b3VjaHN0YXJ0IGV2ZW50IChvciBcInBvaW50ZXJkb3duXCJcbiAgICAgICAgICogZGVwZW5kaW5nIG9uIHRoZSBicm93c2VyKS4gSXQgcmVtb3ZlcyB0aGUgb3RoZXIgY3JpdGljYWxcbiAgICAgICAgICogZXZlbnQgaGFuZGxlciAodGhhdCB3b3VsZCBvcGVuIHRoZSBtZW51KSBmcm9tIHRoZSBsaXN0XG4gICAgICAgICAqIGVsZW1lbnQgaWYgdGhlIHRoZSBtb3VzZWVudGVyIHdhcyBleGVjdXRlZCBiZWZvcmUgYW5kXG4gICAgICAgICAqIGEgY2xpY2sgb3IgdG91Y2ggZXZlbnQgd2lsbCBiZSBwZXJmb3JtZWQgYWZ0ZXJ3YXJkcy4gVGhpc1xuICAgICAgICAgKiBpcyBuZWVkZWQgdG8gcHJldmVudCB0aGUgYnJvd3NlciBlbmdpbmUgd29ya2Fyb3VuZHMgd2hpY2hcbiAgICAgICAgICogd2lsbCBhdXRvbWF0aWNhbGx5IHBlcmZvcm0gbW91c2UgLyBjbGljay1ldmVudHMgb24gdG91Y2hcbiAgICAgICAgICogYWxzby5cbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfdG91Y2hIYW5kbGVyID0gZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cbiAgICAgICAgICAgIHZhciAkc2VsZiA9ICQodGhpcyksXG4gICAgICAgICAgICAgICAgdmlld3BvcnQgPSBtb2RlLmlkIDw9IG9wdGlvbnMuYnJlYWtwb2ludCA/ICdtb2JpbGUnIDogJ2Rlc2t0b3AnLFxuICAgICAgICAgICAgICAgIGV2ZW50cyA9IChvcHRpb25zLmV2ZW50cyAmJiBvcHRpb25zLmV2ZW50c1t2aWV3cG9ydF0pID8gb3B0aW9ucy5ldmVudHNbdmlld3BvcnRdIDogW107XG5cbiAgICAgICAgICAgICRsaXN0LmZpbmQoJy5lbnRlci1jYXRlZ29yeScpLnNob3coKTtcbiAgICAgICAgICAgICRsaXN0LmZpbmQoJy5kcm9wZG93biA+IGEnKS5vbignY2xpY2snLCBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICBpZiAoZS5kYXRhLnR5cGUgPT09ICdzdGFydCcpIHtcbiAgICAgICAgICAgICAgICB0b3VjaGVTdGFydEV2ZW50ID0ge2V2ZW50OiBlLCB0aW1lc3RhbXA6IG5ldyBEYXRlKCkuZ2V0VGltZSgpLCB0b3A6ICR3aW5kb3cuc2Nyb2xsVG9wKCl9O1xuICAgICAgICAgICAgICAgICRsaXN0Lm9mZignbW91c2VlbnRlci5tZW51IG1vdXNlbGVhdmUubWVudScpO1xuICAgICAgICAgICAgfSBlbHNlIGlmICgkLmluQXJyYXkoJ3RvdWNoJywgZXZlbnRzKSA+IC0xICYmICFfdG91Y2hNb3ZlRGV0ZWN0KGUpKSB7XG4gICAgICAgICAgICAgICAgX3NldEV2ZW50VHlwZUNsYXNzKCRzZWxmLCAndG91Y2gnKTtcblxuICAgICAgICAgICAgICAgIGlmICgkLmluQXJyYXkoJ2hvdmVyJywgZXZlbnRzKSA9PT0gLTEgfHwgdG91Y2hFdmVudHMuc3RhcnQgIT09ICdwb2ludGVyZG93bicpIHtcbiAgICAgICAgICAgICAgICAgICAgX29wZW5NZW51LmNhbGwoJHNlbGYsIGUsIHZpZXdwb3J0KTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAkbGlzdC5vbignbW91c2VsZWF2ZScsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgJGxpc3RcbiAgICAgICAgICAgICAgICAgICAgICAgIC5vbignbW91c2VlbnRlci5tZW51JywgJ2xpJywge2V2ZW50OiAnaG92ZXInfSwgX21vdXNlSGFuZGxlcilcbiAgICAgICAgICAgICAgICAgICAgICAgIC5vbignbW91c2VsZWF2ZS5tZW51JywgJ2xpJywge2V2ZW50OiAnaG92ZXInLCBhY3Rpb246ICdsZWF2ZSd9LCBfbW91c2VIYW5kbGVyKTtcbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFN0b3JlcyB0aGUgbGFzdCB0b3VjaCBwb3NpdGlvbiBvbiB0b3VjaG1vdmVcbiAgICAgICAgICogQHBhcmFtICAgICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF90b3VjaE1vdmVIYW5kbGVyID0gZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIHRvdWNoZUVuZEV2ZW50ID0ge2V2ZW50OiBlLCB0aW1lc3RhbXA6IG5ldyBEYXRlKCkuZ2V0VGltZSgpLCB0b3A6ICR3aW5kb3cuc2Nyb2xsVG9wKCl9O1xuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBFdmVudCBoYW5kbGVyIGZvciBjbG9zaW5nIHRoZSBtZW51IGlmXG4gICAgICAgICAqIHRoZSB1c2VyIGludGVyYWN0cyB3aXRoIHRoZSBwYWdlXG4gICAgICAgICAqIG91dHNpZGUgb2YgdGhlIG1lbnVcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAgICAgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgZCAgICAgICBqUXVlcnkgc2VsZWN0aW9uIG9mIHRoZSBldmVudCBlbWl0dGVyXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX2Nsb3NlRmx5b3V0ID0gZnVuY3Rpb24gKGUsIGQpIHtcbiAgICAgICAgICAgIGlmIChkICE9PSAkdGhpcyAmJiAkdGhpcy5maW5kKCQoZS50YXJnZXQpKS5sZW5ndGggPT09IDApIHtcbiAgICAgICAgICAgICAgICAvLyBSZW1vdmUgb3BlbiBhbmQgY2xvc2UgdGltZXJcbiAgICAgICAgICAgICAgICBfY2xlYXJUaW1lb3V0cygpO1xuXG4gICAgICAgICAgICAgICAgLy8gUmVtb3ZlIGFsbCBzdGF0ZS1jbGFzc2VzIGZyb20gdGhlIG1lbnVcbiAgICAgICAgICAgICAgICBpZiAob3B0aW9ucy5tZW51VHlwZSA9PT0gJ2hvcml6b250YWwnKSB7XG4gICAgICAgICAgICAgICAgICAgICRsaXN0XG4gICAgICAgICAgICAgICAgICAgICAgICAuZmluZCgnLnRvdWNoLCAubW91c2UsIC5sZWF2ZSwgLicgKyBvcHRpb25zLm9wZW5DbGFzcylcbiAgICAgICAgICAgICAgICAgICAgICAgIC5yZW1vdmVDbGFzcygndG91Y2ggbW91c2UgbGVhdmUgJyArIG9wdGlvbnMub3BlbkNsYXNzKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgdmFyIF9vbkNsaWNrQWNjb3JkaW9uID0gZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cbiAgICAgICAgICAgIGlmICgkKHRoaXMpLnBhcmVudHMoJy5uYXZiYXItdG9wYmFyLWl0ZW0nKS5sZW5ndGggPiAwKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAoJCh0aGlzKS5oYXNDbGFzcygnZHJvcGRvd24nKSkge1xuICAgICAgICAgICAgICAgIGlmICgkKHRoaXMpLmhhc0NsYXNzKG9wdGlvbnMub3BlbkNsYXNzKSkge1xuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpXG4gICAgICAgICAgICAgICAgICAgICAgICAucmVtb3ZlQ2xhc3Mob3B0aW9ucy5vcGVuQ2xhc3MpXG4gICAgICAgICAgICAgICAgICAgICAgICAuZmluZCgnLicgKyBvcHRpb25zLm9wZW5DbGFzcylcbiAgICAgICAgICAgICAgICAgICAgICAgIC5yZW1vdmVDbGFzcyhvcHRpb25zLm9wZW5DbGFzcyk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKVxuICAgICAgICAgICAgICAgICAgICAgICAgLmFkZENsYXNzKG9wdGlvbnMub3BlbkNsYXNzKVxuICAgICAgICAgICAgICAgICAgICAgICAgLnBhcmVudHNVbnRpbCgkdGhpcywgJ2xpJylcbiAgICAgICAgICAgICAgICAgICAgICAgIC5hZGRDbGFzcyhvcHRpb25zLm9wZW5DbGFzcyk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICBsb2NhdGlvbi5ocmVmID0gJCh0aGlzKS5maW5kKCdhJykuYXR0cignaHJlZicpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9O1xuXG4gICAgICAgIHZhciBfYmluZEhvcml6b250YWxFdmVudEhhbmRsZXJzID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgJGxpc3RcbiAgICAgICAgICAgICAgICAub24odG91Y2hFdmVudHMuc3RhcnQgKyAnLm1lbnUnLCAnbGknLCB7dHlwZTogJ3N0YXJ0J30sIF90b3VjaEhhbmRsZXIpXG4gICAgICAgICAgICAgICAgLm9uKHRvdWNoRXZlbnRzLm1vdmUgKyAnLm1lbnUnLCAnbGknLCB7dHlwZTogJ3N0YXJ0J30sIF90b3VjaE1vdmVIYW5kbGVyKVxuICAgICAgICAgICAgICAgIC5vbih0b3VjaEV2ZW50cy5lbmQgKyAnLm1lbnUnLCAnbGknLCB7dHlwZTogJ2VuZCd9LCBfdG91Y2hIYW5kbGVyKVxuICAgICAgICAgICAgICAgIC5vbignY2xpY2subWVudScsICdsaScsIHtldmVudDogJ2NsaWNrJywgJ2RlbGF5JzogMH0sIF9tb3VzZUhhbmRsZXIpXG4gICAgICAgICAgICAgICAgLm9uKCdtb3VzZWVudGVyLm1lbnUnLCAnbGknLCB7ZXZlbnQ6ICdob3ZlcicsIGFjdGlvbjogJ2VudGVyJ30sIF9tb3VzZUhhbmRsZXIpXG4gICAgICAgICAgICAgICAgLm9uKCdtb3VzZWxlYXZlLm1lbnUnLCAnbGknLCB7ZXZlbnQ6ICdob3ZlcicsIGFjdGlvbjogJ2xlYXZlJ30sIF9tb3VzZUhhbmRsZXIpO1xuXG4gICAgICAgICAgICAkYm9keVxuICAgICAgICAgICAgICAgIC5vbihqc2UubGlicy50aGVtZS5ldmVudHMuTUVOVV9SRVBPU0lUSU9ORUQoKSwgX3VwZGF0ZUNhdGVnb3J5TWVudSk7XG4gICAgICAgIH07XG5cbiAgICAgICAgdmFyIF91bmJpbmRIb3Jpem9udGFsRXZlbnRIYW5kbGVycyA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICRsaXN0XG4gICAgICAgICAgICAgICAgLm9mZih0b3VjaEV2ZW50cy5zdGFydCArICcubWVudScsICdsaScpXG4gICAgICAgICAgICAgICAgLm9mZih0b3VjaEV2ZW50cy5tb3ZlICsgJy5tZW51JywgJ2xpJylcbiAgICAgICAgICAgICAgICAub2ZmKHRvdWNoRXZlbnRzLmVuZCArICcubWVudScsICdsaScpXG4gICAgICAgICAgICAgICAgLm9mZignY2xpY2subWVudScsICdsaScpXG4gICAgICAgICAgICAgICAgLm9mZignbW91c2VlbnRlci5tZW51JywgJ2xpJylcbiAgICAgICAgICAgICAgICAub2ZmKCdtb3VzZWxlYXZlLm1lbnUnLCAnbGknKTtcbiAgICAgICAgfTtcblxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuICAgICAgICAgKiBAY29uc3RydWN0b3JcbiAgICAgICAgICovXG4gICAgICAgIG1vZHVsZS5pbml0ID0gZnVuY3Rpb24gKGRvbmUpIHtcbiAgICAgICAgICAgIC8vIEB0b2RvIEdldHRpbmcgdGhlIFwidG91Y2hFdmVudHNcIiBjb25maWcgdmFsdWUgcHJvZHVjZXMgcHJvYmxlbXMgaW4gdGFibGV0IGRldmljZXMuXG4gICAgICAgICAgICB0b3VjaEV2ZW50cyA9IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ3RvdWNoJyk7XG4gICAgICAgICAgICB0cmFuc2l0aW9uLmNsYXNzT3BlbiA9IG9wdGlvbnMub3BlbkNsYXNzO1xuXG4gICAgICAgICAgICBfZ2V0U2VsZWN0aW9ucygpO1xuICAgICAgICAgICAgX3Jlc2V0SW5pdGlhbENzcygpO1xuXG4gICAgICAgICAgICAkYm9keVxuICAgICAgICAgICAgICAgIC5vbihqc2UubGlicy50aGVtZS5ldmVudHMuQlJFQUtQT0lOVCgpLCBfYnJlYWtwb2ludEhhbmRsZXIpXG4gICAgICAgICAgICAgICAgLm9uKGpzZS5saWJzLnRoZW1lLmV2ZW50cy5PUEVOX0ZMWU9VVCgpICsgJyBjbGljayAnICsgdG91Y2hFdmVudHMuZW5kLCBfY2xvc2VGbHlvdXQpO1xuXG4gICAgICAgICAgICAkKCcuY2xvc2UtbWVudS1jb250YWluZXInKS5vbigndG91Y2hzdGFydCB0b3VjaGVuZCBjbGljaycsIF9jbG9zZU1lbnUpXG5cbiAgICAgICAgICAgICQoJy5jbG9zZS1mbHlvdXQnKS5vbigndG91Y2hzdGFydCB0b3VjaGVuZCBjbGljaycsIF9jbG9zZU1lbnUpO1xuXG4gICAgICAgICAgICBpZiAob3B0aW9ucy5tZW51VHlwZSA9PT0gJ2hvcml6b250YWwnKSB7XG4gICAgICAgICAgICAgICAgX2JpbmRIb3Jpem9udGFsRXZlbnRIYW5kbGVycygpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAob3B0aW9ucy5tZW51VHlwZSA9PT0gJ3ZlcnRpY2FsJykge1xuICAgICAgICAgICAgICAgIGlmIChvcHRpb25zLmFjY29yZGlvbiA9PT0gdHJ1ZSkge1xuICAgICAgICAgICAgICAgICAgICAkdGhpcy5vbignY2xpY2snLCAnbGknLCBfb25DbGlja0FjY29yZGlvbik7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgLy8gaWYgdGhlcmUgaXMgbm8gdG9wIGhlYWRlciB3ZSBtdXN0IGNyZWF0ZSBkdW1teSBodG1sIGJlY2F1c2Ugb3RoZXIgbW9kdWxlcyB3aWxsIG5vdCB3b3JrIGNvcnJlY3RseVxuICAgICAgICAgICAgICAgIGlmICgkKCcjY2F0ZWdvcmllcycpLmxlbmd0aCA9PT0gMCkge1xuICAgICAgICAgICAgICAgICAgICB2YXIgaHRtbCA9ICc8ZGl2IGlkPVwiY2F0ZWdvcmllc1wiPjxkaXYgY2xhc3M9XCJuYXZiYXItY29sbGFwc2UgY29sbGFwc2VcIj4nXG4gICAgICAgICAgICAgICAgICAgICAgICArICc8bmF2IGNsYXNzPVwibmF2YmFyLWRlZmF1bHQgbmF2YmFyLWNhdGVnb3JpZXMgaGlkZGVuXCI+PC9uYXY+PC9kaXY+PC9kaXY+JztcbiAgICAgICAgICAgICAgICAgICAgJCgnI2hlYWRlcicpLmFwcGVuZChodG1sKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIF9icmVha3BvaW50SGFuZGxlcigpO1xuXG4gICAgICAgICAgICAvKipcbiAgICAgICAgICAgICAqIFN0b3AgdGhlIHByb3BhZ2F0aW9uIG9mIHRoZSBldmVudHMgaW5zaWRlIHRoaXMgY29udGFpbmVyXG4gICAgICAgICAgICAgKiAoV29ya2Fyb3VuZCBmb3IgdGhlIFwibW9yZVwiLWRyb3Bkb3duKVxuICAgICAgICAgICAgICovXG4gICAgICAgICAgICAkdGhpc1xuICAgICAgICAgICAgICAgIC5maW5kKCcuJyArIG9wdGlvbnMuaWdub3JlQ2xhc3MpXG4gICAgICAgICAgICAgICAgLm9uKCdtb3VzZWxlYXZlLm1lbnUgbW91c2VlbnRlci5tZW51IGNsaWNrLm1lbnUgJyArIHRvdWNoRXZlbnRzLnN0YXJ0ICsgJyAnXG4gICAgICAgICAgICAgICAgICAgICsgdG91Y2hFdmVudHMuZW5kLCAnbGknLCBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgICAgICAgICBlLnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICBpZiAob3B0aW9ucy5vcGVuQWN0aXZlKSB7XG4gICAgICAgICAgICAgICAgdmFyICRhY3RpdmUgPSAkdGhpcy5maW5kKCcuYWN0aXZlJyk7XG4gICAgICAgICAgICAgICAgJGFjdGl2ZVxuICAgICAgICAgICAgICAgICAgICAucGFyZW50c1VudGlsKCR0aGlzLCAnbGknKVxuICAgICAgICAgICAgICAgICAgICAuYWRkQ2xhc3MoJ29wZW4nKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgJCgnbGkuY3VzdG9tLWVudHJpZXMgYScpLm9uKCdjbGljaycsIGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB2YXIgdmlld3BvcnQgPSBtb2RlLmlkIDw9IG9wdGlvbnMuYnJlYWtwb2ludCA/ICdtb2JpbGUnIDogJ2Rlc2t0b3AnO1xuXG4gICAgICAgICAgICBpZiAodmlld3BvcnQgPT0gJ21vYmlsZScpIHtcbiAgICAgICAgICAgICAgICAkKCcubGV2ZWwtMScpLmNzcygncGFkZGluZy1ib3R0b20nLCAnMjAwcHgnKTsgLy8gVGhpcyBwYWRkaW5nIGNvcnJlY3RzIGV4cGFuZC9jb2xsYXBzZSBiZWhhdmlvciBvZiBsb3dlciBtZW51IGl0ZW1zIGluIHZhcmlvdXMgbW9iaWxlIGJyb3dzZXJzLiBcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgZG9uZSgpO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcbiAgICAgICAgcmV0dXJuIG1vZHVsZTtcbiAgICB9KTtcbiJdfQ==
