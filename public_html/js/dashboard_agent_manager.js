'use strict';

const commandAgent = (function() {
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
                var str = '';
                switch (valid[i]) {
                    case 'name' :
                        str = '기업명을 입력해주세요.';
                        break;
                    case 'ceo' :
                        str = '대표자명을 입력해주세요.';
                        break;
                    case 'business' :
                        str = '업종 및 생산품을 입력해주세요.';
                        break;
                    case 'tel' :
                        str = '연락처를 입력해주세요.';
                        break;
                    case 'zip' :
                        str = '우편번호를 입력해주세요.';
                        break;
                    case 'address' :
                        str = '주소를 입력해주세요.';
                        break;
                    case 'address_detail' :
                        str = '상세 주소를 입력해주세요.';
                        break;
                }
                common.err(str);
                return ;
            }
        }

        common.ajax('/dashboard/agent/register', data, 'POST', (res) => {
            function func() {
                window.location.href = '/dashboard/agent/lists';
            }
            common.pop_blue('정상 처리가 완료되었습니다.', func);
            //alert('정상 처리가 완료되었습니다.');

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
                    case 'name' :
                        str = '기업명을 입력해주세요.';
                        break;
                    case 'ceo' :
                        str = '대표자명을 입력해주세요.';
                        break;
                    case 'business' :
                        str = '업종 및 생산품을 입력해주세요.';
                        break;
                    case 'tel' :
                        str = '연락처를 입력해주세요.';
                        break;
                    case 'zip' :
                        str = '우편번호를 입력해주세요.';
                        break;
                    case 'address' :
                        str = '주소를 입력해주세요.';
                        break;
                    case 'address_detail' :
                        str = '상세 주소를 입력해주세요.';
                        break;
                }
                common.err(str);
                return ;
            }
        }

        let agent = $('#agent').val();
        if (util.isEmpty(agent) === true) {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다.');
            return ;
        }
        data['agent'] = agent;

        common.ajax('/dashboard/agent/modify', data, 'POST', (res) => {
            //alert('정상 처리가 완료되었습니다.');
            function func() {
                window.location.href = '/dashboard/agent/lists';
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
        common.ajax('/dashboard/agent/access', {access : data}, 'POST', (res) => {
            function func() {
                window.location.reload();
            }
            //alert('정상 처리가 완료되었습니다.');
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
                data.push(util.Number.parseInt($(this).val()));
            }
        });
        if (data.length == 0) {
            //alert('선택된 정보가 없습니다.');
            common.err('선택된 정보가 없습니다.');
            return;
        }

        common.ajax('/dashboard/agent/deny', {deny : data}, 'POST', (res) => {
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
        let agent = $(event).attr('agent');
        if (util.isEmpty(agent) === true) {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다.');
            return ;
        }

        common.ajax('/dashboard/agent/remove', {agent : agent}, 'POST', (res) => {
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
        let agent = $(event).attr('agent');
        if (util.isEmpty(agent) === true) {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다.');
            return ;
        }

        common.ajax('/dashboard/agent/restore', {agent : agent}, 'POST', (res) => {
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

    function agent(event) {
        $('#agent').val($(event).attr('agent'));
    };

    function chk(event) {
        let target = $(event).attr('id');
        let checked = $('input:checkbox[id="' + target + '"]').is(':checked');
        $('input[name=' + target + ']:checkbox').each(function() {
            $(this).prop('checked', checked);
        });
    };

    function append(event) {
        let agent = $('#agent').val();
        let form = $(event).attr('form');

        if (util.isEmpty(agent) === true) {
            //alert('잘못된 선택 정보입니다.');
            common.err('잘못된 정보가 있습니다.');
            return;
        }
        let target = [];
        /*$('input[name=' + form + ']:checked').each(function() {
            target.push(util.Number.parseInt($(this).val()));
        });*/
        $('input[name=' + form + ']').each(function() {
            if($(this).is(':checked')){
                target.push(util.Number.parseInt($(this).val()));
            }
        });
        if (target.length == 0) {
            //alert('선택된 정보가 없습니다.');
            common.err('선택된 정보가 없습니다.');
            return;
        }

        common.ajax('/dashboard/agent/designate', {
            agent : agent,
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
        let agent = $('#agent').val();
        if (util.isEmpty(agent) === true) {
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

        common.ajax('/dashboard/agent/undesignate', {
            agent : agent,
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
            tpl += "<th>기업명</th>";
            tpl += "<th>대표자명</th>";
            tpl += "<th>업종 및 생산품</th>";
            tpl += "<th>연락처</th>";
            tpl += "<th>주소</th>";
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
                    tpl += "<td><input name='" + type + "' value='" + obj['company_idx'] + "' class='checkbox-warning' type='checkbox'></td>";
                    tpl += "<td>" + obj['company_name'] + "</td>";
                    tpl += "<td>" + obj['company_ceo'] + "</td>";
                    tpl += "<td>" + obj['company_business'] + "</td>";
                    tpl += "<td>" + obj['company_tel'] + "</td>";
                    tpl += "<td>" + obj['company_zip'] + obj['company_address'] + obj['company_address_detail'] + "</td>";
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
            /*<button type="button" class="btn btn-primary removeButton" form="user"> 삭제 </button>
             <button type="button" class="btn btn-secondary formClear" data-dismiss="modal"> 닫기 </button>*/
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
                //tpl += "<li class='" + active  + "'><a href='#' class='searchPageMove " + active  + "' form='" + type + "' type='" + condition + "' page='" + j + "'>" + j + "</a></li>";
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
            //tpl += "<div class='clearfix'></div>";
        }

        return tpl;
    };

    function search(event) {
        result($(event).attr('form'), $(event).attr('type'), undefined, $(event).attr('about'));
    };

    function find(type, agent, about) {
        console.log(agent);
        if (util.isEmpty(agent) === true) {
            //alert('잘못된 선택 정보입니다.');
            common.err('잘못된 선택 정보입니다.');
            return;
        }
        result(type, 'manage', agent, about);
    };

    function result(type, condition, agent, about) {
        let data = {};
        let url = '';
        url = '/dashboard/agent/' + type + '/search';
        if (util.isEmpty(agent) === false) {
            let search = type + 's';
            if (type == 'company') search = 'companies';
            //search = 'companies';
            url = '/dashboard/agent/' + type + '/' + search;
            data['agent'] = agent;
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
        /*data['type'] = $(key + '_type').val();
        data['keyword'] = $(key + '_keyword').val();*/
        data['page'] = $(key + '_page').val();
        common.ajax(url, data, 'POST', (res) => {
            $(key + '_result').html(gridSearchForm(type, condition, res, about));
        }, (req) => {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다.');
        });
    };

    function manage(event) {
        let agent = $('#agent').val();
        let form = $(event).attr('form');
        let about = $(event).attr('about');
        find(form, agent, about);
    };

    function member(event) {
        let agent = $(event).attr('agent');
        let form = $(event).attr('form');
        let about = $(event).attr('about')

        find(form, agent, about);
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
                find(form, $('#agent').val());
            break;

            default:
            break;
        }
    };

    function formClear() {
        $('#user_search_type, #user_manage_type, #company_search_type, #company_manage_type').val('name');
        $('#user_search_page, #user_manage_page, #company_search_page, #company_manage_page').val('1');
        $('#user_search_keyword, #user_manage_keyword, #company_search_keyword, #company_manage_keyword').val('');
        $('#user_manage_result, #user_search_result, #company_manage_result, #company_search_result').html('');
    };

    return {
        register: () => {
            register();
        },
        modify: () => {
            modify();
        },
        agent: (event) => {
            formClear();
            agent(event);
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
            agent(event);
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
    $(document).on('click', '#agentRegister', (event) => {
        event.preventDefault();
        commandAgent.register();
    });

    $(document).on('click', '#agentModify', (event) => {
        event.preventDefault();
        commandAgent.modify();
    });

    $(document).on('click', '#agentAccess', () => {
        commandAgent.access();
    });

    $(document).on('click', '#agentDeny', () => {
        commandAgent.deny();
    });

    $(document).on('click', '.agentRemove', (event) => {
        commandAgent.remove(event.target);
    });

    $(document).on('click', '.agentRestore', (event) => {
        commandAgent.restore(event.target);
    });

    $(document).on('click', '.chkItem', (event) => {
        commandAgent.chk(event.target);
    });

    $(document).on('click', '.agentUserSearch, .agentCompanySearch', (event) => {
        commandAgent.agent(event.target);
    });

    $(document).on('click', '.agentUserRemove, .agentCompanyRemove', (event) => {
        commandAgent.member(event.target);
    });

    $(document).on('click', '.searchButton', (event) => {
        commandAgent.search(event.target);
    });

    $(document).on('click', '.searchManageButton', (event) => {
        commandAgent.manage(event.target);
    });

    $(document).on('click', '.appendButton', (event) => {
        commandAgent.append(event.target);
    });

    $(document).on('click', '.removeButton', (event) => {
        commandAgent.erase(event.target);
    });

    $(document).on('click', '.formClear', () => {
        commandAgent.clear();
    });

    $(document).on('click', '.searchPageMove', (event) => {
        commandAgent.paginator(event.target);
    });

})(jQuery);
