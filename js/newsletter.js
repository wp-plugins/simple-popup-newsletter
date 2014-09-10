
var $j = jQuery.noConflict();
 
$j(document).ready(function($) {

	$('.popup-box-wrapper').on('click', function(event){
		if( $(event.target).is('.popup-box-close') || $(event.target).is('.popup-box-wrapper') ) {
			event.preventDefault();
			$(this).removeClass('is-visible');
	    $('.popup-box').removeClass('anim');
		}
	});
  
	$('.popup-box-wrapper-unsub').on('click', function(event){
		if( $(event.target).is('.popup-box-close') || $(event.target).is('.popup-box-wrapper-unsub') ) {
			event.preventDefault();
			$(this).removeClass('is-visible');
	    $('.popup-box').removeClass('anim');
		}
	});
  
	$('#box_sub').on('click', function(event){
			event.preventDefault();
			$('.popup-box-wrapper').removeClass('is-visible');
	    $('.popup-box').removeClass('anim');
      popup_box_form.submit();
	});

	$('#box_exit').on('click', function(event){
			event.preventDefault();
			$('.popup-box-wrapper').removeClass('is-visible');
	    $('.popup-box').removeClass('anim');
	});
  
	$('#box_unsub').on('click', function(event){
			event.preventDefault();
			$('.popup-box-wrapper').removeClass('is-visible');
	    $('.popup-box').removeClass('anim');
      popup_box_form_unsub.submit();
	});

	$('#box_exit_unsub').on('click', function(event){
			event.preventDefault();
			$('.popup-box-wrapper-unsub').removeClass('is-visible');
	    $('.popup-box').removeClass('anim');
	});
  
	$('#subscribtion_button').on('click', function(event){
	   event.preventDefault();
	   $('.popup-box-wrapper').addClass('is-visible');
	   $('.popup-box').addClass('anim');
	});
  
	$('#unsubscribtion_button').on('click', function(event){
	   event.preventDefault();
	   $('.popup-box-wrapper-unsub').addClass('is-visible');
	   $('.popup-box').addClass('anim');
	});
  
	$('#all_checked').on('click', function(event){
     $('.chk').attr('checked', this.checked);
	});

	$('#table_order').on('change', function(event){
      subscribers_form.submit();
	});
  

});

function show_popup() {
	 $j('.popup-box-wrapper').addClass('is-visible');
	 $j('.popup-box').addClass('anim');
}

function show_popup_unsub() {
	 $j('.popup-box-wrapper-unsub').addClass('is-visible');
	 $j('.popup-box').addClass('anim');
}

