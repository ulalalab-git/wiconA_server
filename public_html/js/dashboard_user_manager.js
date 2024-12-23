'use strict';

const commandUser = (function() {
    let common = $.Common || {};
    let data = {};

    function lengthCheck(target)
    {
        let valid = $('#' + target).val();
        if (util.isEmpty(valid) === true) {
            $('#' + target).focus();
            return false;
        }
        data[target] = valid;
    }

    function register() {
        let valid = [
            'passwd',
            'name',
            'name_last',
            'tel',
            'access',
        ];

        let email = $('#email').val();
        if (util.form.VTypes.email(email) == false) {
            $('#email').focus();
            //alert('email');
            common.err('이메일을 올바르게 입력하세요.');
            return ;
        }

        data = {};
        data['email'] = email;
        for (var i in valid) {
            if (lengthCheck(valid[i]) === false) {
                //alert(valid[i]);
                //var str = valid[i];
                var str = '';
                switch (valid[i]) {
                    case 'email' :
                        str = '이메일을 입력하세요.';
                        break;
                    case 'passwd' :
                        str = '비밀번호를 입력하세요.';
                        break;
                    case 'name' :
                        str = '이름을 입력하세요.';
                        break;
                    case 'name_last' :
                        str = '성을 입력하세요.';
                        break;
                    case 'tel' :
                        str = '연락처를 입력하세요.';
                        break;
                }

                common.err(str);
                return ;
            }
        }

        common.ajax('/dashboard/user/register', data, 'POST', (res) => {
            //alert('정상 처리가 완료되었습니다.');
            function func() {
                window.location.href = '/dashboard/user/lists';
            }
            common.pop_blue('정상 처리가 완료되었습니다.', func);
        }, (req) => {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다.');
        });
    };

    function modify() {
        let valid = [
            'name',
            'name_last',
            'tel',
            'access',
        ];

        let user = $('#user').val();
        let email = $('#email').val();
        if (util.isEmpty(user) === true) {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다.');
            return ;
        }

        data = {};
        data['user'] = user;
        data['email'] = email;
        let passwd = $('#modifypsswd').val();
        if (util.isEmpty(passwd) === false ) {
            if(passwd != $('#modifypsswdchk').val()) {
                common.err('비밀번호를 확인해주세요.');
                return;
            }
            data['passwd'] = passwd;
        }

        for (var i in valid) {
            if (lengthCheck(valid[i]) === false) {
                //alert(valid[i]);
                //var str = valid[i];
                var str = '';
                switch (valid[i]) {
                    case 'name' :
                        str = '이름을 입력하세요.';
                        break;
                    case 'name_last' :
                        str = '성을 입력하세요.';
                        break;
                    case 'tel' :
                        str = '연락처를 입력하세요.';
                        break;
                }
                common.err(str);
                return ;
            }
        }
        common.ajax('/dashboard/user/modify', data, 'POST', (res) => {
            //alert('정상 처리가 완료되었습니다.');
            function func() {
                window.location.href = '/dashboard/user/lists';
            }
            common.pop_blue('정상 처리가 완료되었습니다.', func);
        }, (req) => {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다.');
        });
    };

    function access() {
        let data = [];
        /*$('input[name=options]:checked').each(function() {
            if ($(this).attr('email') === 'mail@ulalalab.com') {
                return
            }
            data.push(util.Number.parseInt($(this).val()));
        });*/
        $('input[name=options]').each(function() {
            if($(this).is(':checked')) {
                if ($(this).attr('email') === 'mail@ulalalab.com') {
                    return
                }
                data.push(util.Number.parseInt($(this).val()));
            }
        });
        if (data.length == 0) {
            //alert('선택된 정보가 없습니다.');
            common.err('선택된 정보가 없습니다.');
            return;
        }

        common.ajax('/dashboard/user/access', {access : data}, 'POST', (res) => {
            //alert('정상 처리가 완료되었습니다.');
            function func() {
                window.location.reload();
            }
            common.pop_blue('정상 처리가 완료되었습니다.', func);
        }, (req) => {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다.');
        });
    };

    function deny() {
        let data = [];
        /*$('input[name=options]:checked').each(function() {
            data.push(util.Number.parseInt($(this).val()));
        });*/
        $('input[name=options]').each(function() {
            if($(this).is(':checked')) {
                if ($(this).attr('email') === 'mail@ulalalab.com') {
                    return
                }
                data.push(util.Number.parseInt($(this).val()));
            }
        });
        if (data.length == 0) {
            //alert('선택된 정보가 없습니다.');
            common.err('선택된 정보가 없습니다.');
            return;
        }

        common.ajax('/dashboard/user/deny', {deny : data}, 'POST', (res) => {
            //alert('정상 처리가 완료되었습니다.');
            function func() {
                window.location.reload();
            }
            common.pop_blue('정상 처리가 완료되었습니다.', func);
        }, (req) => {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다.');
        });
    };

    function remove(event) {
        let user = $(event).attr('user');
        if (util.isEmpty(user) === true) {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다.');
            return ;
        }

        common.ajax('/dashboard/user/remove', {user : user}, 'POST', (res) => {
            //alert('정상 처리가 완료되었습니다.');
            function func() {
                window.location.reload();
            }
            common.pop_blue('정상 처리가 완료되었습니다.', func);
        }, (req) => {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다.');
        });
    };

    function restore(event) {
        let user = $(event).attr('user');
        if (util.isEmpty(user) === true) {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다.');
            return ;
        }

        common.ajax('/dashboard/user/restore', {user : user}, 'POST', (res) => {
            //alert('정상 처리가 완료되었습니다.');
            function func() {
                window.location.reload();
            }
            common.pop_blue('정상 처리가 완료되었습니다.', func);
        }, (req) => {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다.');
        });
    };

    function chk() {
        let checked = $('input:checkbox[id="chk"]').is(':checked');
        $('input[name=options]:checkbox').each(function() {
            if ($(this).attr('disabled') === 'disabled') {
                return ;
            }
            $(this).prop('checked', checked);
        });
    };

    return {
        register: () => {
            register();
        },
        modify: () => {
            modify();
        },
        remove: (event) => {
            remove(event);
        },
        restore: (event) => {
            restore(event);
        },
        access: () => {
            access();
        },
        deny: () => {
            deny();
        },
        chk: () => {
            chk();
        },
    };
})();

;(function($) {
    $(document).on('click', '#userRegister', (event) => {
        event.preventDefault();
        commandUser.register();
    });

    $(document).on('click', '#userModify', (event) => {
        event.preventDefault();
        commandUser.modify();
    });

    $(document).on('click', '#userAccess', () => {
        commandUser.access();
    });

    $(document).on('click', '#userDeny', () => {
        commandUser.deny();
    });

    $(document).on('click', '.userRemove', (event) => {
        commandUser.remove(event.target);
    });

    $(document).on('click', '.userRestore', (event) => {
        commandUser.restore(event.target);
    });

    $(document).on('click', '#chk', () => {
        commandUser.chk();
    });
})(jQuery);
