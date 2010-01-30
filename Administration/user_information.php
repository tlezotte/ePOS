<?php 
/**
 * Request System
 *
 * index.php main Administration page.
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
require_once('../Connections/connStandards.php'); 
/**
 * - Check User Access
 */
require_once('../security/check_user.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 
/**
 * - Form Validation
 */
include('vdaemon/vdaemon.php');


/* Set Cookie Expiration */
$cookie_days = 90;
$cookie_expire = time()+60*60*24*$cookie_days;


/**
 * - Process $_POST['action']
 */
switch ($_POST['action']) {
	case 'update':
		$sql="UPDATE Employees SET dept='" . $_POST['dept'] . "', 
									shift='" . $_POST['shift'] . "', 
									phn='" . $_POST['phn'] . "', 
									lst='" . $_POST['lst'] . "', 
									fst='" . $_POST['fst'] . "', 
									mdl='" . $_POST['mdl'] . "', 
									Job_Description='" . $_POST['Job_Description'] . "', 
									Location='" . $_POST['Location'] . "', 
									email='" . $_POST['email'] . "' 
			   WHERE eid='" . $_SESSION['eid'] . "'";		   
		$dbh_standards->query($sql);
		
		/* ----- Record transaction for history ----- */
		History($_SESSION['eid'], $_POST['action'], $_SERVER['PHP_SELF'], addslashes(htmlentities($sql)));							   
	break;

	case 'settings':
		$USER=$dbh->getRow("SELECT * FROM Users WHERE eid='" . $_SESSION['eid'] . "'");
//		print_r($USER); echo "<br><br>";
		/* ----- Set vacation mode on or off ----- */
		$sql="UPDATE Users SET vacation='" . $_POST['vacation'] . "' WHERE eid='" . $_SESSION['eid'] . "'";
		$dbh->query($sql);
		
//		echo "CURRENT: " . $USER['vacation'] . "=" . strlen($USER['vacation']) . "<br>";
//		echo "VACATION: " . $_POST['vacation'] . "=" . strlen($_POST['vacation']) . "<br>";
		
		/* ----- Convert current Requisitions to DofA user ----- */
		if (strlen($USER['vacation']) == 5 AND $_POST['vacation'] == '0') {
//		echo "STOP<br>";
			stopDelegate($USER['vacation'], $_SESSION['eid']);									// Turn off vacation
		} elseif ($USER['vacation'] == '0' AND strlen($_POST['vacation']) == 5) {
//		echo "START<br>";
			startDelegate($_SESSION['eid'], $_POST['vacation']);								// Turn on vacation
		}

		/* ----- Set Vacation mode ----- */
		if (strlen($_POST['vacation']) == 5) {
//		echo "COOKIE ON<br>";
			setcookie(request_vacation, $_POST['vacation'], $cookie_expire);			// Set vacation mode
		} else {
//		echo "COOKIE OFF<br>";
			setcookie(request_vacation, $_POST['vacation'], time() - 3600);			// Turn off vacation mode
		}
		
		/* ----- Record transaction for history ----- */
		History($_SESSION['eid'], $_POST['action'], $_SERVER['PHP_SELF'], addslashes(htmlentities($sql)));	
	break;
		
	case 'changepassword':
		$sql="UPDATE Employees SET password='" . $_POST['newpassword1'] . "' WHERE eid='" . $_SESSION['eid'] . "'";
		$dbh_standards->query($sql);	
		
		/* ----- Record transaction for history ----- */
		History($_SESSION['eid'], $_POST['action'], $_SERVER['PHP_SELF'], addslashes(htmlentities($sql)));			
	break;
}


/* ---------------------------------------------------
 * -------------- START DATABASE ACCESS -------------- 
 * --------------------------------------------------- 
 */
/* ---------- GET EMPLOYEE INFORMATION ---------- */
$INFO = $dbh_standards->getRow("SELECT * 
								FROM Employees e
								  INNER JOIN Request.Users u ON u.eid=e.eid
								WHERE e.eid='" . $_SESSION['eid'] . "'");
/* ---------- Get Plant information ---------- */
$plants_sql = $dbh->prepare("SELECT id, name
						     FROM Standards.Plants
						     WHERE status = '0'
						     ORDER BY name");
/* ---------- Get Department information ---------- */							 
$dept_sql = $dbh->prepare("SELECT id, name 
						   FROM Standards.Department 
						   WHERE status = '0' 
						   ORDER BY name");
/* ---------- Get Current Users ---------- */						   
$emp_sql = $dbh->prepare("SELECT E.eid, E.fst, E.lst
					   	 FROM Users U
						  INNER JOIN Standards.Employees E ON U.eid = E.eid
						 WHERE E.status = '0' AND U.status = '0'
					   	 ORDER BY E.lst");
/* ---------------------------------------------------
 * -------------- END DATABASE ACCESS -------------- 
 * --------------------------------------------------- 
 */
 

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
  <script type="text/JavaScript">
<!--
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>
  <!-- InstanceEndEditable -->
  <?php if ($ONLOAD_OPTIONS) { ?>
  <script language="javascript">
	AJS.AEV(window, "load", <?= $ONLOAD_OPTIONS; ?>);
  </script>
  <?php } ?>
  </head>

  <body class="yui-skin-sam" onLoad="MM_preloadImages('../images/button.php?i=inputField.png&amp;l=<?= $INFO[password]; ?>')">  
    <img src="/Common/images/CompanyPrint.gif" alt="Your Company" width="437" height="61" id="Print" />
	<div id="noPrint">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" summary="">
      <tbody>
        <tr>
          <td valign="top"><a href="../home.php" title="<?= $default['title1']; ?> Home"><img name="Company" src="/Common/images/Company.gif" width="300" height="50" border="0"></a></td>
          <td align="right" valign="top">
          <!-- InstanceBeginEditable name="topRightMenu" --><!-- InstanceEndEditable --></td>
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
    <!-- InstanceBeginEditable name="main" -->
<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="200" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><!-- #BeginLibraryItem "/Library/user_admin.lbi" --><table cellspacing="0" cellpadding="0" width="200" align="left" summary="" border="0">
    <tr>
      <td valign="top" width="13" background="../images/asyltlb.gif"><img height="20" alt="" src="../images/t.gif" width="13" border="0"></td>
      <td valign="top" width="165" bgcolor="#cccc99"><img height="1" alt="" src="../images/asybase.gif" width="145" border="0"> <br>
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection"><a href="user_information.php" class="dark">Your Information </a></td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection"><a href="user_information.php#password" class="dark">Change Password </a></td>
            </tr>
          </table>
		  <!--
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection"><a href="../Administration/vacation.php" class="dark">Vacation</a></td>
            </tr>
          </table>--></td>
      <td valign="top" width="22" background="../images/asyltrb.gif"><img height="20" alt="" src="../images/t.gif" width="22" border="0"></td>
    </tr>
    <tr>
      <td valign="top" width="22" colspan="3"><img height="37" alt="" src="../images/asyltb.gif" width="200" border="0"></td>
    </tr>
</table>
<!-- #EndLibraryItem --></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><!-- #BeginLibraryItem "/Library/history.lbi" -->
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
      </tr>
    </table></td>
    <td align="center">
	<form name="Form" method="post" action="<?= $_SERVER['PHP_SELF']; ?>" runat="vdaemon">
    <br>
    <br>
    <table border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td class="BGAccentVeryDark"><div align="left">
              <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="50%" height="30" class=
                                  "DarkHeaderSubSub">&nbsp;&nbsp;Your Information...</td>
                  <td width="50%"><div align="left"> </div></td>
                </tr>
              </table>
          </div></td>
        </tr>
        <tr>
          <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0">
              <tr>
                <td>Employee ID:</td>
                <td><?= $INFO['eid']; ?></td>
              </tr>
              <tr>
                <td>Name:</td>
                <td><input name="fst" type="text" id="fst" size="20" maxlength="20" value="<?= $INFO['fst']; ?>">
                  <vlvalidator name="fst" type="required" control="fst" errmsg="Your first name is required.">
                    <input name="mdl" type="text" id="mdl" size="5" maxlength="10" value="<?= $INFO['mdl']; ?>">
                    <input name="lst" type="text" id="lst" size="30" maxlength="30" value="<?= $INFO['lst']; ?>">
                    <vlvalidator name="lst" type="required" control="lst" errmsg="Your last name is required."></td>
              </tr>
              <tr>
                <td>Email:</td>
                <td><input name="email" type="text" id="email" size="50" maxlength="50" value="<?= $INFO['email']; ?>">
                  <vlvalidator name="email" type="email" control="email" errmsg="Email address is incorrect."></td>
              </tr>
              <tr>
                <td>Plant:</td>
                <td><select name="Location">
                    <option value="0">Select One</option>
                    <?php
				  $plant_sth = $dbh->execute($plants_sql);
				  while($plant_sth->fetchInto($PLANTS)) {
					$selected = ($INFO['Location'] == $PLANTS[id]) ? selected : $blank;
					print "<option value=\"".$PLANTS[id]."\" ".$selected.">".ucwords(strtolower($PLANTS[name]))."</option>\n";
				  }
				  ?>
                </select></td>
              </tr>
              <tr>
                <td>Department:</td>
                <td><select name="dept" id="dept">
                    <option value="0">Select One</option>
                    <?php
				  $dept_sth = $dbh->execute($dept_sql);
				  while($dept_sth->fetchInto($DEPT)) {
					$selected = ($INFO['dept'] == $DEPT[id]) ? selected : $blank;
					print "<option value=\"".$DEPT[id]."\" ".$selected.">(".$DEPT[id].") ".ucwords(strtolower($DEPT[name]))."</option>\n";
				  }
				  ?>
                </select></td>
              </tr>
              <tr>
                <td>Shift: </td>
                <td><select name="shift">
                    <option value="1" <?= ($INFO['shift'] == '1') ? selected : $blank; ?>>First</option>
                    <option value="2" <?= ($INFO['shift'] == '2') ? selected : $blank; ?>>Second</option>
                    <option value="3" <?= ($INFO['shift'] == '3') ? selected : $blank; ?>>Third</option>
                  </select>                </td>
              </tr>
              <tr>
                <td>Job Description: </td>
                <td><input name="Job_Description" type="text" id="Job_Description" size="40" maxlength="40" value="<?= $INFO['Job_Description']; ?>"></td>
              </tr>
              <tr>
                <td>Phone:</td>
                <td><input name="phn" type="text" id="phn" size="15" maxlength="15" value="<?= $INFO['phn']; ?>"></td>
              </tr>
              <tr>
                <td>Hire Date: </td>
                <td><?= date("F d, Y", strtotime($INFO['hire'])); ?></td>
              </tr>
              
          </table></td>
        </tr>
        <tr>
          <td height="5"><img src="../images/spacer.gif" width="5" height="5"></td>
        </tr>
        <tr>
          <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><a href="authorization.php">&nbsp;</a></td>
                <td><div align="right">
                    <input name="action" type="hidden" id="action" value="update">
                    <input name="imageField" type="image" src="../images/button.php?i=b70.png&l=Update" alt="Update" border="0">
                  &nbsp; </div></td>
              </tr>
          </table></td>
        </tr>
        <tr>
          <td><vlsummary class="valErrorList" headertext="Error(s) found:" displaymode="bulletlist"></td>
        </tr>
      </table>
	</form>
	  <br>
	  <br>
	  <br>
	  <form name="Form2" method="post" action="<?= $_SERVER['PHP_SELF']; ?>" runat="vdaemon">
        <br>
        <table border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td class="BGAccentVeryDark"><div align="left">
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td width="50%" height="30" nowrap class="DarkHeaderSubSub">&nbsp;&nbsp;<a name="settings" id="settings"></a>Settings...</td>
                    <td width="50%"><div align="left"> </div></td>
                  </tr>
                </table>
            </div></td>
          </tr>
          <tr>
            <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0">
                <tr>
                  <td nowrap>Delegation of Authority / Vacation:</td>
                  <td><select name="vacation" id="vacation">
                    <option value="0" class="highlightOption">Off</option>
                    <?php
					  $emp_sth = $dbh->execute($emp_sql);
					  while($emp_sth->fetchInto($EMPLOYEE)) {
						$selected = ($INFO['vacation'] == $EMPLOYEE['eid']) ? selected : $blank;
						print "<option value=\"" . $EMPLOYEE['eid'] . "\" ".$selected.">" . caps($EMPLOYEE[lst].", ".$EMPLOYEE[fst]) . "</option>";
					  }
					?>
					</select></td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td height="5"><img src="../images/spacer.gif" width="5" height="5"></td>
          </tr>
          <tr>
            <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td><a href="authorization.php">&nbsp;</a></td>
                  <td><div align="right">
                      <input name="action" type="hidden" id="action" value="settings">
                      <input name="imageField2" type="image" src="../images/button.php?i=b70.png&l=Change" alt="Save" border="0">
                    &nbsp; </div></td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
        </table>
      </form>
	  <br>
	  <br>
	  <br>
    <form name="Form3" method="post" action="<?= $_SERVER['PHP_SELF']; ?>" runat="vdaemon">
      <br>
      <table border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td class="BGAccentVeryDark"><div align="left">
              <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="50%" height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;<a name="password"></a>Change Password...</td>
                  <td width="50%"><div align="left"> </div></td>
                </tr>
              </table>
          </div></td>
          </tr>
        <tr>
          <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0">
              
              <tr>
                <td>Current Password:</td>
                <td><img src="../images/button.php?i=inputField.png&l=Mouseover&c=warn" width="146" height="22" id="Image11" onMouseOver="MM_swapImage('Image11','','../images/button.php?i=inputField.png&amp;l=<?= $INFO[password]; ?>',1)" onMouseOut="MM_swapImgRestore()"></td>
              </tr>
              <tr>
                <td><vllabel form="Form3" validators="newpassword1" errclass="valError">New Password:</vllabel></td>
                <td><input name="newpassword1" type="password" id="newpassword1" size="20" maxlength="20">
                  <vlvalidator name="newpassword1" type="required" control="newpassword1" errmsg="New Password requires 5-20 characters." minlength="5">
                  <vlvalidator name="PassCmp" type="compare" control="newpassword1" errmsg="Both Password fields must be equal" validtype="string" comparecontrol="newpassword2" operator="e"></td>
              </tr>
              <tr>
                <td nowrap><vllabel form="Form3" validators="newpassword2" errclass="valError">Confirm New Password:</vllabel></td>
                <td><input name="newpassword2" type="password" id="newpassword2" size="20" maxlength="20">
                  <vlvalidator name="newpassword2" type="required" control="newpassword2" errmsg="Change New Password requires 5-20 characters ." minlength="5"></td>
              </tr>
          </table></td>
          </tr>
        <tr>
          <td height="5"><img src="../images/spacer.gif" width="5" height="5"></td>
          </tr>
        <tr>
          <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><a href="authorization.php">&nbsp;</a></td>
                <td><div align="right">
                  <input name="action" type="hidden" id="action" value="changepassword">
                    <input name="imageField3" type="image" src="../images/button.php?i=b70.png&l=Change" alt="Change" border="0">
                  &nbsp; </div></td>
              </tr>
          </table></td>
          </tr>
        <tr>
          <td><vlsummary class="valErrorList" headertext="Error(s) found:" displaymode="bulletlist"></td>
          </tr>
      </table>
    </form>
    <br>
    </td>
    <td width="200" align="left" valign="top">&nbsp;</td>
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
                <td width="50%"><div id="noPrint" align="right"><!-- InstanceBeginEditable name="version" --><!-- #BeginLibraryItem "/Library/versionadmin.lbi" --><script type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<table cellspacing="0" cellpadding="0" summary="" border="0">
  <tbody>
    <tr>
      <td class="DarkHeaderSubSub">&nbsp;<a href="javascript:void(0);" onClick="MM_openBrWindow('../Help/releasenotes.php','help','scrollbars=yes,resizable=yes,width=800,height=800')" class="dark">v1.0</a></td>
      <td width="20" class="DarkHeaderSubSub"><div align="right"><a href="javascript:void(0);" onClick="MM_openBrWindow('../Help/releasenotes.php','help','scrollbars=yes,resizable=yes,width=800,height=800')"><img src="../images/notes.gif" alt="Release Notes" width="12" height="15" border="0" align="absmiddle"></a></div></td>
    </tr>
  </tbody>
</table>
<!-- #EndLibraryItem --><!-- InstanceEndEditable --></div></td>
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