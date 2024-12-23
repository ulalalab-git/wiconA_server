! function($) {
    'use common';
    const Common = function() {};
    Common.prototype.language = (lang) => {
        Common.setCookie('language', lang);
        window.document.location.reload();
    };
    Common.prototype.left_menu = function(val) {
        Common.setCookie('leftmenu', val);
    };
    Common.prototype.setCookieEmail = (id) => {
        Common.setCookie('save_id', id);
    };
    Common.prototype.getCookie = (key) => {
        var name = key + '=';
        var ca = window.document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return '';
    };
    Common.setCookie = (key, val) => {
        let expire = new Date();
        expire.setDate(expire.getDate() + 1);
        let cookies = key + '=' + escape(val) + '; path=/ ';
        if (typeof 1 != 'undefined') {
            cookies += ';expires=' + expire.toGMTString() + ';';
        }
        window.document.cookie = cookies;
    };
    Common.prototype.ajax = (url, data, type, success, error) => {
        type = type || 'GET';
        type = type.toUpperCase();

        $.ajax({
            xhrFields: {
                withCredentials: true
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-SERVER-WIMX': 'atlas',
            },
            type: type,
            async: false,
            url: url,
            data: data,
            dataType: 'json',
            timeout: 30000,
            cache: false,
            contentType: 'application/x-www-form-urlencoded; charset=utf-8',
            success: (response, status, request) => {
                success(response);
            },
            error: (request, status, error) => {
                error(request);
            },
            befforeSend: () => {},
            complete: () => {},
        });
    };

    Common.prototype.pop_green = () => {
        pop.green({
            ment:'기업정보를 수정했습니다.',
            dimm:true,
            callback:function(){
                window.location.reload();
            }
        });
    };

    Common.prototype.pop_blue = (ment, func) => {
        // 선택됱 정보가 없습니다.
        pop.blue({
            ment:ment,
            dimm:true,
            callback:function(){
                //alert('확인')
                //window.location.href = url;
                if(func){
                    func();
                }
            }
        });
    };

    /*Common.prototype.pop_red = () => {
        // 선택됱 정보가 없습니다.
        pop.red({
            ment:'관리자로 설정할<br />회원을 선택합니다.',
            dimm:true,
            callback:function(){
                //alert('확인');
            }
        });
    };*/

    Common.prototype.err = (ment, func) => {
        // 선택됱 정보가 없습니다.
        pop.err({
            ment: ment,
            dimm:true,
            callback:function(){
                //alert('확인')
                if(func){
                    func();
                }
            }
        });
    };
    $.Common = new Common;
}(window.jQuery),
function($) {
    'use common';
}(window.jQuery);


$(document).ready(function () {
    //your code here
    modalTab();
});
var lnbStatus = true;
var lnbMoveStatus = false;
function lnMenuToggle(){
    'use strict';
    if(lnbMoveStatus === true){return false};
    $("#lnbBox .lnbSub").hide();
    $("#lnbBox .depth_1").removeClass("hover");

    if(lnbStatus){

        $("#lnbBox").addClass("mini")
        lnbMoveStatus = true;
        $(".logo h1").animate({opacity:0},400,function(){
            $(".lnbWrap").animate({width:60},600,function(){
                $("#lnbBox .depth_1 >a").css({width:0});
                $(".lnbMenu").css({width:60});
            });
            $(".container").animate({marginLeft:60},600,function(){
                lnbMoveStatus = false;
            });
            lnbStatus = false;
        });

    }else{
        lnbMoveStatus = true;
        $(".lnbMenu").css({width:210});
        $("#lnbBox .depth_1 >a").css({width:"auto"});
        $("#lnbBox").removeClass("mini");
        $(".lnbWrap")
        //.css({display:"block"})
            .animate({width:210},600);
        $(".container").animate({marginLeft:210},600,function(){
            //$(".logo h1").animate({width:91},400)	;
            $(".logo h1").animate({opacity:1},400,function(){
                lnbMoveStatus = false;
            });
        })
        lnbStatus = true;

    }
}
function lnbMenu(){
    'use strict';
    var lnbMenus = $("#lnbBox .depth_1");
    var lnb = $("#lnbBox");
    lnbMenus.each(function(){
        var $this  =  $(this);
        this.sub  =  $(".lnbSub" , this);
        this.sub$  =  this.sub;
        this.sub = this.sub[0] ? this.sub : null ;
        this.one  =  $(">a" , this);
        this.one[0].li  =  $this;
        this.one[0].sub  =  this.sub;
        if(this.sub){
            this.sub[0].li  =  $this;
            this.sub[0].sub  =  this.sub;
        }
        this.one.status = false;
        this.one.on("click",function(){
            //if(!lnb.hasClass("mini")){
            if(this.sub){
                if(this.li.hasClass("hover")){
                    this.li.removeClass("hover");
                    this.sub.slideUp();
                }else{
                    $(".lnbSub").hide();
                    lnbMenus.removeClass("hover");
                    this.li.addClass("hover");
                    this.sub.slideDown();
                }
                return false;
            }
            //}
        });
        $(".container").click(function(){
            if(lnb.hasClass("mini")){
                $(".lnbSub").hide();
                lnbMenus.removeClass("hover");
            }
        });

    });
}
function inputTitleTxt(obj){
    'use strict';
    function chk_focus(This){
        if(This.attr("type") ==="password"){
            This.addClass("bg");
        }
        if(This.attr("readonly")){ return false;}
        var tit = This.attr("title");
        if(This.hasClass("bg")){
            This[0].bgType = true;
        }
        This.removeClass("bg");
        if(tit === This.val() ||  This.val() === ""){
            This.val("");
            This.addClass("on");
        }
    }
    function chk_blur(This){
        if(This.attr("readonly")){ return false;}
        var tit = This.attr("title");
        if(tit === This.val() ||  This.val() === ""){
            if(!This[0].bgType){
                This.val(tit);
            }else{
                This.addClass("bg");
            }
            This.removeClass("on");
        }
    }
    if(obj){
        obj.each(function(){
            chk_focus($(this));
        });

        obj.on("focus",function(){
            chk_focus($(this));
        })
        obj.on("blur",function(){
            chk_blur($(this));
        });
    }else{
        $("input").each(function(){
            var This = $(this);
            if(This.attr("readonly")){ return false;}
            var tit = This.attr("title");
            if(tit !== This.val()){
                if(This.hasClass("bg")){
                    This[0].bgType = true;
                }
                This.addClass("on");
            }
        });

        $("input").on("focus",function(){
            chk_focus($(this));
        });
        $("input").on("blur",function(){
            chk_blur($(this));
        });
    }

}

