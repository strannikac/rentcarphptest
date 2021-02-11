const redirectTimeout = 3000;
const inbasketTimeout = 3000;
const effectsTime = 500;

const customerUrl = apiUrl + 'customer/';
const profileUrl = apiUrl + 'profile/';

let screenWidth = 0;
let screenHeight = 0;

const tabletWidth = 990;
const mobileWidth = 600;

function logout() {
    save_cookie( 'customerId', '' );
    save_cookie( 'customerToken', '' );

    location.href = homeUrl;
}

function successCountry( result ) {
	if(result.data.country != undefined && result.data.country.id != undefined && result.data.regions != undefined && result.data.regions != "") {
		let currentUri = $('.js-location-switcher .js-region .js-current').attr('data-uri');

		let html = '';
		$.each( result.data.regions, function( k, v) {
			let link = location.href.replace('/' + currentUri + '/', '/' + v.url + '/');
			html += '<li>' + 
					'<a href="' + link + '" data-uri="' + v.url + '" data-id="' + v.id + '">' + v.path + '</a>' +
				'</li>';
		});
		$('.js-location-switcher .js-region ul').html(html);

		$('.js-location-switcher .js-country .js-current').html(result.data.country.title);
		$('.js-location-switcher .js-country .js-current').attr('data-id', result.data.country.id);
		$('.js-location-switcher .js-country').mouseleave();

		if(($('.js-popup-registration').attr('class') == undefined || $('.js-popup-registration').css('display') == 'none') && $('.js-frm-profile').attr('class') == undefined) {
			$('.js-location-switcher .js-region .js-current').mouseover();
		}

		setLocationForRegistration();
		setLocationForConsole();
	}
}

function setLocationForRegistration() {
	if($('.js-popup-registration').attr('class') != undefined) {
		let countries = '';
		let regions = '';
		let currentId = $('.js-location-switcher .js-country .js-current').attr('data-id');

		$('.js-location-switcher .js-country ul li a').each( function() {
			countries += '<option value="' + $(this).attr("data-id") + '"' + (currentId == $(this).attr("data-id") ? ' selected="selected"' : '') + '>' + $(this).text() + '</option>';
		});

		$('.js-frm-registration select[name=country]').html(countries);

		$('.js-location-switcher .js-region ul li a').each( function() {
			regions += '<option value="' + $(this).attr("data-id") + '"' + ($(this).parent().hasClass('sel') ? ' selected="selected"' : '') + '>' + $(this).text() + '</option>';
		});

		$('.js-frm-registration select[name=region]').html(regions);
	}
}

function setLocationForConsole() {
	if($('.js-frm-profile').attr('class') != undefined) {
		let countries = '';
		let regions = '';
		let currentId = $('.js-location-switcher .js-country .js-current').attr('data-id');

		$('.js-location-switcher .js-country ul li a').each( function() {
			countries += '<option value="' + $(this).attr("data-id") + '"' + (currentId == $(this).attr("data-id") ? ' selected="selected"' : '') + '>' + $(this).text() + '</option>';
		});

		$('.js-frm-profile select[name=country]').html(countries);

		$('.js-location-switcher .js-region ul li a').each( function() {
			regions += '<option value="' + $(this).attr("data-id") + '"' + ($(this).parent().hasClass('sel') ? ' selected="selected"' : '') + '>' + $(this).text() + '</option>';
		});

		$('.js-frm-profile select[name=region]').html(regions);
	}
}

function changeCountry(id) {
	let currentId = $('.js-location-switcher .js-country .js-current').attr('data-id');
		
	if( currentId != id ) {
		sendAjax( '', 'GET', customerUrl + 'country/' + id + '/', '', 'successCountry' );
	}
}

function setPage() {
	screenHeight = +$(window).height();
	screenWidth = +$(window).width();
}

$(function(){
	$(window).resize(setPage);
	setPage();

	setLocationForRegistration();
	setLocationForConsole();

    $('body').on("click mouseover", '.js-lang-switcher .js-current', function(){
        $('.js-lang-switcher ul').css('display', 'flex');
    });

    $('body').on("mouseleave", '.js-lang-switcher', function(){
        $(this).find('ul').hide();
    });

    $('body').on("click mouseover", '.js-location-switcher .js-country .js-current', function(){
        $('.js-location-switcher .js-country ul').css('display', 'flex');
    });

    $('body').on("mouseleave", '.js-location-switcher .js-country', function(){
        $(this).find('ul').hide();
    });

    $('body').on("click mouseover", '.js-location-switcher .js-region .js-current', function(){
        let el = $('.js-location-switcher .js-region ul');
        let width = el.parents('.block-center').width();

		if(screenWidth > tabletWidth) {
			width = width / 2;
		}

        el.css({'width': width + 'px', 'display': 'flex'});
    });

    $('body').on("mouseleave", '.js-location-switcher .js-region', function(){
        $(this).find('ul').hide();
    });

    $('body').on("click", '.js-location-switcher .js-country ul li a', function(){
		let id = $(this).attr('data-id');
		changeCountry(id);
        return false;
    });

    $('body').on("click", '.js-link-mobile-nav', function(){
		$(window).scrollTop(0);
		hide_popups();
		
		$('.js-mobile-nav').fadeIn();

        return false;
    });
});