'use strict';

$(function () {
    // Disable next and previous arrows if current item is the last slide
    $('[data-wrap="false"]').each(function (index, carousel) {
        $(carousel).on('slid.bs.carousel', function () {
            var leftCarouselArrow = $(carousel).find('.left.carousel-control');
            var rightCarouselArrow = $(carousel).find('.right.carousel-control');

            var fistCarouselSlide = $(carousel).find('.item:first-child');
            if (fistCarouselSlide.hasClass('active')) {
                $(leftCarouselArrow).addClass('disabled');
            } else {
                $(leftCarouselArrow).removeClass('disabled');
            }

            var lastCarouselSlide = $(carousel).find('.item:last-child');
            if (lastCarouselSlide.hasClass('active')) {
                $(rightCarouselArrow).addClass('disabled');
            } else {
                $(rightCarouselArrow).removeClass('disabled');
            }
        });
    });
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkdsb2JhbC9zY3JpcHRzLmpzIl0sIm5hbWVzIjpbIiQiLCJlYWNoIiwiaW5kZXgiLCJjYXJvdXNlbCIsIm9uIiwibGVmdENhcm91c2VsQXJyb3ciLCJmaW5kIiwicmlnaHRDYXJvdXNlbEFycm93IiwiZmlzdENhcm91c2VsU2xpZGUiLCJoYXNDbGFzcyIsImFkZENsYXNzIiwicmVtb3ZlQ2xhc3MiLCJsYXN0Q2Fyb3VzZWxTbGlkZSJdLCJtYXBwaW5ncyI6Ijs7QUFBQUEsRUFBRSxZQUFXO0FBQ1Q7QUFDQUEsTUFBRSxxQkFBRixFQUF5QkMsSUFBekIsQ0FBOEIsVUFBU0MsS0FBVCxFQUFnQkMsUUFBaEIsRUFBMEI7QUFDcERILFVBQUVHLFFBQUYsRUFBWUMsRUFBWixDQUFlLGtCQUFmLEVBQW1DLFlBQVk7QUFDM0MsZ0JBQUlDLG9CQUFvQkwsRUFBRUcsUUFBRixFQUFZRyxJQUFaLENBQWlCLHdCQUFqQixDQUF4QjtBQUNBLGdCQUFJQyxxQkFBcUJQLEVBQUVHLFFBQUYsRUFBWUcsSUFBWixDQUFpQix5QkFBakIsQ0FBekI7O0FBRUEsZ0JBQUlFLG9CQUFvQlIsRUFBRUcsUUFBRixFQUFZRyxJQUFaLENBQWlCLG1CQUFqQixDQUF4QjtBQUNBLGdCQUFHRSxrQkFBa0JDLFFBQWxCLENBQTJCLFFBQTNCLENBQUgsRUFBeUM7QUFDckNULGtCQUFFSyxpQkFBRixFQUFxQkssUUFBckIsQ0FBOEIsVUFBOUI7QUFDSCxhQUZELE1BRU87QUFDSFYsa0JBQUVLLGlCQUFGLEVBQXFCTSxXQUFyQixDQUFpQyxVQUFqQztBQUNIOztBQUVELGdCQUFJQyxvQkFBb0JaLEVBQUVHLFFBQUYsRUFBWUcsSUFBWixDQUFpQixrQkFBakIsQ0FBeEI7QUFDQSxnQkFBR00sa0JBQWtCSCxRQUFsQixDQUEyQixRQUEzQixDQUFILEVBQXlDO0FBQ3JDVCxrQkFBRU8sa0JBQUYsRUFBc0JHLFFBQXRCLENBQStCLFVBQS9CO0FBQ0gsYUFGRCxNQUVPO0FBQ0hWLGtCQUFFTyxrQkFBRixFQUFzQkksV0FBdEIsQ0FBa0MsVUFBbEM7QUFDSDtBQUNKLFNBakJEO0FBa0JILEtBbkJEO0FBb0JILENBdEJEIiwiZmlsZSI6Ikdsb2JhbC9zY3JpcHRzLmpzIiwic291cmNlc0NvbnRlbnQiOlsiJChmdW5jdGlvbigpIHtcbiAgICAvLyBEaXNhYmxlIG5leHQgYW5kIHByZXZpb3VzIGFycm93cyBpZiBjdXJyZW50IGl0ZW0gaXMgdGhlIGxhc3Qgc2xpZGVcbiAgICAkKCdbZGF0YS13cmFwPVwiZmFsc2VcIl0nKS5lYWNoKGZ1bmN0aW9uKGluZGV4LCBjYXJvdXNlbCkge1xuICAgICAgICAkKGNhcm91c2VsKS5vbignc2xpZC5icy5jYXJvdXNlbCcsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIGxldCBsZWZ0Q2Fyb3VzZWxBcnJvdyA9ICQoY2Fyb3VzZWwpLmZpbmQoJy5sZWZ0LmNhcm91c2VsLWNvbnRyb2wnKTtcbiAgICAgICAgICAgIGxldCByaWdodENhcm91c2VsQXJyb3cgPSAkKGNhcm91c2VsKS5maW5kKCcucmlnaHQuY2Fyb3VzZWwtY29udHJvbCcpO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICBsZXQgZmlzdENhcm91c2VsU2xpZGUgPSAkKGNhcm91c2VsKS5maW5kKCcuaXRlbTpmaXJzdC1jaGlsZCcpO1xuICAgICAgICAgICAgaWYoZmlzdENhcm91c2VsU2xpZGUuaGFzQ2xhc3MoJ2FjdGl2ZScpKSB7XG4gICAgICAgICAgICAgICAgJChsZWZ0Q2Fyb3VzZWxBcnJvdykuYWRkQ2xhc3MoJ2Rpc2FibGVkJyk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICQobGVmdENhcm91c2VsQXJyb3cpLnJlbW92ZUNsYXNzKCdkaXNhYmxlZCcpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgXG4gICAgICAgICAgICBsZXQgbGFzdENhcm91c2VsU2xpZGUgPSAkKGNhcm91c2VsKS5maW5kKCcuaXRlbTpsYXN0LWNoaWxkJyk7XG4gICAgICAgICAgICBpZihsYXN0Q2Fyb3VzZWxTbGlkZS5oYXNDbGFzcygnYWN0aXZlJykpIHtcbiAgICAgICAgICAgICAgICAkKHJpZ2h0Q2Fyb3VzZWxBcnJvdykuYWRkQ2xhc3MoJ2Rpc2FibGVkJyk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICQocmlnaHRDYXJvdXNlbEFycm93KS5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfSk7XG59KTsiXX0=
