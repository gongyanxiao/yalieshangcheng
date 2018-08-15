//检查结果
var _CHECK_RESULT = {
    // 邮箱检查结果是否可以注册
    email: false,
    // 手机检查结果是否可以注册
    mobile_phone: false
};

function chkstr(str) {
    for (var i = 0; i < str.length; i++) {
        if (str.charCodeAt(i) < 127 && !str.substr(i, 1).match(/^\w+$/ig)) {
            return false;
        }
    }
    return true;
}

/**
 * 检查密码
 * @param password
 * @returns {Boolean}
 */
function check_password(password) {
    conform_password = document.getElementById('conform_password').value;
    if (password.indexOf(" ") != -1) {
        document.getElementById('password_notice').innerHTML = "登录密码不能包含空格";
        document.getElementById('password_notice').style.color = "#E31939";
        $("#pwd_notice").show();
        $("#pwd_intensity").hide();
        return false;
    } else if (password.length < 6) {
        document.getElementById('password_notice').innerHTML = password_shorter;
        document.getElementById('password_notice').style.color = "#E31939";
        $("#pwd_notice").show();
        $("#pwd_intensity").hide();
        return false;
    } else if (conform_password.length > 0) {
        if (password != conform_password) {
            document.getElementById('password_notice').innerHTML = confirm_password_invalid;
            document.getElementById('password_notice').style.color = "#E31939";
            $("#pwd_notice").show();
            $("#pwd_intensity").hide();
            return false;
        } else {
            document.getElementById('password_notice').innerHTML = msg_can_rg;
            document.getElementById('password_notice').style.color = "#093";
            document.getElementById('conform_password_notice').innerHTML = msg_can_rg;
            document.getElementById('conform_password_notice').style.color = "#093";
            $("#pwd_notice").hide();
            $("#pwd_intensity").show();
        }
    } else {
        document.getElementById('password_notice').innerHTML = msg_can_rg;
        document.getElementById('password_notice').style.color = "#093";
        $("#pwd_notice").hide();
        $("#pwd_intensity").show();
    }
    return true;
}



/**
 * 检查确认密码
 * @param conform_password
 * @returns {Boolean}
 */
function check_conform_password(conform_password) {
    var password = document.getElementById('password').value;

    if (conform_password.indexOf(" ") != -1) {
        document.getElementById('conform_password_notice').innerHTML = "登录密码不能包含空格";
        document.getElementById('conform_password_notice').style.color = "#E31939";
        $("#conform_password_notice").show();
        return false;
    } else if (conform_password.length < 6) {
        document.getElementById('conform_password_notice').innerHTML = password_shorter;
        document.getElementById('conform_password_notice').style.color = "#E31939";
        return false;
    }
    if (conform_password != password) {
        document.getElementById('conform_password_notice').innerHTML = confirm_password_invalid;
        document.getElementById('conform_password_notice').style.color = "#E31939";
        return false;
    } else {
        document.getElementById('conform_password_notice').innerHTML = msg_can_rg;
        document.getElementById('conform_password_notice').style.color = "#093";
        return false;
    }
    return true;
}

/**
 * 检查确认密码
 * @param conform_password
 * @returns {Boolean}
 */
function check_conform_pay_password(conform_password) {
    var password = document.getElementById('pay_password').value;

    if (conform_password.indexOf(" ") != -1) {
        document.getElementById('conform_pay_password_notice').innerHTML = "二级密码不能包含空格";
        document.getElementById('conform_pay_password_notice').style.color = "#E31939";
        $("#conform_pay_password_notice").show();
        return false;
    } else if (conform_password.length < 6) {
        document.getElementById('conform_pay_password_notice').innerHTML = "二级密码长度不能少于6位";
        document.getElementById('conform_pay_password_notice').style.color = "#E31939";
        return false;
    }
    if (conform_password != password) {
        document.getElementById('conform_pay_password_notice').innerHTML = "二级确认密码与二级密码不一致";
        document.getElementById('conform_pay_password_notice').style.color = "#E31939";
        return false;
    } else {
        document.getElementById('conform_pay_password_notice').innerHTML = "可以使用当前二级密码";
        document.getElementById('conform_pay_password_notice').style.color = "#093";
        return false;
    }
    return true;
}
 

 

