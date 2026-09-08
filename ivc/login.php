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
                                        </div>
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

function showLoginError(msg) {
    var err = ivcEl('err');
    if (err) {
        err.textContent = msg;
        err.style.display = 'block';
    }
    ivcHide('divpin');
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
            key: currentPinKey
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
            ivcShow('divlogin');
            ivcHide('divpin');
            pinReady = false;
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