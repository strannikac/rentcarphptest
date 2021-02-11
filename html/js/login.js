function successLogin( result ) {
    save_cookie( 'customerId', result.data.customerID );
    save_cookie( 'customerToken', result.data.token );

    customerID = result.data.customerID;
    customerToken = result.data.token;

    location.reload();
}

function successRecovery( res ) {
    hide_popups();
    $('.js-popup-recovery-response').css('display', 'flex');

    setTimeout(function() {
        location.href = homeUrl;
    }, redirectTimeout);
}

function successForgot( res ) {
    hide_popups();
    $('.js-popup-forgot-response').css('display', 'flex');
}

function showRecoveryPopup(token) {
	hide_popups();
	$('.js-popup-recovery input[name=token]').val(token); 
	$('.js-popup-recovery').css('display', 'flex');
}

$(function(){
	$('body').on("click", '.js-link-login', function(){
        hide_popups();
		$('.js-popup-login').css('display', 'flex');
		clearForm('.js-frm-login');
		return false;
    });
    
    $('body').on("submit", '.js-frm-login', function(){
        sendForm( '.js-frm-login', 'POST', customerUrl + 'login/', 'successLogin' );
        return false;
    });

	$('body').on("click", '.js-link-forgot', function(){
        hide_popups();
		clearForm('.js-frm-forgot');
		$('.js-popup-forgot').css('display', 'flex');
		return false;
    });
	
    $('body').on("submit", '.js-frm-forgot', function(){
		sendForm( '.js-frm-forgot', 'POST', customerUrl + 'forgot/', 'successForgot' );
        return false;
    });
	
    $('body').on("submit", '.js-frm-recovery', function(){
		sendForm( '.js-frm-recovery', 'POST', customerUrl + 'recovery/', 'successRecovery' );
        return false;
    });
});