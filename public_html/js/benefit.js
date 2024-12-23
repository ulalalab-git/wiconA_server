'use strict';

const command = (function() {
    let common = $.Common || {};
    let data = {};
    let email = null; // undefined
    let passwd = null;

    function cookie() {
        common.setCookieEmail($('#email').val());
    };

    function login() {
        let email = $('#email').val();
        if (util.isEmpty(email) === true) {
            // swal('이메일 주소가 바르지 않습니다.');
            $('#email').focus();
            return ;
        }
        let passwd = $('#passwd').val();
        if (util.isEmpty(passwd) === true) {
            // swal('비밀번호를 확인해 주시기 바랍니다.');
            $('#passwd').focus();
            return ;
        }
        $('#loginForm').submit();
    };

    function lengthCheck(target)
    {
        let valid = $('#' + target).val();
        if (util.isEmpty(valid) === true) {
            $('#' + target).focus();
            return false;
        }
        data[target] = valid;
    }

    function equalsCheck(target, confirm)
    {
        let valid = $('#' + target).val();
        if (valid !== $('#' + confirm).val() || valid.length < 6) {
            $('#' + target).focus();
            return false;
        }
        data[target] = valid;
    }

    function change() {
        let passwd = $('#passwd').val();
        if (passwd.length < 6) {
            $('#passwd').focus();
            return ;
        }

        common.ajax('/initialize', {
            token : $('#token').val(),
            passwd : passwd,
        }, 'POST', (res) => {
            //alert('비밀번호가 정상적으로 변경되었습니다.');
            function func() {
                window.location.href = '/';
            }
            common.pop_blue('비밀번호가 정상적으로 변경되었습니다.', func);
        }, (req) => {
            //alert('비밀번호를 확인해주시기 바랍니다.');
            common.err('비밀번호를 확인해주시기 바랍니다.');
        });
    };

    function valid() {
        data = {};
        let checked = $('#checkbox-signup').is(':checked');
        if (checked == false) {
            $('#checkbox-signup').focus();
            common.err('이용약관 동의에 체크해주세요.');
            return false;
        }
        if($('#checkbox-atlas').is(':checked')) {
            common.err('아트라스 콥코 전용에 체크해주세요.');
            return false;
        }
        if (lengthCheck('email') === false) {
            return false;
        }

        if (lengthCheck('name') === false) {
            return false;
        }

        if (lengthCheck('name_last') === false) {
            return false;
        }

        if (equalsCheck('passwd', 'confirm') === false) {
            return false;
        }

        return true;
    }

    function register() {
        if (valid() !== true) {
            return ;
        }

        $('#registerForm').submit();
    };

    function password() {
        let email = $('#email').val();
        if (util.form.VTypes.email(email) == false) {
            $('#email').focus();
            return ;
        }

        common.ajax('/password', {email : email}, 'POST', (res) => {
            //alert('이메일이 정상적으로 발송되었습니다.');
            common.pop_blue('이메일이 정상적으로 발송되었습니다.');
        }, (req) => {
            //alert('email을 확인해주시기 바랍니다.');
            common.err('email을 확인해주시기 바랍니다.');
        });

    };

    function goLogin() {
        window.location.href = '/';
    }

    return {
        saveId: () => {
            cookie();
        },
        valid: () => {
            login();
        },
        register: () => {
            register();
        },
        password: () => {
            password();
        },
        change: () => {
            change();
        },
        goLogin: () => {
            goLogin();
        },
    };
})();

;(function($) {
    $(document).on('click', '#register', (event) => {
        event.preventDefault();
        command.register();
    });

    $(document).on('click', '#loginButton', (event) => {
        event.preventDefault();
        command.valid();
    });

    $(document).on('click', '#find', () => {
        command.password();
    });

    $(document).on('click', '#save_id', () => {
        command.saveId();
    });

    $(document).on('click', '#change', () => {
        command.change();
    });

    $(document).on('click', '#registerConfirm', () => {
        command.goLogin();
    })
})(jQuery);
