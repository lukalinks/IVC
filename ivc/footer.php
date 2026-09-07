
				
		<div class="row" style="padding: 20px; margin-top: 15px;">

			<div class="col-md-12" style="text-align: center;">
				<img src="protected_small.png" class="img img-responsive" style="margin: 0 auto; width:180px;">
				
				<p style="color: #000; text-align: center; font-size: 14px; font-weight: normal; margin-top:20px;">© 2018-<?=date('Y')?> International Vacation Club Ltd.<br>All Rights Reserved.</p>
			</div>
			
		</div>	
	</div>
		
<script type="text/javascript">

$(document).ready(function() {
		
	});
	
//When the user scrolls the page, execute myFunction 
window.onscroll = function() {myFunction()};

// Get the navbar
var navbar = document.getElementById("navbar");
var sticky = navbar ? navbar.offsetTop : 0;

function myFunction() {
  if (!navbar) {
    return;
  }
  if (window.pageYOffset >= sticky) {
    navbar.classList.add("sticky");
    $('#topmenu').hide();
    //navbar2.classList.add("whitebg");
  } else {
    navbar.classList.remove("sticky");
    $('#topmenu').show();
    //navbar2.classList.remove("whitebg")
  }
}

</script>    

</body>

</html>