<span class="Copyright">
<?php 
if ($_SERVER['HTTPS'] == 'on') { 
	
	$certificate = "<b>Organization:</b> ". $_SERVER['SSL_SERVER_S_DN_O'] ."<br>" .
				   "<b>Location:</b> ". $_SERVER['SSL_SERVER_S_DN_L'] ."<br>" .
				   "<b>State:</b> ". $_SERVER['SSL_SERVER_S_DN_ST'] ."<br>" .
				   "<b>Email:</b> " . $_SERVER['SSL_SERVER_S_DN_Email'] . "<br><br>" .
				   "<b>Version:</b> ". $_SERVER['SSL_SERVER_M_VERSION'] ." - ". $_SERVER['SSL_CIPHER'] ."<br>" .
				   "<b>Created:</b> ". $_SERVER['SSL_SERVER_V_START'] ."<br>" .
				   "<b>Expires:</b> ". $_SERVER['SSL_SERVER_V_END'] ."<br>";
?>
<a href="javascript:void(0);" title="SSL Certificate Information|<?= $certificate; ?>"><img src="/Common/images/lock.gif" width="13" height="15" border="0" align="texttop"></a>
<?php } ?>
Copyright &copy; 2006, Your Company. All rights reserved.</span>