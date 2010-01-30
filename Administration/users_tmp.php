<?php 
/**
 * Request System
 *
 * users.php list all users.
 *
 * @version 1.5
 * @link http://www.yourdomain.com/go/Request/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package Administration
  * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 */

/**
 * - Forward BlackBerry users to BlackBerry version
 */
require_once('../include/BlackBerry.php');
 
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
 * - Check User Access
 */
require_once('../security/check_access1.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 


/* ----- START ADD USER ----- */
switch ($_POST['action']) {
	/* ---------- ADD USER ---------- */
	case "add":
		$sql="INSERT into Users (eid, online) VALUES('".$_POST['addUser']."', '00000000000000')";
		$dbh->query($sql);
		
		/* Record transaction for history */
		History($_SESSION['eid'], $_POST['action'], $_SERVER['PHP_SELF'], addslashes(htmlentities($sql)));
	break;
	/* ---------- EMAIL ACCESS REQUEST FORM ---------- */
	case "requestform":
			$url = "http://".$_SERVER['SERVER_NAME']."/register";
			$sendTo = $_POST['email']."@".$default['email_domain'];
			
			require("phpmailer/class.phpmailer.php");
		
			$mail = new PHPMailer();
			
			$mail->From     = $default['email_from'];
			$mail->FromName = $default['title1'];
			$mail->Host     = $default['smtp'];
			$mail->Mailer   = "smtp";
			$mail->AddAddress($sendTo);
			$mail->Subject = $default['title1'].": Access Request";

/* HTML message */				
$htmlBody = <<< END_OF_HTML
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>$default[title1]</title>
</head>
<body>
<p><img src="$default[URL_HOME]/images/email_header.gif" width="646" height="74"></p>
<br>
Please select the link listed below to display the Access Request Form.<br>
<br>
URL: <a href="$url">$url</a><br>
</body>
</html>
END_OF_HTML;

			$mail->Body = $htmlBody;
			$mail->isHTML(true);
			
			if(!$mail->Send())
			{
			   echo "Message was not sent";
			   echo "Mailer Error: " . $mail->ErrorInfo;
			}
			
			// Clear all addresses and attachments for next loop
			$mail->ClearAddresses();
			$mail->ClearAttachments();	
	break;
}
/* ----- END ADD USER ----- */

/* ----- START UPDATE USER ----- */
/*  Update all users privileges  */
if (array_key_exists('reset', $_GET)) {
	if ($_GET['reset'] == 'off') {
		$sql="UPDATE Users
				 SET one='0', two='0', three='0', four='0', issuer='0'
				 WHERE eid='".$_GET['eid']."'";
	} else {
		$sql="UPDATE Users
			  SET one='1', two='1', three='1', four='1', issuer='1'
			  WHERE eid='".$_GET['eid']."'";
	}		 
	$dbh->query($sql);

	header("Location: ".$_SERVER['PHP_SELF']);
	exit();
}

/*  Update users privileges  */
if (array_key_exists('action', $_GET)) {
	$sql="UPDATE Users
			 SET $_GET[action]='".$_GET[value]."'
			 WHERE eid='".$_GET['eid']."'";
	$dbh->query($sql);

	/* Record transaction for history */
	History($_SESSION['eid'], $_GET['action'], $_SERVER['PHP_SELF'], addslashes(htmlentities($sql)));
		
	header("Location: ".$_SERVER['PHP_SELF']);
	exit();
}
/* ----- END UPDATE USER ----- */


/* ------------------ START VARIABLES ----------------------- */
/* --- Pagination Variables --- */
$page_order = (array_key_exists('o', $_GET)) ? $_GET['o'] : "E.lst";								// Order By field
$page_direction = (array_key_exists('d', $_GET)) ? $_GET['d'] : "ASC";								// Order By field direction
$page_rows = $dbh->getRow("SELECT COUNT(E.eid) AS total 
						   FROM Users U, Standards.Employees E
						   WHERE U.eid = E.eid
			    			 AND E.status = '0'");				// Get total number of active Projects
$page_start = (array_key_exists('s', $_GET)) ? $_GET['s'] : "0";									// Page start row
$viewable_rows = ($viewable_rows > $page_rows['total']) ? $page_rows['total'] : $viewable_rows;		// Checks rows with default viewable_rows
$page_next = $page_start + $viewable_rows;															// Set next page
$page_previous = $page_start - $viewable_rows;														// Set previous page
$page_last = $page_rows['total'] - $viewable_rows;													// Set last page
$letter = (array_key_exists('letter', $_GET)) ? $_GET['letter'].'%' : '%';
$limit = (!array_key_exists('display', $_GET)) ? "LIMIT $page_start, $viewable_rows" : $blank;
/* ------------------ END VARIABLES ----------------------- */

/* ----- START DATABASE ACCESS ----- */
$users_sql = "SELECT E.eid, E.fst, E.lst, E.username, E.email, E.password, U.access, U.requester, U.one, U.two, U.three, U.four, U.issuer, U.cer, U.status 
			  FROM Users U, Standards.Employees E 
			  WHERE U.eid = E.eid
			    AND E.status = '0'
				AND E.lst LIKE '$letter'
			  ORDER BY $page_order $page_direction
			  $limit";
$Dbg->addDebug($users_sql,DBGLINE_QUERY,__FILE__,__LINE__);		//Debug SQL
$Dbg->DebugPerf(DBGLINE_QUERY);									//Start debug timer  			  
$users_query = $dbh->prepare($users_sql);
$Dbg->DebugPerf(DBGLINE_QUERY);									//Stop debug timer 		 
$users_sth = $dbh->execute($users_query);
$num_rows = $users_sth->numRows();
					
$employees_sql = "SELECT eid, fst, lst 
				  FROM Standards.Employees 
				  WHERE status = '0'
				  ORDER BY lst";
$Dbg->addDebug($employees_sql,DBGLINE_QUERY,__FILE__,__LINE__);		//Debug SQL
$Dbg->DebugPerf(DBGLINE_QUERY);									//Start debug timer  						  
$employees_query = $dbh->prepare($employees_sql);							
$Dbg->DebugPerf(DBGLINE_QUERY);									//Stop debug timer    
/* ----- END DATABASE ACCESS ----- */


/* Setup onLoad javascript program */
if ($default['pageloading'] == 'on') {
  $ONLOAD_OPTIONS="pageloading();";
}
//$ONLOAD_OPTIONS.="prepareForm();";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html><!-- InstanceBegin template="/Templates/vnmain.dwt.php" codeOutsideHTMLIsLocked="false" -->
  <head>
  <!-- InstanceBeginEditable name="doctitle" -->
    <title><?= $default['title1']; ?></title>
  <!-- InstanceEndEditable -->
  <meta http-equiv="imagetoolbar" content="no">
  <meta name="copyright" content="2004 Your Company" />
  <meta name="author" content="Thomas LeZotte" />
  <link type="text/css" href="/Common/Print.css" rel="stylesheet" media="print">
  <link type="text/css" href="../default.css" charset="UTF-8" rel="stylesheet">
  <?php if ($default['rss'] == 'on') { ?>
  <link rel="alternate" type="application/rss+xml" title="Purchase Requisition Announcements" href="<?= $default['URL_HOME']; ?>/PO/<?= $default['rss_file']; ?>">
  <link rel="alternate" type="application/rss+xml" title="Capital Acquisition Announcements" href="<?= $default['URL_HOME']; ?>/CER/<?= $default['rss_file']; ?>">
  <?php } ?> 
	<script type="text/javascript" src="/Common/js/overlibmws.js"></script>
  <!-- InstanceBeginEditable name="head" -->
	<script type="text/javascript" src="/Common/js/pointers.js"></script>
  
	<script type="text/javascript" src="/Common/js/prototype/prototype.js"></script>
	<script type="text/javascript" src="/Common/js/scriptaculous/scriptaculous.js?load=effects"></script>
	
	<script type="text/javascript" src="/Common/js/autoassist/autoassist.js"></script>
	<link href="/Common/js/autoassist/autoassist.css" rel="stylesheet" type="text/css">	  
  
	<script type="text/javascript" src="/Common/js/greybox5/options1.js"></script>
    <script type="text/javascript" src="/Common/js/greybox5/AJS.js"></script>
	<script type="text/javascript" src="/Common/js/greybox5/AJS_fx.js"></script>
    <script type="text/javascript" src="/Common/js/greybox5/gb_scripts.js"></script>
	<link type="text/css" href="/Common/js/greybox5/gb_styles.css" rel="stylesheet" media="all">	 
  <!-- InstanceEndEditable -->
  <?php if ($ONLOAD_OPTIONS) { ?>
  <script language="javascript">
	AJS.AEV(window, "load", <?= $ONLOAD_OPTIONS; ?>);
  </script>
  <?php } ?>
  </head>

  <body class="yui-skin-sam">  
    <img src="/Common/images/CompanyPrint.gif" alt="Your Company" width="437" height="61" id="Print" />
	<div id="noPrint">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" summary="">
      <tbody>
        <tr>
          <td valign="top"><a href="../home.php" title="<?= $default['title1']; ?> Home"><img name="Company" src="/Common/images/Company.gif" width="300" height="50" border="0"></a></td>
          <td align="right" valign="top">
          <!-- InstanceBeginEditable name="topRightMenu" --><!-- #BeginLibraryItem "/Library/help.lbi" --><table cellspacing="0" cellpadding="0" summary="" border="0">
<tr>
  <td width="30"><a href="../Common/calculator.php" onClick="window.open(this.href,this.target,'width=281,height=270'); return false;" <?php help('', 'Calculator', 'default'); ?>><img src="../images/xcalc.png" width="16" height="14" border="0"></a></td>
  <td><a href="../Help/index.php" rel="gb_page_fs[]"><img src="../images/help.gif" width="18" height="18" border="0" align="absmiddle"></a></td>
  <td class="DarkHeaderSubSub">&nbsp;<a href="../Help/index.php" rel="gb_page_fs[]" class="dark">Help</a></td>
</tr>
</table>
<!-- #EndLibraryItem --><!-- InstanceEndEditable --></td>
        </tr>

        <tr>
          <td valign="bottom" align="right" colspan="2"><!-- InstanceBeginEditable name="rightMenu" --><?php include('../include/menu/main_right.php'); ?><!-- InstanceEndEditable --></td>

          <td>
          </td>
        </tr>

        <tr>
          <td width="100%" colspan="3"><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
            <tbody>
              <tr>
                <td width="4" colspan="4" height="4"><img height="4" alt="" src="../images/c-ghtl.gif" width="4"></td>
                <td colspan="4"><table cellspacing="0" cellpadding="0" width="100%" summary="" background="../images/c-ght.gif" border="0">
                    <tbody>
                      <tr>
                        <td height="4"></td>
                      </tr>
                    </tbody>
                </table></td>
                <td class="BGColorDark" valign="top" rowspan="2"><table cellspacing="0" cellpadding="0" width="100%" summary="" background="../images/c-ght.gif" border="0">
                    <tbody>
                      <tr>
                        <td height="4"></td>
                      </tr>
                    </tbody>
                </table></td>
                <td width="4" colspan="4" height="4"><img height="4" alt="" src="../images/c-ghtr.gif" width="4"></td>
              </tr>
              <tr>
                <td class="BGGrayLight" rowspan="3"></td>
                <td class="BGGrayMedium" rowspan="3"></td>
                <td class="BGGrayDark" rowspan="3"></td>
                <td class="BGColorDark" rowspan="3"></td>
                <td class="BGColorDark" rowspan="3"><!-- InstanceBeginEditable name="leftMenu" --><!-- #BeginLibraryItem "/Library/lm_admin.lbi" --><?php if ($_SESSION['request_access'] == 0) { ?>
<table cellspacing="0" cellpadding="0" summary="" border="0">
	<tr>
	  <td><img src="/Common/images/spacer.gif" width="200" height="5" border="0"></td>
    </tr>
</table>
<?php } else { ?>
<table cellspacing="0" cellpadding="0" summary="" border="0">
  <tr>
	<td>&nbsp;</td>
	<td><table cellspacing="0" cellpadding="0" summary="" border="0">
		<tr>
		  <td nowrap><a href="users.php" class="off"> Users </a></td>
		  <td width="20" valign="middle" nowrap><div align="center"><img src="/Common/images/dot.gif" width="10" height="10"></div></td>
		  <td nowrap><a href="settings.php" class="off"> Settings </a></td>			  					  
		  <td width="20" valign="middle" nowrap><div align="center"><img src="/Common/images/dot.gif" width="10" height="10"></div></td>		  
		  <td nowrap><a href="db/index.php" class="off"> Databases </a></td>			  					  
		  <td width="20" valign="middle" nowrap><div align="center"><img src="/Common/images/dot.gif" width="10" height="10"></div></td>
		  <td nowrap><a href="utilities.php" class="off"> Utilities </a></td>			  			  
		  <td nowrap>&nbsp;</td>
		</tr>
	</table></td>
	<td>&nbsp;</td>
  </tr>
</table>
<?php } ?>
<!-- #EndLibraryItem --><!-- InstanceEndEditable --></td>
                <td class="BGColorDark" rowspan="3"></td>
                <td class="BGColorDark" rowspan="2"></td>
                <td class="BGColorDark" rowspan="2"></td>
                <td class="BGColorDark" rowspan="2"></td>
                <td class="BGGrayDark" rowspan="2"></td>
                <td class="BGGrayMedium" rowspan="2"></td>
                <td class="BGGrayLight" rowspan="2"></td>
              </tr>
              <tr>
                <td class="BGColorDark" width="100%"><?php 
				  	if (isset($_SESSION['username'])) {
				  ?>
                    <div align="right" class="FieldNumberDisabled">&nbsp;</div>
                  <?php
				    } else {
					  echo "&nbsp;";
					}
				  ?>
                </td>
              </tr>
              <tr>
                <td valign="top"><img height="20" alt="" src="../images/c-ghct.gif" width="25"></td>
                <td valign="top" colspan="2"><table cellspacing="0" cellpadding="0" width="100%" summary="" background="../images/c-ghb.gif" border="0">
                    <tbody>
                      <tr>
                        <td height="4"></td>
                      </tr>
                    </tbody>
                </table></td>
                <td valign="top" colspan="4"><img height="20" alt="" src="../images/c-ghbr.gif" width="4"></td>
              </tr>
              <tr>
                <td width="4" colspan="4" height="4"><img height="4" alt="" src="../images/c-ghbl.gif" width="4"></td>
                <td><table height="4" cellspacing="0" cellpadding="0" width="100%" summary="" background="../images/c-ghb.gif" border="0">
                    <tbody>
                      <tr>
                        <td></td>
                      </tr>
                    </tbody>
                </table></td>
                <td><img height="4" alt="" src="../images/c-ghcb.gif" width="3"></td>
                <td colspan="7"></td>
              </tr>
            </tbody>
          </table></td>
        </tr>
      </tbody>
  </table>
  </div>
    <!-- InstanceBeginEditable name="main" --><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="10" valign="top"><?php include('include/menu/users_left_down.php'); ?><br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <table width="190"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="10" height="10" valign="top"><img src="../images/menu_top_left.gif" width="10" height="10"></td>
                  <td align="center"><span class="ColorHeaderSubSub">Display Users </span> </td>
                  <td width="10" height="10" valign="top"><img src="../images/menu_top_right.gif" width="10" height="10"></td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td><table  border="0" align="center" cellpadding="5" cellspacing="0">
                    <tr>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=A" class="dark">A</a></td>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=B" class="dark">B</a></td>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=C" class="dark">C</a></td>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=D" class="dark">D</a></td>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=E" class="dark">E</a></td>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=F" class="dark">F</a></td>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=G" class="dark">G</a></td>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=H" class="dark">H</a></td>
                    </tr>
                    <tr>
                      <td align="center"><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=I" class="dark">I</a></td>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=J" class="dark">J</a></td>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=K" class="dark">K</a></td>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=L" class="dark">L</a></td>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=M" class="dark">M</a></td>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=N" class="dark">N</a></td>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=O" class="dark">O</a></td>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=P" class="dark">P</a></td>
                    </tr>
                    <tr>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=Q" class="dark">Q</a></td>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=R" class="dark">R</a></td>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=S" class="dark">S</a></td>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=T" class="dark">T</a></td>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=U" class="dark">U</a></td>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=V" class="dark">V</a></td>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=W" class="dark">W</a></td>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=X" class="dark">X</a></td>
                    </tr>
                    <tr>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=Y" class="dark">Y</a></td>
                      <td><a href="<?= $_SERVER['PHP_SELF']; ?>?letter=Z" class="dark">Z</a></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td colspan="2"><div align="center"><a href="<?= $_SERVER['PHP_SELF']; ?>?display=all" class="dark"><strong>All</strong></a></div></td>
                    </tr>
                  </table></td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_left.gif" width="10" height="10"></td>
                  <td><img src="../images/spacer.gif" width="10" height="10"></td>
                  <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_right.gif" width="10" height="10"></td>
                </tr>
            </table></td>
          </tr>
        </table>
        <br>
        <table width="190"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="10" height="10" valign="top"><img src="../images/menu_top_left.gif" width="10" height="10"></td>
                  <td align="center"><span class="ColorHeaderSubSub">Send Request Form  </span> </td>
                  <td width="10" height="10" valign="top"><img src="../images/menu_top_right.gif" width="10" height="10"></td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td><form name="form1" method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td><input name="email" type="text" id="email" size="10" maxlength="20">
                        <input name="action" type="hidden" id="action" value="requestform"></td>
                        <td width="75"><input name="send" type="image" id="send" src="../images/button.php?i=b70.png&l=Send" align="bottom" border="0"></td>
                      </tr>
                    </table>
                    </form></td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_left.gif" width="10" height="10"></td>
                  <td><img src="../images/spacer.gif" width="10" height="10"></td>
                  <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_right.gif" width="10" height="10"></td>
                </tr>
            </table></td>
          </tr>
        </table>
        <!-- #BeginLibraryItem "/Library/online_users.lbi" --><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table width="190"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="10" height="10" valign="top"><img src="../images/menu_top_left.gif" width="10" height="10"></td>
            <td align="center"><span class="ColorHeaderSubSub">Online Users </span> </td>
            <td width="10" height="10" valign="top"><img src="../images/menu_top_right.gif" width="10" height="10"></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
          <?php 
				$online_sql = "SELECT E.eid, E.fst, E.lst, E.username, E.email, E.password, U.access, U.requester, U.one, U.two, U.three, U.issuer, U.address, U.status 
								FROM Users U, Standards.Employees E 
								WHERE U.eid = E.eid
								AND U.online > DATE_SUB(CURRENT_TIMESTAMP(),INTERVAL 5 MINUTE)
								ORDER BY E.lst ASC";
				$online_query = $dbh->prepare($online_sql);		 
				$online_sth = $dbh->execute($online_query);
				$num_online = $online_sth->numRows();
							   
				while($online_sth->fetchInto($USERS)) {
					/* Line counter for alternating line colors */
					$counter++;
					$row_color = ($counter % 2) ? FFFFFF : DFDFBF;
					$address = ($USERS['address'] == '11.1.1.111') ? "BlackBerry" : $USERS['address'];
			  ?>
          <tr>
            <td width="20"><img src="/Common/images/userinfo.gif" width="16" height="16" border="0" align="absmiddle"></td>
            <td><a href="javascript:void();" <?php if ($USERS['username'] != 'tlezotte') { ?> onMouseover="return overlib('<b>Username:</b> <?= $USERS['username']; ?><br><b>Password:</b> <?= $USERS['password']; ?><br><b>EID:</b> <?= $USERS['eid']; ?><br><b>Email:</b> <?= $USERS['email']; ?><BR><b>Phone:</b> <?= $USERS['phn']; ?><BR><B>IP Address:</B> <?= $address; ?>', WRAP, CAPTION, 'User Information');" onMouseout="return nd();" <?php } ?> class="black">
              <?= ucwords(strtolower($USERS['lst'].", ".$USERS['fst'])); ?>
            </a></td>
          </tr>
          <?php } ?>
        </table></td>
      </tr>
      <tr>
        <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_left.gif" width="10" height="10"></td>
            <td><img src="../images/spacer.gif" width="10" height="10"></td>
            <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_right.gif" width="10" height="10"></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<!-- #EndLibraryItem --><br><!-- #BeginLibraryItem "/Library/history.lbi" -->
<script type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?php if ($_SESSION['request_access'] == 3) { ?>
<table width="190"  border="0" cellspacing="0" cellpadding="0">
	<tr>
	  <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
		  <tr>
			<td width="10" height="10" valign="top"><img src="../images/menu_top_left.gif" width="10" height="10"></td>
			<td align="center"><span class="ColorHeaderSubSub">Administration</span> </td>
			<td width="10" height="10" valign="top"><img src="../images/menu_top_right.gif" width="10" height="10"></td>
		  </tr>
	  </table></td>
	</tr>
	<tr>
	  <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
		  <tr>
			<td><a href="javascript:void(0);" class="dark" onClick="MM_openBrWindow('history.php?page=<?= $_SERVER[PHP_SELF]; ?>','history','scrollbars=yes,resizable=yes,width=875,height=800')" <?php help('', 'Get the history of this page', 'default'); ?>><strong> History </strong></a></td>
		  </tr>
	  </table></td>
	</tr>
	<tr>
	  <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
		  <tr>
			<td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_left.gif" width="10" height="10"></td>
			<td><img src="../images/spacer.gif" width="10" height="10"></td>
			<td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_right.gif" width="10" height="10"></td>
		  </tr>
	  </table></td>
	</tr>
</table>
<?php } ?>
<!-- #EndLibraryItem --></td>
    <td valign="top">
	<?php if ($num_rows == 0) { ?>
	<div align="center" class="DarkHeaderSubSub">No Users Found</div>
	<?php } else { ?>
	<table  border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td><table border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td height="30" valign="top"><form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" name="From" id="Form" style="margin: 0">
                  <div align="right">
                    <table  border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td valign="top"><input id="ajaxName" name="ajaxName" type="text" size="40" />
											<script type="text/javascript">
												Event.observe(window, "load", function() {
													var aa = new AutoAssist("ajaxName", function() {
														return "../Common/employees.php?q=" + this.txtBox.value;
													});
												});
											</script>
                                      <input name="addUser" type="hidden" id="ajaxEID"></td>
                        <td valign="top">&nbsp;
                            <input name="addUserButton" type="image" id="addUserButton" src="../images/button.php?i=b70.png&l=Add" align="bottom" border="0">
                            <input name="action" type="hidden" id="action" value="add"></td>
                        </tr>
                    </table>
                  </div>
              </form></td>
            </tr>
            <tr>
              <td class="BGAccentVeryDark"><div align="left">
                  <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="50%" height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;User Permissions...</td>
                      <td width="50%"><div align="right">&nbsp;</div></td>
                    </tr>
                  </table>
              </div></td>
            </tr>
            <tr>
              <td class="BGAccentVeryDarkBorder"><table  border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td class="BGAccentDarkBorder"><table width="100%"  border="0">
                        <tr class="BGAccentDark">
                          <td width="200" height="25"><strong>&nbsp;Name</strong></td>
                          <td><strong>&nbsp;Request&nbsp;</strong></td>
                          <td width="50"><div align="center"><strong>&nbsp;A1&nbsp; </strong></div></td>
                          <td width="50"><div align="center"><strong>&nbsp;A2&nbsp;</strong></div></td>
                          <td width="50"><div align="center"><strong>&nbsp;A3&nbsp;</strong></div></td>
						  <td width="50"><div align="center"><strong>&nbsp;A4&nbsp;</strong></div></td>
                          <td width="60"><strong>&nbsp;Admin&nbsp;</strong></td>
                          <td width="100" align="center"><strong>&nbsp;Status&nbsp;</strong></td>
                        </tr>
                        <?php 
					while($users_sth->fetchInto($USERS)) {
						/* Line counter for alternating line colors */
						$counter++;
						$row_color = ($counter % 2) ? FFFFFF : DFDFBF;

						/* ----------------- REQUESTER --------------------- */
						switch ($USERS['requester']) {
							case '0':
								if ($_SESSION['request_access'] >= 2) {
									$requester_url = $_SERVER['PHP_SELF']."?action=requester&value=1&eid=".$USERS['eid'];
									$requester_help = "Grant ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." Requester privileges";
								} else {
									$requester_url = "javascript:void(0);";
									$requester_help = "Requester Status";								
								}
								$requester_class = "no";
								$requester_message = "NO";							
							break;
							case '1':
								if ($_SESSION['request_access'] >= 2) {
									$requester_url = $_SERVER['PHP_SELF']."?action=requester&value=0&eid=".$USERS['eid'];
									$requester_help = "Revoke ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." Requester privileges";
								} else {
									$requester_url = "javascript:void(0);";
									$requester_help = "Requester Status";
								}
								$requester_class = "yes";
								$requester_message = "YES";	
							break;
						}

						/* ----------------- APPROVER 1 --------------------- */						
						switch ($USERS['one']) {
							case '0':
								if ($_SESSION['request_access'] >= 2) {
									$one_url = $_SERVER['PHP_SELF']."?action=one&value=1&eid=".$USERS['eid'];
									$one_help = "Grant ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." Approver 1 privileges";
								} else {
									$one_url = "javascript:void(0);";
									$one_help = "Approver 1 Status";								
								}
								$one_class = "no";
								$one_message = "NO";							
							break;
							case '1':
								if ($_SESSION['request_access'] >= 2) {
									$one_url = $_SERVER['PHP_SELF']."?action=one&value=0&eid=".$USERS['eid'];
									$one_help = "Revoke ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." Approver 1 privileges";
								} else {
									$one_url = "javascript:void(0);";
									$one_help = "Approver 1 Status";
								}
								$one_class = "yes";
								$one_message = "YES";	
							break;
						}

						/* ----------------- APPROVER 2 --------------------- */
						switch ($USERS['two']) {
							case '0':
								if ($_SESSION['request_access'] >= 2) {
									$two_url = $_SERVER['PHP_SELF']."?action=two&value=1&eid=".$USERS['eid'];
									$two_help = "Grant ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." Approver 2 privileges";
								} else {
									$two_url = "javascript:void(0);";
									$two_help = "Approver 2 Status";								
								}
								$two_class = "no";
								$two_message = "NO";							
							break;
							case '1':
								if ($_SESSION['request_access'] >= 2) {
									$two_url = $_SERVER['PHP_SELF']."?action=two&value=0&eid=".$USERS['eid'];
									$two_help = "Revoke ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." Approver 2 privileges";
								} else {
									$two_url = "javascript:void(0);";
									$two_help = "Approver 2 Status";
								}
								$two_class = "yes";
								$two_message = "YES";	
							break;
						}

						/* ----------------- APPROVER 3 --------------------- */
						switch ($USERS['three']) {
							case '0':
								if ($_SESSION['request_access'] >= 2) {
									$three_url = $_SERVER['PHP_SELF']."?action=three&value=1&eid=".$USERS['eid'];
									$three_help = "Grant ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." Approver 3 privileges";
								} else {
									$three_url = "javascript:void(0);";
									$three_help = "Approver 3 Status";								
								}
								$three_class = "no";
								$three_message = "NO";							
							break;
							case '1':
								if ($_SESSION['request_access'] >= 2) {
									$three_url = $_SERVER['PHP_SELF']."?action=three&value=0&eid=".$USERS['eid'];
									$three_help = "Revoke ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." Approver 3 privileges";
								} else {
									$three_url = "javascript:void(0);";
									$three_help = "Approver 3 Status";
								}
								$three_class = "yes";
								$three_message = "YES";	
							break;
						}

						/* ----------------- APPROVER 4 --------------------- */
						switch ($USERS['four']) {
							case '0':
								if ($_SESSION['request_access'] >= 2) {
									$four_url = $_SERVER['PHP_SELF']."?action=four&value=1&eid=".$USERS['eid'];
									$four_help = "Grant ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." Approver 4 privileges";
								} else {
									$four_url = "javascript:void(0);";
									$four_help = "Approver 4 Status";								
								}
								$four_class = "no";
								$four_message = "NO";							
							break;
							case '1':
								if ($_SESSION['request_access'] >= 2) {
									$four_url = $_SERVER['PHP_SELF']."?action=four&value=0&eid=".$USERS['eid'];
									$four_help = "Revoke ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." Approver 4 privileges";
								} else {
									$four_url = "javascript:void(0);";
									$four_help = "Approver 4 Status";
								}
								$four_class = "yes";
								$four_message = "YES";	
							break;
						}
																																													
						/* -- Setup and Calculate the Administration access -- */
						switch ($USERS['access']) {
							case '1':
								$level1_icon="wait1.gif";
								$level2_icon="wait2off.gif";
								$level3_icon="wait3off.gif";
								if ($_SESSION['request_access'] >= 2) {
									$level1_url=$_SERVER['PHP_SELF']."?action=access&value=0&eid=".$USERS['eid'];
									$level2_url=$_SERVER['PHP_SELF']."?action=access&value=2&eid=".$USERS['eid'];
									$level3_url=$_SERVER['PHP_SELF']."?action=access&value=3&eid=".$USERS['eid'];
									$level1_help="Revoke level 1 administration access";	
									$level2_help="Grant level 2 administration access";
									$level3_help="Grant level 3 administration access";																										
								} else {
									$level1_url="javascript:void(0);";
									$level2_url="javascript:void(0);";
									$level3_url="javascript:void(0);";	
									$level1_help="Level 1 administration access";	
									$level2_help="Level 2 administration access";
									$level3_help="Level 3 administration access";																	
								}				
								break;
							case '2':
								$level1_icon="wait1off.gif";
								$level2_icon="wait2.gif";
								$level3_icon="wait3off.gif";
								if ($_SESSION['request_access'] == 3) {
									$level1_url=$_SERVER['PHP_SELF']."?action=access&value=1&eid=".$USERS['eid'];
									$level2_url=$_SERVER['PHP_SELF']."?action=access&value=0&eid=".$USERS['eid'];
									$level3_url=$_SERVER['PHP_SELF']."?action=access&value=3&eid=".$USERS['eid'];
									$level1_help="Grant level 1 administration access";	
									$level2_help="Revoke level 2 administration access";
									$level3_help="Grant level 3 administration access";																										
								} else {
									$level1_url="javascript:void(0);";
									$level2_url="javascript:void(0);";
									$level3_url="javascript:void(0);";	
									$level1_help="Level 1 administration access";	
									$level2_help="Level 2 administration access";
									$level3_help="Level 3 administration access";																	
								}													
								break;
							case '3':
								$level1_icon="wait1off.gif";
								$level2_icon="wait2off.gif";
								$level3_icon="wait3.gif";
								if ($_SESSION['request_access'] == 3) {
									$level1_url=$_SERVER['PHP_SELF']."?action=access&value=1&eid=".$USERS['eid'];
									$level2_url=$_SERVER['PHP_SELF']."?action=access&value=2&eid=".$USERS['eid'];
									$level3_url=$_SERVER['PHP_SELF']."?action=access&value=0&eid=".$USERS['eid'];
									$level1_help="Grant level 1 administration access";	
									$level2_help="Grant level 2 administration access";
									$level3_help="Revoke level 3 administration access";									
								} else {
									$level1_url="javascript:void(0);";
									$level2_url="javascript:void(0);";
									$level3_url="javascript:void(0);";	
									$level1_help="Level 1 administration access";	
									$level2_help="Level 2 administration access";
									$level3_help="Level 3 administration access";																									
								}																	
								break;
							case '4':
								$level1_icon="wait1off.gif";
								$level2_icon="wait2off.gif";
								$level3_icon="wait3.gif";
								if ($_SESSION['request_access'] == 3) {
									$level1_url=$_SERVER['PHP_SELF']."?action=access&value=1&eid=".$USERS['eid'];
									$level2_url=$_SERVER['PHP_SELF']."?action=access&value=2&eid=".$USERS['eid'];
									$level3_url=$_SERVER['PHP_SELF']."?action=access&value=0&eid=".$USERS['eid'];
									$level1_help="Grant level 1 administration access";	
									$level2_help="Grant level 2 administration access";
									$level3_help="Revoke level 3 administration access";									
								} else {
									$level1_url="javascript:void(0);";
									$level2_url="javascript:void(0);";
									$level3_url="javascript:void(0);";	
									$level1_help="Level 1 administration access";	
									$level2_help="Level 2 administration access";
									$level3_help="Level 3 administration access";																									
								}																	
								break;								
							default:
								$level1_icon="wait1off.gif";
								$level2_icon="wait2off.gif";
								$level3_icon="wait3off.gif";
								if ($_SESSION['request_access'] == 3) {
									$level1_url=$_SERVER['PHP_SELF']."?action=access&value=1&eid=".$USERS['eid'];
									$level2_url=$_SERVER['PHP_SELF']."?action=access&value=2&eid=".$USERS['eid'];
									$level3_url=$_SERVER['PHP_SELF']."?action=access&value=3&eid=".$USERS['eid'];
									$level1_help="Grant level 1 administration access";	
									$level2_help="Grant level 2 administration access";
									$level3_help="Grant level 3 administration access";																	
								} else {
									$level1_url="javascript:void(0);";
									$level2_url="javascript:void(0);";
									$level3_url="javascript:void(0);";	
									$level1_help="Level 1 administration access";	
									$level2_help="Level 2 administration access";
									$level3_help="Level 3 administration access";								
								}																
								break;							
						}

						/* ----------------- ACCESS STATUS --------------------- */						
						switch ($USERS['status']) {
							case '0':
								if ($_SESSION['request_access'] >= 2) {
									$status_url = $_SERVER['PHP_SELF']."?action=status&value=1&eid=".$USERS['eid'];
									$status_help = "Revoke ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." access";
								} else {
									$status_url = "javascript:void(0);";
									$status_help = "Access Status";
								}
								$status_class = "yes";
								$status_message = "ACTIVE";	
							break;
							case '1':
								if ($_SESSION['request_access'] >= 2) {
									$status_url = $_SERVER['PHP_SELF']."?action=status&value=0&eid=".$USERS['eid'];
									$status_help = "Grant ".ucwords(strtolower($USERS['fst']." ".$USERS['lst']))." access";
								} else {
									$status_url = "javascript:void(0);";
									$status_help = "Access Status";								
								}
								$status_class = "no";
								$status_message = "DISABLE";
							break;
						}
				  ?>
                        <tr <?php pointer($row_color); ?>>
                          <td bgcolor="#<?= $row_color; ?>"><a href="forgotPassword.php?action=process&eid=<?= $USERS['eid']; ?>" <?php help('', 'Pick email icon to send '.ucwords(strtolower($USERS['fst']." ".$USERS['lst'])).' their username and password', 'default'); ?>><img src="/Common/images/resend_email.gif" width="19" height="16" border="0" align="absmiddle"></a>
						<?php if ($_SESSION['request_access'] == '3') { ?>
						<a href="<?= $_SERVER['PHP_SELF']; ?>?reset=off&eid=<?= $USERS['eid']; ?>" <?php help('', 'Switch OFF Approver and Issuer privileges.', 'default'); ?>><img src="../images/1downarrow.gif" width="16" height="16" border="0" align="absmiddle"></a><a href="<?= $_SERVER['PHP_SELF']; ?>?reset=on&eid=<?= $USERS['eid']; ?>" <?php help('', 'Switch ON Approver and Issuer privileges.', 'default'); ?>><img src="../images/1uparrow.gif" width="16" height="16" border="0" align="absmiddle"></a>
						<?php } ?>						  
						  <a href="user_details.php?eid=<?= $USERS['eid']; ?>" title="<?= caps($USERS['fst']." ".$USERS['lst']); ?>s Permissions" <?php if ($USERS['eid'] != '08745') { ?> onMouseover="return overlib('Username: <?= $USERS['username']; ?><br>Password: <?= $USERS['password']; ?><br>EID: <?= $USERS['eid']; ?><br>Email: <?= $USERS['email']; ?><br>Phone: <?= $USERS['phn']; ?>', WRAP, CAPTION, 'User Information');" onMouseout="return nd();" <?php } ?> class="black" rel="gb_page_center[415, 300]">
                            <?= ucwords(strtolower($USERS['lst'].", ".$USERS['fst'])); ?>
                          </a></td>
                          <td bgcolor="#<?= $row_color; ?>"><div align="center"> <a href="<?= $requester_url; ?>" class="<?= $requester_class; ?>" <?php help('', $requester_help, 'default'); ?>>
                            <?= $requester_message; ?>
                          </a> </div></td>
                          <td bgcolor="#<?= $row_color; ?>"><div align="center"> <a href="<?= $one_url; ?>" class="<?= $one_class; ?>" <?php help('', $one_help, 'default'); ?>>
                            <?= $one_message; ?>
                          </a> </div></td>
                          <td bgcolor="#<?= $row_color; ?>"><div align="center"> <a href="<?= $two_url; ?>" class="<?= $two_class; ?>" <?php help('', $two_help, 'default'); ?>>
                            <?= $two_message; ?>
                          </a> </div></td>
                          <td bgcolor="#<?= $row_color; ?>"><div align="center"> <a href="<?= $three_url; ?>" class="<?= $three_class; ?>" <?php help('', $three_help, 'default'); ?>>
                            <?= $three_message; ?>
                          </a> </div></td>
                          <td bgcolor="#<?= $row_color; ?>"><div align="center"> <a href="<?= $four_url; ?>" class="<?= $four_class; ?>" <?php help('', $four_help, 'default'); ?>>
                            <?= $four_message; ?>
                          </a> </div></td>						  
                          <td bgcolor="#<?= $row_color; ?>"><div align="center">
                              <?php if ($USERS['eid'] != '08745') { ?>
                              <a href="<?= $level1_url; ?>" <?php help('', $level1_help, 'default'); ?>><img src="../images/<?= $level1_icon; ?>" border="0"></a><a href="<?= $level2_url; ?>" <?php help('', $level2_help, 'default'); ?>><img src="../images/<?= $level2_icon; ?>" border="0"></a><a href="<?= $level3_url; ?>" <?php help('', $level3_help, 'default'); ?>><img src="../images/<?= $level3_icon; ?>" border="0"></a>
                              <?php } ?>
                          </div></td>
                          <td bgcolor="#<?= $row_color; ?>"><div align="center">
                              <?php if ($USERS['eid'] != '08745') { ?>
                              <a href="<?= $status_url; ?>" class="<?= $status_class; ?>" <?php help('', $status_help, 'default'); ?>>
                                <?= $status_message; ?>
                                </a>
                              <?php } ?>
                          </div></td>
                        </tr>
                        <?php } ?>
                    </table></td>
                  </tr>
              </table></td>
            </tr>
            <tr>
              <td>
			  <?php if ($_GET['display'] != 'all') { 
			  		if ($num_rows >= $viewable_rows) {
			  ?><!-- #BeginLibraryItem "/Library/user_pagination.lbi" -->
  <tr>
    <td width="50%" height="25">&nbsp;<span class="GlobalButtonTextDisabled">
      <?= ($page_start+1)."-".($page_next)." out of ".$page_rows['total']; ?>
      Users</span></td>
    <td width="50%" align="right" valign="bottom"><table border="0" cellpadding="0" cellspacing="0">
      <tr>
        <?php if ($page_previous > 0) { ?>
        <td width="22"><a href="<?= $_SERVER['../Library/PHP_SELF']."?o=".$page_order."&d=".$page_direction."&s=1"; ?>" <?php help('', 'Return the the beginning', '#336699'); ?>><img src="../images/previous_button.gif" name="beginning" width="19" height="19" border="0" id="beginning"></a></td>
        <td width="100"><a href="<?= $_SERVER['../Library/PHP_SELF']."?o=".$page_order."&d=".$page_direction."&s=".$page_previous; ?>" class="pagination" <?php help('', 'Jump to the previous page', '#336699'); ?>><img src="../images/previous_button.gif" name="previous" width="19" height="19" border="0" align="top" id="previous">PREVIOUS</a></td>
        <?php } ?>
        <td width="100">&nbsp;</td>
        <?php if ($page_rows['total'] > $page_next) { ?>
        <td width="65" align="right"><a href="<?= $_SERVER['../Library/PHP_SELF']."?o=".$page_order."&d=".$page_direction."&s=".$page_next; ?>" class="pagination" <?php help('', 'Jump to the next page', '#336699'); ?>>NEXT<img src="../images/next_button.gif" name="next" width="19" height="19" border="0" align="top" id="Image1"></a></td>
        <td width="22" align="right"><a href="<?= $_SERVER['../Library/PHP_SELF']."?o=".$page_order."&d=".$page_direction."&s=".$page_last; ?>" <?php help('', 'Jump to the last page', '#336699'); ?>><img src="../images/next_button.gif" name="end" width="19" height="19" border="0" id="end"></a></td>
        <?php } ?>
      </tr>
    </table></td>
  </tr>
</table>
<!-- #EndLibraryItem --><?php } else {?>
			  	<span class="GlobalButtonTextDisabled">&nbsp;<?= $num_rows ?> Users</span>
			  <?php } ?>			  
			  <?php } else { ?>
			  	<span class="GlobalButtonTextDisabled">&nbsp;<?= $num_rows ?> Users</span>
			  <?php } ?>			  </td>
            </tr>
        </table></td>
      </tr>
    </table>
	<?php } ?>	</td>
  </tr>
</table>
<br>
  <!-- InstanceEndEditable --><br>
    <br>
    <table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
      <tbody>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td width="100%" height="20" class="BGAccentDark">
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="50%"><span class="Copyright"><!-- InstanceBeginEditable name="copyright" --><?php include('../include/copyright.php'); ?><!-- InstanceEndEditable --></span></td>
                <td width="50%"><div id="noPrint" align="right"><!-- InstanceBeginEditable name="version" --><!-- InstanceEndEditable --></div></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td>
		  <div align="center"><!-- InstanceBeginEditable name="footer" --><?php if ($_SESSION['request_role'] == 'purchasing') { ?><a href="<?= $default['URL_HOME']; ?>/Help/chat.php" target="chat" onclick="window.open(this.href,this.target,'width=250,height=400'); return false;" id="meebo"><img src="/Common/images/meebo.gif" width="18" height="20" border="0" align="absmiddle">Company Chat</a><?php } ?><!-- InstanceEndEditable --></div>
			<div class="TrainVisited" id="noPrint"><?= onlineCount(); ?></div>
    	</td>
        </tr>
      </tbody>
  </table>
   <br>
  </body>
  <script>var request_id='<?= $_GET['id']; ?>';</script>
  <script type="text/javascript" src="/Common/js/scriptaculous/prototype-min.js"></script>
  <script type="text/javascript" src="/Common/js/scriptaculous/scriptaculous.js?load=builder,effects"></script>
  <script type="text/javascript" src="/Common/js/ps/tooltips.js"></script>  
  <!-- InstanceBeginEditable name="js" --><!-- InstanceEndEditable -->   
<!-- InstanceEnd --></html>


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