function checkMobile(sMobile) {
    if (!(/^1[3|4|5|7|8][0-9]\d{4,8}$/.test(sMobile))) {
        return false;
    } else
    {
        return true;
    }
}

function checkMobilePhone(mobile, callback) {
    var submit_disabled = false;

    var mobileObj = null;

    if (typeof (mobile) == 'object') {
        mobileObj = $(mobile);
        mobile = mobileObj.val();
    }

    if (mobile == '') {
        document.getElementById('mobile_phone_notice').innerHTML = msg_mobile_phone_blank;
        document.getElementById('mobile_phone_notice').style.color = '#E31939';
        submit_disabled = true;

        if (mobileObj != null) {
            mobileObj.focus();
        }

    } else if (!Utils.isMobile(mobile)) {
        document.getElementById('mobile_phone_notice').innerHTML = msg_mobile_phone_format;
        document.getElementById('mobile_phone_notice').style.color = '#E31939';
        submit_disabled = true;

        if (mobileObj != null) {
            mobileObj.focus();
        }
    } else if (!checkMobile(mobile)) {
        document.getElementById('mobile_phone_notice').innerHTML = msg_mobile_phone_format;
        document.getElementById('mobile_phone_notice').style.color = '#E31939';
        submit_disabled = true;

        if (mobileObj != null) {
            mobileObj.focus();
        }
    }

    if (submit_disabled) {
        document.forms['formUser'].elements['Submit'].disabled = 'disabled';
        return false;
    }

    if (mobileObj == null) {
        checkMobilePhoneExist(mobile, callback);
    } else {
        checkMobilePhoneExist(mobileObj, callback);
    }
}
function checkTuiJianMobilePhone(mobile, callback) {
    var submit_disabled = false;

    var mobileObj = null;

    if (typeof (mobile) == 'object') {
        mobileObj = $(mobile);
        mobile = mobileObj.val();
    }

    if (mobile == '') {
        document.getElementById('tui_jian_mobile_phone_notice').innerHTML = msg_mobile_phone_blank;
        document.getElementById('tui_jian_mobile_phone_notice').style.color = '#E31939';
        submit_disabled = true;

        if (mobileObj != null) {
            mobileObj.focus();
        }

    } else if (!Utils.isMobile(mobile)) {
        document.getElementById('tui_jian_mobile_phone_notice').innerHTML = msg_mobile_phone_format;
        document.getElementById('tui_jian_mobile_phone_notice').style.color = '#E31939';
        submit_disabled = true;

        if (mobileObj != null) {
            mobileObj.focus();
        }
    } else if (!checkMobile(mobile)) {
        document.getElementById('tui_jian_mobile_phone_notice').innerHTML = msg_mobile_phone_format;
        document.getElementById('tui_jian_mobile_phone_notice').style.color = '#E31939';
        submit_disabled = true;

        if (mobileObj != null) {
            mobileObj.focus();
        }
    }

    if (submit_disabled) {
        document.forms['formUser'].elements['Submit'].disabled = 'disabled';
        return false;
    }

    if (mobileObj == null) {
    	checkTuiJianMobilePhoneExist(mobile, callback);
    } else {
    	checkTuiJianMobilePhoneExist(mobileObj, callback);
    }
}



