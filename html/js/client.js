let customerID = '';
let customerToken = '';

const MAX_FILE_SIZE = 4194304;

$.datepicker.regional['ru'] = {
    closeText: 'Закрыть',
    prevText: 'Предыдущий',
    nextText: 'Следующий',
    currentText: 'Сегодня',
    monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
    monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'],
    dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
    dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
    dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
    weekHeader: 'Не',
    dateFormat: 'dd.mm.yy',
    firstDay: 1,
    isRTL: false,
    showMonthAfterYear: false,
    yearSuffix: ''
};

$.datepicker.regional['et'] = {
    closeText: 'Sulge',
    prevText: 'Eelmine',
    nextText: 'Järgmine',
    currentText: 'Täna',
    monthNames: ['Jaanuar','Veebruar','Märts','Aprill','Mai','Juuni','Juuli','August','September','Oktober','Novermber','Detsember'],
    monthNamesShort: ['Jnr','Vbr','Mrt','Apr','Mai','Jni','Jli','Aug','Spt','Okt','Nov','Dts'],
    dayNames: ['Pühapäev','Esmaspäev','Teisipäev','Kolmapäev','Neljapäev','Reede','Laupäev'],
    dayNamesShort: ['Php','Esm','Tsp','Klm','Nlp','rde','Lpv'],
    dayNamesMin: ['Ph','Es','Ts','Kl','Nl','Rd','Lp'],
    weekHeader: 'Nd',
    dateFormat: 'dd.mm.yy',
    firstDay: 1,
    isRTL: false,
    showMonthAfterYear: false,
    yearSuffix: ''
};

if(languageIso != 'en') {
    $.datepicker.setDefaults($.datepicker.regional[languageIso]);
}

function get_cookie( cookie ) {
    var str = $.cookie(cookie);
    if( str == undefined || str == null || str == '' ) {
        str = '';
    }
    return str;
}

function save_cookie( cookie, val ) {
    $.cookie(cookie, val, { expires: 1, path: '/;SameSite=None', secure: true});
}

function in_array(value, array) {
    for(var i = 0; i < array.length; i++) {
        if(array[i] == value) return true;
    }
    return false;
}

function hide_popups() {
    $('.js-popup').hide();
}

function sendAjax( frm, method, uri, data, successFunc ) {
    $.ajax({
        type: method,
        url: uri,
        data: data,
        processData: false,
        contentType: false,
        success: function(res){
            res = $.parseJSON(res);

            if(res.logout != undefined) {
                logout();
            }

            if( res.data.error == undefined || res.data.error == '' ) {
                if( frm != '' && res.data.msg != undefined && res.data.msg != '' ) {
                    showSuccessMessage( frm, res.data.msg );
                }
                eval(successFunc + '(res);');
            } else {
                if( frm != '' ) {
                    parseFormErrors( frm, res.data.error, true );
                }
            }
        }
    });
}

function sendForm( frm, method, uri, successFunc, fld, additionalData ) {
    if( !checkForm( frm, fld ) ) {
        return false;
    }

    let data = serializeForm(frm, fld);

    if( additionalData != undefined && additionalData != '' ) {
        $.each( additionalData, function( k, v) {
            data.append(k, v);
        });
    }

    sendAjax( frm, method, uri, data, successFunc );
}

function serializeForm( frm, fld ) {
    let val = '';
    let data = new FormData();

    let elements = $(frm + ' input, ' + frm + ' textarea, ' + frm + ' select');
    if( fld != undefined && fld.length > 0 ) {
        elements = [];
        $.each( fld, function( k, v) {
            elements[k] = $(frm + ' [name=' + v + ']');
        });
    }

    $(elements).each( function() {
        if($(this).attr('type') == 'file') {
            let count = $(this)[0].files.length;

            if( count > 0 ) {
                for( i = 0; i < count; i++ ) {
                    if( $(this)[0].files[i] != undefined ) {
                        data.append($(this).attr('name') + '[]', $(this)[0].files[i]);
                    }
                }
            }
        } else if ( $(this).attr('type') == 'checkbox' ) {
            if( $(this).prop('checked') ) {
                val = $(this).val();
                data.append($(this).attr('name'), val);
            }
        } else {
            val = $(this).val();

            if( val != '' ) {
                if($(this).attr('type') == 'password') {
                    val = b64_sha1(val);
                } else if($(this).children('option:selected').attr('data-id') != undefined) {
                    val = $(this).children('option:selected').attr('data-id');
                }

                data.append($(this).attr('name'), val);
            }
        }
    });

    return data;
}

