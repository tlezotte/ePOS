<?php
/**
 * Request System
 *
 * checkInformation.php information needed to generate Check Request.
 *
 * @version 1.5
 * @link http://www.yourdomain.com/go/Request/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package PO
  * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 */
 
/**
 * - Start Page Loading Timer
 */
include_once('../include/Timer.php');
$starttime = StartLoadTimer();
/**
 * - Set debug mode
 */
$debug_page = false;
include_once('debug/header.php');

/**
 * - Database Connection
 */
require_once('../Connections/connDB.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 
/**
 * - Check User Access
 */
require_once('../security/check_user.php');


/* ------------------ START PROCESSING ----------------------- */
/* if ($_POST['stage'] == 'generate') {
	if ($_POST['reason'] == '2' and $_POST['total'] != $_POST['amtTotal']) {
		$_SESSION['error'] = "Invoice Total needs to equal Check Request Total";
		$goTo = '../error.php';
	} else {
		$goTo = 'CheckRequest.php?id='.$_GET['id'];
	}
	header("Location: ".$goTo);
	exit();
} */
/* ------------------ END PROCESSING ----------------------- */

/* ------------------ START DATABASE CONNECTIONS ----------------------- */
/* Getting PO information */
$PO = $dbh->getRow("SELECT * 
				    FROM PO 
				    WHERE id = ?",array($_GET['id']));
/* Getting Authoriztions for above PO */
$AUTH = $dbh->getRow("SELECT * FROM Authorization WHERE type_id = ? and type = 'PO'",array($PO['id']));
/* Get Employee names from Standards database */
$EMPLOYEES = $dbh->getAssoc("SELECT e.eid, CONCAT(e.fst,' ',e.lst) AS name 
							 FROM Users u, Standards.Employees e 
							 WHERE e.eid = u.eid");					
/* Getting plant locations from Standards.Plants */								
$plant_sql = $dbh->prepare("SELECT id, name FROM Standards.Plants ORDER BY name ASC");
/* Getting companies from Standards.Companies */								
$company_sql = $dbh->prepare("SELECT id, name 
						      FROM Standards.Companies 
						      WHERE id > 0 
						      ORDER BY name");
/* Getting suppliers from Suppliers */						 
$supplier_sql = $dbh->prepare("SELECT BTVEND AS id, CONCAT(BTNAME, ' ( ', BTVEND, ' )', ' ( ', BTADR7, ' )') AS name, BTSTAT AS status
							    FROM Standards.Vendor
							    WHERE BTVEND NOT LIKE '%R'
								  AND BTCLAS NOT LIKE 'I'
							    ORDER BY name");	
/* Getting approver 1's from Users */									 
$app1_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst 
					       FROM Users U, Standards.Employees E 
					       WHERE U.eid = E.eid 
						     AND(U.one = '1' or U.two = '1') 
						     AND U.status = '0' and E.status = '0' 
					       ORDER BY E.lst ASC");		   					 	
/* ------------------ END DATABASE CONNECTIONS ----------------------- */

/* Setup onLoad javascript program */
if ($default['pageloading'] == 'on') {
  $ONLOAD_OPTIONS="pageloading();";
}
$ONLOAD_OPTIONS.="prepareForm();";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
  <head>
  
    <title><?= $default['title1']; ?>
    </title>
  
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="imagetoolbar" content="no">
  <meta name="copyright" content="2004 Your Company" />
  <meta name="author" content="Thomas LeZotte" />
  <link type="text/css" href="/Common/noPrint.css" rel="stylesheet">
  <link type="text/css" href="/Common/Print.css" rel="stylesheet" media="print">
  <link type="text/css" href="/Common/newCompany.css" rel="stylesheet" media="screen">
  <link type="text/css" href="../epos.css" charset="UTF-8" rel="stylesheet">
  <?php if ($default['rss'] == 'on') { ?>
  <link rel="alternate" type="application/rss+xml" title="Purchase Requisition Announcements" href="<?= $default['URL_HOME']; ?>/PO/<?= $default['rss_file']; ?>">
  <link rel="alternate" type="application/rss+xml" title="Capital Acquisition Announcements" href="<?= $default['URL_HOME']; ?>/CER/<?= $default['rss_file']; ?>">
  <?php } ?>
  <?php if ($default['pageloading'] == 'on') { ?>
  <script type="text/javascript" src="/Common/js/pageloading.js"></script>
  <?php } ?>
  <script type="text/javascript" src="/Common/js/overlibmws.js"></script>
  <script type="text/javascript" src="/Common/js/overlibmws/overlibmws_iframe.js"></script>
  <script type="text/javascript" SRC="/Common/js/googleAutoFillKill.js"></script>
  <script type="text/javascript" src="/Common/js/disableEnterKey.js"></script>  
  
	<script language="javascript">
	function cent(amount) {
	// returns the amount in the .99 format 
		amount -= 0;
		amount = (Math.round(amount*100))/100;
		return (amount == Math.floor(amount)) ? amount + '.00' : (  (amount*10 == Math.floor(amount*10)) ? amount + '0' : amount);
	}
	
	function Calculate()
	{
	  total = parseFloat(Form.amt1.value) + parseFloat(Form.amt2.value) + parseFloat(Form.amt3.value) + parseFloat(Form.amt4.value) + parseFloat(Form.amt5.value) + parseFloat(Form.amt6.value) + parseFloat(Form.amt7.value);
	  Form.amtTotal.value = cent(total);
	}
	</script>
		
	<script type="text/javascript">
	
	/*
	Combo-Box Viewer script- Created by and Â© Dynamicdrive.com
	Visit http://www.dynamicdrive.com/ for this script and more
	This notice MUST stay intact for legal use
	*/
	
	if (document.getElementById){
	document.write('<style type="text/css">\n')
	document.write('.dropcontent{display:none;}\n')
	document.write('</style>\n')
	}
	
	function contractall(){
	if (document.getElementById){
	var inc=0
	while (document.getElementById("reason"+inc)){
	document.getElementById("reason"+inc).style.display="none"
	inc++
	}
	}
	}
	
	function expandone(){
	if (document.getElementById){
	var selectedItem=document.Form.reason.selectedIndex
	contractall()
	document.getElementById("reason"+selectedItem).style.display="block"
	}
	}
	
	if (window.addEventListener)
	window.addEventListener("load", expandone, false)
	else if (window.attachEvent)
	window.attachEvent("onload", expandone)
	
	</script>
	<SCRIPT SRC="/Common/js/overlibmws/overlibmws_exclusive.js"></SCRIPT>
	<SCRIPT SRC="/Common/js/overlibmws/overlibmws_iframe.js"></SCRIPT>
	<SCRIPT SRC="/Common/js/overlibmws/overlibmws_draggable.js"></SCRIPT>
	<SCRIPT SRC="/Common/js/overlibmws/calendarmws.js"></SCRIPT>
  </head>

  <body <?= $ONLOAD; ?>>
  <form action="CheckRequest.php" method="POST" name="Form" id="Form">
    <table border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td></td>
              <td height="26" valign="top"></td>
            </tr>
            <tr class="BGAccentVeryDark">
              <td width="50%" height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Check Request...</td>
              <td width="50%"></td>
            </tr>
        </table></td>
      </tr>
      <tr>
        <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td valign="top" class="BGAccentDarkBorder"><table width="100%"  border="0">
                  <tr>
                    <td height="25" colspan="6" class="BGAccentDark"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td width="50%"><strong>&nbsp;&nbsp;Information</strong> </td>
                          <td width="50%"><div align="right" class="mainsection">&nbsp;</div></td>
                        </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td width="75">Vendor:</td>
                    <td width="20">&nbsp;</td>
                    <td><select name="supplier" id="supplier">
                        <option value="0">Select One</option>
                        <?php
											  $supplier_sth = $dbh->execute($supplier_sql);
											  while($supplier_sth->fetchInto($SUPPLIER)) {
												$selected = ($PO['sup'] == $SUPPLIER[id]) ? selected : $blank;
												print "<option value=\"".$SUPPLIER[id]."\" ".$selected.">".ucwords(strtolower($SUPPLIER[name]))."</option>\n";
											  }
											?>
                    </select></td>
                    <td width="125" nowrap>Date for FedEx:</td>
                    <td width="20">&nbsp;</td>
                    <td><input name="fedexDate" type="text" id="fedexDate" size="10" maxlength="10" readonly>
                      &nbsp;<a href="javascript:show_calendar('Form.fedexDate')" <?php help('', 'Click here to choose a date', 'default'); ?>><img src="../images/calendar.gif" width="17" height="18" border="0" align="absmiddle"></a></td>
                  </tr>
                  <tr>
                    <td>Plant:</td>
                    <td>&nbsp;</td>
                    <td><select name="plant" id="plant">
                        <option value="0">Select One</option>
                        <?php
											  $plant_sql_sth = $dbh->execute($plant_sql);
											  while($plant_sql_sth->fetchInto($row)) {
												$selected = ($PO['ship'] == $row[id]) ? selected : $blank;
												print "<option value=\"".$row[id]."\" ".$selected.">".ucwords(strtolower($row[name]))."</option>\n";
											  }
											?>
                    </select></td>
                    <td nowrap>Amount of Request: $</td>
                    <td nowrap>&nbsp;</td>
                    <td><input name="total" type="text" id="total" value="<?= $PO['total']; ?>" size="15" maxlength="15"></td>
                  </tr>
                  <tr>
                    <td height="5" colspan="6"><img src="../images/spacer.gif" width="5" height="5"></td>
                  </tr>
                  <tr>
                    <td>Reason:</td>
                    <td><?= $WARNING; ?></td>
                    <td><table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td><select name="reason" id="reason" onChange="expandone()">
                              <option value="0">Select One</option>
                              <option value="1">Advance Payment</option>
                              <option value="2">Old Invoice Paid</option>
                              <option value="3">Held Check</option>
                              <option value="4">Other</option>
                          </select></td>
                        </tr>
                    </table></td>
                    <td>Controller:</td>
                    <td>&nbsp;</td>
                    <td><select name="controller" id="select2">
                        <option value="0">Select One</option>
                        <?php
												  $app1_sth = $dbh->execute($app1_sql);
												  while($app1_sth->fetchInto($APP1)) {
													$selected = ($AUTH['app1'] == $APP1[eid]) ? selected : $blank;
													print "<option value=\"".$APP1[eid]."\" ".$selected.">".ucwords(strtolower($APP1[lst].", ".$APP1[fst]))."</option>";
												  }
												 ?>
                    </select></td>
                  </tr>
              </table></td>
            </tr>
          </table>
            <div id="reason0" class="dropcontent"></div>
          <div id="reason1" class="dropcontent"></div>
          <div id="reason3" class="dropcontent"></div>
          <div id="reason4" class="dropcontent"></div>
          <div id="reason2" class="dropcontent"> <br>
                <table border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                    <td class="BGAccentDarkBorder"><table  border="0">
                        <tr>
                          <td height="25" colspan="5" class="BGAccentDark"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td><strong>&nbsp;&nbsp;Old Invoices Information </strong> </td>
                                <td><div align="right" class="mainsection">&nbsp;</div></td>
                              </tr>
                          </table></td>
                        </tr>
                        <tr>
                          <td height="25" class="BGAccentMedium">&nbsp;&nbsp;Invoice #
                            <?= $WARNING; ?></td>
                          <td height="25" class="BGAccentMedium">&nbsp;&nbsp;Amount $&nbsp;
                              <?= $WARNING; ?></td>
                          <td rowspan="6"><img src="../images/spacer.gif" width="10" height="10"></td>
                          <td class="BGAccentMedium">&nbsp;&nbsp;Invoice #
                            <?= $WARNING; ?></td>
                          <td class="BGAccentMedium">Amount $&nbsp;
                              <?= $WARNING; ?></td>
                        </tr>
                        <tr>
                          <td><input name="inv1" type="text" id="inv1" size="15" maxlength="15"></td>
                          <td><input name="amt1" type="text" id="amt1" value="0" size="15" maxlength="15" onBlur="Calculate();"></td>
                          <td><input name="inv2" type="text" id="inv2" size="15" maxlength="15"></td>
                          <td><input name="amt2" type="text" id="amt2" value="0" size="15" maxlength="15" onBlur="Calculate();"></td>
                        </tr>
                        <tr>
                          <td><input name="inv3" type="text" id="inv3" size="15" maxlength="15"></td>
                          <td><input name="amt3" type="text" id="amt3" value="0" size="15" maxlength="15" onBlur="Calculate();"></td>
                          <td><input name="inv4" type="text" id="inv4" size="15" maxlength="15"></td>
                          <td><input name="amt4" type="text" id="amt4" value="0" size="15" maxlength="15" onBlur="Calculate();"></td>
                        </tr>
                        <tr>
                          <td><input name="inv5" type="text" id="inv5" size="15" maxlength="15"></td>
                          <td><input name="amt5" type="text" id="amt5" value="0" size="15" maxlength="15" onBlur="Calculate();"></td>
                          <td><input name="inv6" type="text" id="inv6" size="15" maxlength="15"></td>
                          <td><input name="amt6" type="text" id="amt6" value="0" size="15" maxlength="15" onBlur="Calculate();"></td>
                        </tr>
                        <tr>
                          <td><input name="inv7" type="text" id="inv7" size="15" maxlength="15"></td>
                          <td><input name="amt7" type="text" id="amt7" value="0" size="15" maxlength="15" onBlur="Calculate();"></td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                        </tr>
                        <tr>
                          <td>&nbsp;&nbsp;</td>
                          <td>&nbsp;&nbsp;</td>
                          <td><div align="right">Total:&nbsp;</div></td>
                          <td><input name="amtTotal" type="text" id="amtTotal" size="15" maxlength="15" readonly></td>
                        </tr>
                    </table></td>
                  </tr>
                </table>
          </div></td>
      </tr>
      <tr>
        <td height="5" valign="bottom"><img src="../images/spacer.gif" width="5" height="5"></td>
      </tr>
      <tr>
        <td><div align="right">
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="50%" valign="middle">&nbsp;</td>
                <td width="50%" align="right"><input name="id" type="hidden" id="id" value="<?= $_GET['id']; ?>">
                    <input name="imageField" type="image" src="../images/button.php?i=b70.png&l=Done" border="0">
                  &nbsp;</td>
              </tr>
            </table>
        </div></td>
      </tr>
    </table>
  </form>
  </body>
</html>


<?php
/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh->disconnect();
?>