<?php 
/**
 * Request System
 *
 * index.php is Login page.
 *
 * @version 1.5
 * @link http://www.yourdomain.com/go/Request/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @global mixed $default[]
  * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 */


/**
 * - Forward BlackBerry users to BlackBerry version
 */
require_once('include/BlackBerry.php');

/**
 * - Start Page Loading Timer
 */
include_once('include/Timer.php');
$starttime = StartLoadTimer();
/**
 * - Set debug mode
 */
$debug_page = false;
include_once('debug/header.php');

/**
 * - Database Connection
 */
require_once('Connections/connDB.php'); 
/**
 * - Config Information
 */
require_once('include/config.php'); 

/* Forward user if they are allready logged in */
if (isset($_SESSION['username']) and ! isset($_SESSION['error'])) {
	header("Location: home.php");
}

/* Set Cookie Expiration */
$cookie_days = 90;
$cookie_expire = time()+60*60*24*$cookie_days;


/* Check user login stats,  store that information into session and cookies */
if (isset($_POST['username']) OR isset($_COOKIE['username'])) {
	if (isset($_COOKIE['username'])) {
		/* Set COOKIE variables as SESSION variables */
		$_SESSION['fullname'] = $_COOKIE['fullname'];
		$_SESSION['username'] = $_COOKIE['username'];
		$_SESSION['request_access']  = $_COOKIE['request_access'];
		$_SESSION['eid']  = $_COOKIE['eid'];
		$_SESSION['request_role']  = $_COOKIE['request_role'];
		
		$GoTo = (isset($_SESSION['redirect'])) ? $_SESSION['redirect'] : "home.php";	
	} else {
		$login_query = $dbh->prepare("SELECT CONCAT(e.fst,' ',e.lst) AS fullname, e.username, e.password, u.access, u.eid, u.vacation, u.role
									  FROM Users u
									    INNER JOIN Standards.Employees e ON e.eid=u.eid
									  WHERE e.username like '".$_POST['username']."' AND u.status = '0' AND e.status = '0';");
		$login_exe = $dbh->execute($login_query);
		$num_rows = $login_exe->numRows();
		$login_db = $login_exe->fetchInto($login);
		
		if ( $num_rows == '1' and $_POST['password'] == $login['password']) {
		  /* Set form variables as session variables */
		  $_SESSION['fullname']  = $login['fullname'];
		  $_SESSION['username'] = $_POST['username'];
		  $_SESSION['request_access']  = $login['access'];
		  $_SESSION['eid']  = $login['eid'];
		  $_SESSION['request_role']  = $login['role'];
		  
		  /* Using SESSION variable set COOKIE variables for 30days */
		  if ($_POST['remember'] == "yes") {
		  	  setcookie(fullname, $_SESSION['fullname'], $cookie_expire);
			  setcookie(username, $_SESSION['username'], $cookie_expire);
			  setcookie(request_access, $_SESSION['request_access'], $cookie_expire);
			  setcookie(eid, $_SESSION['eid'], $cookie_expire);
			  setcookie(request_role, $_SESSION['request_role'], $cookie_expire);
		  }
		  
		  /* Show that user is logged in */
		  $res = $dbh->query("UPDATE Users SET online = '1' WHERE eid = '".$_SESSION['eid']."'");
		  
		  /* ----- Check Vacation status ----- */
		  if (strlen($login['vacation']) == 5 OR strlen($_COOKIE['requst_vacation']) == 5) {
			$GoTo = 'home.php?v=on';
		  } else {
			$GoTo = (isset($_SESSION['redirect'])) ? $_SESSION['redirect'] : "home.php";
		  }
		} else {
			/* Incorrect Username and Password */
			$_SESSION['error'] = "<a href=\"Administration/forgotPassword.php\" title=\"Forgot your Password?\" class=\"white\" rel=\"gb_page_center[400, 250]\">Username or Password is incorrect<br>Forgot your username or password? Select here.</a>";
			$GoTo = "index.php"; 
		}
	}
	
	//unset($_SESSION['error']);			//Cleanup errors after proper login
	//unset($_SESSION['redirect']);		//Cleanup errors after proper login		
	
	header("Location: ".$GoTo); 
	exit();
}


/* Setup onLoad javascript program */
if ($default['pageloading'] == 'on') {
  $ONLOAD_OPTIONS="pageloading();";
}
//$ONLOAD_OPTIONS.="Form.focusFirstElement('login')";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><!-- InstanceBegin template="/Templates/vnmain.dwt.php" codeOutsideHTMLIsLocked="false" -->
  <head>
  <!-- InstanceBeginEditable name="doctitle" -->
    <title><?= $default['title1']; ?></title>
	<script src="/Common/js/capslock.js" type="text/javascript"></script>
  <!-- InstanceEndEditable -->
  <meta http-equiv="imagetoolbar" content="no">
  <meta name="copyright" content="2004 Your Company" />
  <meta name="author" content="Thomas LeZotte" />
  <link type="text/css" href="/Common/Print.css" rel="stylesheet" media="print">
  <link type="text/css" href="default.css" charset="UTF-8" rel="stylesheet">
  <?php if ($default['rss'] == 'on') { ?>
  <link rel="alternate" type="application/rss+xml" title="Purchase Requisition Announcements" href="<?= $default['URL_HOME']; ?>/PO/<?= $default['rss_file']; ?>">
  <link rel="alternate" type="application/rss+xml" title="Capital Acquisition Announcements" href="<?= $default['URL_HOME']; ?>/CER/<?= $default['rss_file']; ?>">
  <?php } ?> 
	<script type="text/javascript" src="/Common/js/overlibmws.js"></script>
  <!-- InstanceBeginEditable name="head" -->
	<script type="text/javascript" src="/Common/js/prototype/prototype.js"></script>
	<script type="text/javascript" src="/Common/js/scriptaculous/scriptaculous.js?load=effects"></script>
	  
	<script type="text/javascript" src="/Common/js/greybox5/options1.js"></script>
    <script type="text/javascript" src="/Common/js/greybox5/AJS.js"></script>
	<script type="text/javascript" src="/Common/js/greybox5/AJS_fx.js"></script>
    <script type="text/javascript" src="/Common/js/greybox5/gb_scripts.js"></script>
	<link type="text/css" href="/Common/js/greybox5/gb_styles.css" rel="stylesheet" media="all">
    <script type="text/javascript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
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

  <body class="yui-skin-sam">  
    <img src="/Common/images/CompanyPrint.gif" alt="Your Company" width="437" height="61" id="Print" />
	<div id="noPrint">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" summary="">
      <tbody>
        <tr>
          <td valign="top"><a href="home.php" title="<?= $default['title1']; ?> Home"><img name="Company" src="/Common/images/Company.gif" width="300" height="50" border="0"></a></td>
          <td align="right" valign="top">
          <!-- InstanceBeginEditable name="topRightMenu" --><!-- InstanceEndEditable --></td>
        </tr>

        <tr>
          <td valign="bottom" align="right" colspan="2"><!-- InstanceBeginEditable name="rightMenu" -->
		  <?php if (isset($_SESSION['username'])) {?>
            <?php include('../include/rightmenu.php'); ?>
          <?php } else { ?>
<table border="0" cellpadding="0" cellspacing="0">
<tr><td height="17">&nbsp;</td></tr>
</table>
<?php } ?><!-- InstanceEndEditable --></td>

          <td>
          </td>
        </tr>

        <tr>
          <td width="100%" colspan="3"><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
            <tbody>
              <tr>
                <td width="4" colspan="4" height="4"><img height="4" alt="" src="images/c-ghtl.gif" width="4"></td>
                <td colspan="4"><table cellspacing="0" cellpadding="0" width="100%" summary="" background="images/c-ght.gif" border="0">
                    <tbody>
                      <tr>
                        <td height="4"></td>
                      </tr>
                    </tbody>
                </table></td>
                <td class="BGColorDark" valign="top" rowspan="2"><table cellspacing="0" cellpadding="0" width="100%" summary="" background="images/c-ght.gif" border="0">
                    <tbody>
                      <tr>
                        <td height="4"></td>
                      </tr>
                    </tbody>
                </table></td>
                <td width="4" colspan="4" height="4"><img height="4" alt="" src="images/c-ghtr.gif" width="4"></td>
              </tr>
              <tr>
                <td class="BGGrayLight" rowspan="3"></td>
                <td class="BGGrayMedium" rowspan="3"></td>
                <td class="BGGrayDark" rowspan="3"></td>
                <td class="BGColorDark" rowspan="3"></td>
                <td class="BGColorDark" rowspan="3"><!-- InstanceBeginEditable name="leftMenu" --><!-- #BeginLibraryItem "/Library/lm_spacer.lbi" --><table cellspacing="0" cellpadding="0" summary="" border="0">
	<tr>
	  <td><img src="images/t.gif" width="200" height="5" border="0"></td>
    </tr>
</table>
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
                <td valign="top"><img height="20" alt="" src="images/c-ghct.gif" width="25"></td>
                <td valign="top" colspan="2"><table cellspacing="0" cellpadding="0" width="100%" summary="" background="images/c-ghb.gif" border="0">
                    <tbody>
                      <tr>
                        <td height="4"></td>
                      </tr>
                    </tbody>
                </table></td>
                <td valign="top" colspan="4"><img height="20" alt="" src="images/c-ghbr.gif" width="4"></td>
              </tr>
              <tr>
                <td width="4" colspan="4" height="4"><img height="4" alt="" src="images/c-ghbl.gif" width="4"></td>
                <td><table height="4" cellspacing="0" cellpadding="0" width="100%" summary="" background="images/c-ghb.gif" border="0">
                    <tbody>
                      <tr>
                        <td></td>
                      </tr>
                    </tbody>
                </table></td>
                <td><img height="4" alt="" src="images/c-ghcb.gif" width="3"></td>
                <td colspan="7"></td>
              </tr>
            </tbody>
          </table></td>
        </tr>
      </tbody>
  </table>
  </div>
    <!-- InstanceBeginEditable name="main" -->
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="200" valign="top"><!-- #BeginLibraryItem "/Library/login.lbi" --><table cellspacing="0" cellpadding="0" width="200" align="left" summary="" border="0">
  <tr>
    <td valign="top" width="13" background="images/asyltlb.gif"><img height="20" alt="" src="images/t.gif" width="13" border="0"></td>
    <td valign="top" width="165" bgcolor="#cccc99"><img height="1" alt="" src="images/asybase.gif" width="145" border="0"> <br>
        <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
          <tr>
            <td class="mainsection"><a href="Administration/forgotPassword.php" title="Forgot your Password?" class="dark" rel="gb_page_center[400, 250]"><img src="/Common/images/addition.gif" alt="" width="16" height="16" border="0" align="absmiddle">&nbsp;Forgot your password?
            </a></td>
          </tr>
        </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
          <tr>
            <td class="mainsection"><a href="Administration/accessRequest.php" title="Request Access" class="dark" rel="gb_page_center[500, 300]"><img src="/Common/images/addition.gif" alt="" width="16" height="16" border="0" align="absmiddle">&nbsp;Request Access...
            </a></td>
          </tr>
        </table>
      <!--
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection"><a href="../Administration/user_new.php" class="dark">New User? </a></td>
            </tr>
          </table>--></td>
    <td valign="top" width="22" background="images/asyltrb.gif"><img height="20" alt="" src="images/t.gif" width="22" border="0"></td>
  </tr>
  <tr>
    <td valign="top" width="22" colspan="3"><img height="37" alt="" src="images/asyltb.gif" width="200" border="0"></td>
  </tr>
</table>
<!-- #EndLibraryItem --></td>
          <td><br>
            <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td nowrap class="ErrorNameText"><div align="center"><span class="DarkHeaderSubSub"><?= $default['title0']; ?></span><br>
                        <span class="DarkHeader"><?= $default['title1']; ?></span><br>
                        <span class="DarkHeaderSubSub"><?= $default['title2']; ?></span></div></td>
              </tr>
            </table>
			<?php if ($default['maintenance'] == 'on') {?>
            <div id="hotMessage" style="width: 98%;margin:3px auto;padding:10px;">
            The system is down for maintenance<br>
            Estimated return time is <?= $default['maintenance_time']; ?><br>
            Sorry for the inconvenience
            </div>
            <?php } else if (array_key_exists('error',$_SESSION)) { ?>
            <div id="hotMessage" style="width: 98%;margin:3px auto;"><?= $_SESSION['error']; ?></div>
            <?php } else { ?>
            <div style="height:50px;">&nbsp;</div>
            <?php } ?>
            <?php if ((is_null($_SESSION['username']) and $default['maintenance'] == 'off') or $_GET['maint'] == 'off') {?>
			<form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" name="login" id="login" style="margin: 0">
            <table border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td class="BGAccentVeryDark"><div align="left">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;<?= (array_key_exists('fullname', $_COOKIE)) ? $_COOKIE['fullname'] .", Please Login" : "Please Login"; ?>...</td>
                        <td>&nbsp;</td>
                      </tr>
                    </table>
                </div></td>
              </tr>
              <tr>
                <td class="BGAccentVeryDarkBorder"><table  border="0" align="center">
                    <tr>
                      <td width="100"><label for="username">Username</label></td>
                      <td><input name="username" type="text" id="username" onKeyPress="checkCapsLock( event )" size="25" maxlength="10" autocomplete="off"></td>
                    </tr>
                    <tr>
                      <td><label for="password">Password</label></td>
                      <td><input name="password" type="password" id="password" onKeyPress="checkCapsLock( event )" size="25"></td>
                    </tr>
                </table></td>
              </tr>
              <tr>
                <td height="5"><img src="images/spacer.gif" width="5" height="5"></td>
              </tr>
              <tr>
                <td><div align="right">
                    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td nowrap>
                          <input name="remember" type="checkbox" id="remember" value="yes"><label for="remember"><a href="javascript:void();" class="NavBarActiveLink" <?php help('', 'Login information will be stored for '.$cookie_days.' days.', 'default'); ?>>Remember Login</a></label></td>
                        <td><div align="right">
                          <input name="redirect" type="hidden" id="redirect" value="<?= $_SESSION['redirect']; ?>">
                          <input name="login" type="image" id="login" src="images/button.php?i=b70.png&l=Login" border="0">
&nbsp;&nbsp;</div></td>
                      </tr>
                    </table>
                    </div></td>
              </tr>
            </table>
			<div align="right"></div>
			</form>
          <?php } ?></td>
        </tr>
      </table>
    <div style="height:100px;">&nbsp;</div>
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
                <td width="50%"><span class="Copyright"><!-- InstanceBeginEditable name="copyright" -->
                  <?php include('include/copyright.php'); ?>
                <!-- InstanceEndEditable --></span></td>
                <td width="50%"><div id="noPrint" align="right"><!-- InstanceBeginEditable name="version" -->
                  <?php include('include/version.php'); ?>
                <!-- InstanceEndEditable --></div></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td>
		  <div align="center"><!-- InstanceBeginEditable name="footer" --><!-- InstanceEndEditable --></div>
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
