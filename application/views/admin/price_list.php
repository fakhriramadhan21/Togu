<?php 
require_once("header.php"); 

function UserPass() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 5; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

?>
<style type="text/css">
<!--
.style2 {color: #990000}
.style3 {font-size: 18px}
-->
</style>
<p align="center" class="style3"><strong>Disabled in Demo Version</strong></p>
<p align="center" class="style3"><strong><a href="http://rpcits.co.in/" target="_blank" class="style2"><u>Upgrade</u></a> to full version for start @ ₹ 5,999/- (99$) only <u>with 100% Source Code</u> <a href="http://rpcits.co.in/" target="_blank"><u><span class="style2">www.rpcits.co.in</span> </u></a></strong></p>
<br/>
<p align="center" class="style3"><strong>If you want only online service (laundy system + domain name + hosting ) for just ₹ 3,999/- (49$) per year </strong> <br/> <span style='font-size:13px;'> ( We will not provide any hosting account details ( only get upgrated online laundry system, domain name etc) </span> </p>
<center>
<form action="#" method="post" target="_top">
<div align="center">
  <input type="hidden" name="cmd" value="_s-xclick">
  <input type="hidden" name="hosted_button_id" value="4BKA5ZHWXARDU">
</div>
<table>
<tr><td><input type="hidden" name="on0" value="Select Software:">Contact Us:</td></tr>
<tr><td> Red Planet Computers </td></tr>
<tr><td> How to Use See => <a href="https://www.youtube.com/watch?v=BPiLJI1MyVU" target="_blank">  Youtube Video </a>  </td></tr>
<tr><td> Latest & Full Version => <a href="http://laundry.rpcits.co.in/admin" target="_blank"> Free Trial </a> </td></tr>
</table>
</center>
<table>
<tr> <td> <br/> <br/>  <br/> <br/>  Some Images Follows : </td> 
<tr> 
<td> <img src="<?php echo base_url();?>assets/images/demo1.png" style="height:400px; width:550px;"> </td> 
<td> <img src="<?php echo base_url();?>assets/images/demo2.png" style="height:400px; width:550px;"> </td> 
</tr>
<tr> 
<td> <img src="<?php echo base_url();?>assets/images/demo3.png" style="height:400px; width:550px;"> </td> 
<td> <img src="<?php echo base_url();?>assets/images/demo4.png" style="height:400px; width:550px;"> </td> 
</tr>

</table>

</form>

<?php require_once("footer.php"); ?>

		


		
	</body>
</html>