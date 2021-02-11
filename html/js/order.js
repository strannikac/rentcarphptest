let orderUrl = apiUrl + 'order/';

function successRenewOrder( result ) {
    location.href = result.data.link;
}

function successTerminateOrder( result ) {
    location.href = result.data.link;
}

function calculateRenewTotalPrice() {
    let total = 0;

    $('.js-item .js-price').each( function() {
        total += +$(this).text();
    });

    if(total > 0) {
        $('.js-total-price').text(total.toFixed(2));
    } else {
        $('.js-frm-renew').addClass('hidden');
    }
}

function calculateRenewPrice(tr) {
    let startDate = createDate(tr.find(".js-from").val());
    let endDate = createDate(tr.find(".js-till").val());

    const oneDay = 24 * 60 * 60 * 1000;
    const days = Math.round(Math.abs((endDate - startDate) / oneDay)) + 1;

    const dayPrice = +tr.attr('data-price');
    let price = dayPrice * days;

    const holidayPercent = +tr.attr('data-holidayPercent');
    if(holidayPercent > 0) {
        const holidays = getHolidays(days, startDate);
        let count = holidays.length;

        if(count > 0) {
            const holidaysFee = dayPrice * count * holidayPercent / 100;
            price += holidaysFee;
        }
    }

    price += +tr.attr('data-renewalFee');

    tr.find('.js-price').text(price.toFixed(2));
}

function disableRentDates(date) {
    const dates = JSON.parse($(this).parents('.js-item').find('.js-rented-dates').text());

    let str = $.datepicker.formatDate('yy-mm-dd', date);
    const count = dates.length;

    for(let i = 0; i < count; i++) {
        if(str >= dates[i].start_date && str <= dates[i].end_date) {
            return [false];
        }
    }
    
    return [true]; 
}

function selectRenewDateFrom(date, item) {
    const prevFrom = item.attr('data-prev');

    let tr = item.parents('.js-item');
    let till = tr.find('.js-till');

    let tillDate = createDate(till.val());
    let minTillDate = createDate(date);

    const minDays = tr.attr('data-minDays');
    if(minDays > 1) {
        minTillDate.setDate(minTillDate.getDate() + (minDays - 1));
    }

    minTillDate = dateToString(minTillDate);
    tillDate = dateToString(tillDate);

    till.datepicker("option", "minDate", new Date(minTillDate));

    if(tillDate < minTillDate) {
        till.datepicker("setDate", new Date(minTillDate));
    }

    const maxDays = +tr.attr('data-maxDays');
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
        calculateRenewPrice(tr);
        calculateRenewTotalPrice();
    }
}

function setRenewOrderData() {
    $(".js-frm-renew .datepicker.js-from").datepicker({
        minDate: 0, 
        beforeShowDay: disableRentDates, 
        onSelect: function(date){
            selectRenewDateFrom(date, $(this));
        }
    });

    $(".js-frm-renew .datepicker.js-till").datepicker({
        minDate: 0, 
        beforeShowDay: disableRentDates, 
        onSelect: function(date){
            const prevTill = $(this).attr('data-prev');

            if(date != prevTill) {
                let tr = $(this).parents('.js-item');
                let from = tr.find('.js-from');

                let fromDate = from.val();
                let tillDate = createDate(date);

                const maxDays = +tr.attr('data-maxDays');
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

                calculateRenewPrice(tr);
                calculateRenewTotalPrice();
            }
        }
    });

    $('.js-item .js-price').each( function() {
        const val = +$(this).text();
        $(this).text(val.toFixed(2));
    });

    const val = +$('.js-total-price').text();
    $('.js-total-price').text(val.toFixed(2));

    $('.js-frm-renew .datepicker.js-from').each( function() {
        $(this).datepicker("option", "minDate", new Date($(this).val()));
        selectRenewDateFrom($(this).val(), $(this));
    });
}

$(function() {
    if($('.js-frm-renew').attr('class') != undefined) {
        setRenewOrderData();
    }
    
    $('body').on("click", '.js-item .js-link-details', function(){
        const tr = $(this).parents('.js-item').next();
        tr.toggleClass('hidden');

        return false;
    });
    
    $('body').on("click", '.js-item .js-link-cars', function(){
        const id = $(this).parents('.js-item').attr('data-id');
        $('.js-car-item[data-order=' + id + ']').toggleClass('hidden');

        return false;
    });
    
    $('body').on("click", '.js-car-item .js-link-details', function(){
        const tr = $(this).parents('.js-car-item').next();
        tr.toggleClass('hidden');

        return false;
    });
    
    $('body').on("submit", '.js-frm-renew', function(){
        const id = $(this).attr('data-id');

        sendForm( '.js-frm-renew', 'POST', orderUrl + 'renew/' + id + '/', 'successRenewOrder' );
        return false;
    });
    
    $('body').on("submit", '.js-frm-terminate', function(){
        const id = $(this).attr('data-id');

        sendForm( '.js-frm-terminate', 'POST', orderUrl + 'terminate/' + id + '/', 'successTerminateOrder' );
        return false;
    });
});