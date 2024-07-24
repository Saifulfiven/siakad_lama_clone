function dropdown() {
	$('.dropdown').mouseover(function(e) {
		$('.dropdown-menus').hide();
		$(this).find('.dropdown-menus').show();
		$(this).find('.dropdown-menus').mouseleave(function(){
			$('.dropdown-menus').hide();
		});
	});

	$(document).click(function(){
		$('.dropdown-menus').hide();
	});
}

function toggle() {
	$('#toggle-menu').click(function () {
		$('header').show('fast');
	});

	$('#close-menu').click(function () {
		$('header').hide('fast');
	});
}

function checkAll(id) {
	$(id).change(function() {
		var x = $(this).data('check');
		console.log(x);
		if ($(this).prop('checked') == true) {
			$('input[data-check="' + x + '"]').prop('checked', 'true');
		} else {
			$('input[data-check="' + x + '"]').removeAttr('checked');
		}
	});
}

dropdown();
toggle();

var wow = new WOW();
wow.init();

$(document).ready(function() {  
	$("header").niceScroll({
		cursorcolor: "#00aeef",
		cursorwidth: "3px",
		cursorborder: "0",
		cursorborderradius: "5px",
	});
});