var pop = {
    body : function(txt2,dimchk){
        var txt = '';
        if(dimchk ==true) txt += '<div class="dimm2" style=""><\/div>';
        txt += '<div class="layerWrap2" id="alertPopup">';
        txt += '	<div class="btnclose">';
        txt += '		<a href="#" class="layerClose"><div class="layerClose-img"></div></a>';
        txt += '	</div>';
        txt += '	<div class="layerContents">';
        txt += txt2;
        txt += '	</div>';
        txt += '</div>';
        return txt;
    },
    alert : function(ment,calback){
        var dimchk = true;
        if(ment.dimm == false){
            dimchk = false;
        }
        var txt2 = '		<div class="ment">';
        txt2 +=	ment.ment;
        txt2 += '		</div>';
        txt2 += '		<div class="btnbox">';
        txt2 //+= '			<!-- <a href="#" class="btn02 layerCancel"><span class="">취소</span></a> -->';
        txt2 += '			<a href="#" class="btn01" class=""><span class="">확인</span></a>';
        txt2 += '		</div>';

        txt = pop.body(txt2,dimchk);

        var div = $("<div>");
        div.html(txt);
        $("body").append(div);
        $("#alertPopup a.btn01").click(function(){
            if(ment.callback) ment.callback.apply();
            $("#alertPopup").remove();
            $(".dimm2").fadeOut(function(){
                $(".dimm2").remove();
            });
            return false;
        });
        $("#alertPopup .layerClose").click(function(){
            $(".dimm2").fadeOut(function(){
                $(".dimm2").remove();
            });
            $(".dimm2").fadeOut(function(){$(".dimm2").remove()});
            return false;
        });
    },
    err : function(ment){
        var dimchk = true;
        if(ment.dimm == false){
            dimchk = false;
        }
        var txt2 = '	<div class="err">';
        txt2 +=	ment.ment;
        txt2 += '	</div>';
        txt2 += '		<div class="btnbox">';
        txt2 //+= '			<!-- <a href="#" class="btn02 layerCancel"><span class="">취소</span></a> -->';
        txt2 += '			<a href="#" class="btn03 confirm" class=""><span class="">확인</span></a>';
        txt2 += '		</div>';
        txt = pop.body(txt2,dimchk);

        var div = $("<div>");
        div.html(txt);
        $("body").append(div);
        $("#alertPopup a.btn03").click(function(){
            if(ment.callback) ment.callback.apply();
            $(".dimm2").fadeOut(function(){
                $(".dimm2").remove();
            });
            $(this).parents(".layerWrap2").remove();
            return false;
        });
        $("#alertPopup .layerClose").click(function(){
            $(".dimm2").fadeOut(function(){
                $(".dimm2").remove();
            });
            $(this).parents(".layerWrap2").remove();
            return false;
        });
    },
    blue : function(ment){
        var dimchk = true;
        if(ment.dimm == false){
            dimchk = false;
        }
        var txt2 = '	<div class="blue">';
        txt2 +=	ment.ment;
        txt2 += '	</div>';
        txt2 += '		<div class="btnbox">';
        txt2 //+= '			<!-- <a href="#" class="btn02 layerCancel"><span class="">취소</span></a> -->';
        txt2 += '			<a href="#" class="btn04 confirm" class=""><span class="">OK</span></a>';
        txt2 += '		</div>';
        txt = pop.body(txt2,dimchk);

        var div = $("<div>");
        div.html(txt);
        $("body").append(div);
        $("#alertPopup a.btn04").click(function(){
            if(ment.callback) ment.callback.apply();
            $(".dimm2").fadeOut(function(){
                $(".dimm2").remove();
            });
            $(this).parents(".layerWrap2").remove();
            return false;
        });
        $("#alertPopup .layerClose").click(function(){
            $(".dimm2").fadeOut(function(){
                $(".dimm2").remove();
            });
            $(this).parents(".layerWrap2").remove();
            return false;
        });
    },
    green : function(ment){
        var dimchk = true;
        if(ment.dimm == false){
            dimchk = false;
        }
        var txt2 = '	<div class="green">';
        txt2 +=	ment.ment;
        txt2 += '	</div>';
        txt2 += '		<div class="btnbox">';
        txt2 //+= '			<!-- <a href="#" class="btn02 layerCancel"><span class="">취소</span></a> -->';
        txt2 += '			<a href="#" class="btn05 confirm" class=""><span class="">OK</span></a>';
        txt2 += '		</div>';
        txt = pop.body(txt2,dimchk);

        var div = $("<div>");
        div.html(txt);
        $("body").append(div);
        $("#alertPopup a.btn05").click(function(){
            if(ment.callback) ment.callback.apply();
            $(".dimm2").fadeOut(function(){
                $(".dimm2").remove();
            });
            $(this).parents(".layerWrap2").remove();
            return false;
        });
        $("#alertPopup .layerClose").click(function(){
            $(".dimm2").fadeOut(function(){
                $(".dimm2").remove();
            });
            $(this).parents(".layerWrap2").remove();
            return false;
        });
    },
    red : function(ment){
        var dimchk = true;
        if(ment.dimm == false){
            dimchk = false;
        }
        var txt2 = '	<div class="red">';
        txt2 +=	ment.ment;
        txt2 += '	</div>';
        txt2 += '		<div class="btnbox">';
        txt2 //+= '			<!-- <a href="#" class="btn02 layerCancel"><span class="">취소</span></a> -->';
        txt2 += '			<a href="#" class="btn06 confirm" class=""><span class="">OK</span></a>';
        txt2 += '		</div>';
        txt = pop.body(txt2,dimchk);

        var div = $("<div>");
        div.html(txt);
        $("body").append(div);
        $("#alertPopup a.btn06").click(function(){
            if(ment.callback) ment.callback.apply();
            $(".dimm2").fadeOut(function(){
                $(".dimm2").remove();
            });
            $(this).parents(".layerWrap2").remove();
            return false;
        });
        $("#alertPopup .layerClose").click(function(){
            $(".dimm2").fadeOut(function(){
                $(".dimm2").remove();
            });
            $(this).parents(".layerWrap2").remove();
            return false;
        });
    }
}

