let carUrl = apiUrl + 'car/';

function successCars( result ) {
    result.data.hidden == 1 ? $('.js-frm-cars-list').addClass('hidden') : $('.js-frm-cars-list').removeClass('hidden');
    $('.js-cars').html(result.data.cars);
    setCarsData();
}

function successInbasket( result ) {
    const count = result.data.count;
    
    if(count > 0) {
        $('.js-link-basket .amount').text(count);
        $('.js-link-basket .amount').removeClass('hidden');
    } else {
        $('.js-link-basket .amount').addClass('hidden');
        $('.js-link-basket .amount').text('');
    }

    setTimeout(function() {
        $('.js-frm-inbasket .error, .js-frm-inbasket .success').hide();
    }, inbasketTimeout);
}

function calculatePrice(frm) {
    let startDate = createDate(frm.find(".js-from").val());
    let endDate = createDate(frm.find(".js-till").val());

    const oneDay = 24 * 60 * 60 * 1000;
    const days = Math.round(Math.abs((endDate - startDate) / oneDay)) + 1;

    const dayPrice = +frm.attr('data-price');
    let price = dayPrice * days;

    const holidayPercent = +frm.attr('data-holidayPercent');
    if(holidayPercent > 0) {
        const holidays = getHolidays(days, startDate);
        let count = holidays.length;

        if(count > 0) {
            const holidaysFee = dayPrice * count * holidayPercent / 100;
            price += holidaysFee;
        }
    }

    price += +frm.attr('data-oneTimeFee');

    const discountDays = +frm.attr('data-discountDays');
    const discountAddDays = +frm.attr('data-discountAddDays');

    if(discountDays > 0 && discountAddDays > 0 && days > discountDays) {
        price -= discountAddDays * dayPrice;
    }

    frm.parent().find('.js-price').text(price.toFixed(2));
}

function disableRentDates(date) {
    const dates = JSON.parse($(this).parents('.js-frm-inbasket').find('.js-rented-dates').text());

    let str = $.datepicker.formatDate('yy-mm-dd', date);
    const count = dates.length;

    for(let i = 0; i < count; i++) {
        if(str >= dates[i].start_date && str <= dates[i].end_date) {
            return [false];
        }
    }
    
    return [true]; 
}

function setCarsData() {
    $(".datepicker.js-from").datepicker({
        minDate: 0, 
        beforeShowDay: disableRentDates, 
        onSelect: function(date){
            let frm = $(this).parents('.js-frm-inbasket');
            let till = frm.find('.js-till');

            let tillDate = createDate(till.val());
            let minTillDate = createDate(date);

            const minDays = frm.attr('data-minDays');
            if(minDays > 1) {
                minTillDate.setDate(minTillDate.getDate() + (minDays - 1));
            }

            minTillDate = dateToString(minTillDate);
            tillDate = dateToString(tillDate);

            till.datepicker("option", "minDate", new Date(minTillDate));

            if(tillDate < minTillDate) {
                till.datepicker("setDate", new Date(minTillDate));
            }

            const maxDays = +frm.attr('data-maxDays');
            if(maxDays > 0) {
                let maxTillDate = createDate(date);

                maxTillDate.setDate(maxTillDate.getDate() + (maxDays - 1));
                till.datepicker("option", "maxDate", maxTillDate);
                maxTillDate = dateToString(maxTillDate);

                if(tillDate > maxTillDate) {
                    $(this).datepicker("setDate", new Date(maxTillDate));
                }
            }

            calculatePrice(frm);
        }
    });

    $(".datepicker.js-till").datepicker({
        minDate: 0, 
        beforeShowDay: disableRentDates, 
        onSelect: function(date){
            let frm = $(this).parents('.js-frm-inbasket');
            let from = frm.find('.js-from');

            let fromDate = from.val();
            let tillDate = createDate(date);

            const maxDays = +frm.attr('data-maxDays');
            if(maxDays > 0) {
                let maxTillDate = createDate(fromDate);
                maxTillDate.setDate(maxTillDate.getDate() + (maxDays - 1));
                $(this).datepicker("option", "maxDate", maxTillDate);

                maxTillDate = dateToString(maxTillDate);
                tillDate = dateToString(tillDate);

                if(tillDate > maxTillDate) {
                    $(this).datepicker("setDate", new Date(maxTillDate));
                }
            }

            calculatePrice(frm);
        }
    });

    $('.js-car .js-price').each( function() {
        const val = +$(this).text();
        $(this).text(val.toFixed(2));
    });
}

$(function() {
    setCarsData();

    $('body').on("change", '.js-frm-cars-search input[type=checkbox]', function(){
		$('.js-frm-cars-search').submit();
    });
    
    $('body').on("change", '.js-frm-cars-list select[name=currency]', function(){
        $('.js-frm-cars-search').submit();
    });
    
    $('body').on("click", '.js-frm-cars-list .js-sortby a', function(){
        let desc = 0;
        if($(this).hasClass('active')) {
            if($(this).attr('data-desc') != 1) {
                desc = 1;
            }
        }

        $('.js-frm-cars-list .js-sortby a').removeClass('active');
        $('.js-frm-cars-list .js-sortby a span').html('');
        $(this).addClass('active');

        $('.js-frm-cars-list .js-sortby a').attr('data-desc', 0);
        $(this).attr('data-desc', desc);

        const arrow = desc == 1 ? '&uarr;' : '&darr;';

        $(this).find('span').html(arrow);


        $('.js-frm-cars-search').submit();

        return false;
    });
    
    $('body').on("submit", '.js-frm-cars-search', function(){
        const currency = $('.js-frm-cars-list select[name=currency]').val();
        const sortby = $('.js-frm-cars-list .js-sortby a.active').attr('data-sort');
        const desc = $('.js-frm-cars-list .js-sortby a.active').attr('data-desc');

        const params = '?currency=' + currency + '&sortby=' + sortby + '&desc=' + desc;

        sendForm( '.js-frm-cars-search', 'POST', carUrl + 'search/' + params, 'successCars' );
        return false;
    });
    
    $('body').on("submit", '.js-frm-inbasket', function(){
        const id = $(this).attr('data-id');
        const currency = $('.js-frm-cars-list select[name=currency]').val();
        const params = '?currency=' + currency;

        $('.js-frm-inbasket[data-id=' + id + '] .error, .js-frm-inbasket[data-id=' + id + '] .success').hide();

        sendForm( '.js-frm-inbasket[data-id=' + id + ']', 'POST', carUrl + 'inbasket/' + id + '/' + params, 'successInbasket' );
        return false;
    });
    
    $('body').on("click", '.js-link-filters', function(){
        $('.js-cars-main aside').animate({marginLeft: '0px'}, effectsTime, 'linear');
        return false;
    });
    
    $('body').on("click", '.js-link-filters-back', function(){
        $('.js-cars-main aside').animate({marginLeft: '-300px'}, effectsTime, 'linear');
        return false;
    });
});