function checkForm( frm, fld ) {
    let is_err = false;

    $(frm + ' .error').hide();
    $(frm + ' input, ' + frm + ' textarea, ' + frm + ' select').removeClass('el-err');

    let val = '';
    let pass = '';
    let err = '';

    let elements = $(frm + ' input, ' + frm + ' textarea, ' + frm + ' select');
    if( fld != undefined && fld.length > 0 ) {
        elements = [];
        $.each( fld, function( k, v) {
            elements[k] = $(frm + ' [name=' + v + ']');
        });
    }

    $(elements).each( function() {
        if($(this).attr('type') == 'file') {
            var check = checkFile($(this));
            if( check != '' ) {
                is_err = true;
            }
        } else if($(this).attr('type') == 'checkbox') {
            if( $(this).attr('required') != undefined && !$(this).prop('checked') ) {
                $(this).addClass('el-err');
                is_err = true;
            }
        }  else {
            if( $(this).attr('required') != undefined ) {
                val = $(this).val();
                if( val == '' || val == '0' ) {
                    $(this).addClass('el-err');
                    is_err = true;
                }
            }
        }

        val = '';
        if($(this).attr('type') == 'password') {
            if( $(this).attr('data-retype') == undefined ) {
                pass = $(this).val();
            } else {
                val = $(this).val();
            }
        }

        if( pass != '' && val != '' ) {
            err = checkPassword( pass, val );
            if( err != '' ) {
                is_err = true;
            }
        }
    });

    if( is_err ) {
        if( err != '' ) {
            showFormError(frm, err);
        }

        return false;
    }

    return true;
}

function checkPassword( pass, retype ) {
    let err = '';

    const re = /[0-9]/;
    const re2 = /[^0-9]/;

    if (pass != retype) {
        err = ARR_LOCALES['ERR_2011'];
    } else if (pass.length < 8 || pass.length > 20) {
        err = ARR_LOCALES['ERR_2006'];
    } else if (!re.test(pass) || !re2.test(pass)) {
        err = ARR_LOCALES['ERR_2007'];
    }

    return err;
}

function checkFile( obj ) {
    let err = '';

    obj.parents(".file").find('span').text(obj.val().split('\\').pop());

    let fpos = -1;
    let fext = '';
    let fsizeCC = 0;
    let fsizeFF = 0;

    let count = obj[0].files.count;

    if( count > 0 ) {
        for( i = 0; i < count; i++ ) {
            if( obj.attr('required') != undefined && obj[0].files[i] == undefined ) {
                err = ARR_LOCALES['EMPTY_FILE_SELECTED'];
            } else {
                if( obj[0].files[i] != undefined ) {
                    let strExt = obj.attr('data-ext');

                    if( strExt != undefined && strExt != '' ) {
                        fpos = obj[0].files[i].name.lastIndexOf('.');
                        if( fpos != -1 ) {
                            fext = obj[0].files[i].name.substr(fpos+1).toLowerCase();
                        }

                        let arrExt = strExt.split(', ');

                        if( !in_array(fext, arrExt) ) {
                            return ARR_LOCALES['ERR_4004'];
                        }
                    }

                    fsizeCC = obj[0].files[i].size;
                    fsizeFF = obj[0].files[i].fileSize;

                    if (fsizeCC < 1 || fsizeFF < 1) {
                        err = ARR_LOCALES['EMPTY_FILE_SELECTED'];
                    } else if (fsizeCC > MAX_FILE_SIZE || fsizeFF > MAX_FILE_SIZE) {
                        err = ARR_LOCALES['ERR_4003'];
                    }
                }
            }
        }
    }

    if( err != '' ) {
        return err;
    }

    return '';
}