var cur_tui_jian_mobile_phone = null;
function checkTuiJianMobilePhoneExist(mobile, callback) {
    var mobileObj = null;

    if (typeof (mobile) == 'object') {
        mobileObj = $(mobile);
        mobile = mobileObj.val();
    }
 
    if (mobile == cur_mobile_phone && !$.isFunction(callback)) {
        return;
    }

    $.post('register.php?act=check_mobile_exist', {
        mobile: mobile
    }, function (result) {
        if (result == 'false') {
            document.getElementById('tui_jian_mobile_phone_notice').innerHTML = "推荐人信息不存在";
            document.getElementById('tui_jian_mobile_phone_notice').style.color = '#E31939';
            document.forms['formUser'].elements['Submit'].disabled = '';
            if ($.isFunction(callback)) {
                callback(true);
            }
        } else {
            document.getElementById('tui_jian_mobile_phone_notice').innerHTML = "推荐人信息存在,可以注册";
            document.getElementById('tui_jian_mobile_phone_notice').style.color = '#093';
            document.forms['formUser'].elements['Submit'].disabled = 'disabled';

            if (mobileObj != null) {
                mobileObj.focus();
            }

            if ($.isFunction(callback)) {
                callback(false);
            }
        }

        cur_tui_jian_mobile_phone = mobile;

    }, 'text');
}



var cur_mobile_phone = null;
function checkMobilePhoneExist(mobile, callback) {
    var mobileObj = null;

    if (typeof (mobile) == 'object') {
        mobileObj = $(mobile);
        mobile = mobileObj.val();
    }

    if (mobile == cur_mobile_phone && !$.isFunction(callback)) {
        return;
    }

    $.post('register.php?act=check_mobile_exist', {
        mobile: mobile
    }, function (result) {
        if (result == 'false') {
            document.getElementById('mobile_phone_notice').innerHTML = msg_can_rg;
            document.getElementById('mobile_phone_notice').style.color = '#093';
            document.forms['formUser'].elements['Submit'].disabled = '';

            if ($.isFunction(callback)) {
                callback(true);
            }
        } else {
            document.getElementById('mobile_phone_notice').innerHTML = msg_mobile_phone_registered;
            document.getElementById('mobile_phone_notice').style.color = '#E31939';
            document.forms['formUser'].elements['Submit'].disabled = 'disabled';

            if (mobileObj != null) {
                mobileObj.focus();
            }

            if ($.isFunction(callback)) {
                callback(false);
            }
        }

        cur_mobile_phone = mobile;

    }, 'text');
}

/**
 * 用户注册
 * 
 * @param register_type
 *            注册类型：email、mobile
 */
function register(register_type) {
    if (register_type == "email") {
        return reg_by_email();
    } else {
        return reg_by_mobile();
    }
}

/**
 * 通过邮箱注册
 * 
 * @returns {Boolean}
 */
