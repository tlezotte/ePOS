<?php
$sql = "SELECT cc.id, p.name
		FROM CreditConfig cc
		  INNER JOIN Standards.Plants p ON p.id=cc.plant
		ORDER BY p.name";
$PLANTS = $dbh->getAssoc($sql);
?>


<table cellspacing="0" cellpadding="0" width="200" align="left" summary="" border="0">
    <tr>
      <td valign="top" width="13" background="/Common/images/asyltlb.gif"><img height="20" alt="" src="/Common/images/t.gif" width="13" border="0"></td>
      <td valign="top" width="165" bgcolor="#cccc99"><img height="1" alt="" src="/Common/images/asybase.gif" width="145" border="0"> <br>
	  <table cellspacing="0" cellpadding="0" border="0">
		<tr>
		  <td><a href="<?= $_SERVER['PHP_SELF']; ?>?a=payment" class="dark">Make Payment</a></td>
		</tr>
		<tr>
		  <td><a href="<?= $_SERVER['PHP_SELF']; ?>" class="dark">All Transactions</a></td>
		</tr>	
		<tr>
		  <td class="mainsection">&nbsp;</td>
		</tr>			
	  </table>	  
	  <table cellspacing="0" cellpadding="0" border="0">
		<tr>
		  <td class="mainsection">Plant Transactions</td>
		</tr>
	  </table>	  
	  <?php while (list($id, $name) = each($PLANTS)) { ?>
	  <table cellspacing="0" cellpadding="0" border="0">
		<tr>
		  <td class="mainsection"><a href="<?= $_SERVER['PHP_SELF'] . "?l=" . $id; ?>" class="dark"><?= $name; ?></a> </td>
		</tr>
	  </table>
	  <?php } ?>
	  </td>
      <td valign="top" width="22" background="/Common/images/asyltrb.gif"><img height="20" alt="" src="/Common/images/t.gif" width="22" border="0"></td>
    </tr>
    <tr>
      <td valign="top" width="22" colspan="3"><img height="37" alt="" src="/Common/images/asyltb.gif" width="200" border="0"></td>
    </tr>
</table>
