<?
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
                                            <input class="form-control" value="" name="pin" id="pin" autocomplete="off" type="password" inputmode="numeric" placeholder="PIN digits" style="background:none; border-color:#cc0000; color:#cc0000; margin:0 auto; margin-top:20px; max-width:245px;">
                                            
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

function startLogin()
{
    var pernum = $("#pernum").val().trim();
    var pwd = $("#password").val();
    $("#err").hide();

    if (pernum === '' || pwd === '') {
        $("#err").html("Please enter your account number and password.");
        $("#err").show();
        return false;
    }

    $("#pintext").html("Loading PIN prompt...");
    $("#divlogin").hide();
    $("#divpin").show();
    getnewpin();
    return false;
}

function login()
{
    var pernum=$("#pernum").val();
    var pwd=$("#password").val();
    var pin=$("#pin").val();
    $("#err").hide();
    $("#pinerr").hide();

    if(pin==='')
    {
        $("#pinerr").html("Please enter the requested PIN digits.");
        $("#pinerr").show();
        return;
    }

    if(pernum!='' && pwd!='' && pin!='')
    {
        var request = $.ajax({
            url: "ajax.login.php",
            method: "POST",
            data: { pernum : pernum, pwd : pwd, pin : pin },
            dataType: "html"
            });

            request.done(function( msg ) {
                msg = $.trim(msg);
                if(msg!='success')
                {
                    $("#err").html(msg);
                    $("#err").show();
                    $("#divpin").hide();
                    $("#divlogin").show();
                    $("#pin").val('');
                }
                else
                {
                    document.location.href="home.php";
                }
            });

            request.fail(function( jqXHR, textStatus ) {
            $("#pinerr").html("Request failed: " + textStatus);
            $("#pinerr").show();
            $("#pin").val('');
        });
    }
}

function getnewpin()
{
        var request = $.ajax({
            url: "ajax.getnewpin.php",
            method: "POST",
            dataType: "html"
            });

            request.done(function( msg ) {
                $("#pintext").html(msg);
            });

            request.fail(function() {
                $("#pintext").html("Unable to load PIN prompt. Please try again.");
            });
}

function keypad(td, key)
{
    if(key=='b')
    {
        if($('#pin').val()=='')
        {
            $('#divlogin').show(); 
            $('#divpin').hide();
        }
        else
        {
            var txt = $('#pin');
            txt.val(txt.val().slice(0, -1));
        }
    }
    else if(key=='e')
    {
        
    }
    else
    {
        $('#pin').val($('#pin').val()+key);
    }
}       


</script>          
                    
				
<?
include('footer.php');
?>