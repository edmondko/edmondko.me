/**
 * navigation.js
 *
 * Handles toggling the navigation menu for small screens.
 */

$(document).ready(function(){

	var $nav = $('.site-header');
	var $button = $('#menuToggle');
	var $menu = $('div.nav-menu');

	if ($button.not(':visible')){$menu.show()}
	
	$button.click(function(){
		if (! $menu.hasClass('nav-menu')){
			$menu.addClass('nav-menu');
		}
		if($button.hasClass('toggled-on'))
		{
			$button.removeClass('toggled-on');
			$menu.removeClass('toggled-on');
		}
		else 
		{
			$button.addClass('toggled-on');
			$menu.addClass('toggled-on');
		}
 	 	
	});


	$(function() {

	// grab the initial top offset of the navigation 
	var sticky_navigation_offset_top = $('.site-header').offset().top;
	
	// our function that decides weather the navigation bar should have "fixed" css position or not.
	var sticky_navigation = function(){
		var scroll_top = $(window).scrollTop(); // our current vertical position from the top
		
		// if we've scrolled more than the navigation, change its position to fixed to stick to top, otherwise change it back to relative
		if (scroll_top > sticky_navigation_offset_top) { 
			$('.site-header').css({ 'position': 'fixed', 'top':0, 'left':0 });
		} else {
			$('.site-header').css({ 'position': 'relative' }); 
		}   
	};
	
	// run our function on load
	sticky_navigation();
	
	// and run it again every time you scroll
	$(window).scroll(function() {
		 sticky_navigation();
	});
	
	// NOT required:
	// for this demo disable all links that point to "#"
	$('a[href="#"]').click(function(event){ 
		event.preventDefault(); 
	});
	
});
});