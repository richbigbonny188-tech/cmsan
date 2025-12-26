'use strict';

/* --------------------------------------------------------------
 product_listing_filter.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Component for switching the view and submitting
 * the filter settings on change at the product
 * listing page
 */
gambio.widgets.module('product_listing_filter', ['url_arguments', gambio.source + '/libs/events'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        $body = $('body'),
        $target = null,
        $form = null,
        $hidden = null,
        $viewmode = null,
        $pagination = null,
        historyAvailable = false,
        transition = {},
        defaults = {
        target: null // The target the classes getting added (e.g. the product list)
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## HELPER FUNCTIONS ##########

    /**
     * Helper function to switch the view of the
     * list. If an animation is given in the
     * option execute it
     * @param       {object}    config      Contains the "data" values of the clicked element.
     * @private
     */
    var _switchView = function _switchView(config) {

        // Get all "add" classes from the other buttons
        // to remove them in the next step
        var removeClasses = '';
        $viewmode.find('a').each(function () {
            var $self = $(this),
                dataset = $self.parseModuleData('product_listing_filter');

            if (config.add !== dataset.add) {
                removeClasses += dataset.add + ' ';
                $self.removeClass('active');
            } else {
                $self.addClass('active');
            }
        });

        // Switch the classes after the fadeout transition finished
        // and then start the fadein animation
        transition.open = false;
        $target.off(jse.libs.theme.events.TRANSITION_FINISHED()).one(jse.libs.theme.events.TRANSITION_FINISHED(), function () {
            transition.open = true;
            $target.removeClass(removeClasses).addClass(config.add).trigger(jse.libs.theme.events.TRANSITION(), transition);
        }).trigger(jse.libs.theme.events.TRANSITION(), transition);
    };

    /**
     * Sets the pagination URLs on viewmode
     * change, so that the parameter "view_mode"
     * is set correctly in the URL
     * @param       {string}        mode        The value of the view_mode-parameter
     * @private
     */
    var _setPaginationURLs = function _setPaginationURLs(mode) {
        $pagination.find('a').each(function () {
            var url = $(this).attr('href');
            $(this).attr('href', jse.libs.url_arguments.replaceParameterValue(url, 'view_mode', mode));
        });
    };

    // ########## EVENT HANDLER ##########

    /**
     * Function that gets called if a view change
     * is triggered. It checks the current state of
     * the buttons and siwtches the view if the button
     * state has changed. If the history object is
     * available the viewchange gets logged to that
     * object
     * @param         {object}      e           jQuery event object
     * @private
     */
    var _viewChangeHandler = function _viewChangeHandler(e) {
        // Only prevent the default behaviour
        // if the functions gets called by an event
        // handler
        if (e) {
            e.preventDefault();
        }

        // Get the settings for this button
        var $self = $(this),
            dataset = $self.parseModuleData('product_listing_filter'),
            viewMode = dataset.urlParam;

        // Only do something if the state isn't already set
        if (!$self.hasClass('active')) {

            // Close all opened layers
            $this.trigger(jse.libs.theme.events.OPEN_FLYOUT(), $this);

            // Add / remove classes
            _switchView(dataset);

            // Update the pagination URLs
            _setPaginationURLs(viewMode);

            // Set the hidden value for the viewmode
            // so that the submit will transfer correct
            // values
            $hidden.val(viewMode);

            // If needed, add an history element
            // (the history parameter is set via the user-click event only)
            if (historyAvailable && e && e.data && e.data.history) {
                var url = jse.libs.url_arguments.replaceParameterValue(location.href, 'view_mode', viewMode);

                history.pushState({ state: viewMode }, viewMode, url);

                // Trigger a pushstate event to notify other widgets
                // about the url change
                $this.trigger('pushstate', { state: viewMode });
            }
        }
    };

    /**
     * Event handler to change the view depending
     * on the history state
     * @param       {object}    e       jQuery event object
     * @param       {object}    d       JSON object that contains the state (if e.originalEvent.state isn't set)
     * @private
     */
    var _historyHandler = function _historyHandler(e, d) {
        var eventData = d || (e.originalEvent ? e.originalEvent : { state: '' }),
            $button = $viewmode.find('[data-product_listing_filter-url-param="' + eventData.state + '"]');

        if ($button.length && !d.noButton) {
            _viewChangeHandler.call($button);
        } else {
            // Get the settings for this button
            var $activeButton = $this.find('.jsPanelViewmode a.active'),
                dataset = $activeButton.parseModuleData('product_listing_filter');
            _setPaginationURLs(dataset.urlParam);
        }
    };

    /**
     * Event handler for the submit action
     * on change of the selects
     * @private
     */
    var _changeHandler = function _changeHandler() {
        $form.submit();
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        $target = $(options.target);
        $form = $this.find('form');
        $hidden = $form.find('input[name="view_mode"]');
        $viewmode = $this.find('.jsPanelViewmode');
        $pagination = $this.find('.pagination');
        historyAvailable = jse.core.config.get('history');
        transition.classClose = 'fadeOut';

        // Replace the current history entry with
        // one with a dataset that represent the
        // current state
        if (historyAvailable) {
            var viewMode = jse.libs.url_arguments.getUrlParameters().view_mode,
                state = history.state || {},
                url = jse.libs.url_arguments.replaceParameterValue(location.href, 'view_mode', viewMode);

            state.state = viewMode;
            history.replaceState(state, viewMode, url);
        }

        // Bind listener for user input
        $this.on('change', 'select.jsReload', _changeHandler).on('click', '.jsPanelViewmode a', { history: true }, _viewChangeHandler);

        // Bind event listener to check
        // if the history entry has changed
        $body.on('pushstate pushstate_no_history', _historyHandler);
        $(window).on('popstate', _historyHandler);

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcHJvZHVjdF9saXN0aW5nX2ZpbHRlci5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwic291cmNlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiRib2R5IiwiJHRhcmdldCIsIiRmb3JtIiwiJGhpZGRlbiIsIiR2aWV3bW9kZSIsIiRwYWdpbmF0aW9uIiwiaGlzdG9yeUF2YWlsYWJsZSIsInRyYW5zaXRpb24iLCJkZWZhdWx0cyIsInRhcmdldCIsIm9wdGlvbnMiLCJleHRlbmQiLCJfc3dpdGNoVmlldyIsImNvbmZpZyIsInJlbW92ZUNsYXNzZXMiLCJmaW5kIiwiZWFjaCIsIiRzZWxmIiwiZGF0YXNldCIsInBhcnNlTW9kdWxlRGF0YSIsImFkZCIsInJlbW92ZUNsYXNzIiwiYWRkQ2xhc3MiLCJvcGVuIiwib2ZmIiwianNlIiwibGlicyIsInRoZW1lIiwiZXZlbnRzIiwiVFJBTlNJVElPTl9GSU5JU0hFRCIsIm9uZSIsInRyaWdnZXIiLCJUUkFOU0lUSU9OIiwiX3NldFBhZ2luYXRpb25VUkxzIiwibW9kZSIsInVybCIsImF0dHIiLCJ1cmxfYXJndW1lbnRzIiwicmVwbGFjZVBhcmFtZXRlclZhbHVlIiwiX3ZpZXdDaGFuZ2VIYW5kbGVyIiwiZSIsInByZXZlbnREZWZhdWx0Iiwidmlld01vZGUiLCJ1cmxQYXJhbSIsImhhc0NsYXNzIiwiT1BFTl9GTFlPVVQiLCJ2YWwiLCJoaXN0b3J5IiwibG9jYXRpb24iLCJocmVmIiwicHVzaFN0YXRlIiwic3RhdGUiLCJfaGlzdG9yeUhhbmRsZXIiLCJkIiwiZXZlbnREYXRhIiwib3JpZ2luYWxFdmVudCIsIiRidXR0b24iLCJsZW5ndGgiLCJub0J1dHRvbiIsImNhbGwiLCIkYWN0aXZlQnV0dG9uIiwiX2NoYW5nZUhhbmRsZXIiLCJzdWJtaXQiLCJpbml0IiwiZG9uZSIsImNvcmUiLCJnZXQiLCJjbGFzc0Nsb3NlIiwiZ2V0VXJsUGFyYW1ldGVycyIsInZpZXdfbW9kZSIsInJl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QUFJQyxJQUFKLENBQVNXLGFBQVQsQ0FBdUIrQixnQkFBdkIsR0FBMENDLFNBRHpEO0FBQUEsZ0JBRUlsQixRQUFRSixRQUFRSSxLQUFSLElBQWlCLEVBRjdCO0FBQUEsZ0JBR0loQixNQUFNVixJQUFJQyxJQUFKLENBQVNXLGFBQVQsQ0FBdUJDLHFCQUF2QixDQUE2Q1UsU0FBU0MsSUFBdEQsRUFBNEQsV0FBNUQsRUFBeUVQLFFBQXpFLENBSFY7O0FBS0FTLGtCQUFNQSxLQUFOLEdBQWNULFFBQWQ7QUFDQUssb0JBQVF1QixZQUFSLENBQXFCbkIsS0FBckIsRUFBNEJULFFBQTVCLEVBQXNDUCxHQUF0QztBQUNIOztBQUVEO0FBQ0FyQyxjQUNLeUUsRUFETCxDQUNRLFFBRFIsRUFDa0IsaUJBRGxCLEVBQ3FDVixjQURyQyxFQUVLVSxFQUZMLENBRVEsT0FGUixFQUVpQixvQkFGakIsRUFFdUMsRUFBQ3hCLFNBQVMsSUFBVixFQUZ2QyxFQUV3RFIsa0JBRnhEOztBQUlBO0FBQ0E7QUFDQXZDLGNBQU11RSxFQUFOLENBQVMsZ0NBQVQsRUFBMkNuQixlQUEzQztBQUNBckQsVUFBRXlFLE1BQUYsRUFBVUQsRUFBVixDQUFhLFVBQWIsRUFBeUJuQixlQUF6Qjs7QUFFQVk7QUFDSCxLQWxDRDs7QUFvQ0E7QUFDQSxXQUFPckUsTUFBUDtBQUNILENBOU5MIiwiZmlsZSI6IndpZGdldHMvcHJvZHVjdF9saXN0aW5nX2ZpbHRlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gcHJvZHVjdF9saXN0aW5nX2ZpbHRlci5qcyAyMDE2LTAzLTA5XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBDb21wb25lbnQgZm9yIHN3aXRjaGluZyB0aGUgdmlldyBhbmQgc3VibWl0dGluZ1xuICogdGhlIGZpbHRlciBzZXR0aW5ncyBvbiBjaGFuZ2UgYXQgdGhlIHByb2R1Y3RcbiAqIGxpc3RpbmcgcGFnZVxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG4gICAgJ3Byb2R1Y3RfbGlzdGluZ19maWx0ZXInLFxuXG4gICAgW1xuICAgICAgICAndXJsX2FyZ3VtZW50cycsXG4gICAgICAgIGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvZXZlbnRzJ1xuICAgIF0sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgdmFyICR0aGlzID0gJCh0aGlzKSxcbiAgICAgICAgICAgICRib2R5ID0gJCgnYm9keScpLFxuICAgICAgICAgICAgJHRhcmdldCA9IG51bGwsXG4gICAgICAgICAgICAkZm9ybSA9IG51bGwsXG4gICAgICAgICAgICAkaGlkZGVuID0gbnVsbCxcbiAgICAgICAgICAgICR2aWV3bW9kZSA9IG51bGwsXG4gICAgICAgICAgICAkcGFnaW5hdGlvbiA9IG51bGwsXG4gICAgICAgICAgICBoaXN0b3J5QXZhaWxhYmxlID0gZmFsc2UsXG4gICAgICAgICAgICB0cmFuc2l0aW9uID0ge30sXG4gICAgICAgICAgICBkZWZhdWx0cyA9IHtcbiAgICAgICAgICAgICAgICB0YXJnZXQ6IG51bGwgICAgICAgIC8vIFRoZSB0YXJnZXQgdGhlIGNsYXNzZXMgZ2V0dGluZyBhZGRlZCAoZS5nLiB0aGUgcHJvZHVjdCBsaXN0KVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuICAgICAgICAgICAgbW9kdWxlID0ge307XG5cblxuLy8gIyMjIyMjIyMjIyBIRUxQRVIgRlVOQ1RJT05TICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGVscGVyIGZ1bmN0aW9uIHRvIHN3aXRjaCB0aGUgdmlldyBvZiB0aGVcbiAgICAgICAgICogbGlzdC4gSWYgYW4gYW5pbWF0aW9uIGlzIGdpdmVuIGluIHRoZVxuICAgICAgICAgKiBvcHRpb24gZXhlY3V0ZSBpdFxuICAgICAgICAgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgY29uZmlnICAgICAgQ29udGFpbnMgdGhlIFwiZGF0YVwiIHZhbHVlcyBvZiB0aGUgY2xpY2tlZCBlbGVtZW50LlxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9zd2l0Y2hWaWV3ID0gZnVuY3Rpb24gKGNvbmZpZykge1xuXG4gICAgICAgICAgICAvLyBHZXQgYWxsIFwiYWRkXCIgY2xhc3NlcyBmcm9tIHRoZSBvdGhlciBidXR0b25zXG4gICAgICAgICAgICAvLyB0byByZW1vdmUgdGhlbSBpbiB0aGUgbmV4dCBzdGVwXG4gICAgICAgICAgICB2YXIgcmVtb3ZlQ2xhc3NlcyA9ICcnO1xuICAgICAgICAgICAgJHZpZXdtb2RlXG4gICAgICAgICAgICAgICAgLmZpbmQoJ2EnKVxuICAgICAgICAgICAgICAgIC5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyICRzZWxmID0gJCh0aGlzKSxcbiAgICAgICAgICAgICAgICAgICAgICAgIGRhdGFzZXQgPSAkc2VsZi5wYXJzZU1vZHVsZURhdGEoJ3Byb2R1Y3RfbGlzdGluZ19maWx0ZXInKTtcblxuICAgICAgICAgICAgICAgICAgICBpZiAoY29uZmlnLmFkZCAhPT0gZGF0YXNldC5hZGQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlbW92ZUNsYXNzZXMgKz0gZGF0YXNldC5hZGQgKyAnICc7XG4gICAgICAgICAgICAgICAgICAgICAgICAkc2VsZi5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkc2VsZi5hZGRDbGFzcygnYWN0aXZlJyk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgLy8gU3dpdGNoIHRoZSBjbGFzc2VzIGFmdGVyIHRoZSBmYWRlb3V0IHRyYW5zaXRpb24gZmluaXNoZWRcbiAgICAgICAgICAgIC8vIGFuZCB0aGVuIHN0YXJ0IHRoZSBmYWRlaW4gYW5pbWF0aW9uXG4gICAgICAgICAgICB0cmFuc2l0aW9uLm9wZW4gPSBmYWxzZTtcbiAgICAgICAgICAgICR0YXJnZXRcbiAgICAgICAgICAgICAgICAub2ZmKGpzZS5saWJzLnRoZW1lLmV2ZW50cy5UUkFOU0lUSU9OX0ZJTklTSEVEKCkpXG4gICAgICAgICAgICAgICAgLm9uZShqc2UubGlicy50aGVtZS5ldmVudHMuVFJBTlNJVElPTl9GSU5JU0hFRCgpLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgIHRyYW5zaXRpb24ub3BlbiA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgICR0YXJnZXRcbiAgICAgICAgICAgICAgICAgICAgICAgIC5yZW1vdmVDbGFzcyhyZW1vdmVDbGFzc2VzKVxuICAgICAgICAgICAgICAgICAgICAgICAgLmFkZENsYXNzKGNvbmZpZy5hZGQpXG4gICAgICAgICAgICAgICAgICAgICAgICAudHJpZ2dlcihqc2UubGlicy50aGVtZS5ldmVudHMuVFJBTlNJVElPTigpLCB0cmFuc2l0aW9uKTtcbiAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgICAgIC50cmlnZ2VyKGpzZS5saWJzLnRoZW1lLmV2ZW50cy5UUkFOU0lUSU9OKCksIHRyYW5zaXRpb24pO1xuXG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFNldHMgdGhlIHBhZ2luYXRpb24gVVJMcyBvbiB2aWV3bW9kZVxuICAgICAgICAgKiBjaGFuZ2UsIHNvIHRoYXQgdGhlIHBhcmFtZXRlciBcInZpZXdfbW9kZVwiXG4gICAgICAgICAqIGlzIHNldCBjb3JyZWN0bHkgaW4gdGhlIFVSTFxuICAgICAgICAgKiBAcGFyYW0gICAgICAge3N0cmluZ30gICAgICAgIG1vZGUgICAgICAgIFRoZSB2YWx1ZSBvZiB0aGUgdmlld19tb2RlLXBhcmFtZXRlclxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9zZXRQYWdpbmF0aW9uVVJMcyA9IGZ1bmN0aW9uIChtb2RlKSB7XG4gICAgICAgICAgICAkcGFnaW5hdGlvblxuICAgICAgICAgICAgICAgIC5maW5kKCdhJylcbiAgICAgICAgICAgICAgICAuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgIHZhciB1cmwgPSAkKHRoaXMpLmF0dHIoJ2hyZWYnKTtcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5hdHRyKCdocmVmJywganNlLmxpYnMudXJsX2FyZ3VtZW50cy5yZXBsYWNlUGFyYW1ldGVyVmFsdWUodXJsLCAndmlld19tb2RlJywgbW9kZSkpO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICB9O1xuXG5cbi8vICMjIyMjIyMjIyMgRVZFTlQgSEFORExFUiAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEZ1bmN0aW9uIHRoYXQgZ2V0cyBjYWxsZWQgaWYgYSB2aWV3IGNoYW5nZVxuICAgICAgICAgKiBpcyB0cmlnZ2VyZWQuIEl0IGNoZWNrcyB0aGUgY3VycmVudCBzdGF0ZSBvZlxuICAgICAgICAgKiB0aGUgYnV0dG9ucyBhbmQgc2l3dGNoZXMgdGhlIHZpZXcgaWYgdGhlIGJ1dHRvblxuICAgICAgICAgKiBzdGF0ZSBoYXMgY2hhbmdlZC4gSWYgdGhlIGhpc3Rvcnkgb2JqZWN0IGlzXG4gICAgICAgICAqIGF2YWlsYWJsZSB0aGUgdmlld2NoYW5nZSBnZXRzIGxvZ2dlZCB0byB0aGF0XG4gICAgICAgICAqIG9iamVjdFxuICAgICAgICAgKiBAcGFyYW0gICAgICAgICB7b2JqZWN0fSAgICAgIGUgICAgICAgICAgIGpRdWVyeSBldmVudCBvYmplY3RcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfdmlld0NoYW5nZUhhbmRsZXIgPSBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgLy8gT25seSBwcmV2ZW50IHRoZSBkZWZhdWx0IGJlaGF2aW91clxuICAgICAgICAgICAgLy8gaWYgdGhlIGZ1bmN0aW9ucyBnZXRzIGNhbGxlZCBieSBhbiBldmVudFxuICAgICAgICAgICAgLy8gaGFuZGxlclxuICAgICAgICAgICAgaWYgKGUpIHtcbiAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vIEdldCB0aGUgc2V0dGluZ3MgZm9yIHRoaXMgYnV0dG9uXG4gICAgICAgICAgICB2YXIgJHNlbGYgPSAkKHRoaXMpLFxuICAgICAgICAgICAgICAgIGRhdGFzZXQgPSAkc2VsZi5wYXJzZU1vZHVsZURhdGEoJ3Byb2R1Y3RfbGlzdGluZ19maWx0ZXInKSxcbiAgICAgICAgICAgICAgICB2aWV3TW9kZSA9IGRhdGFzZXQudXJsUGFyYW07XG5cbiAgICAgICAgICAgIC8vIE9ubHkgZG8gc29tZXRoaW5nIGlmIHRoZSBzdGF0ZSBpc24ndCBhbHJlYWR5IHNldFxuICAgICAgICAgICAgaWYgKCEkc2VsZi5oYXNDbGFzcygnYWN0aXZlJykpIHtcblxuICAgICAgICAgICAgICAgIC8vIENsb3NlIGFsbCBvcGVuZWQgbGF5ZXJzXG4gICAgICAgICAgICAgICAgJHRoaXMudHJpZ2dlcihqc2UubGlicy50aGVtZS5ldmVudHMuT1BFTl9GTFlPVVQoKSwgJHRoaXMpO1xuXG4gICAgICAgICAgICAgICAgLy8gQWRkIC8gcmVtb3ZlIGNsYXNzZXNcbiAgICAgICAgICAgICAgICBfc3dpdGNoVmlldyhkYXRhc2V0KTtcblxuICAgICAgICAgICAgICAgIC8vIFVwZGF0ZSB0aGUgcGFnaW5hdGlvbiBVUkxzXG4gICAgICAgICAgICAgICAgX3NldFBhZ2luYXRpb25VUkxzKHZpZXdNb2RlKTtcblxuICAgICAgICAgICAgICAgIC8vIFNldCB0aGUgaGlkZGVuIHZhbHVlIGZvciB0aGUgdmlld21vZGVcbiAgICAgICAgICAgICAgICAvLyBzbyB0aGF0IHRoZSBzdWJtaXQgd2lsbCB0cmFuc2ZlciBjb3JyZWN0XG4gICAgICAgICAgICAgICAgLy8gdmFsdWVzXG4gICAgICAgICAgICAgICAgJGhpZGRlbi52YWwodmlld01vZGUpO1xuXG4gICAgICAgICAgICAgICAgLy8gSWYgbmVlZGVkLCBhZGQgYW4gaGlzdG9yeSBlbGVtZW50XG4gICAgICAgICAgICAgICAgLy8gKHRoZSBoaXN0b3J5IHBhcmFtZXRlciBpcyBzZXQgdmlhIHRoZSB1c2VyLWNsaWNrIGV2ZW50IG9ubHkpXG4gICAgICAgICAgICAgICAgaWYgKGhpc3RvcnlBdmFpbGFibGUgJiYgZSAmJiBlLmRhdGEgJiYgZS5kYXRhLmhpc3RvcnkpIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyIHVybCA9IGpzZS5saWJzLnVybF9hcmd1bWVudHMucmVwbGFjZVBhcmFtZXRlclZhbHVlKGxvY2F0aW9uLmhyZWYsICd2aWV3X21vZGUnLCB2aWV3TW9kZSk7XG5cbiAgICAgICAgICAgICAgICAgICAgaGlzdG9yeS5wdXNoU3RhdGUoe3N0YXRlOiB2aWV3TW9kZX0sIHZpZXdNb2RlLCB1cmwpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vIFRyaWdnZXIgYSBwdXNoc3RhdGUgZXZlbnQgdG8gbm90aWZ5IG90aGVyIHdpZGdldHNcbiAgICAgICAgICAgICAgICAgICAgLy8gYWJvdXQgdGhlIHVybCBjaGFuZ2VcbiAgICAgICAgICAgICAgICAgICAgJHRoaXMudHJpZ2dlcigncHVzaHN0YXRlJywge3N0YXRlOiB2aWV3TW9kZX0pO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogRXZlbnQgaGFuZGxlciB0byBjaGFuZ2UgdGhlIHZpZXcgZGVwZW5kaW5nXG4gICAgICAgICAqIG9uIHRoZSBoaXN0b3J5IHN0YXRlXG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3RcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIGQgICAgICAgSlNPTiBvYmplY3QgdGhhdCBjb250YWlucyB0aGUgc3RhdGUgKGlmIGUub3JpZ2luYWxFdmVudC5zdGF0ZSBpc24ndCBzZXQpXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX2hpc3RvcnlIYW5kbGVyID0gZnVuY3Rpb24gKGUsIGQpIHtcbiAgICAgICAgICAgIHZhciBldmVudERhdGEgPSBkIHx8IChlLm9yaWdpbmFsRXZlbnQgPyBlLm9yaWdpbmFsRXZlbnQgOiB7c3RhdGU6ICcnfSksXG4gICAgICAgICAgICAgICAgJGJ1dHRvbiA9ICR2aWV3bW9kZS5maW5kKCdbZGF0YS1wcm9kdWN0X2xpc3RpbmdfZmlsdGVyLXVybC1wYXJhbT1cIicgKyBldmVudERhdGEuc3RhdGUgKyAnXCJdJyk7XG5cbiAgICAgICAgICAgIGlmICgkYnV0dG9uLmxlbmd0aCAmJiAhZC5ub0J1dHRvbikge1xuICAgICAgICAgICAgICAgIF92aWV3Q2hhbmdlSGFuZGxlci5jYWxsKCRidXR0b24pO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAvLyBHZXQgdGhlIHNldHRpbmdzIGZvciB0aGlzIGJ1dHRvblxuICAgICAgICAgICAgICAgIHZhciAkYWN0aXZlQnV0dG9uID0gJHRoaXMuZmluZCgnLmpzUGFuZWxWaWV3bW9kZSBhLmFjdGl2ZScpLFxuICAgICAgICAgICAgICAgICAgICBkYXRhc2V0ID0gJGFjdGl2ZUJ1dHRvbi5wYXJzZU1vZHVsZURhdGEoJ3Byb2R1Y3RfbGlzdGluZ19maWx0ZXInKTtcbiAgICAgICAgICAgICAgICBfc2V0UGFnaW5hdGlvblVSTHMoZGF0YXNldC51cmxQYXJhbSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEV2ZW50IGhhbmRsZXIgZm9yIHRoZSBzdWJtaXQgYWN0aW9uXG4gICAgICAgICAqIG9uIGNoYW5nZSBvZiB0aGUgc2VsZWN0c1xuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9jaGFuZ2VIYW5kbGVyID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgJGZvcm0uc3VibWl0KCk7XG4gICAgICAgIH07XG5cblxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuICAgICAgICAgKiBAY29uc3RydWN0b3JcbiAgICAgICAgICovXG4gICAgICAgIG1vZHVsZS5pbml0ID0gZnVuY3Rpb24gKGRvbmUpIHtcblxuICAgICAgICAgICAgJHRhcmdldCA9ICQob3B0aW9ucy50YXJnZXQpO1xuICAgICAgICAgICAgJGZvcm0gPSAkdGhpcy5maW5kKCdmb3JtJyk7XG4gICAgICAgICAgICAkaGlkZGVuID0gJGZvcm0uZmluZCgnaW5wdXRbbmFtZT1cInZpZXdfbW9kZVwiXScpO1xuICAgICAgICAgICAgJHZpZXdtb2RlID0gJHRoaXMuZmluZCgnLmpzUGFuZWxWaWV3bW9kZScpO1xuICAgICAgICAgICAgJHBhZ2luYXRpb24gPSAkdGhpcy5maW5kKCcucGFnaW5hdGlvbicpO1xuICAgICAgICAgICAgaGlzdG9yeUF2YWlsYWJsZSA9IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2hpc3RvcnknKTtcbiAgICAgICAgICAgIHRyYW5zaXRpb24uY2xhc3NDbG9zZSA9ICdmYWRlT3V0JztcblxuICAgICAgICAgICAgLy8gUmVwbGFjZSB0aGUgY3VycmVudCBoaXN0b3J5IGVudHJ5IHdpdGhcbiAgICAgICAgICAgIC8vIG9uZSB3aXRoIGEgZGF0YXNldCB0aGF0IHJlcHJlc2VudCB0aGVcbiAgICAgICAgICAgIC8vIGN1cnJlbnQgc3RhdGVcbiAgICAgICAgICAgIGlmIChoaXN0b3J5QXZhaWxhYmxlKSB7XG4gICAgICAgICAgICAgICAgdmFyXG4gICAgICAgICAgICAgICAgICAgIHZpZXdNb2RlID0ganNlLmxpYnMudXJsX2FyZ3VtZW50cy5nZXRVcmxQYXJhbWV0ZXJzKCkudmlld19tb2RlLFxuICAgICAgICAgICAgICAgICAgICBzdGF0ZSA9IGhpc3Rvcnkuc3RhdGUgfHwge30sXG4gICAgICAgICAgICAgICAgICAgIHVybCA9IGpzZS5saWJzLnVybF9hcmd1bWVudHMucmVwbGFjZVBhcmFtZXRlclZhbHVlKGxvY2F0aW9uLmhyZWYsICd2aWV3X21vZGUnLCB2aWV3TW9kZSk7XG5cbiAgICAgICAgICAgICAgICBzdGF0ZS5zdGF0ZSA9IHZpZXdNb2RlO1xuICAgICAgICAgICAgICAgIGhpc3RvcnkucmVwbGFjZVN0YXRlKHN0YXRlLCB2aWV3TW9kZSwgdXJsKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy8gQmluZCBsaXN0ZW5lciBmb3IgdXNlciBpbnB1dFxuICAgICAgICAgICAgJHRoaXNcbiAgICAgICAgICAgICAgICAub24oJ2NoYW5nZScsICdzZWxlY3QuanNSZWxvYWQnLCBfY2hhbmdlSGFuZGxlcilcbiAgICAgICAgICAgICAgICAub24oJ2NsaWNrJywgJy5qc1BhbmVsVmlld21vZGUgYScsIHtoaXN0b3J5OiB0cnVlfSwgX3ZpZXdDaGFuZ2VIYW5kbGVyKTtcblxuICAgICAgICAgICAgLy8gQmluZCBldmVudCBsaXN0ZW5lciB0byBjaGVja1xuICAgICAgICAgICAgLy8gaWYgdGhlIGhpc3RvcnkgZW50cnkgaGFzIGNoYW5nZWRcbiAgICAgICAgICAgICRib2R5Lm9uKCdwdXNoc3RhdGUgcHVzaHN0YXRlX25vX2hpc3RvcnknLCBfaGlzdG9yeUhhbmRsZXIpO1xuICAgICAgICAgICAgJCh3aW5kb3cpLm9uKCdwb3BzdGF0ZScsIF9oaXN0b3J5SGFuZGxlcik7XG5cbiAgICAgICAgICAgIGRvbmUoKTtcbiAgICAgICAgfTtcblxuICAgICAgICAvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG4gICAgICAgIHJldHVybiBtb2R1bGU7XG4gICAgfSk7Il19
