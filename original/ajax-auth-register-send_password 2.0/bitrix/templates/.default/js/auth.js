$(function(){

    // ---------------------------------------------------------
    // Auth modal
    // ---------------------------------------------------------
    var auth_url = '/bitrix/templates/.default/ajax/auth.php';
    var auth_timeout = 5000;
    var auth_error_timeout = 'Внимание! Время ожидания ответа сервера истекло';
    var auth_error_default = 'Внимание! Произошла ошибка, попробуйте отправить информацию еще раз';

    $('#auth-modal').on('submit','form',function(){

        $.ajax({
            type: "POST",
            url: auth_url,
            data: $(this).serializeArray(),
            timeout: auth_timeout,
            error: function(request,error) {
                if (error == "timeout") {
                    alert(auth_error_timeout);
                }
                else {
                    alert(auth_error_default);
                }
            },
            success: function(data) {
                $('#auth-modal .uk-modal-content').html(data);
                $('#auth-modal .backurl').val(window.location.pathname);
            }
        });

        return false;
    });

    $('#auth-modal').on('click','.ajax-link',function(){

        $.ajax({
            type: "GET",
            url: $(this).attr('href'),
            timeout: auth_timeout,
            error: function(request,error) {
                if (error == "timeout") {
                    alert(auth_error_timeout);
                }
                else {
                    alert(auth_error_default);
                }
            },
            success: function(data) {
                $('#auth-modal .uk-modal-content').html(data);
                $('#auth-modal .backurl').val(window.location.pathname);
            }
        });

        return false;
    });

    $('#auth-modal').on('click','.reload-captcha',function(){

        var reload_captcha = $(this);
        reload_captcha.find('.uk-icon-refresh').addClass('uk-icon-spin');

        $.ajax({
            type: "GET",
            url: $(this).attr('href'),
            timeout: auth_timeout,
            error: function(request,error) {
                if (error == "timeout") {
                    alert(auth_error_timeout);
                }
                else {
                    alert(auth_error_default);
                }
            },
            success: function(data) {
                $('#auth-modal .bx-captcha').html(data);
                reload_captcha.find('.uk-icon-refresh').removeClass('uk-icon-spin');
            }
        });

        return false;
    });

    $('#auth-modal').on({

        'show.uk.modal': function(modal){

            //login form
            $.ajax({
                type: "POST",
                url: auth_url,
                timeout: auth_timeout,
                error: function(request,error) {
                    if (error == "timeout") {
                        alert(auth_error_timeout);
                    }
                    else {
                        alert(auth_error_default);
                    }
                },
                success: function(data) {
                    $(modal.target).find('.uk-modal-content').html(data);
                    $(modal.target).find('.backurl').val(window.location.pathname);
                }
            });
        },

        'hide.uk.modal': function(){
            //Empty
        }
    });

});