function modalTab() {
    $('.profile').hide();
    $('.profile:first').show();
}

function changeTab(index) {
    if (index == 1) {
        $('.profile').hide();
        $('.profile:first').show();
    } else {
        $('.profile').hide();
        $('.profile:last').show();
    }
}

function changePassword() {
    let common = $.Common || {};
    let pw = $('#userPassword').val();
    if(pw.length > 5 && pw == $('#userPasswordChk').val()) {
        let data = {};
        data.user = $('#userIdx').val();
        //data.passwd = $('#userPassword').val();
        data.name = $('#userName').val();
        data.name_last = $('#userNameLast').val();
        data.tel = $('#userTel').val();
        data.access = $('#userAccess').val();


        common.ajax('/dashboard/user/modify', data, 'POST', (res) => {
            //alert('정상 처리가 완료되었습니다.');
            common.pop_green();
        }, (req) => {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다.');
        });
    }else {
        common.err('패스워드를 확인 해 주세요');
    }

}

function changeUser() {
    let common = $.Common || {};
    let data = {};
    data.user = $('#userIdx').val();
    data.name = $('#userName').val();
    data.name_last = $('#userNameLast').val();
    data.tel = $('#userTel').val();
    data.access = $('#userAccess').val();
    data.email = $('#userEmail').val();
    data.profile = $('#profile').val();
    if(data.name == '') {
        common.err('이름을 입력하세요.');
    }else if (data.name_last == '') {
        common.err('성을 입력하세요.');
    }else if(data.tel == '') {
        common.err('연락처를 입력하세요.');
    }


    common.ajax('/dashboard/user/modify', data, 'POST', (res) => {
        //alert('정상 처리가 완료되었습니다.');
        common.pop_green();
    }, (req) => {
        //alert('잘못된 정보가 있습니다.');
        common.err('잘못된 정보가 있습니다.');
    });
}