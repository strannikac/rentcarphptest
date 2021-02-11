let languageLink = '';

function successLang( result ) {
    location.href = languageLink;
}

function successSave( result ) {
    console.log('profile data changed');
}

function successPassword( result ) {
    console.log('password changed');
}

$(function() {
    customerID = get_cookie( 'customerId' );
    customerToken = get_cookie( 'customerToken' );

    $('body').on("click", '.js-link-logout', function(){
        logout();
        return false;
	});

    $('body').on("click", '.js-lang-switcher ul li a', function(){
		let id = $(this).attr('data-id');
		
        if( customerID != '' && customerToken != '' ) {
			languageLink = $(this).attr('href');
            console.log('languageLink!!!');

            if( languageId != id ) {
                sendAjax( '', 'POST', profileUrl + 'lang/' + id + '/', '', 'successLang' );
            }
        }

        return false;
    });
    
    $('body').on("change", '.js-frm-profile select[name=country]', function(){
		let id = $(this).val();
		changeCountry(id);
    });
    
    $('body').on("submit", '.js-frm-profile', function(){
        sendForm( '.js-frm-profile', 'POST', profileUrl + 'save/', 'successSave' );
        return false;
    });
    
    $('body').on("submit", '.js-frm-profile-password', function(){
        sendForm( '.js-frm-profile-password', 'POST', profileUrl + 'password/', 'successPassword' );
        return false;
    });
});