function successRegistration( res ) {
    hide_popups();
    $('.js-popup-registration-confirm').css('display', 'flex');
}

function successRegistrationConfirm( res ) {
    hide_popups();
    $('.js-popup-registration-complete').css('display', 'flex');

    setTimeout(function() {
        location.href = homeUrl;
    }, redirectTimeout);
}

function showRegistrationConfirmPopup(code) {
	hide_popups();
	$('.js-popup-registration-confirm input[name=code]').val(code); 
	$('.js-popup-registration-confirm').css('display', 'flex');
}

$(function(){
	$('body').on("click", '.js-link-registration', function(){
        hide_popups();
		$('.js-popup-registration').css('display', 'flex');
		clearForm('.js-frm-registration');
		return false;
    });
    
    $('body').on("change", '.js-frm-registration select[name=country]', function(){
		let id = $(this).val();
		changeCountry(id);
    });
	
    $('body').on("submit", '.js-frm-registration', function(){
		sendForm( '.js-frm-registration', 'POST', customerUrl + 'registration/', 'successRegistration' );
        return false;
    });

    $('body').on("submit", '.js-frm-registration-confirm', function(){
        sendForm( '.js-frm-registration-confirm', 'POST', customerUrl + 'confirmation/', 'successRegistrationConfirm' );
        return false;
    });
});
