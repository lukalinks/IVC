<?php
include("config.php");
include("functions.php");
$isPartnerLogin = (isset($_GET['role']) && $_GET['role'] === 'partner');
include('header.php');
?>
<style>
td {
        background-image: -webkit-linear-gradient(top,#cc0000 0,#000 100%);
        background-image: -o-linear-gradient(top,#cc0000 0,#000 100%);
        background-image: -webkit-gradient(linear,left top,left bottom,from(#cc0000),to(#000));
        background-image: linear-gradient(to bottom,#cc0000 0,#000 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffcc0000', endColorstr='#ff000', GradientType=0);
        filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
        background-repeat: repeat-x;
        color:#fff;
        font-weight:bold;
        height:72px;
        font-size:30px;
        cursor:pointer;
        
}
</style>  
    
    <div class="row" style="max-width:1000px; margin: 0 auto; margin-top:100px;">
            <div class="col-sm-12">
                <div class="row" style="max-width:400px; width:100%; margin:0 auto;">
                    <!---->
                    <div class="col-md-12" style="color:#cc0000; background:none; font-size:18px; padding:20px;">
                        <div id="divlogin" class="row text-center" style="margin-top:0px; color:#cc0000; padding:15px;">
                            <div class="col-sm-12" style="padding:15px;">
                                <div class="card-deck" style="max-width: 900px; margin: 0 auto;">
                                    <div class="card" style="max-width:400px; margin:0 auto; background:none; border-color:#cc0000; width:100%; margin-top:15px;">
                                        <div class="card-header" style="border-bottom: 1px solid rgba(204, 0, 0, .5);"><?= $isPartnerLogin ? 'PARTNER LOGIN' : 'LOGIN' ?></div>
                                        <div class="card-body text-center">
                                            
                                            <p id="err" class="alert alert-danger" style="display:none;"></p>
                                            <?php if ($isPartnerLogin): ?>
                                            <p style="color:#650B14; font-size:14px; margin-bottom:0;">Hotels, travel agencies, and other industry partners — use your SafeZone account number, password, and PIN.</p>
                                            <?php endif; ?>
                                            
                                            <form id="frm" method="post" action="login.php" autocomplete="off" onsubmit="return startLogin();">
                                                <input class="form-control" value="" name="pernum" id="pernum" autocomplete="off" type="text" inputmode="numeric" placeholder="Account #" style="background:none; border-color:#cc0000; color:#cc0000; margin:0 auto; margin-top:32px; max-width:245px;">
                                                
                                                <input class="form-control" name="password" id="password" autocomplete="off" type="password" value="" placeholder="Password" style="background:none; border-color:#cc0000; margin:0 auto; margin-top:10px; color:#cc0000; max-width:245px;">
                                                <button type="submit" class="btn btn-primary" style="background-color:#cc0000; border-color:#cc0000; margin-top:30px; color:#fff; font-weight:bold; max-width:245px; width:100%;">LOG IN</button>
                                            </form>
                                            <p style="margin-top:18px; font-size:13px; line-height:1.6;">
                                                <a href="javascript:void(0);" onclick="showForgotPanel('pernum');" style="color:#650B14;">Forgot Account #?</a><br>
                                                <a href="javascript:void(0);" onclick="showForgotPanel('password');" style="color:#650B14;">Forgot Password?</a><br>
                                                <a href="javascript:void(0);" onclick="showForgotPanel('mp');" style="color:#650B14;">Forgot Master PIN?</a>
                                            </p>
                                        </div>
                                    </div>
                                
                                    
                                </div>  
                                
                            </div>
                        </div>

                        <div id="divforgotpernum" class="row text-center" style="margin-top:0px; color:#cc0000; padding:15px; display:none;">
                            <div class="col-sm-12" style="padding:15px;">
                                <div class="card" style="max-width:400px; margin:0 auto; background:none; border-color:#cc0000; width:100%;">
                                    <div class="card-header" style="border-bottom: 1px solid rgba(204, 0, 0, .5);">FORGOT ACCOUNT #</div>
                                    <div class="card-body text-center">
                                        <p id="forgotpernumerr" class="alert alert-danger" style="display:none;"></p>
                                        <p id="forgotpernumok" class="alert alert-success" style="display:none;"></p>
                                        <p style="color:#650B14; font-size:14px;">Enter the email and password registered with your SafeZone account.</p>
                                        <input class="form-control" id="forgotpernum_email" type="email" placeholder="Email" style="background:none; border-color:#cc0000; color:#cc0000; margin:0 auto; margin-top:16px; max-width:245px;">
                                        <input class="form-control" id="forgotpernum_password" type="password" placeholder="Password" style="background:none; border-color:#cc0000; color:#cc0000; margin:0 auto; margin-top:10px; max-width:245px;">
                                        <button type="button" class="btn btn-primary" onclick="submitForgotPernum();" style="background-color:#cc0000; border-color:#cc0000; margin-top:20px; color:#fff; font-weight:bold; max-width:245px; width:100%;">SUBMIT</button>
                                        <p style="margin-top:16px;"><a href="javascript:void(0);" onclick="showLoginPanel();" style="color:#650B14;">Back to login</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="divforgotpassword" class="row text-center" style="margin-top:0px; color:#cc0000; padding:15px; display:none;">
                            <div class="col-sm-12" style="padding:15px;">
                                <div class="card" style="max-width:400px; margin:0 auto; background:none; border-color:#cc0000; width:100%;">
                                    <div class="card-header" style="border-bottom: 1px solid rgba(204, 0, 0, .5);">FORGOT PASSWORD</div>
                                    <div class="card-body text-center">
                                        <p id="forgotpasserr" class="alert alert-danger" style="display:none;"></p>
                                        <p id="forgotpassok" class="alert alert-success" style="display:none;"></p>
                                        <div id="forgotpass_step1">
                                            <p style="color:#650B14; font-size:14px;">Enter your SafeZone account number to continue.</p>
                                            <input class="form-control" id="forgotpass_pernum" type="text" inputmode="numeric" placeholder="Account #" style="background:none; border-color:#cc0000; color:#cc0000; margin:0 auto; margin-top:16px; max-width:245px;">
                                            <button type="button" class="btn btn-primary" onclick="startForgotPassword();" style="background-color:#cc0000; border-color:#cc0000; margin-top:20px; color:#fff; font-weight:bold; max-width:245px; width:100%;">CONTINUE</button>
                                        </div>
                                        <div id="forgotpass_step2" style="display:none;">
                                            <p id="forgotpass_pintext" style="color:#650B14; font-size:16px; min-height:48px;"></p>
                                            <input class="form-control" id="forgotpass_pin" type="password" inputmode="numeric" placeholder="3 requested digits" style="background:none; border-color:#cc0000; color:#cc0000; margin:0 auto; margin-top:16px; max-width:245px;">
                                            <table width="245" border="1" cellspacing="0" cellpadding="0" style="margin:0 auto; margin-top:10px; border-color:#cc0000;">
                                                <tr>
                                                    <td style="width:33.33%;" onClick="forgotPassKeypad(this, 1)">1</td>
                                                    <td style="width:33.33%;" onClick="forgotPassKeypad(this, 2)">2</td>
                                                    <td style="width:33.33%;" onClick="forgotPassKeypad(this, 3)">3</td>
                                                </tr>
                                                <tr>
                                                    <td onClick="forgotPassKeypad(this, 4)">4</td>
                                                    <td onClick="forgotPassKeypad(this, 5)">5</td>
                                                    <td onClick="forgotPassKeypad(this, 6)">6</td>
                                                </tr>
                                                <tr>
                                                    <td onClick="forgotPassKeypad(this, 7)">7</td>
                                                    <td onClick="forgotPassKeypad(this, 8)">8</td>
                                                    <td onClick="forgotPassKeypad(this, 9)">9</td>
                                                </tr>
                                                <tr>
                                                    <td onClick="forgotPassKeypad(this, 0)">0</td>
                                                    <td onClick="forgotPassKeypad(this, 'b')" colspan="2"><img src="https://safe.zone/images/backspace.png" width="34" height="23" alt=""/></td>
                                                </tr>
                                            </table>
                                            <button type="button" class="btn btn-primary" onclick="submitForgotPassword();" style="background-color:#cc0000; border-color:#cc0000; margin-top:20px; color:#fff; font-weight:bold; max-width:245px; width:100%;">SUBMIT</button>
                                        </div>
                                        <p style="margin-top:16px;"><a href="javascript:void(0);" onclick="showLoginPanel();" style="color:#650B14;">Back to login</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="divforgotmp" class="row text-center" style="margin-top:0px; color:#cc0000; padding:15px; display:none;">
                            <div class="col-sm-12" style="padding:15px;">
                                <div class="card" style="max-width:400px; margin:0 auto; background:none; border-color:#cc0000; width:100%;">
                                    <div class="card-header" style="border-bottom: 1px solid rgba(204, 0, 0, .5);">FORGOT MASTER PIN</div>
                                    <div class="card-body text-center">
                                        <p id="forgotmperr" class="alert alert-danger" style="display:none;"></p>
                                        <p id="forgotmpok" class="alert alert-success" style="display:none;"></p>
                                        <p style="color:#650B14; font-size:14px;">Enter the email and password registered with your SafeZone account.</p>
                                        <input class="form-control" id="forgotmp_email" type="email" placeholder="Email" style="background:none; border-color:#cc0000; color:#cc0000; margin:0 auto; margin-top:16px; max-width:245px;">
                                        <input class="form-control" id="forgotmp_password" type="password" placeholder="Password" style="background:none; border-color:#cc0000; color:#cc0000; margin:0 auto; margin-top:10px; max-width:245px;">
                                        <button type="button" class="btn btn-primary" onclick="submitForgotMp();" style="background-color:#cc0000; border-color:#cc0000; margin-top:20px; color:#fff; font-weight:bold; max-width:245px; width:100%;">SUBMIT</button>
                                        <p style="margin-top:16px;"><a href="javascript:void(0);" onclick="showLoginPanel();" style="color:#650B14;">Back to login</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="divpin" class="row text-center" style="margin-top:0px; color:#cc0000; padding:15px; display:none;">
                            <div class="col-sm-12">
                                <div class="card-deck" style="max-width: 900px; margin: 0 auto;">
                                    <div class="card" style="max-width:400px; margin:0 auto; background:none; border-color:#cc0000; width:100%; margin-top:0px;">
                                        <div class="card-header" style="border-bottom: 1px solid rgba(204, 0, 0, .5);">ONE-TIME PIN</div>
                                        <div class="card-body text-center">
                                            <p id="pinerr" class="alert alert-danger" style="display:none;"></p>
                                            <p id="pintext" style="color:#650B14; font-size:16px; min-height:48px;"></p>
                                            <input class="form-control" value="" name="pin" id="pin" autocomplete="off" type="password" inputmode="numeric" placeholder="3 requested digits" style="background:none; border-color:#cc0000; color:#cc0000; margin:0 auto; margin-top:20px; max-width:245px;" onkeydown="if(event.key==='Enter'){ login(); return false; }">
                                            
                                            <table width="245" border="1" cellspacing="0" cellpadding="0" style="margin:0 auto; margin-top:10px; border-color:#cc0000;">
                                                <tr>
                                                    <td style="width:33.33%;" onClick="keypad(this, 1)">1</td>
                                                    <td style="width:33.33%;" onClick="keypad(this, 2)">2</td>
                                                    <td style="width:33.33%;" onClick="keypad(this, 3)">3</td>
                                                </tr>
                                                <tr>
                                                    <td onClick="keypad(this, 4)">4</td>
                                                    <td onClick="keypad(this, 5)">5</td>
                                                    <td onClick="keypad(this, 6)">6</td>
                                                </tr>
                                                <tr>
                                                    <td onClick="keypad(this, 7)">7</td>
                                                    <td onClick="keypad(this, 8)">8</td>
                                                    <td onClick="keypad(this, 9)">9</td>
                                                </tr>
                                                <tr>
                                                    <td onClick="keypad(this, 0)">0</td>
                                                    <td onClick="keypad(this, 'b')" colspan=2><img src="https://safe.zone/images/backspace.png" width="34" height="23"  alt=""/></td>
                                                    
                                                </tr>
                                            </table> 
                                            
                                            <a href="javascript:void(0);" onclick="login();" class="btn btn-primary" style="background-color:#cc0000; border-color:#cc0000; margin-top:20px; color:#FFF; font-weight:bold; max-width:245px; width:100%;">SUBMIT</a>

                                        </div>
                                    </div>
                                
                                    
                                </div>    
                            </div>
                        </div>
                    </div>  
                    <!---->
                </div>
                
            </div>

        </div>



<script>
var navbar = document.getElementById("navbar");
if (navbar) {
    navbar.classList.add("sticky");
}
var currentUid = '';
var currentPernum = '';
var currentPinKey = '';
var pinReady = false;
var forgotPassPernum = '';
var forgotPassPinKey = '';

function ivcEl(id) {
    return document.getElementById(id);
}

function ivcShow(id) {
    var node = ivcEl(id);
    if (node) {
        node.style.display = '';
    }
}

function ivcHide(id) {
    var node = ivcEl(id);
    if (node) {
        node.style.display = 'none';
    }
}

function ivcHideForgotPanels() {
    ivcHide('divforgotpernum');
    ivcHide('divforgotpassword');
    ivcHide('divforgotmp');
}

function ivcClearForgotAlerts(prefix) {
    var err = ivcEl(prefix + 'err');
    var ok = ivcEl(prefix + 'ok');
    if (err) {
        err.style.display = 'none';
        err.textContent = '';
    }
    if (ok) {
        ok.style.display = 'none';
        ok.textContent = '';
    }
}

function showLoginPanel() {
    ivcHide('divpin');
    ivcHideForgotPanels();
    ivcShow('divlogin');
    pinReady = false;
    currentUid = '';
    currentPernum = '';
    currentPinKey = '';
    forgotPassPernum = '';
    forgotPassPinKey = '';
}

function showForgotPanel(kind) {
    ivcHide('divlogin');
    ivcHide('divpin');
    ivcHideForgotPanels();
    pinReady = false;
    if (kind === 'pernum') {
        ivcClearForgotAlerts('forgotpernum');
        ivcShow('divforgotpernum');
    } else if (kind === 'password') {
        ivcClearForgotAlerts('forgotpass');
        forgotPassPernum = '';
        forgotPassPinKey = '';
        var step1 = ivcEl('forgotpass_step1');
        var step2 = ivcEl('forgotpass_step2');
        if (step1) {
            step1.style.display = '';
        }
        if (step2) {
            step2.style.display = 'none';
        }
        var pinInput = ivcEl('forgotpass_pin');
        if (pinInput) {
            pinInput.value = '';
        }
        ivcShow('divforgotpassword');
    } else if (kind === 'mp') {
        ivcClearForgotAlerts('forgotmp');
        ivcShow('divforgotmp');
    }
}

function ivcForgotFetch(body) {
    return fetch('safezone.forgot.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body)
    }).then(function (response) {
        return response.text().then(function (text) {
            var result;
            try {
                result = JSON.parse(text);
            } catch (e) {
                throw new Error('Service returned an invalid response. Please try again.');
            }
            return result;
        });
    });
}

function submitForgotPernum() {
    ivcClearForgotAlerts('forgotpernum');
    var email = (ivcEl('forgotpernum_email') ? ivcEl('forgotpernum_email').value : '').trim();
    var password = (ivcEl('forgotpernum_password') ? ivcEl('forgotpernum_password').value : '').trim();
    if (email === '' || password === '') {
        var err = ivcEl('forgotpernumerr');
        if (err) {
            err.textContent = 'Please enter your email and password.';
            err.style.display = 'block';
        }
        return;
    }
    ivcForgotFetch({ action: 'pernum', email: email, password: password })
        .then(function (result) {
            if (!result.success) {
                var fail = ivcEl('forgotpernumerr');
                if (fail) {
                    fail.textContent = result.message || 'Invalid email/password.';
                    fail.style.display = 'block';
                }
                return;
            }
            var ok = ivcEl('forgotpernumok');
            if (ok) {
                ok.textContent = result.message || 'Request submitted successfully.';
                ok.style.display = 'block';
            }
        })
        .catch(function (err) {
            var fail = ivcEl('forgotpernumerr');
            if (fail) {
                fail.textContent = (err && err.message) ? err.message : 'Request failed. Please try again.';
                fail.style.display = 'block';
            }
        });
}

function applyForgotPasswordPrompt(result) {
    forgotPassPinKey = String(result.pin_key || '');
    var pinText = ivcEl('forgotpass_pintext');
    if (pinText) {
        pinText.textContent = result.prompt || 'Enter the 3 requested digits of your Master PIN.';
    }
    var pinInput = ivcEl('forgotpass_pin');
    if (pinInput) {
        pinInput.value = '';
    }
}

function startForgotPassword() {
    ivcClearForgotAlerts('forgotpass');
    var pernum = (ivcEl('forgotpass_pernum') ? ivcEl('forgotpass_pernum').value : '').replace(/\D/g, '');
    if (pernum === '') {
        var err = ivcEl('forgotpasserr');
        if (err) {
            err.textContent = 'Please enter your account number.';
            err.style.display = 'block';
        }
        return;
    }
    forgotPassPernum = pernum;
    ivcForgotFetch({ action: 'password_init' })
        .then(function (result) {
            if (!result.success) {
                throw new Error(result.message || 'Unable to start password reset.');
            }
            applyForgotPasswordPrompt(result);
            ivcHide('forgotpass_step1');
            ivcShow('forgotpass_step2');
        })
        .catch(function (err) {
            var fail = ivcEl('forgotpasserr');
            if (fail) {
                fail.textContent = (err && err.message) ? err.message : 'Request failed. Please try again.';
                fail.style.display = 'block';
            }
        });
}

function submitForgotPassword() {
    ivcClearForgotAlerts('forgotpass');
    var pin = (ivcEl('forgotpass_pin') ? ivcEl('forgotpass_pin').value : '').replace(/\D/g, '');
    if (forgotPassPernum === '' || forgotPassPinKey.length !== 3) {
        var err = ivcEl('forgotpasserr');
        if (err) {
            err.textContent = 'Please enter your account number and wait for the PIN prompt.';
            err.style.display = 'block';
        }
        return;
    }
    if (pin === '') {
        var pinErr = ivcEl('forgotpasserr');
        if (pinErr) {
            pinErr.textContent = 'Enter the 3 digits shown in the PIN prompt.';
            pinErr.style.display = 'block';
        }
        return;
    }
    ivcForgotFetch({
        action: 'password',
        pernum: forgotPassPernum,
        pin: pin,
        match: forgotPassPinKey
    })
        .then(function (result) {
            if (!result.success) {
                var fail = ivcEl('forgotpasserr');
                if (fail) {
                    fail.textContent = result.message || 'PIN does not match.';
                    fail.style.display = 'block';
                }
                if (result.pin_key) {
                    applyForgotPasswordPrompt(result);
                }
                return;
            }
            ivcHide('forgotpass_step1');
            ivcHide('forgotpass_step2');
            var ok = ivcEl('forgotpassok');
            if (ok) {
                ok.textContent = result.message || 'Request submitted successfully.';
                ok.style.display = 'block';
            }
        })
        .catch(function (err) {
            var fail = ivcEl('forgotpasserr');
            if (fail) {
                fail.textContent = (err && err.message) ? err.message : 'Request failed. Please try again.';
                fail.style.display = 'block';
            }
        });
}

function submitForgotMp() {
    ivcClearForgotAlerts('forgotmp');
    var email = (ivcEl('forgotmp_email') ? ivcEl('forgotmp_email').value : '').trim();
    var password = (ivcEl('forgotmp_password') ? ivcEl('forgotmp_password').value : '').trim();
    if (email === '' || password === '') {
        var err = ivcEl('forgotmperr');
        if (err) {
            err.textContent = 'Please enter your email and password.';
            err.style.display = 'block';
        }
        return;
    }
    ivcForgotFetch({ action: 'mp', email: email, password: password })
        .then(function (result) {
            if (!result.success) {
                var fail = ivcEl('forgotmperr');
                if (fail) {
                    fail.textContent = result.message || 'Invalid email address or password.';
                    fail.style.display = 'block';
                }
                return;
            }
            var ok = ivcEl('forgotmpok');
            if (ok) {
                ok.textContent = result.message;
                ok.style.display = 'block';
            }
        })
        .catch(function (err) {
            var fail = ivcEl('forgotmperr');
            if (fail) {
                fail.textContent = (err && err.message) ? err.message : 'Request failed. Please try again.';
                fail.style.display = 'block';
            }
        });
}

function forgotPassKeypad(td, key) {
    var pinInput = ivcEl('forgotpass_pin');
    if (!pinInput) {
        return;
    }
    if (key === 'b') {
        pinInput.value = pinInput.value.slice(0, -1);
    } else if (pinInput.value.length < 6) {
        pinInput.value = pinInput.value + key;
    }
}

function showLoginError(msg) {
    var err = ivcEl('err');
    if (err) {
        err.textContent = msg;
        err.style.display = 'block';
    }
    ivcHide('divpin');
    ivcHideForgotPanels();
    ivcShow('divlogin');
    var pinInput = ivcEl('pin');
    if (pinInput) {
        pinInput.value = '';
    }
    pinReady = false;
    currentUid = '';
    currentPernum = '';
    currentPinKey = '';
}

function showPinError(msg) {
    var pinErr = ivcEl('pinerr');
    if (pinErr) {
        pinErr.textContent = msg;
        pinErr.style.display = 'block';
    }
}

function startLogin()
{
    var pernumInput = ivcEl('pernum');
    var passwordInput = ivcEl('password');
    var pernum = (pernumInput ? pernumInput.value : '').replace(/\D/g, '');
    var pwd = (passwordInput ? passwordInput.value : '').trim();
    if (pernumInput) {
        pernumInput.value = pernum;
    }

    var err = ivcEl('err');
    var pinErr = ivcEl('pinerr');
    if (err) {
        err.style.display = 'none';
    }
    if (pinErr) {
        pinErr.style.display = 'none';
    }

    if (pernum === '' || pwd === '') {
        showLoginError("Please enter your account number and password.");
        return false;
    }

    pinReady = false;
    currentUid = '';
    currentPernum = '';
    currentPinKey = '';
    try {
        sessionStorage.setItem('ivc_login_pernum', pernum);
        sessionStorage.setItem('ivc_login_pwd', pwd);
    } catch (e) {}

    var pinInput = ivcEl('pin');
    if (pinInput) {
        pinInput.value = '';
    }
    var pinText = ivcEl('pintext');
    if (pinText) {
        pinText.textContent = 'Checking account...';
    }
    ivcHideForgotPanels();
    ivcHide('divlogin');
    ivcShow('divpin');

    fetch('safezone.login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ pernum: pernum, password: pwd })
    })
    .then(function (response) {
        return response.text().then(function (text) {
            var result;
            try {
                result = JSON.parse(text);
            } catch (e) {
                var hint = (text || '').trim();
                if (hint && hint.charAt(0) !== '<') {
                    throw new Error(hint.substring(0, 220));
                }
                throw new Error('Login service returned an invalid response. Please try again in a moment.');
            }
            return result;
        });
    })
    .then(function (result) {
        if (!result.success) {
            showLoginError(result.message || 'Invalid account number or password.');
            return;
        }
        currentUid = String(result.uid || '');
        currentPernum = String(result.pernum || pernum);
        currentPinKey = String(result.pin_key || '');
        pinReady = currentUid !== '' && currentPinKey.length === 3;
        if (pinInput) {
            pinInput.value = '';
        }
        if (pinText) {
            pinText.textContent = result.prompt || 'Enter the 3 requested digits of your Master PIN.';
        }
    })
    .catch(function (err) {
        showLoginError((err && err.message) ? err.message : 'Unable to reach the login service. Check your connection and try again.');
    });

    return false;
}

function login()
{
    var pernumInput = ivcEl('pernum');
    var pernum = (pernumInput ? pernumInput.value : '').replace(/\D/g, '');
    try {
        if (!pernum && sessionStorage.getItem('ivc_login_pernum')) {
            pernum = sessionStorage.getItem('ivc_login_pernum');
        }
        if (!currentPernum && pernum) {
            currentPernum = pernum;
        }
    } catch (e) {}

    var pinInput = ivcEl('pin');
    var pin = (pinInput ? pinInput.value : '').replace(/\D/g, '');
    if (pinInput) {
        pinInput.value = pin;
    }

    var err = ivcEl('err');
    var pinErr = ivcEl('pinerr');
    if (err) {
        err.style.display = 'none';
    }
    if (pinErr) {
        pinErr.style.display = 'none';
    }

    if (!pinReady || currentUid === '' || currentPinKey.length !== 3) {
        showPinError("Wait for the PIN prompt to load, then enter the requested digits.");
        return;
    }

    if (pin === '') {
        showPinError("Enter the 3 digits shown in the PIN prompt.");
        return;
    }

    var pinText = ivcEl('pintext');
    if (pinText) {
        pinText.textContent = 'Checking login...';
    }

    fetch('safezone.verifyPin.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            uid: currentUid,
            pernum: currentPernum,
            pin: pin,
            key: currentPinKey,
            login_role: <?= $isPartnerLogin ? "'partner'" : "''" ?>
        })
    })
    .then(function (response) {
        return response.text().then(function (text) {
            var result;
            try {
                result = JSON.parse(text);
            } catch (e) {
                throw new Error('PIN service returned an invalid response. Please try again.');
            }
            return result;
        });
    })
    .then(function (result) {
        if (!result.success) {
            showPinError(result.message || 'PIN verification failed.');
            return;
        }
        try {
            sessionStorage.removeItem('ivc_login_pernum');
            sessionStorage.removeItem('ivc_login_pwd');
        } catch (e) {}
        window.location.href = result.redirect || 'home.php';
    })
    .catch(function (err) {
        showPinError((err && err.message) ? err.message : 'Request failed. Please refresh the page and try again.');
    });
}

function keypad(td, key)
{
    var pinInput = ivcEl('pin');
    if (!pinInput) {
        return;
    }

    if (key === 'b') {
        if (pinInput.value === '') {
            showLoginPanel();
        } else {
            pinInput.value = pinInput.value.slice(0, -1);
        }
    } else if (key !== 'e') {
        if (pinInput.value.length < 6) {
            pinInput.value = pinInput.value + key;
        }
    }
}       


</script>          
                    
				
<?php
include('footer.php');
?>