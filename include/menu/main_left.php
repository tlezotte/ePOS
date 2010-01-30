<?php
$menu1 = $default['url_home'] . "/PO/index.php";
$menu2 = $default['url_home'] . "/PO/list.php?action=my&access=0";
$menu3 = $default['url_home'] . "/PO/list.php";
$menu4 = $default['url_home'] . "/PO/search.php";
$menu5 = $default['url_home'] . "/PO/track.php";
$menu6 = $default['url_home'] . "/PO/Reports/index.php";
?>
<script type="text/javascript">
//Contents for menu 5
var left7=new Array()
left7[0]='<a href="/go/Request/PO/blanket.php">Blanket Purchases</a>'
left7[1]='<a href="/go/Request/PO/creditcards.php">Credit Cards</a>'
left7[2]='<a href="/go/Request/PO/payments.php">Payments List</a>'
</script>
<table cellspacing="0" cellpadding="0" summary="" border="0">
	<tr>
	  <td>&nbsp;</td>
	  <td>
		<table cellspacing="0" cellpadding="0" summary="" border="0">
			<tr>
		  	  <td nowrap>&nbsp;<a href="<?= $menu1; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu1) ? on : off; ?>" onmouseover="return overlib('Start a new Purchase Request',  TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">NEW</a>&nbsp;</td>
			  <td width="20" valign="middle" nowrap><div align="center"><img src="/Common/images/dot.gif" width="10" height="10"></div></td>			
			  <td nowrap>&nbsp;<a href="<?= $menu2; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu2) ? on : off; ?>" onmouseover="return overlib('List of your Purchase Requests', TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">My Requisitions</a>&nbsp;</td>
			  <td width="20" valign="middle" nowrap><div align="center"><img src="/Common/images/dot.gif" width="10" height="10"></div></td>
			  <td nowrap>&nbsp;<a href="<?= $menu3; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu3) ? on : off; ?>" onmouseover="return overlib('List all Purchase Requests', TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">All Requisitions</a>&nbsp;</td>
		  	  <td width="20" valign="middle" nowrap><div align="center"><img src="/Common/images/dot.gif" width="10" height="10"></div></td>
			  <td nowrap>&nbsp;<a href="<?= $menu4; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu4) ? on : off; ?>" onmouseover="return overlib('Search all Purchase Request', TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">Search</a>&nbsp;</td>
		  	  <td width="20" valign="middle" nowrap><div align="center"><img src="/Common/images/dot.gif" width="10" height="10"></div></td>
			  <td nowrap>&nbsp;<a href="<?= $menu5; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu5) ? on : off; ?>" onmouseover="return overlib('Track Shipments or Deliveries from FedEx, UPS, USPS and DHL', TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">Tracking</a>&nbsp;</td>
		  	  <td width="20" valign="middle" nowrap><div align="center"><img src="/Common/images/dot.gif" width="10" height="10"></div></td>
			  <td nowrap>&nbsp;<a href="<?= $menu6; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu6) ? on : off; ?>" onmouseover="return overlib('Reports on spending habits', TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">Reports</a>&nbsp;</td>
			  <?php if ($_SESSION['request_role'] == 'purchasing' OR $_SESSION['request_role'] == 'executive') { ?>
		  	  <td width="20" valign="middle" nowrap><div align="center"><img src="/Common/images/dot.gif" width="10" height="10"></div></td>			  
			  <td nowrap>&nbsp;<a href="javascript:void(0);" class="off" onClick="return clickreturnvalue()" onMouseover="dropdownmenu(this, event, left7, '150px')" onMouseout="delayhidemenu()">Purchasing</a>&nbsp;</td>	
			  <?php } ?>		  
			  </tr>
		</table>
	  </td>
	  <td>&nbsp;</td>
	</tr>
</table>