function parseFormErrors( frm, errors, showErr ) {
    let err = '';

    $.each( errors, function( i, item ) {
        el = $(frm + ' [name=' + i + ']');

        if( el.attr('name') != undefined ) {
            el.addClass('el-err');
        }

        if( showErr ) {
            if( err != '' ) {
                err += '<br/>';
            }

            err += item;
        }
    });

    if( err != '' ) {
        showFormError( frm, err );
    }
}

function showFormError( frm, err ) {
    $(frm + ' .error').html(err);
    $(frm + ' .error').show();
}

function showSuccessMessage( frm, msg ) {
    $(frm + ' .success').html(msg);
    $(frm + ' .success').show();
}

function setFormData(frm, val, data) {
    let name = '';
    let html = '';
    let el = {};

    $(frm + ' select').each( function() {
        el = $(this);
        name = el.attr('name');
        html = '';

        if(data[name] != undefined && data[name] != '') {
            $.each( data[name], function( k, v) {
                if( v.id != undefined && v.value != undefined ) {
                    if(v.parent > 0) {
                        v.value = '-' + v.value;
                        html += '<option value="' + k + '" data-id="' + v.id + '">' + v.value + '</option>';
                    } else {
                        var disabled = '';

                        if(name == 'category') {
                            disabled = ' disabled="disabled"';
                        }
                        html += '<option value="' + k + '" data-id="' + v.id + '"' + disabled + '>' + v.value + '</option>';
                    }
                } else {
                    html += '<option value="' + k + '">' + v + '</option>';
                }
                
            });

            $(frm + ' select[name=' + name + ']').html(html);

            if(val[name] != undefined && val[name] != '') {
                $(frm + ' select[name=' + name + ']').val(val[name]);
            }
        }
    });

    $(frm +' input, ' + frm +' textarea').each( function() {
        if($(this).attr('type') != 'file' && val[name] != undefined && val[name] != '') {
            $(frm + ' [name=' + name + ']').val(val[name]);
        }
    });

    $.each( data, function( name, obj) {
        if( $(frm +' .js-check-' + name).attr('class') != undefined) {
            html = '';
            $.each( data[name], function( k, v) {
                html += '<label><input type="checkbox" name="' + name + '[]" value="' + k + '" />' + v + '</label>';
            });

            $(frm +' .js-check-' + name).html(html);
        }
    });

    if(val.hdr != undefined && val.hdr != '') {
        $(frm + ' .js-hdr').html(val.hdr);
    }

    if(val.files != undefined && val.files != '') {
        $(frm + ' .js-files').html(val.files);
    }
}

function clearForm( frm ) {
    $(frm)[0].reset();
}

function dateToString(date) {
    let month = date.getMonth() + 1;
    if(month < 10) {
        month = '0' + month;
    }

    let day = date.getDate();
    if(day < 10) {
        day = '0' + day;
    }

    return date.getFullYear() + '-' + month + '-' + day;
}

function getHolidays(days, startDate) {
    let holidays = [];

    for(let i = days; i > 0; i--) {
        if(i < days) {
            startDate.setDate(startDate.getDate() + 1);
        }

        const weekday = startDate.getDay();
        if(weekday == 0 || weekday == 6) {
            holidays.push(dateToString(startDate));
        }
    }

    return holidays;
}

function createDate(str) {
    const sep = '.';
    let date = new Date(str);

    if(date == 'Invalid Date' || str.indexOf(sep) != -1) {
        let dateParts = str.split(sep);
        date = new Date(+dateParts[2], dateParts[1] - 1, +dateParts[0]); 
    }

    return date;
}

$(function(){
    $('body').on("click", '.js-link-scrolltop', function(){
        $(window).scrollTop(0,0);
        return false;
    });

    $(document).click(function (e) {
        let container = $('.js-popup .popup-fix');

        if (!container.is(e.target) 
            && container.has(e.target).length === 0)
        {
            container.parent().hide();
        }
    });

    $('body').on("click", '.js-link-closepopup', function(){
        hide_popups();
        return false;
    });
});