function reg_by_email() {
    var frm = document.forms['formUser'];
    // 邮箱注册时不支持用户名注册
    // var username = Utils.trim(frm.elements['username'].value);
    var email = frm.elements['email'].value;
    var password = Utils.trim(frm.elements['password'].value);
    var confirm_password = Utils.trim(frm.elements['confirm_password'].value);
    var checked_agreement = frm.elements['agreement'].checked;
    var msn = frm.elements['extend_field1'] ? Utils.trim(frm.elements['extend_field1'].value) : '';
    var qq = frm.elements['extend_field2'] ? Utils.trim(frm.elements['extend_field2'].value) : '';
    var home_phone = frm.elements['extend_field4'] ? Utils.trim(frm.elements['extend_field4'].value) : '';
    var office_phone = frm.elements['extend_field3'] ? Utils.trim(frm.elements['extend_field3'].value) : '';
    // 邮箱注册不能绑定手机，许多注册成功后再绑定
    // var mobile_phone = frm.elements['extend_field5'] ?
    // Utils.trim(frm.elements['extend_field5'].value) : '';
    var passwd_answer = frm.elements['passwd_answer'] ? Utils.trim(frm.elements['passwd_answer'].value) : '';
    var sel_question = frm.elements['sel_question'] ? Utils.trim(frm.elements['sel_question'].value) : '';
    // 邮箱验证码
    var email_code = frm.elements['email_code'] ? Utils.trim(frm.elements['email_code'].value) : '';
    // 验证码
    var captcha = frm.elements['captcha'] ? Utils.trim(frm.elements['captcha'].value) : '';

    var msg = "";
    // 检查输入
    var msg = '';

    if (email.length == 0) {
        msg += email_empty + '\n';
    } else {
        if (!(Utils.isEmail(email))) {
            msg += email_invalid + '\n';
        }
    }
    if (password.length == 0) {
        msg += password_empty + '\n';
    } else if (password.length < 6) {
        msg += password_shorter + '\n';
    }
    if (/ /.test(password) == true) {
        msg += passwd_balnk + '\n';
    }
    if (confirm_password != password) {
        msg += confirm_password_invalid + '\n';
    }
    if (checked_agreement != true) {
        msg += agreement + '\n';
    }

    if (msn.length > 0 && (!Utils.isEmail(msn))) {
        msg += msn_invalid + '\n';
    }

    if (qq.length > 0 && (!Utils.isNumber(qq))) {
        msg += qq_invalid + '\n';
    }

    if (office_phone.length > 0) {
        var reg = /^[\d|\-|\s]+$/;
        if (!reg.test(office_phone)) {
            msg += office_phone_invalid + '\n';
        }
    }
    if (home_phone.length > 0) {
        var reg = /^[\d|\-|\s]+$/;

        if (!reg.test(home_phone)) {
            msg += msg_email_code_blank + '\n';
        }
    }

    if ($("#email_code").size() > 0 && email_code.length == 0) {
        msg += msg_email_code_blank + '\n';
    }

    if ($("#captcha").size() > 0 && captcha.length == 0) {
        msg += msg_captcha_blank + '\n';
    }

    // if (mobile_phone.length > 0) {
    // var reg = /^[\d|\-|\s]+$/;
    // if (!reg.test(mobile_phone)) {
    // msg += mobile_phone_invalid + '\n';
    // }
    // }
    if (passwd_answer.length > 0 && sel_question == 0 || document.getElementById('passwd_quesetion') && passwd_answer.length == 0) {
        msg += no_select_question + '\n';
    }

    for (var i = 4; i < frm.elements.length - 4; i++) // 从第五项开始循环检查是否为必填项
    {
        var needinput = document.getElementById(frm.elements[i].name + 'i') ? document.getElementById(frm.elements[i].name + 'i') : '';

        if (needinput != '' && frm.elements[i].value.length == 0) {
            msg += '- ' + frm.elements[i].placeholder + "不能为空！" + '\n';
            //msg += '- ' + needinput.innerHTML + msg_blank + '\n';
        }
    }

    if (msg.length > 0) {
        alert(msg);
        return false;
    } else {
        return true;
    }
}
var k_btn_text = 10;
function setBtnText() {
    if (k_btn_text * 1 === 0 || k_btn_text * 1 <= 0) {
        $(".layui-layer-btn0").text("我知道了");
        $(".layui-layer-btn0").removeAttr("disabled");
        $("input[name='xieyi']").val(1);
        return;
    } else
    {
        $(".layui-layer-btn0").text("我知道了（" + (k_btn_text--) + "秒）");
        setTimeout("setBtnText()", 1000);
    }
}
function reg_by_mobile() {
	 
    var frm = document.forms['formUser'];


    var mobile_phone = frm.elements['mobile_phone'].value;
    var password = Utils.trim(frm.elements['password'].value);
    var confirm_password = Utils.trim(frm.elements['confirm_password'].value);
    var checked_agreement = frm.elements['agreement'].checked;

    // 手机验证码
    var mobile_code = frm.elements['mobile_code'] ? Utils.trim(frm.elements['mobile_code'].value) : '';
 
  
    // 验证码
    var captcha = frm.elements['captcha'] ? Utils.trim(frm.elements['captcha'].value) : '';
   
      var country = frm.elements['country'] ? Utils.trim(frm.elements['country'].value) : 1;
    var province = frm.elements['province'] ? Utils.trim(frm.elements['province'].value) :0;
    var city = frm.elements['city'] ? Utils.trim(frm.elements['city'].value) : 0;
    var district = frm.elements['district'] ? Utils.trim(frm.elements['district'].value) :0;

    var msg = "";
    // 检查输入
    var msg = '';

    if (mobile_phone.length == 0) {
        msg += msg_mobile_phone_blank + '\n';
    } else {
        if (!(Utils.isMobile(mobile_phone))) {
            msg += mobile_phone_invalid + '\n';
        }
    }
  

    if (password.length == 0) {
        msg += password_empty + '\n';
    } else if (password.length < 6) {
        msg += password_shorter + '\n';
    }
    if (/ /.test(password) == true) {
        msg += passwd_balnk + '\n';
    }
    if (confirm_password != password) {
        msg += confirm_password_invalid + '\n';
    }
    
  
    if (checked_agreement != true) {
        msg += agreement + '\n';
    }

  
  
    if ($("#mobile_code").size() > 0 && mobile_code.length == 0) {
        msg += msg_mobile_phone_code_blank + '\n';
    }

    if ($("#captcha").size() > 0 && captcha.length == 0) {
        msg += msg_captcha_blank + '\n';
    }
   
 
    for (var i = 4; i < frm.elements.length - 4; i++) // 从第五项开始循环检查是否为必填项
    {
        var needinput = document.getElementById(frm.elements[i].name + 'i') ? document.getElementById(frm.elements[i].name + 'i') : '';

        if (needinput != '' && frm.elements[i].value.length == 0) {
            msg += '- ' + frm.elements[i].placeholder + "不能为空！" + '\n';
            //msg += '- ' + needinput.innerHTML + msg_blank + '\n';
        }
    } 
  
    var card = frm.elements['card'] ? Utils.trim(frm.elements['card'].value) : '';
 
    if(province*1===0)
    {
        msg+="请选择省份"+ '\n';
    }
    if(city*1===0)
    {
        msg+="请选择城市"+ '\n';
    }
    if(district*1===0)
    {
        msg+="请选择地区"+ '\n';
    }
    
  
    if (msg.length > 0) {
        alert(msg);
        return false;
    } else {
        var isxieyi = $("input[name='xieyi']").val();
      
        if (isxieyi * 1 === 0)
        {
            $.ajax({
                url: "register.php?act=xieyi",
                type: "POST",
                data: {captcha: captcha, mobile_phone: mobile_phone, mobile_code: mobile_code,province:province,city:city,district:district},
                dataType:"json",
                success: function (result) {
                    if (result.code * 1 === 0)
                    {
                        //示范一个公告层
                        layer.open({
                            type: 1
                            , title: "网站注册协议" //不显示标题栏
                            , closeBtn: false
                            , area: ['80%', '70%']
                            , shade: 0.8
                            , id: 'LAY_layuipro' //设定一个id，防止重复弹出
                            , resize: false
                            , btn: ['我知道了（10秒）']
                            , btnAlign: 'r'
                            , moveType: 1 //拖拽模式，0或者1
                            , content: result.message
                            , yes: function (layero) {
                                isxieyi = $("input[name='xieyi']").val();
                                if (isxieyi * 1 === 1)
                                {
                                    layer.closeAll();
                                    document.forms['formUser'].submit();
                                } else
                                {
                                    return false;
                                }
                            }
                        });
                        $(".layui-layer-btn0").attr("disabled", "disabled");
                        setBtnText();
                        return false;
                    } else
                    {
                        alert(result.message);
                        return false;
                    }
                }
            });
            return false;
        }
        return true;
    }
}

