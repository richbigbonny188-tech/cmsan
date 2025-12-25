'use strict';

/* --------------------------------------------------------------
 core_workarounds.js 2019-10-07
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Core Workarounds Module
 *
 * This file contains workarounds that do not belong in other JS modules.
 */
gambio.widgets.module('core_workarounds', [], function () {

    'use strict';

    var module = {};

    var _initMobileMenu = function _initMobileMenu() {
        var $profile = $('#topbar-container nav > ul> li').clone(),
            $login = $profile.find('.login-off-item'),
            $loginClone = $login.clone();

        $loginClone.addClass('dropdown navbar-topbar-item');
        $login.remove();
        $profile = $profile.add($loginClone);

        $('#categories nav > ul').append($profile);
        $('#categories nav > ul').attr('data-gambio-widget', 'link_crypter'); //reinitialize widgets
        gambio.widgets.init($('#categories nav > ul'));

        var $verticalMenu = $('.navbar-categories-left');
        if ($verticalMenu.length > 0) {
            $verticalMenu.find('ul.level-1').append($profile.clone());

            $verticalMenu.find('ul.level-1').attr('data-gambio-widget', 'link_crypter');
            gambio.widgets.init($verticalMenu.find('ul.level-1'));

            // hide the new elements
            $verticalMenu.find('.navbar-topbar-item').hide();
        }
    };

    var _initIE11Workarounds = function _initIE11Workarounds() {
        var isIE11 = !window.ActiveXObject && "ActiveXObject" in window;
        if (!isIE11) {
            return;
        }

        // crude workaround for IE11’s inability to handle HTML5 form attributes
        var extFormButtons = $('button.extform');
        extFormButtons.each(function () {
            var formId = $(this).attr('form');
            $(this).on('click', function () {
                var formInputs = $('input[form="' + formId + '"]');
                var theForm = $('form#' + formId);
                formInputs.each(function () {
                    theForm.append($('input').attr('type', 'hidden').attr('name', $(this).attr('name')).attr('value', $(this).val()));
                });
                theForm.submit();
            });
        });
    };

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {
        _initMobileMenu();
        _initIE11Workarounds();

        done();
    };

    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvY29yZV93b3JrYXJvdW5kcy5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiX2luaXRNb2JpbGVNZW51IiwiJHByb2ZpbGUiLCIkIiwiY2xvbmUiLCIkbG9naW4iLCJmaW5kIiwiJGxvZ2luQ2xvbmUiLCJhZGRDbGFzcyIsInJlbW92ZSIsImFkZCIsImFwcGVuZCIsImF0dHIiLCJpbml0IiwiJHZlcnRpY2FsTWVudSIsImxlbmd0aCIsImhpZGUiLCJfaW5pdElFMTFXb3JrYXJvdW5kcyIsImlzSUUxMSIsIndpbmRvdyIsIkFjdGl2ZVhPYmplY3QiLCJleHRGb3JtQnV0dG9ucyIsImVhY2giLCJmb3JtSWQiLCJvbiIsImZvcm1JbnB1dHMiLCJ0aGVGb3JtIiwidmFsIiwic3VibWl0IiwiZG9uZSJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7OztBQUtBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FBc0Isa0JBQXRCLEVBQTBDLEVBQTFDLEVBQThDLFlBQVk7O0FBRXREOztBQUVBLFFBQUlBLFNBQVMsRUFBYjs7QUFFQSxRQUFJQyxrQkFBa0IsU0FBbEJBLGVBQWtCLEdBQVk7QUFDOUIsWUFBSUMsV0FBV0MsRUFBRSxnQ0FBRixFQUFvQ0MsS0FBcEMsRUFBZjtBQUFBLFlBQ0lDLFNBQVNILFNBQVNJLElBQVQsQ0FBYyxpQkFBZCxDQURiO0FBQUEsWUFFSUMsY0FBY0YsT0FBT0QsS0FBUCxFQUZsQjs7QUFJQUcsb0JBQVlDLFFBQVosQ0FBcUIsNkJBQXJCO0FBQ0FILGVBQU9JLE1BQVA7QUFDQVAsbUJBQVdBLFNBQVNRLEdBQVQsQ0FBYUgsV0FBYixDQUFYOztBQUVBSixVQUFFLHNCQUFGLEVBQTBCUSxNQUExQixDQUFpQ1QsUUFBakM7QUFDQUMsVUFBRSxzQkFBRixFQUEwQlMsSUFBMUIsQ0FBK0Isb0JBQS9CLEVBQXFELGNBQXJELEVBVjhCLENBVXlDO0FBQ3ZFZCxlQUFPQyxPQUFQLENBQWVjLElBQWYsQ0FBb0JWLEVBQUUsc0JBQUYsQ0FBcEI7O0FBRUEsWUFBSVcsZ0JBQWdCWCxFQUFFLHlCQUFGLENBQXBCO0FBQ0EsWUFBSVcsY0FBY0MsTUFBZCxHQUF1QixDQUEzQixFQUE4QjtBQUMxQkQsMEJBQWNSLElBQWQsQ0FBbUIsWUFBbkIsRUFBaUNLLE1BQWpDLENBQXdDVCxTQUFTRSxLQUFULEVBQXhDOztBQUVBVSwwQkFBY1IsSUFBZCxDQUFtQixZQUFuQixFQUFpQ00sSUFBakMsQ0FBc0Msb0JBQXRDLEVBQTRELGNBQTVEO0FBQ0FkLG1CQUFPQyxPQUFQLENBQWVjLElBQWYsQ0FBb0JDLGNBQWNSLElBQWQsQ0FBbUIsWUFBbkIsQ0FBcEI7O0FBRUE7QUFDQVEsMEJBQWNSLElBQWQsQ0FBbUIscUJBQW5CLEVBQTBDVSxJQUExQztBQUNIO0FBQ0osS0F2QkQ7O0FBeUJBLFFBQUlDLHVCQUF1QixTQUF2QkEsb0JBQXVCLEdBQVk7QUFDbkMsWUFBSUMsU0FBUyxDQUFFQyxPQUFPQyxhQUFULElBQTJCLG1CQUFtQkQsTUFBM0Q7QUFDQSxZQUFHLENBQUNELE1BQUosRUFBWTtBQUNSO0FBQ0g7O0FBRUQ7QUFDQSxZQUFJRyxpQkFBaUJsQixFQUFFLGdCQUFGLENBQXJCO0FBQ0FrQix1QkFBZUMsSUFBZixDQUFvQixZQUFXO0FBQzNCLGdCQUFJQyxTQUFTcEIsRUFBRSxJQUFGLEVBQVFTLElBQVIsQ0FBYSxNQUFiLENBQWI7QUFDQVQsY0FBRSxJQUFGLEVBQVFxQixFQUFSLENBQVcsT0FBWCxFQUFvQixZQUFXO0FBQzNCLG9CQUFJQyxhQUFhdEIsRUFBRSxpQkFBaUJvQixNQUFqQixHQUEwQixJQUE1QixDQUFqQjtBQUNBLG9CQUFJRyxVQUFVdkIsRUFBRSxVQUFVb0IsTUFBWixDQUFkO0FBQ0FFLDJCQUFXSCxJQUFYLENBQWdCLFlBQVc7QUFDdkJJLDRCQUFRZixNQUFSLENBQWVSLEVBQUUsT0FBRixFQUFXUyxJQUFYLENBQWdCLE1BQWhCLEVBQXdCLFFBQXhCLEVBQWtDQSxJQUFsQyxDQUF1QyxNQUF2QyxFQUErQ1QsRUFBRSxJQUFGLEVBQVFTLElBQVIsQ0FBYSxNQUFiLENBQS9DLEVBQXFFQSxJQUFyRSxDQUEwRSxPQUExRSxFQUFtRlQsRUFBRSxJQUFGLEVBQVF3QixHQUFSLEVBQW5GLENBQWY7QUFDSCxpQkFGRDtBQUdBRCx3QkFBUUUsTUFBUjtBQUNILGFBUEQ7QUFRSCxTQVZEO0FBV0gsS0FuQkQ7O0FBcUJBOzs7O0FBSUE1QixXQUFPYSxJQUFQLEdBQWMsVUFBVWdCLElBQVYsRUFBZ0I7QUFDMUI1QjtBQUNBZ0I7O0FBRUFZO0FBQ0gsS0FMRDs7QUFPQSxXQUFPN0IsTUFBUDtBQUNILENBaEVEIiwiZmlsZSI6IndpZGdldHMvY29yZV93b3JrYXJvdW5kcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gY29yZV93b3JrYXJvdW5kcy5qcyAyMDE5LTEwLTA3XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBDb3JlIFdvcmthcm91bmRzIE1vZHVsZVxuICpcbiAqIFRoaXMgZmlsZSBjb250YWlucyB3b3JrYXJvdW5kcyB0aGF0IGRvIG5vdCBiZWxvbmcgaW4gb3RoZXIgSlMgbW9kdWxlcy5cbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKCdjb3JlX3dvcmthcm91bmRzJywgW10sIGZ1bmN0aW9uICgpIHtcblxuICAgICd1c2Ugc3RyaWN0JztcblxuICAgIHZhciBtb2R1bGUgPSB7fTtcblxuICAgIHZhciBfaW5pdE1vYmlsZU1lbnUgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgIHZhciAkcHJvZmlsZSA9ICQoJyN0b3BiYXItY29udGFpbmVyIG5hdiA+IHVsPiBsaScpLmNsb25lKCksXG4gICAgICAgICAgICAkbG9naW4gPSAkcHJvZmlsZS5maW5kKCcubG9naW4tb2ZmLWl0ZW0nKSxcbiAgICAgICAgICAgICRsb2dpbkNsb25lID0gJGxvZ2luLmNsb25lKCk7XG5cbiAgICAgICAgJGxvZ2luQ2xvbmUuYWRkQ2xhc3MoJ2Ryb3Bkb3duIG5hdmJhci10b3BiYXItaXRlbScpO1xuICAgICAgICAkbG9naW4ucmVtb3ZlKCk7XG4gICAgICAgICRwcm9maWxlID0gJHByb2ZpbGUuYWRkKCRsb2dpbkNsb25lKTtcblxuICAgICAgICAkKCcjY2F0ZWdvcmllcyBuYXYgPiB1bCcpLmFwcGVuZCgkcHJvZmlsZSk7XG4gICAgICAgICQoJyNjYXRlZ29yaWVzIG5hdiA+IHVsJykuYXR0cignZGF0YS1nYW1iaW8td2lkZ2V0JywgJ2xpbmtfY3J5cHRlcicpOyAgLy9yZWluaXRpYWxpemUgd2lkZ2V0c1xuICAgICAgICBnYW1iaW8ud2lkZ2V0cy5pbml0KCQoJyNjYXRlZ29yaWVzIG5hdiA+IHVsJykpO1xuXG4gICAgICAgIHZhciAkdmVydGljYWxNZW51ID0gJCgnLm5hdmJhci1jYXRlZ29yaWVzLWxlZnQnKTtcbiAgICAgICAgaWYgKCR2ZXJ0aWNhbE1lbnUubGVuZ3RoID4gMCkge1xuICAgICAgICAgICAgJHZlcnRpY2FsTWVudS5maW5kKCd1bC5sZXZlbC0xJykuYXBwZW5kKCRwcm9maWxlLmNsb25lKCkpO1xuXG4gICAgICAgICAgICAkdmVydGljYWxNZW51LmZpbmQoJ3VsLmxldmVsLTEnKS5hdHRyKCdkYXRhLWdhbWJpby13aWRnZXQnLCAnbGlua19jcnlwdGVyJyk7XG4gICAgICAgICAgICBnYW1iaW8ud2lkZ2V0cy5pbml0KCR2ZXJ0aWNhbE1lbnUuZmluZCgndWwubGV2ZWwtMScpKTtcblxuICAgICAgICAgICAgLy8gaGlkZSB0aGUgbmV3IGVsZW1lbnRzXG4gICAgICAgICAgICAkdmVydGljYWxNZW51LmZpbmQoJy5uYXZiYXItdG9wYmFyLWl0ZW0nKS5oaWRlKCk7XG4gICAgICAgIH1cbiAgICB9O1xuXG4gICAgdmFyIF9pbml0SUUxMVdvcmthcm91bmRzID0gZnVuY3Rpb24gKCkge1xuICAgICAgICB2YXIgaXNJRTExID0gISh3aW5kb3cuQWN0aXZlWE9iamVjdCkgJiYgXCJBY3RpdmVYT2JqZWN0XCIgaW4gd2luZG93O1xuICAgICAgICBpZighaXNJRTExKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICAvLyBjcnVkZSB3b3JrYXJvdW5kIGZvciBJRTEx4oCZcyBpbmFiaWxpdHkgdG8gaGFuZGxlIEhUTUw1IGZvcm0gYXR0cmlidXRlc1xuICAgICAgICB2YXIgZXh0Rm9ybUJ1dHRvbnMgPSAkKCdidXR0b24uZXh0Zm9ybScpO1xuICAgICAgICBleHRGb3JtQnV0dG9ucy5lYWNoKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgdmFyIGZvcm1JZCA9ICQodGhpcykuYXR0cignZm9ybScpO1xuICAgICAgICAgICAgJCh0aGlzKS5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICB2YXIgZm9ybUlucHV0cyA9ICQoJ2lucHV0W2Zvcm09XCInICsgZm9ybUlkICsgJ1wiXScpO1xuICAgICAgICAgICAgICAgIHZhciB0aGVGb3JtID0gJCgnZm9ybSMnICsgZm9ybUlkKTtcbiAgICAgICAgICAgICAgICBmb3JtSW5wdXRzLmVhY2goZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoZUZvcm0uYXBwZW5kKCQoJ2lucHV0JykuYXR0cigndHlwZScsICdoaWRkZW4nKS5hdHRyKCduYW1lJywgJCh0aGlzKS5hdHRyKCduYW1lJykpLmF0dHIoJ3ZhbHVlJywgJCh0aGlzKS52YWwoKSkpO1xuICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAgICAgdGhlRm9ybS5zdWJtaXQoKTtcbiAgICAgICAgICAgIH0pXG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuICAgICAqIEBjb25zdHJ1Y3RvclxuICAgICAqL1xuICAgIG1vZHVsZS5pbml0ID0gZnVuY3Rpb24gKGRvbmUpIHtcbiAgICAgICAgX2luaXRNb2JpbGVNZW51KCk7XG4gICAgICAgIF9pbml0SUUxMVdvcmthcm91bmRzKCk7XG4gICAgICAgIFxuICAgICAgICBkb25lKCk7XG4gICAgfTtcblxuICAgIHJldHVybiBtb2R1bGU7XG59KTtcbiJdfQ==
