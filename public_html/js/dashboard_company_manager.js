'use strict';

const commandCompany = (function() {
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
            'ceo',
            'business',
            'tel',
            'zip',
            'address',
            'address_detail',
        ];

        data = {};
        for (var i in valid) {
            if (lengthCheck(valid[i]) === false) {
                //alert(valid[i]);
                //var str = valid[i];
                var str = '';
                switch (valid[i]) {
                    case 'name':
                        str = '기업명을 입력해주세요.';
                        break;
                    case 'ceo':
                        str = '대표자명을 입력해주세요.';
                        break;
                    case 'business':
                        str = '업종 및 생산품을 입력해주세요.';
                        break;
                    case 'tel':
                        str = '연락처를 입력해주세요.';
                        break;
                    case 'zip':
                        str = '우편번호를 입력해주세요.';
                        break;
                    case 'address':
                        str = '주소를 입력해주세요.';
                        break;
                    case 'address_detail':
                        str = '상세 주소를 입력해주세요.';
                        break;
                }
                common.err(str);
                return ;
            }
        }

        common.ajax('/dashboard/company/register', data, 'POST', (res) => {
            //alert('정상 처리가 완료되었습니다.');
            function func() {
               window.location.href = '/dashboard/company/lists';
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
            'ceo',
            'business',
            'tel',
            'zip',
            'address',
            'address_detail',
        ];

        data = {};
        for (var i in valid) {
            if (lengthCheck(valid[i]) === false) {
                //alert(valid[i]);
                //var str = valid[i];
                var str = '';
                switch (valid[i]) {
                    case 'name':
                        str = '기업명을 입력해주세요.';
                        break;
                    case 'ceo':
                        str = '대표자명을 입력해주세요.';
                        break;
                    case 'business':
                        str = '업종 및 생산품을 입력해주세요.';
                        break;
                    case 'tel':
                        str = '연락처를 입력해주세요.';
                        break;
                    case 'zip':
                        str = '우편번호를 입력해주세요.';
                        break;
                    case 'address':
                        str = '주소를 입력해주세요.';
                        break;
                    case 'address_detail':
                        str = '상세 주소를 입력해주세요.';
                        break;
                }
                common.err(str);
                return ;
            }
        }

        let company = $('#company').val();
        if (util.isEmpty(company) === true) {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다.');
            return ;
        }
        data['company'] = company;

        common.ajax('/dashboard/company/modify', data, 'POST', (res) => {
            //alert('정상 처리가 완료되었습니다.');
            function func() {
                window.location.href = '/dashboard/company/lists';
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
            data.push(util.Number.parseInt($(this).val()));
        });*/
        $('input[name=options]').each(function() {
            if($(this).is(':checked')) {
                data.push(util.Number.parseInt($(this).val()));
            }
        });
        if (data.length == 0) {
            //alert('선택된 정보가 없습니다.');
            common.err('선택된 정보가 없습니다.');
            return;
        }

        common.ajax('/dashboard/company/access', {access : data}, 'POST', (res) => {
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
            if($(this).is(':checked')){
                data.push(util.Number.parseInt($(this).val()));
            }
        });
        if (data.length == 0) {
            //alert('선택된 정보가 없습니다.');
            common.err('선택된 정보가 없습니다.');
            return;
        }

        common.ajax('/dashboard/company/deny', {deny : data}, 'POST', (res) => {
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
        let company = $(event).attr('company');
        console.log(company);
        if (util.isEmpty(company) === true) {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다.');
            return ;
        }

        common.ajax('/dashboard/company/remove', {company : company}, 'POST', (res) => {
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
        let company = $(event).attr('company');
        if (util.isEmpty(company) === true) {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다.');
            return ;
        }

        common.ajax('/dashboard/company/restore', {company : company}, 'POST', (res) => {
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

    function company(event) {
        //$('#company').val($(event).attr('company'));
    };

    function chk(event) {
        let target = $(event).attr('id');
        let checked = $('input:checkbox[id="' + target + '"]').is(':checked');
        $('input[name=' + target + ']:checkbox').each(function() {
            $(this).prop('checked', checked);
        });
    };

    function append(event) {
        let form = $(event).attr('form');
        let company = $('#company').val();
        if (util.isEmpty(company) === true) {
            //alert('잘못된 선택 정보입니다.');
            common.err('잘못된 선택 정보입니다.');
            return;
        }

        let target = [];
        /*$('input[name=' + form + ']:checked').each(function() {
            target.push(util.Number.parseInt($(this).val()));
        });*/
        $('input[name=' + form + ']').each(function() {
            if($(this).is(':checked')) {
                target.push(util.Number.parseInt($(this).val()));
            }
        });

        if (target.length == 0) {
            //alert('선택된 정보가 없습니다.');
            common.err('선택된 정보가 없습니다.');
            return;
        }

        common.ajax('/dashboard/company/designate', {
            company : company,
            type: form,
            target : target,
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

    function erase(event) {
        let form = $(event).attr('form');
        let company = $('#company').val();
        if (util.isEmpty(company) === true) {
            //alert('잘못된 선택 정보입니다.');
            common.err('잘못된 선택 정보입니다.');
            return;
        }

        let target = [];
        /*$('input[name=' + form + ']:checked').each(function() {
            target.push(util.Number.parseInt($(this).val()));
        });*/
        $('input[name=' + form + ']').each(function() {
            if($(this).is(':checked')) {
                target.push(util.Number.parseInt($(this).val()));
            }
        });

        if (target.length == 0) {
            //alert('선택된 정보가 없습니다.');
            common.err('선택된 정보가 없습니다.');
            return;
        }

        common.ajax('/dashboard/company/undesignate', {
            company : company,
            type: form,
            target : target,
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

    function gridSearchForm(type, condition, items, about) {
        let colspan = (type === 'user') ? 5 : 6;

        let tpl = "<div class='borderList'>";
        tpl += "<table class='list'>";
        if (type === 'user') {
            tpl += "<colgroup><col width='5%'/><col width='23%'/><col width='23%'/><col width='23%'/><col width='23%'/></colgroup>";
            tpl += "<thead>";
            tpl += "<tr>";
            tpl += "<th><input id='" + type + "' class='checkbox-warning chkItem' type='checkbox'></th>";
            tpl += "<th>이메일</th>";
            tpl += "<th>이름</th>";
            tpl += "<th>연락처</th>";
            tpl += "<th>승인여부</th>";
        } else {
            tpl += "<thead>";
            tpl += "<tr>";
            tpl += "<th><input id='" + type + "' class='checkbox-warning chkItem' type='checkbox'></th>";
            tpl += "<th>장비명</th>";
            tpl += "<th>시리얼</th>";
            tpl += "<th>소프트웨어 버전</th>";
            tpl += "<th>하드웨어 버전</th>";
            tpl += "<th>서버 정보</th>";
        }
        tpl += "</tr>";
        tpl += "</thead>";
        tpl += "<tbody>";
        if (util.isEmpty(items.lists) === true) {
            tpl += "<tr>";
            tpl += "<td colspan='" + colspan + "' class='empty'> empty </td>";
            tpl += "</tr>";
        } else {
            for (let i in items.lists) {
                let obj = items.lists[i];
                tpl += "<tr>";
                if (type === 'user') {
                    tpl += "<td><input name='" + type + "' value='" + obj['user_idx'] + "' class='checkbox-warning' type='checkbox'></td>";
                    tpl += "<td>" + obj['user_email'] + "</td>";
                    tpl += "<td>" + obj['user_name'] + " " + obj['user_name_last'] + "</td>";
                    tpl += "<td>" + obj['user_tel'] + "</td>";
                    tpl += "<td>" + obj['user_access'] + "</td>";
                } else {
                    tpl += "<td><input name='" + type + "' value='" + obj['device_idx'] + "' class='checkbox-warning' type='checkbox'></td>";
                    tpl += "<td>" + obj['device_name'] + "</td>";
                    tpl += "<td>" + obj['device_serial'] + "</td>";
                    tpl += "<td>" + obj['device_sw_version'] + "</td>";
                    tpl += "<td>" + obj['device_hw_version'] + "</td>";
                    tpl += "<td>" + obj['device_server'] + "</td>";
                }
                tpl += "</tr>";
            }
        }
        tpl += "</tbody>";
        tpl += "</table>";
        tpl += "</div>";



        tpl += "<div class='bbsBottom'>";
        tpl += "<div class='btnbox'>";

        if(about == 'insert'){
            tpl += "<button type='button' class='btn01 appendButton' form='"+type+"'><span form='"+type+"'>등록</span></button>";
            tpl += "<button type='button' class='btn02 formClear' data-dismiss='modal'><span>닫기</span></button>";
        }else if(about == 'delete') {
            tpl += "<button type='button' class='btn01 removeButton' form='"+type+"'><span form='"+type+"'>삭제</span></button>";
            tpl += "<button type='button' class='btn02 formClear' data-dismiss='modal'><span>닫기</span></button>";
        }
        tpl += "</div>";
        tpl += "</div>";

        if (items.paginator.lastNum > 1) {
            tpl += "<div class='paging'>";
            tpl += "<span class='pagingBox'>";
            if(items.totalPage > 10) {
                tpl += "<a href='#' class='searchPageMove btn btn_1' form='" + type + "' type='" + condition + "' page='1' about='"+about+"'><em></em></a>";
            }
            if (items.paginator.page > 1 && items.paginator.prevPage >= 1) {
                tpl += "<a href='#' class='searchPageMove btn btn_2' form='" + type + "' type='" + condition + "' page='" + items.paginator.prevPage + "' about='"+about+"'><em></em></a>";
            }
            tpl += "<span class='num'>";
            for (let j = items.paginator.startNum; j < (items.paginator.lastNum + 1); ++j) {
                if (j == items.paginator.page) {
                    tpl += "<strong>"+j+"</strong>";
                }else{
                    tpl += "<a href='#' class='searchPageMove' form='"+type+"' type='"+condition+"' page='"+j+"' about='"+about+"'>"+j+"</a>";
                }
            }
            tpl += "</span>";
            if (items.paginator.nextPage > 1 && items.paginator.lastPage > items.paginator.page) {
                tpl += "<a href='#' class='searchPageMove btn btn_3' form='" + type + "' type='" + condition + "' page='" + items.paginator.nextPage + "' about='"+about+"'><em></em></a>";
            }
            if (items.totalPage > 10) {
                tpl += "<a href='#' class='searchPageMove btn_4' form='" + type + "' type='" + condition + "' page='" + items.paginator.lastPage + "' about='"+about+"'><em></em></a>";
            }
            tpl += "</span>";
            tpl += "</div>";
        }

        return tpl;
    };

    function search(event) {
        result($(event).attr('form'), $(event).attr('type'), undefined, $(event).attr('about'));
    };

    function find(type, company, about) {
        if (util.isEmpty(company) === true) {
            //alert('잘못된 선택 정보입니다.');
            common.err('잘못된 선택 정보입니다.');
            return;
        }

        result(type, 'manage', company, about);
    };

    function result(type, condition, company, about) {
        let data = {};
        let url = '';
        url = '/dashboard/company/' + type + '/search';
        if (util.isEmpty(company) === false || typeof company !== 'undefined' ) {
            let search = type + 's';
            url = '/dashboard/company/' + type + '/' + search;
            data['company'] = company;
        }

        let key = '#' + type + '_' + condition;

        if(about === 'insert'){
            let typekey = '#' + type + '_';
            data['type'] = $(typekey + 'set_type').val();
        }else{
            data['type'] = $(key + '_type').val();
        }

        if(about === 'insert'){
            let keywordkey = '#' + type + '_';
            data['keyword'] = $(keywordkey +'set_keyword').val();
        }else{
            data['keyword'] = $(key + '_keyword').val();
        }

        data['page'] = $(key + '_page').val();

        common.ajax(url, data, 'POST', (res) => {
            $(key + '_result').html(gridSearchForm(type, condition, res, about));
        }, (req) => {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다.');
        });
    };

    function manage(event) {
        let company = $('#company').val();
        let form = $(event).attr('form');
        let about = $(event).attr('about');

        find(form, company, about);
    };

    function member(event) {
        let company = $(event).attr('company');
        let form = $(event).attr('form');
        let about = 'delete';

        find(form, company, about);
    };

    function page(event) {
        let type = $(event).attr('type');
        let form = $(event).attr('form');
        let page = $(event).attr('page');
        let about = $(event).attr('about');

        let key = '#' + form + '_' + type;
        $(key + '_page').val(page);

        switch (type) {
            case 'search':
                result(form, type, undefined, about);
            break;

            case 'manage':
                find(form, $('#company').val(), about);
            break;

            default:
            break;
        }
    };

    function formClear() {
        $('#user_search_type, #user_manage_type, #device_search_type, #device_manage_type').val('email');
        $('#user_search_page, #user_manage_page, #device_search_page, #device_manage_page').val('1');
        $('#user_search_keyword, #user_manage_keyword, #device_search_keyword, #device_manage_keyword').val('');
        $('#user_manage_result, #user_search_result, #device_manage_result, #device_search_result').html('');
    };

    return {
        register: () => {
            register();
        },
        modify: () => {
            modify();
        },
        company: (event) => {
            formClear();
            company(event);
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
        chk: (event) => {
            chk(event);
        },
        search: (event) => {
            formClear();
            search(event);
        },
        append: (event) => {
            append(event);
        },
        erase: (event) => {
            erase(event);
        },
        manage: (event) => {
            manage(event);
        },
        member: (event) => {
            formClear();
            company(event);
            member(event);
        },
        clear: () => {
            formClear();
        },
        paginator: (event) => {
            page(event);
        },
    };
})();

;(function($) {
    $(document).on('click', '#companyRegister', (event) => {
        event.preventDefault();
        commandCompany.register();
    });

    $(document).on('click', '#companyModify', (event) => {
        event.preventDefault();
        commandCompany.modify();
    });

    $(document).on('click', '#companyAccess', () => {
        commandCompany.access();
    });

    $(document).on('click', '#companyDeny', () => {
        commandCompany.deny();
    });

    $(document).on('click', '.companyRemove', (event) => {
        commandCompany.remove(event.target);
    });

    $(document).on('click', '.companyRestore', (event) => {
        commandCompany.restore(event.target);
    });

    $(document).on('click', '.chkItem', (event) => {
        commandCompany.chk(event.target);
    });

    $(document).on('click', '.companyUserSearch, .companyDeviceSearch', (event) => {
        commandCompany.company(event.target);
    });

    $(document).on('click', '.companyUserRemove, .companyDeviceRemove', (event) => {
        commandCompany.member(event.target);
    });

    $(document).on('click', '.searchButton', (event) => {
        commandCompany.search(event.target);
    });

    $(document).on('click', '.searchManageButton', (event) => {
        commandCompany.manage(event.target);
    });

    $(document).on('click', '.appendButton', (event) => {
        commandCompany.append(event.target);
    });

    $(document).on('click', '.removeButton', (event) => {
        commandCompany.erase(event.target);
    });

    $(document).on('click', '.formClear', () => {
        commandCompany.clear();
    });

    $(document).on('click', '.searchPageMove', (event) => {
        commandCompany.paginator(event.target);
    });

})(jQuery);