/**
 * 发送邮箱验证码
 * 
 * @param emailObj
 *            邮箱对象
 * @param emailCodeObj
 *            邮箱验证码对象
 * @param sendButton
 *            点击发送邮箱验证码的按钮对象，用于显示倒计时信息
 */
function sendEmailCode(emailObj, emailCodeObj, sendButton) {

    checkEmail(emailObj, function (result) {
        if (result) {
            // 发送邮件
            // &XDEBUG_SESSION_START=ECLIPSE_DBGP
            var url = 'register.php?act=send_email_code';
            $.post(url, {
                captcha: $("#captcha").size() > 0 ? $("#captcha").val() : "",
                email: emailObj.val()
            }, function (result) {
                if (result == 'ok') {
                    // 倒计时
                    countdown(sendButton);
                    $("#captcha_notice").html("");
                    $("#captcha_notice").css({color: '#093'});
                } else {
                    alert(result);
                }
                if ($("#captcha").size() > 0) {
                    $("#captcha_img").click();
                }
            }, 'text');
        }
    });
}

/**
 * 发送手机验证码
 * 
 * @param mobileObj
 *            手机号对象
 * @param mobileCodeObj
 *            短信验证码对象
 * @param sendButton
 *            点击发送短信证码的按钮对象，用于显示倒计时信息
 */
