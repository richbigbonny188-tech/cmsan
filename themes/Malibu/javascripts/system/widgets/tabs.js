'use strict';

/* --------------------------------------------------------------
 tabs.js 2015-09-30 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that enables the tabs / accordion
 */
gambio.widgets.module('tabs', [], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        $tabs = null,
        $content = null,
        $tabList = null,
        $contentList = null,
        transition = {
        classOpen: 'active',
        open: false,
        calcHeight: true
    },
        defaults = {},
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## HELPER FUNCTIONS ##########

    /**
     * Function that sets the active classes to the
     * tabs and the content headers and shows / hides
     * the content
     * @param       {integer}       index       The index of the clicked element
     * @private
     */
    var _setClasses = function _setClasses(index) {
        // Set the active tab
        $tabList.removeClass('active').eq(index).addClass('active');

        transition.open = false;
        var $hide = $contentList.filter('.active').removeClass('active').children('.tab-body'),
            $show = $contentList.eq(index);

        $show.addClass('active').find('.tab-body').addClass('active');
    };

    // ########## EVENT HANDLER ##########

    /**
     * Click handler for the tabs. It hides
     * all other tab content except it's own
     * @param       {object}    e       jQuery event object
     * @private
     */
    var _clickHandlerTabs = function _clickHandlerTabs(e) {
        e.preventDefault();
        e.stopPropagation();

        var $self = $(this),
            index = $self.index();

        if (!$self.hasClass('active')) {
            _setClasses(index);
        }
    };

    /**
     * Click handler for the accordion. It hides
     * all other tab content except it's own
     * @param       {object}    e       jQuery event object
     * @private
     */
    var _clickHandlerAccordion = function _clickHandlerAccordion(e) {
        e.preventDefault();
        e.stopPropagation();

        var $self = $(this),
            $container = $self.closest('.tab-pane'),
            index = $container.index(),
            containerHeight = $self.height();

        if (!$container.hasClass('active')) {
            _setClasses(index);
        }

        $('html,body').animate({ scrollTop: $self.offset().top - containerHeight }, 'slow');
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        $tabs = $this.children('.nav-tabs');
        $tabList = $tabs.children('li');
        $content = $this.children('.tab-content');
        $contentList = $content.children('.tab-pane');

        $this.on('click', '.nav-tabs li', _clickHandlerTabs).on('click', '.tab-content .tab-heading', _clickHandlerAccordion);

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvdGFicy5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiR0YWJzIiwiJGNvbnRlbnQiLCIkdGFiTGlzdCIsIiRjb250ZW50TGlzdCIsInRyYW5zaXRpb24iLCJjbGFzc09wZW4iLCJvcGVuIiwiY2FsY0hlaWdodCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsIl9zZXRDbGFzc2VzIiwiaW5kZXgiLCJyZW1vdmVDbGFzcyIsImVxIiwiYWRkQ2xhc3MiLCIkaGlkZSIsImZpbHRlciIsImNoaWxkcmVuIiwiJHNob3ciLCJmaW5kIiwiX2NsaWNrSGFuZGxlclRhYnMiLCJlIiwicHJldmVudERlZmF1bHQiLCJzdG9wUHJvcGFnYXRpb24iLCIkc2VsZiIsImhhc0NsYXNzIiwiX2NsaWNrSGFuZGxlckFjY29yZGlvbiIsIiRjb250YWluZXIiLCJjbG9zZXN0IiwiY29udGFpbmVySGVpZ2h0IiwiaGVpZ2h0IiwiYW5pbWF0ZSIsInNjcm9sbFRvcCIsIm9mZnNldCIsInRvcCIsImluaXQiLCJkb25lIiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7O0FBR0FBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUFzQixNQUF0QixFQUE4QixFQUE5QixFQUFrQyxVQUFVQyxJQUFWLEVBQWdCOztBQUU5Qzs7QUFFSjs7QUFFSSxRQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLFFBQ0lDLFFBQVEsSUFEWjtBQUFBLFFBRUlDLFdBQVcsSUFGZjtBQUFBLFFBR0lDLFdBQVcsSUFIZjtBQUFBLFFBSUlDLGVBQWUsSUFKbkI7QUFBQSxRQUtJQyxhQUFhO0FBQ1RDLG1CQUFXLFFBREY7QUFFVEMsY0FBTSxLQUZHO0FBR1RDLG9CQUFZO0FBSEgsS0FMakI7QUFBQSxRQVVJQyxXQUFXLEVBVmY7QUFBQSxRQVdJQyxVQUFVVixFQUFFVyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCWCxJQUE3QixDQVhkO0FBQUEsUUFZSUQsU0FBUyxFQVpiOztBQWVKOztBQUVJOzs7Ozs7O0FBT0EsUUFBSWUsY0FBYyxTQUFkQSxXQUFjLENBQVVDLEtBQVYsRUFBaUI7QUFDL0I7QUFDQVYsaUJBQ0tXLFdBREwsQ0FDaUIsUUFEakIsRUFFS0MsRUFGTCxDQUVRRixLQUZSLEVBR0tHLFFBSEwsQ0FHYyxRQUhkOztBQUtBWCxtQkFBV0UsSUFBWCxHQUFrQixLQUFsQjtBQUNBLFlBQUlVLFFBQVFiLGFBQ0hjLE1BREcsQ0FDSSxTQURKLEVBRUhKLFdBRkcsQ0FFUyxRQUZULEVBR0hLLFFBSEcsQ0FHTSxXQUhOLENBQVo7QUFBQSxZQUlJQyxRQUFRaEIsYUFBYVcsRUFBYixDQUFnQkYsS0FBaEIsQ0FKWjs7QUFNQU8sY0FDS0osUUFETCxDQUNjLFFBRGQsRUFFS0ssSUFGTCxDQUVVLFdBRlYsRUFHS0wsUUFITCxDQUdjLFFBSGQ7QUFJSCxLQWxCRDs7QUFxQko7O0FBRUk7Ozs7OztBQU1BLFFBQUlNLG9CQUFvQixTQUFwQkEsaUJBQW9CLENBQVVDLENBQVYsRUFBYTtBQUNqQ0EsVUFBRUMsY0FBRjtBQUNBRCxVQUFFRSxlQUFGOztBQUVBLFlBQUlDLFFBQVExQixFQUFFLElBQUYsQ0FBWjtBQUFBLFlBQ0lhLFFBQVFhLE1BQU1iLEtBQU4sRUFEWjs7QUFHQSxZQUFJLENBQUNhLE1BQU1DLFFBQU4sQ0FBZSxRQUFmLENBQUwsRUFBK0I7QUFDM0JmLHdCQUFZQyxLQUFaO0FBQ0g7QUFDSixLQVZEOztBQVlBOzs7Ozs7QUFNQSxRQUFJZSx5QkFBeUIsU0FBekJBLHNCQUF5QixDQUFVTCxDQUFWLEVBQWE7QUFDdENBLFVBQUVDLGNBQUY7QUFDQUQsVUFBRUUsZUFBRjs7QUFFQSxZQUFJQyxRQUFRMUIsRUFBRSxJQUFGLENBQVo7QUFBQSxZQUNJNkIsYUFBYUgsTUFBTUksT0FBTixDQUFjLFdBQWQsQ0FEakI7QUFBQSxZQUVJakIsUUFBUWdCLFdBQVdoQixLQUFYLEVBRlo7QUFBQSxZQUdJa0Isa0JBQWtCTCxNQUFNTSxNQUFOLEVBSHRCOztBQUtBLFlBQUksQ0FBQ0gsV0FBV0YsUUFBWCxDQUFvQixRQUFwQixDQUFMLEVBQW9DO0FBQ2hDZix3QkFBWUMsS0FBWjtBQUNIOztBQUVEYixVQUFFLFdBQUYsRUFBZWlDLE9BQWYsQ0FBdUIsRUFBQ0MsV0FBV1IsTUFBTVMsTUFBTixHQUFlQyxHQUFmLEdBQXFCTCxlQUFqQyxFQUF2QixFQUEwRSxNQUExRTtBQUNILEtBZEQ7O0FBaUJKOztBQUVJOzs7O0FBSUFsQyxXQUFPd0MsSUFBUCxHQUFjLFVBQVVDLElBQVYsRUFBZ0I7O0FBRTFCckMsZ0JBQVFGLE1BQU1vQixRQUFOLENBQWUsV0FBZixDQUFSO0FBQ0FoQixtQkFBV0YsTUFBTWtCLFFBQU4sQ0FBZSxJQUFmLENBQVg7QUFDQWpCLG1CQUFXSCxNQUFNb0IsUUFBTixDQUFlLGNBQWYsQ0FBWDtBQUNBZix1QkFBZUYsU0FBU2lCLFFBQVQsQ0FBa0IsV0FBbEIsQ0FBZjs7QUFFQXBCLGNBQ0t3QyxFQURMLENBQ1EsT0FEUixFQUNpQixjQURqQixFQUNpQ2pCLGlCQURqQyxFQUVLaUIsRUFGTCxDQUVRLE9BRlIsRUFFaUIsMkJBRmpCLEVBRThDWCxzQkFGOUM7O0FBSUFVO0FBQ0gsS0FaRDs7QUFjQTtBQUNBLFdBQU96QyxNQUFQO0FBQ0gsQ0FwSEQiLCJmaWxlIjoid2lkZ2V0cy90YWJzLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiB0YWJzLmpzIDIwMTUtMDktMzAgZ21cbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIFdpZGdldCB0aGF0IGVuYWJsZXMgdGhlIHRhYnMgLyBhY2NvcmRpb25cbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKCd0YWJzJywgW10sIGZ1bmN0aW9uIChkYXRhKSB7XG5cbiAgICAndXNlIHN0cmljdCc7XG5cbi8vICMjIyMjIyMjIyMgVkFSSUFCTEUgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG4gICAgdmFyICR0aGlzID0gJCh0aGlzKSxcbiAgICAgICAgJHRhYnMgPSBudWxsLFxuICAgICAgICAkY29udGVudCA9IG51bGwsXG4gICAgICAgICR0YWJMaXN0ID0gbnVsbCxcbiAgICAgICAgJGNvbnRlbnRMaXN0ID0gbnVsbCxcbiAgICAgICAgdHJhbnNpdGlvbiA9IHtcbiAgICAgICAgICAgIGNsYXNzT3BlbjogJ2FjdGl2ZScsXG4gICAgICAgICAgICBvcGVuOiBmYWxzZSxcbiAgICAgICAgICAgIGNhbGNIZWlnaHQ6IHRydWVcbiAgICAgICAgfSxcbiAgICAgICAgZGVmYXVsdHMgPSB7fSxcbiAgICAgICAgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG4gICAgICAgIG1vZHVsZSA9IHt9O1xuXG5cbi8vICMjIyMjIyMjIyMgSEVMUEVSIEZVTkNUSU9OUyAjIyMjIyMjIyMjXG5cbiAgICAvKipcbiAgICAgKiBGdW5jdGlvbiB0aGF0IHNldHMgdGhlIGFjdGl2ZSBjbGFzc2VzIHRvIHRoZVxuICAgICAqIHRhYnMgYW5kIHRoZSBjb250ZW50IGhlYWRlcnMgYW5kIHNob3dzIC8gaGlkZXNcbiAgICAgKiB0aGUgY29udGVudFxuICAgICAqIEBwYXJhbSAgICAgICB7aW50ZWdlcn0gICAgICAgaW5kZXggICAgICAgVGhlIGluZGV4IG9mIHRoZSBjbGlja2VkIGVsZW1lbnRcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIHZhciBfc2V0Q2xhc3NlcyA9IGZ1bmN0aW9uIChpbmRleCkge1xuICAgICAgICAvLyBTZXQgdGhlIGFjdGl2ZSB0YWJcbiAgICAgICAgJHRhYkxpc3RcbiAgICAgICAgICAgIC5yZW1vdmVDbGFzcygnYWN0aXZlJylcbiAgICAgICAgICAgIC5lcShpbmRleClcbiAgICAgICAgICAgIC5hZGRDbGFzcygnYWN0aXZlJyk7XG5cbiAgICAgICAgdHJhbnNpdGlvbi5vcGVuID0gZmFsc2U7XG4gICAgICAgIHZhciAkaGlkZSA9ICRjb250ZW50TGlzdFxuICAgICAgICAgICAgICAgIC5maWx0ZXIoJy5hY3RpdmUnKVxuICAgICAgICAgICAgICAgIC5yZW1vdmVDbGFzcygnYWN0aXZlJylcbiAgICAgICAgICAgICAgICAuY2hpbGRyZW4oJy50YWItYm9keScpLFxuICAgICAgICAgICAgJHNob3cgPSAkY29udGVudExpc3QuZXEoaW5kZXgpO1xuXG4gICAgICAgICRzaG93XG4gICAgICAgICAgICAuYWRkQ2xhc3MoJ2FjdGl2ZScpXG4gICAgICAgICAgICAuZmluZCgnLnRhYi1ib2R5JylcbiAgICAgICAgICAgIC5hZGRDbGFzcygnYWN0aXZlJyk7XG4gICAgfTtcblxuXG4vLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG4gICAgLyoqXG4gICAgICogQ2xpY2sgaGFuZGxlciBmb3IgdGhlIHRhYnMuIEl0IGhpZGVzXG4gICAgICogYWxsIG90aGVyIHRhYiBjb250ZW50IGV4Y2VwdCBpdCdzIG93blxuICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3RcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIHZhciBfY2xpY2tIYW5kbGVyVGFicyA9IGZ1bmN0aW9uIChlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcblxuICAgICAgICB2YXIgJHNlbGYgPSAkKHRoaXMpLFxuICAgICAgICAgICAgaW5kZXggPSAkc2VsZi5pbmRleCgpO1xuXG4gICAgICAgIGlmICghJHNlbGYuaGFzQ2xhc3MoJ2FjdGl2ZScpKSB7XG4gICAgICAgICAgICBfc2V0Q2xhc3NlcyhpbmRleCk7XG4gICAgICAgIH1cbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogQ2xpY2sgaGFuZGxlciBmb3IgdGhlIGFjY29yZGlvbi4gSXQgaGlkZXNcbiAgICAgKiBhbGwgb3RoZXIgdGFiIGNvbnRlbnQgZXhjZXB0IGl0J3Mgb3duXG4gICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgdmFyIF9jbGlja0hhbmRsZXJBY2NvcmRpb24gPSBmdW5jdGlvbiAoZSkge1xuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cbiAgICAgICAgdmFyICRzZWxmID0gJCh0aGlzKSxcbiAgICAgICAgICAgICRjb250YWluZXIgPSAkc2VsZi5jbG9zZXN0KCcudGFiLXBhbmUnKSxcbiAgICAgICAgICAgIGluZGV4ID0gJGNvbnRhaW5lci5pbmRleCgpLFxuICAgICAgICAgICAgY29udGFpbmVySGVpZ2h0ID0gJHNlbGYuaGVpZ2h0KCk7XG5cbiAgICAgICAgaWYgKCEkY29udGFpbmVyLmhhc0NsYXNzKCdhY3RpdmUnKSkge1xuICAgICAgICAgICAgX3NldENsYXNzZXMoaW5kZXgpO1xuICAgICAgICB9XG5cbiAgICAgICAgJCgnaHRtbCxib2R5JykuYW5pbWF0ZSh7c2Nyb2xsVG9wOiAkc2VsZi5vZmZzZXQoKS50b3AgLSBjb250YWluZXJIZWlnaHR9LCAnc2xvdycpO1xuICAgIH07XG5cblxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAvKipcbiAgICAgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcbiAgICAgKiBAY29uc3RydWN0b3JcbiAgICAgKi9cbiAgICBtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uIChkb25lKSB7XG5cbiAgICAgICAgJHRhYnMgPSAkdGhpcy5jaGlsZHJlbignLm5hdi10YWJzJyk7XG4gICAgICAgICR0YWJMaXN0ID0gJHRhYnMuY2hpbGRyZW4oJ2xpJyk7XG4gICAgICAgICRjb250ZW50ID0gJHRoaXMuY2hpbGRyZW4oJy50YWItY29udGVudCcpO1xuICAgICAgICAkY29udGVudExpc3QgPSAkY29udGVudC5jaGlsZHJlbignLnRhYi1wYW5lJyk7XG5cbiAgICAgICAgJHRoaXNcbiAgICAgICAgICAgIC5vbignY2xpY2snLCAnLm5hdi10YWJzIGxpJywgX2NsaWNrSGFuZGxlclRhYnMpXG4gICAgICAgICAgICAub24oJ2NsaWNrJywgJy50YWItY29udGVudCAudGFiLWhlYWRpbmcnLCBfY2xpY2tIYW5kbGVyQWNjb3JkaW9uKTtcblxuICAgICAgICBkb25lKCk7XG4gICAgfTtcblxuICAgIC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcbiAgICByZXR1cm4gbW9kdWxlO1xufSk7Il19
