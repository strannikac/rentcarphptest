let basketUrl = apiUrl + 'basket/';

function successBasketDelete( result ) {
    $('.js-item[data-id=' + result.data.id + ']').remove();
    let count = +$('.js-link-basket .amount').text() - 1;
    $('.js-link-basket .amount').text(count);

    calculateTotalPrice();
}

function successBasketSave( result ) {
    const tr = $('.js-item[data-id=' + result.data.id + ']');
    tr.find('.js-price').text(result.data.price.toFixed(2));
    tr.next().find('.js-details').html(result.data.details);
    tr.find('.js-frm-update button').addClass('disabled');
    
    calculateTotalPrice();

    setTimeout(function() {
        $('.js-frm-update .error').hide();
    }, inbasketTimeout);
}

function successCheckout( result ) {
    location.href = result.data.link;
}

function calculateTotalPrice() {
    let total = 0;

    $('.js-item .js-price').each( function() {
        total += +$(this).text();
    });

    if(total > 0) {
        $('.js-total-price').text(total.toFixed(2));
    } else {
        $('.js-items').addClass('hidden');
        $('.js-link-basket .amount').addClass('hidden');
        $('.js-link-basket .amount').text('');
    }
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

    frm.parents('.js-item').find('.js-price').text(price.toFixed(2));
}

function disableRentDates(date) {
    const dates = JSON.parse($(this).parents('.js-frm-update').find('.js-rented-dates').text());

    let str = $.datepicker.formatDate('yy-mm-dd', date);
    const count = dates.length;

    for(let i = 0; i < count; i++) {
        if(str >= dates[i].start_date && str <= dates[i].end_date) {
            return [false];
        }
    }
    
    return [true]; 
}

function selectDateFrom(date, item) {
    const prevFrom = item.attr('data-prev');

    let frm = item.parents('.js-frm-update');
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
            item.datepicker("setDate", new Date(maxTillDate));
        }
    }

    item.attr('data-prev', item.val());
    till.attr('data-prev', till.val());

    if(date != prevFrom) {
        frm.find('button').removeClass('disabled');

        calculatePrice(frm);
        calculateTotalPrice();
    }
}

function setBasketData() {
    $(".datepicker.js-from").datepicker({
        minDate: 0, 
        beforeShowDay: disableRentDates, 
        onSelect: function(date){
            selectDateFrom(date, $(this));
        }
    });

    $(".datepicker.js-till").datepicker({
        minDate: 0, 
        beforeShowDay: disableRentDates, 
        onSelect: function(date){
            const prevTill = $(this).attr('data-prev');

            if(date != prevTill) {
                let frm = $(this).parents('.js-frm-update');
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

                $(this).attr('data-prev', $(this).val());
                frm.find('button').removeClass('disabled');

                calculatePrice(frm);
                calculateTotalPrice();
            }
        }
    });

    $('.js-item .js-price').each( function() {
        const val = +$(this).text();
        $(this).text(val.toFixed(2));
    });

    const val = +$('.js-total-price').text();
    $('.js-total-price').text(val.toFixed(2));

    $('.datepicker.js-from').each( function() {
        selectDateFrom($(this).val(), $(this));
    });
}

$(function() {
    setBasketData();
    
    $('body').on("click", '.js-link-details', function(){
        const tr = $(this).parents('.js-item').next();
        tr.toggleClass('hidden');

        return false;
    });
    
    $('body').on("click", '.js-link-delete', function(){
        const id = $(this).parents('.js-item').attr('data-id');

        sendAjax( '', 'POST', basketUrl + 'delete/' + id + '/', '', 'successBasketDelete' );
        return false;
    });
    
    $('body').on("submit", '.js-frm-update', function(){
        if(!$(this).hasClass('disabled')) {
            const id = $(this).parents('.js-item').attr('data-id');

            sendForm( '.js-item[data-id=' + id + '] .js-frm-update', 'POST', basketUrl + 'save/' + id + '/', 'successBasketSave' );
            return false;
        }
    });
    
    $('body').on("click", '.js-link-checkout', function(){
        sendForm( '.js-frm-checkout', 'POST', basketUrl + 'checkout/', 'successCheckout' );
        return false;
    });
});