function sendMobileCode(mobileObj, mobileCodeObj, sendButton) {
    checkMobilePhone(mobileObj, function (result) {
        if (result) {

            // 发送邮件
            var url = 'register.php?act=send_mobile_code';
            $.post(url, {
                XDEBUG_SESSION_START: 'ECLIPSE_DBGP',
                captcha: $("#captcha").size() > 0 ? $("#captcha").val() : "",
                mobile_phone: mobileObj.val()
            }, function (result) {
                if (result == 'ok') {
                    // 倒计时
                    countdown(sendButton);
                    $("#captcha_notice").html("");
                    $("#captcha_notice").css({color: '#093'});
                } else {
                    alert(result);
                }
                if ($("#captcha").size() > 0) {
                    $("#captcha_img").click();
                }
            }, 'text');
        }
    });
}

/*******************************************************************************
 * 检测密码强度
 * 
 * @param string
 *            pwd 密码
 */
function checkIntensity(pwd) {

    $("#pwd_notice").hide();
    $("#pwd_intensity").show();

    var Mcolor = "#FFF", Lcolor = "#FFF", Hcolor = "#FFF";
    var m = 0;

    var Modes = 0;
    for (var i = 0; i < pwd.length; i++) {
        var charType = 0;
        var t = pwd.charCodeAt(i);
        if (t >= 48 && t <= 57) {
            charType = 1;
        } else if (t >= 65 && t <= 90) {
            charType = 2;
        } else if (t >= 97 && t <= 122)
            charType = 4;
        else
            charType = 4;
        Modes |= charType;
    }

    for (i = 0; i < 4; i++) {
        if (Modes & 1)
            m++;
        Modes >>>= 1;
    }

    if (pwd.length <= 4) {
        m = 1;
    }

    switch (m) {
        case 1:
            Lcolor = "2px solid red";
            Mcolor = Hcolor = "2px solid #DADADA";
            break;
        case 2:
            Mcolor = "2px solid #f90";
            Lcolor = Hcolor = "2px solid #DADADA";
            break;
        case 3:
            Hcolor = "2px solid #3c0";
            Lcolor = Mcolor = "2px solid #DADADA";
            break;
        case 4:
            Hcolor = "2px solid #3c0";
            Lcolor = Mcolor = "2px solid #DADADA";
            break;
        default:
            Hcolor = Mcolor = Lcolor = "";
            break;
    }
    if (document.getElementById("pwd_lower")) {
        document.getElementById("pwd_lower").style.borderBottom = Lcolor;
        document.getElementById("pwd_middle").style.borderBottom = Mcolor;
        document.getElementById("pwd_high").style.borderBottom = Hcolor;
    }

}

var wait = 60;
function countdown(obj, msg) {
    obj = $(obj);

    if (wait == 0) {
        obj.removeAttr("disabled");
        obj.val(msg);
        wait = 60;
    } else {
        if (msg == undefined || msg == null) {
            msg = obj.val();
        }
        obj.attr("disabled", "disabled");
        obj.val(wait + "秒后重新获取");
        wait--;
        setTimeout(function () {
            countdown(obj, msg)
        }, 1000)
    }
}


