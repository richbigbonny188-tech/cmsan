"use strict";

$(function () {

	// Overlay Menu Toggle

	$("[data-toggle=menu]").on("click", function () {
		if (!$("body").hasClass("show-menu")) {
			$("#meco-overlay-menu").css("display", "block");
			setTimeout(function () {
				$("body").addClass("show-menu");
			}, 50);

			if (!$("#meco-overlay-menu #categories").length) {
				$("#meco-overlay-menu").append($('#categories'));
			}

			if ($("#meco-overlay-menu #categories .navbar-categories-left").length > 1) {
				$("#meco-overlay-menu #categories .navbar-categories-left:last").remove();
			}
		} else {
			$("body").removeClass("show-menu");
			setTimeout(function () {
				$("#meco-overlay-menu").css("display", "none");
			}, 500);

			if (!$("#header #categories").length) {
				$("#header").append($('#categories'));
			}
		}
	});

	function open_cart(e) {
		e.preventDefault();
		if (!$("body").hasClass("show-cart") && $("#cart-container:visible").length < 1) {
			window.scrollTo(0, 0);
			$("#offcanvas-cart-overlay").css("display", "block");
			setTimeout(function () {
				$("body").addClass("show-cart");
			}, 50);
		} else {
			$("body").removeClass("show-cart");
			setTimeout(function () {
				$("#offcanvas-cart-overlay").css("display", "none");
			}, 500);
		}

		if (e.data.auto_close) {
			setTimeout(close_cart, 3000);
		}
	}

	function close_cart() {
		$("body").removeClass("show-cart");
		setTimeout(function () {
			$("#offcanvas-cart-overlay").css("display", "none");
		}, 500);
	}

	// Offcanvas Cart Toggle

	$("[data-toggle=cart]").on("click", { auto_close: false }, open_cart);
	$("#offcanvas-cart-overlay").on("click", close_cart);

	$('body').on("CART_DROPDOWN_OPEN", { auto_close: true }, open_cart);

	// Shopping Cart Product Count

	setTimeout(function () {
		$("body").trigger("CART_DROPDOWN_UPDATE");
	}, 1500);

	// Add special class to body element, if the visitor is using IE11
	if (!!navigator.userAgent.match(/Trident.*rv\:11\./)) {
		$("body").addClass("ie11");
	}

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

	// // Category Pages Left Sidebar Behavior

	// if ($("body.page-index-type-cat").length) {

	//     var $left = $("#left"),
	//        $listing = $(".category-product-listing"),
	//        $categories = $(".box-categories", $left),
	//        $filter = $(".box-filter", $left),
	//        leftIsInvisible = $left.css("display") == "none";

	//     if (leftIsInvisible && $categories.length) {

	//        var sidebarNeeded = false;

	//        // check if category menu has sub categories

	//        var $listItems = $("li", $categories),
	//            $activeListItem = $("li.active", $categories);

	//        if ($listItems.length && !$activeListItem.length) {
	//            sidebarNeeded = true;
	//        }

	//        // check if filters available


	//        // wrap product_listing
	//        $sidebar = $("<div class='category-sidebar col-md-3'></div>");
	//        $listing
	//            .addClass("row")
	//            .wrapInner("<div class='col-md-9'></div>")
	//            .prepend($sidebar);
	//        $categories.appendTo($sidebar);
	//        $filter.appendTo($sidebar);
	//     }
	// }
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkdsb2JhbC9zY3JpcHRzLmpzIl0sIm5hbWVzIjpbIiQiLCJvbiIsImhhc0NsYXNzIiwiY3NzIiwic2V0VGltZW91dCIsImFkZENsYXNzIiwibGVuZ3RoIiwiYXBwZW5kIiwicmVtb3ZlIiwicmVtb3ZlQ2xhc3MiLCJvcGVuX2NhcnQiLCJlIiwicHJldmVudERlZmF1bHQiLCJ3aW5kb3ciLCJzY3JvbGxUbyIsImRhdGEiLCJhdXRvX2Nsb3NlIiwiY2xvc2VfY2FydCIsInRyaWdnZXIiLCJuYXZpZ2F0b3IiLCJ1c2VyQWdlbnQiLCJtYXRjaCIsImVhY2giLCJpbmRleCIsImNhcm91c2VsIiwibGVmdENhcm91c2VsQXJyb3ciLCJmaW5kIiwicmlnaHRDYXJvdXNlbEFycm93IiwiZmlzdENhcm91c2VsU2xpZGUiLCJsYXN0Q2Fyb3VzZWxTbGlkZSJdLCJtYXBwaW5ncyI6Ijs7QUFBQUEsRUFBRSxZQUFXOztBQUdaOztBQUVBQSxHQUFFLG9CQUFGLEVBQXdCQyxFQUF4QixDQUEyQixPQUEzQixFQUFvQyxZQUFXO0FBQzlDLE1BQUksQ0FBQ0QsRUFBRSxNQUFGLEVBQVVFLFFBQVYsQ0FBbUIsV0FBbkIsQ0FBTCxFQUFzQztBQUNyQ0YsS0FBRSxvQkFBRixFQUF3QkcsR0FBeEIsQ0FBNEIsU0FBNUIsRUFBdUMsT0FBdkM7QUFDQUMsY0FBVyxZQUFXO0FBQ3JCSixNQUFFLE1BQUYsRUFBVUssUUFBVixDQUFtQixXQUFuQjtBQUNBLElBRkQsRUFFRyxFQUZIOztBQUlBLE9BQUksQ0FBQ0wsRUFBRSxnQ0FBRixFQUFvQ00sTUFBekMsRUFBaUQ7QUFDaEROLE1BQUUsb0JBQUYsRUFBd0JPLE1BQXhCLENBQStCUCxFQUFFLGFBQUYsQ0FBL0I7QUFDQTs7QUFFRCxPQUFJQSxFQUFFLHdEQUFGLEVBQTRETSxNQUE1RCxHQUFxRSxDQUF6RSxFQUE0RTtBQUMzRU4sTUFBRSw2REFBRixFQUFpRVEsTUFBakU7QUFDQTtBQUNELEdBYkQsTUFhTztBQUNOUixLQUFFLE1BQUYsRUFB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QUZELE1BRU87QUFDSEwsTUFBRXlCLGlCQUFGLEVBQXFCaEIsV0FBckIsQ0FBaUMsVUFBakM7QUFDSDs7QUFFRCxPQUFJb0Isb0JBQW9CN0IsRUFBRXdCLFFBQUYsRUFBWUUsSUFBWixDQUFpQixrQkFBakIsQ0FBeEI7QUFDQSxPQUFHRyxrQkFBa0IzQixRQUFsQixDQUEyQixRQUEzQixDQUFILEVBQXlDO0FBQ3JDRixNQUFFMkIsa0JBQUYsRUFBc0J0QixRQUF0QixDQUErQixVQUEvQjtBQUNILElBRkQsTUFFTztBQUNITCxNQUFFMkIsa0JBQUYsRUFBc0JsQixXQUF0QixDQUFrQyxVQUFsQztBQUNIO0FBQ0osR0FqQkQ7QUFrQkgsRUFuQkQ7O0FBc0JIOztBQUVBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7O0FBRUE7O0FBRUE7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7OztBQUdBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsQ0F4SUQiLCJmaWxlIjoiR2xvYmFsL3NjcmlwdHMuanMiLCJzb3VyY2VzQ29udGVudCI6WyIkKGZ1bmN0aW9uKCkge1xuXHRcblx0XG5cdC8vIE92ZXJsYXkgTWVudSBUb2dnbGVcblx0XG5cdCQoXCJbZGF0YS10b2dnbGU9bWVudV1cIikub24oXCJjbGlja1wiLCBmdW5jdGlvbigpIHtcblx0XHRpZiAoISQoXCJib2R5XCIpLmhhc0NsYXNzKFwic2hvdy1tZW51XCIpKSB7XG5cdFx0XHQkKFwiI21lY28tb3ZlcmxheS1tZW51XCIpLmNzcyhcImRpc3BsYXlcIiwgXCJibG9ja1wiKTtcblx0XHRcdHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdCQoXCJib2R5XCIpLmFkZENsYXNzKFwic2hvdy1tZW51XCIpO1xuXHRcdFx0fSwgNTApO1xuXHRcdFx0XG5cdFx0XHRpZiAoISQoXCIjbWVjby1vdmVybGF5LW1lbnUgI2NhdGVnb3JpZXNcIikubGVuZ3RoKSB7XG5cdFx0XHRcdCQoXCIjbWVjby1vdmVybGF5LW1lbnVcIikuYXBwZW5kKCQoJyNjYXRlZ29yaWVzJykpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRpZiAoJChcIiNtZWNvLW92ZXJsYXktbWVudSAjY2F0ZWdvcmllcyAubmF2YmFyLWNhdGVnb3JpZXMtbGVmdFwiKS5sZW5ndGggPiAxKSB7XG5cdFx0XHRcdCQoXCIjbWVjby1vdmVybGF5LW1lbnUgI2NhdGVnb3JpZXMgLm5hdmJhci1jYXRlZ29yaWVzLWxlZnQ6bGFzdFwiKS5yZW1vdmUoKTtcblx0XHRcdH1cblx0XHR9IGVsc2Uge1xuXHRcdFx0JChcImJvZHlcIikucmVtb3ZlQ2xhc3MoXCJzaG93LW1lbnVcIik7XG5cdFx0XHRzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuXHRcdFx0XHQkKFwiI21lY28tb3ZlcmxheS1tZW51XCIpLmNzcyhcImRpc3BsYXlcIiwgXCJub25lXCIpO1xuXHRcdFx0fSwgNTAwKTtcblx0XHRcdFxuXHRcdFx0aWYgKCEkKFwiI2hlYWRlciAjY2F0ZWdvcmllc1wiKS5sZW5ndGgpIHtcblx0XHRcdFx0JChcIiNoZWFkZXJcIikuYXBwZW5kKCQoJyNjYXRlZ29yaWVzJykpO1xuXHRcdFx0fVxuXHRcdH1cblx0fSk7XG5cdFxuXHRmdW5jdGlvbiBvcGVuX2NhcnQoZSkge1xuXHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHRpZiAoISQoXCJib2R5XCIpLmhhc0NsYXNzKFwic2hvdy1jYXJ0XCIpICYmICQoXCIjY2FydC1jb250YWluZXI6dmlzaWJsZVwiKS5sZW5ndGggPCAxKSB7XG5cdFx0XHR3aW5kb3cuc2Nyb2xsVG8oMCwgMCk7XG5cdFx0XHQkKFwiI29mZmNhbnZhcy1jYXJ0LW92ZXJsYXlcIikuY3NzKFwiZGlzcGxheVwiLCBcImJsb2NrXCIpO1xuXHRcdFx0c2V0VGltZW91dChmdW5jdGlvbigpIHtcblx0XHRcdFx0JChcImJvZHlcIikuYWRkQ2xhc3MoXCJzaG93LWNhcnRcIik7XG5cdFx0XHR9LCA1MCk7XG5cdFx0fSBlbHNlIHtcblx0XHRcdCQoXCJib2R5XCIpLnJlbW92ZUNsYXNzKFwic2hvdy1jYXJ0XCIpO1xuXHRcdFx0c2V0VGltZW91dChmdW5jdGlvbigpIHtcblx0XHRcdFx0JChcIiNvZmZjYW52YXMtY2FydC1vdmVybGF5XCIpLmNzcyhcImRpc3BsYXlcIiwgXCJub25lXCIpO1xuXHRcdFx0fSwgNTAwKTtcblx0XHR9XG5cdFx0XG5cdFx0aWYgKGUuZGF0YS5hdXRvX2Nsb3NlKSB7XG5cdFx0XHRzZXRUaW1lb3V0KGNsb3NlX2NhcnQsIDMwMDApO1xuXHRcdH1cblx0fVxuXHRcblx0ZnVuY3Rpb24gY2xvc2VfY2FydCgpIHtcblx0XHQkKFwiYm9keVwiKS5yZW1vdmVDbGFzcyhcInNob3ctY2FydFwiKTtcblx0XHRzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuXHRcdFx0JChcIiNvZmZjYW52YXMtY2FydC1vdmVybGF5XCIpLmNzcyhcImRpc3BsYXlcIiwgXCJub25lXCIpO1xuXHRcdH0sIDUwMCk7XG5cdH1cblx0XG5cdFxuXHQvLyBPZmZjYW52YXMgQ2FydCBUb2dnbGVcblx0XG5cdCQoXCJbZGF0YS10b2dnbGU9Y2FydF1cIikub24oXCJjbGlja1wiLCB7YXV0b19jbG9zZTogZmFsc2V9LCBvcGVuX2NhcnQpO1xuXHQkKFwiI29mZmNhbnZhcy1jYXJ0LW92ZXJsYXlcIikub24oXCJjbGlja1wiLCBjbG9zZV9jYXJ0KTtcblx0XG5cdCQoJ2JvZHknKS5vbihcIkNBUlRfRFJPUERPV05fT1BFTlwiLCB7YXV0b19jbG9zZTogdHJ1ZX0sIG9wZW5fY2FydCk7XG5cdFxuXHQvLyBTaG9wcGluZyBDYXJ0IFByb2R1Y3QgQ291bnRcblx0XG5cdHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG5cdFx0JChcImJvZHlcIikudHJpZ2dlcihcIkNBUlRfRFJPUERPV05fVVBEQVRFXCIpO1xuXHR9LCAxNTAwKTtcblx0XG5cdC8vIEFkZCBzcGVjaWFsIGNsYXNzIHRvIGJvZHkgZWxlbWVudCwgaWYgdGhlIHZpc2l0b3IgaXMgdXNpbmcgSUUxMVxuXHRpZiAoISFuYXZpZ2F0b3IudXNlckFnZW50Lm1hdGNoKC9UcmlkZW50LipydlxcOjExXFwuLykpIHtcblx0XHQkKFwiYm9keVwiKS5hZGRDbGFzcyhcImllMTFcIik7XG5cdH1cbiAgICBcbiAgICAvLyBEaXNhYmxlIG5leHQgYW5kIHByZXZpb3VzIGFycm93cyBpZiBjdXJyZW50IGl0ZW0gaXMgdGhlIGxhc3Qgc2xpZGVcbiAgICAkKCdbZGF0YS13cmFwPVwiZmFsc2VcIl0nKS5lYWNoKGZ1bmN0aW9uKGluZGV4LCBjYXJvdXNlbCkge1xuICAgICAgICAkKGNhcm91c2VsKS5vbignc2xpZC5icy5jYXJvdXNlbCcsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIGxldCBsZWZ0Q2Fyb3VzZWxBcnJvdyA9ICQoY2Fyb3VzZWwpLmZpbmQoJy5sZWZ0LmNhcm91c2VsLWNvbnRyb2wnKTtcbiAgICAgICAgICAgIGxldCByaWdodENhcm91c2VsQXJyb3cgPSAkKGNhcm91c2VsKS5maW5kKCcucmlnaHQuY2Fyb3VzZWwtY29udHJvbCcpO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICBsZXQgZmlzdENhcm91c2VsU2xpZGUgPSAkKGNhcm91c2VsKS5maW5kKCcuaXRlbTpmaXJzdC1jaGlsZCcpO1xuICAgICAgICAgICAgaWYoZmlzdENhcm91c2VsU2xpZGUuaGFzQ2xhc3MoJ2FjdGl2ZScpKSB7XG4gICAgICAgICAgICAgICAgJChsZWZ0Q2Fyb3VzZWxBcnJvdykuYWRkQ2xhc3MoJ2Rpc2FibGVkJyk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICQobGVmdENhcm91c2VsQXJyb3cpLnJlbW92ZUNsYXNzKCdkaXNhYmxlZCcpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgXG4gICAgICAgICAgICBsZXQgbGFzdENhcm91c2VsU2xpZGUgPSAkKGNhcm91c2VsKS5maW5kKCcuaXRlbTpsYXN0LWNoaWxkJyk7XG4gICAgICAgICAgICBpZihsYXN0Q2Fyb3VzZWxTbGlkZS5oYXNDbGFzcygnYWN0aXZlJykpIHtcbiAgICAgICAgICAgICAgICAkKHJpZ2h0Q2Fyb3VzZWxBcnJvdykuYWRkQ2xhc3MoJ2Rpc2FibGVkJyk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICQocmlnaHRDYXJvdXNlbEFycm93KS5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfSk7XG5cdFxuXHRcblx0Ly8gLy8gQ2F0ZWdvcnkgUGFnZXMgTGVmdCBTaWRlYmFyIEJlaGF2aW9yXG5cdFxuXHQvLyBpZiAoJChcImJvZHkucGFnZS1pbmRleC10eXBlLWNhdFwiKS5sZW5ndGgpIHtcblx0XG5cdC8vICAgICB2YXIgJGxlZnQgPSAkKFwiI2xlZnRcIiksXG5cdC8vICAgICAgICAkbGlzdGluZyA9ICQoXCIuY2F0ZWdvcnktcHJvZHVjdC1saXN0aW5nXCIpLFxuXHQvLyAgICAgICAgJGNhdGVnb3JpZXMgPSAkKFwiLmJveC1jYXRlZ29yaWVzXCIsICRsZWZ0KSxcblx0Ly8gICAgICAgICRmaWx0ZXIgPSAkKFwiLmJveC1maWx0ZXJcIiwgJGxlZnQpLFxuXHQvLyAgICAgICAgbGVmdElzSW52aXNpYmxlID0gJGxlZnQuY3NzKFwiZGlzcGxheVwiKSA9PSBcIm5vbmVcIjtcblx0XG5cdC8vICAgICBpZiAobGVmdElzSW52aXNpYmxlICYmICRjYXRlZ29yaWVzLmxlbmd0aCkge1xuXHRcblx0Ly8gICAgICAgIHZhciBzaWRlYmFyTmVlZGVkID0gZmFsc2U7XG5cdFxuXHQvLyAgICAgICAgLy8gY2hlY2sgaWYgY2F0ZWdvcnkgbWVudSBoYXMgc3ViIGNhdGVnb3JpZXNcblx0XG5cdC8vICAgICAgICB2YXIgJGxpc3RJdGVtcyA9ICQoXCJsaVwiLCAkY2F0ZWdvcmllcyksXG5cdC8vICAgICAgICAgICAgJGFjdGl2ZUxpc3RJdGVtID0gJChcImxpLmFjdGl2ZVwiLCAkY2F0ZWdvcmllcyk7XG5cdFxuXHQvLyAgICAgICAgaWYgKCRsaXN0SXRlbXMubGVuZ3RoICYmICEkYWN0aXZlTGlzdEl0ZW0ubGVuZ3RoKSB7XG5cdC8vICAgICAgICAgICAgc2lkZWJhck5lZWRlZCA9IHRydWU7XG5cdC8vICAgICAgICB9XG5cdFxuXHQvLyAgICAgICAgLy8gY2hlY2sgaWYgZmlsdGVycyBhdmFpbGFibGVcblx0XG5cdFxuXHQvLyAgICAgICAgLy8gd3JhcCBwcm9kdWN0X2xpc3Rpbmdcblx0Ly8gICAgICAgICRzaWRlYmFyID0gJChcIjxkaXYgY2xhc3M9J2NhdGVnb3J5LXNpZGViYXIgY29sLW1kLTMnPjwvZGl2PlwiKTtcblx0Ly8gICAgICAgICRsaXN0aW5nXG5cdC8vICAgICAgICAgICAgLmFkZENsYXNzKFwicm93XCIpXG5cdC8vICAgICAgICAgICAgLndyYXBJbm5lcihcIjxkaXYgY2xhc3M9J2NvbC1tZC05Jz48L2Rpdj5cIilcblx0Ly8gICAgICAgICAgICAucHJlcGVuZCgkc2lkZWJhcik7XG5cdC8vICAgICAgICAkY2F0ZWdvcmllcy5hcHBlbmRUbygkc2lkZWJhcik7XG5cdC8vICAgICAgICAkZmlsdGVyLmFwcGVuZFRvKCRzaWRlYmFyKTtcblx0Ly8gICAgIH1cblx0Ly8gfVxufSk7Il19
