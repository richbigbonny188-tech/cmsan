'use strict';

/* --------------------------------------------------------------
 show_password.js 2022-12-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gambio.widgets.module('show_password', [], function (data) {

    'use strict';

    var $this = $(this),
        defaults = {},
        options = $.extend(true, {}, defaults, data),
        module = {};

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {
        var passwordField = $this.find('.form-control');
        var passwordVisibilityToggler = $this.find('.show-password');
        var togglerIcon = passwordVisibilityToggler.find('.fa');

        var changePasswordFieldType = function changePasswordFieldType() {
            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text').focus();
                togglerIcon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                togglerIcon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        };

        var toggleShowPasswordButton = function toggleShowPasswordButton() {
            if (passwordField.val().length) {
                passwordVisibilityToggler.removeClass('hidden').addClass('show');
            } else {
                passwordVisibilityToggler.removeClass('show').addClass('hidden');
            }
        };

        passwordVisibilityToggler.on('click', changePasswordFieldType);
        passwordField.on('keyup', toggleShowPasswordButton);

        done();
    };

    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvc2hvd19wYXNzd29yZC5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsImluaXQiLCJkb25lIiwicGFzc3dvcmRGaWVsZCIsImZpbmQiLCJwYXNzd29yZFZpc2liaWxpdHlUb2dnbGVyIiwidG9nZ2xlckljb24iLCJjaGFuZ2VQYXNzd29yZEZpZWxkVHlwZSIsImF0dHIiLCJmb2N1cyIsInJlbW92ZUNsYXNzIiwiYWRkQ2xhc3MiLCJ0b2dnbGVTaG93UGFzc3dvcmRCdXR0b24iLCJ2YWwiLCJsZW5ndGgiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FBc0IsZUFBdEIsRUFBdUMsRUFBdkMsRUFBMkMsVUFBVUMsSUFBVixFQUFnQjs7QUFFdkQ7O0FBRUEsUUFBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxRQUNJQyxXQUFXLEVBRGY7QUFBQSxRQUVJQyxVQUFVRixFQUFFRyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCSCxJQUE3QixDQUZkO0FBQUEsUUFHSUQsU0FBUyxFQUhiOztBQUtBOzs7O0FBSUFBLFdBQU9PLElBQVAsR0FBYyxVQUFVQyxJQUFWLEVBQWdCO0FBQzFCLFlBQU1DLGdCQUFnQlAsTUFBTVEsSUFBTixDQUFXLGVBQVgsQ0FBdEI7QUFDQSxZQUFNQyw0QkFBNEJULE1BQU1RLElBQU4sQ0FBVyxnQkFBWCxDQUFsQztBQUNBLFlBQU1FLGNBQWNELDBCQUEwQkQsSUFBMUIsQ0FBK0IsS0FBL0IsQ0FBcEI7O0FBRUEsWUFBTUcsMEJBQTBCLFNBQTFCQSx1QkFBMEIsR0FBTTtBQUNsQyxnQkFBR0osY0FBY0ssSUFBZCxDQUFtQixNQUFuQixNQUErQixVQUFsQyxFQUE4QztBQUMxQ0wsOEJBQWNLLElBQWQsQ0FBbUIsTUFBbkIsRUFBMkIsTUFBM0IsRUFBbUNDLEtBQW5DO0FBQ0FILDRCQUFZSSxXQUFaLENBQXdCLFFBQXhCLEVBQWtDQyxRQUFsQyxDQUEyQyxjQUEzQztBQUNILGFBSEQsTUFHTztBQUNIUiw4QkFBY0ssSUFBZCxDQUFtQixNQUFuQixFQUEyQixVQUEzQjtBQUNBRiw0QkFBWUksV0FBWixDQUF3QixjQUF4QixFQUF3Q0MsUUFBeEMsQ0FBaUQsUUFBakQ7QUFDSDtBQUNKLFNBUkQ7O0FBVUEsWUFBTUMsMkJBQTJCLFNBQTNCQSx3QkFBMkIsR0FBTTtBQUNuQyxnQkFBR1QsY0FBY1UsR0FBZCxHQUFvQkMsTUFBdkIsRUFBK0I7QUFDM0JULDBDQUEwQkssV0FBMUIsQ0FBc0MsUUFBdEMsRUFBZ0RDLFFBQWhELENBQXlELE1BQXpEO0FBQ0gsYUFGRCxNQUVPO0FBQ0hOLDBDQUEwQkssV0FBMUIsQ0FBc0MsTUFBdEMsRUFBOENDLFFBQTlDLENBQXVELFFBQXZEO0FBQ0g7QUFDSixTQU5EOztBQVFBTixrQ0FBMEJVLEVBQTFCLENBQTZCLE9BQTdCLEVBQXNDUix1QkFBdEM7QUFDQUosc0JBQWNZLEVBQWQsQ0FBaUIsT0FBakIsRUFBMEJILHdCQUExQjs7QUFFQVY7QUFDSCxLQTNCRDs7QUE2QkEsV0FBT1IsTUFBUDtBQUNILENBM0NEIiwiZmlsZSI6IndpZGdldHMvc2hvd19wYXNzd29yZC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gc2hvd19wYXNzd29yZC5qcyAyMDIyLTEyLTA5XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAyMiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuZ2FtYmlvLndpZGdldHMubW9kdWxlKCdzaG93X3Bhc3N3b3JkJywgW10sIGZ1bmN0aW9uIChkYXRhKSB7XG5cbiAgICAndXNlIHN0cmljdCc7XG5cbiAgICBsZXQgJHRoaXMgPSAkKHRoaXMpLFxuICAgICAgICBkZWZhdWx0cyA9IHt9LFxuICAgICAgICBvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcbiAgICAgICAgbW9kdWxlID0ge307XG5cbiAgICAvKipcbiAgICAgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcbiAgICAgKiBAY29uc3RydWN0b3JcbiAgICAgKi9cbiAgICBtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uIChkb25lKSB7XG4gICAgICAgIGNvbnN0IHBhc3N3b3JkRmllbGQgPSAkdGhpcy5maW5kKCcuZm9ybS1jb250cm9sJyk7XG4gICAgICAgIGNvbnN0IHBhc3N3b3JkVmlzaWJpbGl0eVRvZ2dsZXIgPSAkdGhpcy5maW5kKCcuc2hvdy1wYXNzd29yZCcpO1xuICAgICAgICBjb25zdCB0b2dnbGVySWNvbiA9IHBhc3N3b3JkVmlzaWJpbGl0eVRvZ2dsZXIuZmluZCgnLmZhJyk7XG4gICAgICAgIFxuICAgICAgICBjb25zdCBjaGFuZ2VQYXNzd29yZEZpZWxkVHlwZSA9ICgpID0+IHtcbiAgICAgICAgICAgIGlmKHBhc3N3b3JkRmllbGQuYXR0cigndHlwZScpID09PSAncGFzc3dvcmQnKSB7XG4gICAgICAgICAgICAgICAgcGFzc3dvcmRGaWVsZC5hdHRyKCd0eXBlJywgJ3RleHQnKS5mb2N1cygpO1xuICAgICAgICAgICAgICAgIHRvZ2dsZXJJY29uLnJlbW92ZUNsYXNzKCdmYS1leWUnKS5hZGRDbGFzcygnZmEtZXllLXNsYXNoJyk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIHBhc3N3b3JkRmllbGQuYXR0cigndHlwZScsICdwYXNzd29yZCcpO1xuICAgICAgICAgICAgICAgIHRvZ2dsZXJJY29uLnJlbW92ZUNsYXNzKCdmYS1leWUtc2xhc2gnKS5hZGRDbGFzcygnZmEtZXllJyk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgICAgXG4gICAgICAgIGNvbnN0IHRvZ2dsZVNob3dQYXNzd29yZEJ1dHRvbiA9ICgpID0+IHtcbiAgICAgICAgICAgIGlmKHBhc3N3b3JkRmllbGQudmFsKCkubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgcGFzc3dvcmRWaXNpYmlsaXR5VG9nZ2xlci5yZW1vdmVDbGFzcygnaGlkZGVuJykuYWRkQ2xhc3MoJ3Nob3cnKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgcGFzc3dvcmRWaXNpYmlsaXR5VG9nZ2xlci5yZW1vdmVDbGFzcygnc2hvdycpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIFxuICAgICAgICBwYXNzd29yZFZpc2liaWxpdHlUb2dnbGVyLm9uKCdjbGljaycsIGNoYW5nZVBhc3N3b3JkRmllbGRUeXBlKTtcbiAgICAgICAgcGFzc3dvcmRGaWVsZC5vbigna2V5dXAnLCB0b2dnbGVTaG93UGFzc3dvcmRCdXR0b24pO1xuXG4gICAgICAgIGRvbmUoKTtcbiAgICB9O1xuXG4gICAgcmV0dXJuIG1vZHVsZTtcbn0pO1xuIl19