/*
 根据〖中华人民共和国国家标准 GB 11643-1999〗中有关公民身份号码的规定，公民身份号码是特征组合码，由十七位数字本体码和一位数字校验码组成。排列顺序从左至右依次为：六位数字地址码，八位数字出生日期码，三位数字顺序码和一位数字校验码。
 地址码表示编码对象常住户口所在县(市、旗、区)的行政区划代码。
 出生日期码表示编码对象出生的年、月、日，其中年份用四位数字表示，年、月、日之间不用分隔符。
 顺序码表示同一地址码所标识的区域范围内，对同年、月、日出生的人员编定的顺序号。顺序码的奇数分给男性，偶数分给女性。
 校验码是根据前面十七位数字码，按照ISO 7064:1983.MOD 11-2校验码计算出来的检验码。
 
 出生日期计算方法。
 15位的身份证编码首先把出生年扩展为4位，简单的就是增加一个19或18,这样就包含了所有1800-1999年出生的人;
 2000年后出生的肯定都是18位的了没有这个烦恼，至于1800年前出生的,那啥那时应该还没身份证号这个东东，⊙﹏⊙b汗...
 下面是正则表达式:
 出生日期1800-2099  (18|19|20)?\d{2}(0[1-9]|1[12])(0[1-9]|[12]\d|3[01])
 身份证正则表达式 /^\d{6}(18|19|20)?\d{2}(0[1-9]|1[12])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$/i            
 15位校验规则 6位地址编码+6位出生日期+3位顺序号
 18位校验规则 6位地址编码+8位出生日期+3位顺序号+1位校验位
 
 校验位规则     公式:∑(ai×Wi)(mod 11)……………………………………(1)
 公式(1)中： 
 i----表示号码字符从由至左包括校验码在内的位置序号； 
 ai----表示第i位置上的号码字符值； 
 Wi----示第i位置上的加权因子，其数值依据公式Wi=2^(n-1）(mod 11)计算得出。
 i 18 17 16 15 14 13 12 11 10 9 8 7 6 5 4 3 2 1
 Wi 7 9 10 5 8 4 2 1 6 3 7 9 10 5 8 4 2 1
 
 */
//身份证号合法性验证 
//支持15位和18位身份证号
//支持地址编码、出生日期、校验位验证
function IdentityCodeValid(code) {
    var city = {11: "北京", 12: "天津", 13: "河北", 14: "山西", 15: "内蒙古", 21: "辽宁", 22: "吉林", 23: "黑龙江 ", 31: "上海", 32: "江苏", 33: "浙江", 34: "安徽", 35: "福建", 36: "江西", 37: "山东", 41: "河南", 42: "湖北 ", 43: "湖南", 44: "广东", 45: "广西", 46: "海南", 50: "重庆", 51: "四川", 52: "贵州", 53: "云南", 54: "西藏 ", 61: "陕西", 62: "甘肃", 63: "青海", 64: "宁夏", 65: "新疆", 71: "台湾", 81: "香港", 82: "澳门", 91: "国外 "};
    var tip = "";
    var pass = true;

    if (!code || !/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/i.test(code)) {
        //    /^\d{6}(18|19|20)?\d{2}(0[1-9]|1[12])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$/i
        tip = "身份证号格式错误";
        pass = false;
    } else if (!city[code.substr(0, 2)]) {
        tip = "地址编码错误";
        pass = false;
    } else {
        //18位身份证需要验证最后一位校验位
        if (code.length == 18) {
            code = code.split('');
            //∑(ai×Wi)(mod 11)
            //加权因子
            var factor = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
            //校验位
            var parity = [1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2];
            var sum = 0;
            var ai = 0;
            var wi = 0;
            for (var i = 0; i < 17; i++)
            {
                ai = code[i];
                wi = factor[i];
                sum += ai * wi;
            }
            var last = parity[sum % 11];
            if (parity[sum % 11] != code[17]) {
                tip = "校验位错误";
                pass = false;
            }
        }
    }
    //if(!pass) alert(tip);
    return pass;
}
//var c = '130981199312253466';
//var res= IdentityCodeValid(c);