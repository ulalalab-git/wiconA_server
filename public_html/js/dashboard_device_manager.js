'use strict';

const commandDevice = (function() {
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
            'name',
            'serial',
            'sw_version',
            'hw_version',
            'server',
        ];

        data = {};
        for (var i in valid) {
            if (lengthCheck(valid[i]) === false) {
                //alert(valid[i]);
                //var str = valid[i];
                var str = '';
                switch (valid[i]) {
                    case 'name' :
                        str = '장비명을 입력하세요.';
                        break;
                    case 'serial' :
                        str = '시리얼을 입력하세요.';
                        break;
                    case 'sw_version' :
                        str = '소프트웨어 버전을 입력하세요.';
                        break;
                    case 'hw_version' :
                        str = '하드웨어 버전을 입력하세요.';
                        break;
                    case 'server' :
                        str = '서버 정보를 입력하세요.';
                        break;
                }
                common.err(str);
                return ;
            }
        }

        common.ajax('/dashboard/device/register', data, 'POST', (res) => {
            //alert('정상 처리가 완료되었습니다.');
            function func() {
                window.location.href = '/dashboard/device/lists';
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
            'serial',
            'sw_version',
            'hw_version',
            'server',
        ];

        let device = $('#device').val();
        if (util.isEmpty(device) === true) {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다.');
            return ;
        }

        data = {};
        data['device'] = device;
        for (var i in valid) {
            if (lengthCheck(valid[i]) === false) {
                //alert(valid[i]);
                //var str = valid[i];
                var str = '';
                switch (valid[i]) {
                    case 'name' :
                        str = '장비명을 입력하세요.';
                        break;
                    case 'serial' :
                        str = '시리얼을 입력하세요.';
                        break;
                    case 'sw_version' :
                        str = '소프트웨어 버전을 입력하세요.';
                        break;
                    case 'hw_version' :
                        str = '하드웨어 버전을 입력하세요.';
                        break;
                    case 'server' :
                        str = '서버 정보를 입력하세요.';
                        break;
                }
                common.err(str);
                return ;
            }
        }

        common.ajax('/dashboard/device/modify', data, 'POST', (res) => {
            //alert('정상 처리가 완료되었습니다.');
            function func() {
                window.location.href = '/dashboard/device/lists';
            }
            common.pop_blue('정상 처리가 완료되었습니다.', func);
        }, (req) => {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다.');
        });
    };

    function remove(event) {
        let device = $(event).attr('device');
        if (util.isEmpty(device) === true) {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다..');
            return ;
        }
        common.ajax('/dashboard/device/restore', {device : device}, 'POST', (res) => {
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
        console.log(event);
        let device = $(event).attr('device');
        console.log(device);
        if (util.isEmpty(device) === true) {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다..');
            return ;
        }
        let restore = $(event).attr('restore');

        common.ajax('/dashboard/device/restore', {device : device,restore: restore}, 'POST', (res) => {
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

    function checkbox(event) {
        let check = $(event).val();
        let target = $(event).attr('name');
        let checked = $(event).is(':checked');
        let user = (checked === true) ? check : '';
        $('#user').val(user);
        $('input[name=' + target + ']:checkbox').each(function() {
            if ($(this).val() === check) {
                return ;
            }

            $(this).attr('disabled', checked);
        });
    };


    function chk(event) {
        let target = $(event).attr('id');
        let checked = $('input:checkbox[id="' + target + '"]').is(':checked');
        $('input[name=' + target + ']:checkbox').each(function() {
            $(this).attr('checked', checked);
        });
    };

    function gridSearchForm(type, items) {
        let tpl = "<div>";
        tpl += "<div class='borderList'>"
        tpl += "<table class='list'>";
        tpl += "<thead>";
        tpl += "<tr>";
        tpl += "<th>선택<!--<input id='" + type + "' class='checkbox-warning chkItem' type='checkbox'>--></th>";
        tpl += "<th>이메일</th>";
        tpl += "<th>이름</th>";
        tpl += "<th>연락처</th>";
        tpl += "<th>승인여부</th>";
        tpl += "</tr>";
        tpl += "</thead>";
        tpl += "<tbody>";
        if (util.isEmpty(items.lists) === true) {
            tpl += "<tr>";
            tpl += "<td colspan='5'> empty </td>";
            tpl += "</tr>";
        } else {
            for (let i in items.lists) {
                let obj = items.lists[i];
                tpl += "<tr>";
                tpl += "<td><input name='" + type + "' value='" + obj['user_idx'] + "' class='checkbox-warning checkboxValid' type='checkbox'></td>";
                tpl += "<td style='font-weight:bold; color:#fff;'>" + obj['user_email'] + "</td>";
                tpl += "<td>" + obj['user_name'] + " " + obj['user_name_last'] + "</td>";
                tpl += "<td>" + obj['user_tel'] + "</td>";
                tpl += "<td>" + obj['user_access'] + "</td>";
                tpl += "</tr>";
            }
        }
        tpl += "</tbody>";
        tpl += "</table>";
        tpl += "</div>"
        tpl += "</div>";

        /*tpl += "<div class='bbsBottom'>";
        tpl += "    <div class='btnbox'>";
        tpl +=  "       <button type='button' class='btn01' id='portAppend'> 등록 </button>";
        tpl +=  "       <button type='button' class='btn02 formClear' data-dismiss='modal'> 닫기 </button>";
        tpl += "    </div>";
        tpl += "</div>";*/

        if (items.paginator.lastNum > 1) {
            tpl += "<div class='paging'>"
            tpl += "<span class='pagingBox'>";
            if(items.paginator.totalPage > 10) {
                tpl += "<a href='#' class='searchPageMove btn btn_1' type='"+type+"' page='1'><em></em></a>";
            }
            if (items.paginator.page > 1 && items.paginator.prevPage >= 1) {
                tpl += "<a href='#' class='searchPageMove btn btn_2' type='" + type + "' page='" + items.paginator.prevPage + "'><em></em></a>";
            }
            tpl += "<span class='num'>";
            for (let j = items.paginator.startNum; j < (items.paginator.lastNum + 1); ++j) {
                //let active = "";
                if (j == items.paginator.page) {
                    //active = "active";
                    tpl += "<strong>"+j+"</strong>";
                }else{
                    tpl += "<a href='#' class='searchPageMove' type='" + type + "' page='" + j + "'>" + j + "</a>";
                }
            }
            tpl += "</span>";
            if (items.paginator.nextPage > 1 && items.paginator.lastPage > items.paginator.page) {
                tpl += "<a href='#' class='searchPageMove btn btn_3' type='" + type + "' page='" + items.paginator.nextPage + "'><em></em></a>";
            }
            if(items.paginator.totalPage > 10) {
                tpl += "<a href='#' class='searchPageMove btn btn_4' type='"+type+"' page='"+items.paginator.lastPage+"'><em></em></a>";
            }
            /*tpl += "<div class='clearfix'></div>";*/
            tpl += "</div>"
            tpl += "</span>";
        }

        return tpl;
    };

    function search() {
        result('user', 'search');
    };

    function append() {
        let valid = [
            'user',
            'port',
        ];

        let device = $('#device').val();
        if (util.isEmpty(device) === true) {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다.');
            return ;
        }

        data = {};
        data['device'] = device;
        for (var i in valid) {
            if (lengthCheck(valid[i]) === false) {
                //alert(valid[i]);
                var str = valid[i];
                if(valid[i] == 'port') {
                    str = '포트 번호를 입력해주세요.';
                }else{
                    str = '유저를 선택해주세요.';
                }
                common.err(str);
                return ;
            }
        }

        common.ajax('/dashboard/device/config/port', data, 'POST', (res) => {
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

    function result(type, condition) {
        let data = {};
        let url = '/dashboard/company/' + type + '/search';

        let key = '#' + type + '_' + condition;
        data['type'] = $(key + '_type').val();
        data['keyword'] = $(key + '_keyword').val();
        data['page'] = $(key + '_page').val();
        common.ajax(url, data, 'POST', (res) => {
            $(key + '_result').html(gridSearchForm(type, res));
        }, (req) => {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다.');
        });
    };

    function page(event) {
        let page = $(event).attr('page');
        $('#user_search_page').val(page);

        return search();
    };

    function formClear() {
        $('#user_search_type').val('email');
        $('#user_search_page').val('1');
        $('#user_search_keyword, #port, #user').val('');
        $('#user_manage_result, #selectedUser').html('');
    };

    function port(event) {
        $('#selectedUser').html($(event).attr('user'));
        $('#port').val($(event).attr('port'));
    }

    function virtualRemove(event){
        let wv_idx = $(event).attr('vitual');

        if (util.isEmpty(wv_idx) === true ) {
            alert('잘못된 정보가 있습니다.......');
            return ;
        }

        common.ajax('/dashboard/device/config/remove', {
            wv_idx : wv_idx
        }, 'POST', (res) => {
            //alert('정상 처리가 완료되었습니다.');
            function func() {
            window.location.reload();
        }
        common.pop_blue('삭제 처리 되었습니다.', func);
    }, (req) => {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다.');
        });
    }

    function used(event) {
        let device = $(event).attr('device');
        let port = $(event).attr('port');
        let state = $(event).attr('state');

        if (util.isEmpty(device) === true ||
            util.isEmpty(state) === true ||
            util.isEmpty(port) === true) {
            alert('잘못된 정보가 있습니다.');
            return ;
        }

        common.ajax('/dashboard/device/config/state', {
            device : device,
            state : state,
            port : port,
        }, 'POST', (res) => {
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
        virtualRemove: (event) => {
            virtualRemove(event);
        },
        restore: (event) => {
            restore(event);
        },
        chk: (event) => {
            chk(event);
        },
        search: () => {
            // formClear();
            search();
        },
        valid: (event) => {
            checkbox(event);
        },
        paginator: (event) => {
            page(event);
        },
        append: () => {
            append();
        },
        clear: () => {
            formClear();
        },
        used: (event) => {
            used(event);
        },
        port: (event) => {
            formClear();
            port(event);
        },
    };
})();

;(function($) {
    $(document).on('click', '#deviceRegister', (event) => {
        event.preventDefault();
        commandDevice.register();
    });

    $(document).on('click', '#deviceModify', (event) => {
        event.preventDefault();
        commandDevice.modify();
    });

    $(document).on('click', '.devicePort', () => {
        commandDevice.clear();
    });

    $(document).on('click', '.deviceRemove', (event) => {
        commandDevice.remove(event.target);
    });

    $(document).on('click', '.deviceRestore', (event) => {
        commandDevice.restore(event.target);
    });

    $(document).on('click', '.chkItem', (event) => {
        commandDevice.chk(event.target);
    });

    $(document).on('click', '.checkboxValid', (event) => {
        commandDevice.valid(event.target);
    });

    $(document).on('click', '#userSearch', () => {
        commandDevice.search();
    });

    $(document).on('click', '.searchPageMove', (event) => {
        commandDevice.paginator(event.target);
    });

    $(document).on('click', '#portAppend', () => {
        commandDevice.append();
    });

    $(document).on('click', '.userUsed', (event) => {
        commandDevice.used(event.target);
    });

    $(document).on('click', '.userPortModify', (event) => {
        commandDevice.port(event.target);
    });

    $(document).on('click', '.virtualRemove', (event) => {
        commandDevice.virtualRemove(event.target);
});

})(jQuery);
