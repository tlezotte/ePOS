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

/* Get Purchase Request users */
$employees_sql = "SELECT U.eid, E.fst, E.lst, E.email 
				  FROM Users U
				    INNER JOIN Standards.Employees E ON E.eid=U.eid
				  WHERE U.status = '0' and E.status = '0'
				  ORDER BY E.lst ASC"; 
$employees_query = $dbh->prepare($employees_sql);
$employees_sth = $dbh->execute($employees_query);


/* Setup onLoad javascript program */
if ($default['pageloading'] == 'on') {
  $ONLOAD_OPTIONS="pageloading();";
}
//$ONLOAD_OPTIONS.="Form.focusFirstElement('login')";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title><?= $default['title1']; ?></title>
    <meta http-equiv="imagetoolbar" content="no">
    <meta name="copyright" content="2004 Your Company" />
    <meta name="author" content="Thomas LeZotte" />
    <link type="text/css" rel="stylesheet" href="/Common/js/yahoo/reset-fonts-grids/reset-fonts-grids.css" /> 		<!-- CSS Grid -->
    <link type="text/css" rel="stylesheet" href="/Common/Print.css" media="print" />
    <link type="text/css" rel="stylesheet" href="default.css" />
    <link type="text/css" rel="alternate stylesheet" title="seasonal" href="/Common/themes/winter/default.css" />
    <link type="text/css" rel="alternate stylesheet" title="night" href="/Common/themes/night/default.css" />   
    <?php if ($default['rss'] == 'on') { ?>
    <link rel="alternate" type="application/rss+xml" title="<?= $default['title1']; ?> Announcements" href="<?= $default['URL_HOME']; ?>/data/<?= $default['rss_file']; ?>">
    <link rel="alternate" type="application/rss+xml" title="Capital Acquisition Announcements" href="<?= $default['URL_HOME']; ?>/data/<?= $default['rss_file']; ?>">
    <?php } ?>
        
	<link type="text/css" href="/Common/js/greybox5/gb_styles.css" rel="stylesheet" media="all" /> 
       
	<script type="text/javascript" src="/Common/js/styleswitcher.js"></script>
    
    <script type="text/javascript" src="/Common/js/jquery/jquery-min.js"></script>
</head>
<body class="yui-skin-sam">
  <div id="doc3" class="yui-t7">
    <div id="hd">
    <div id="hd">
      <div class="yui-gb">
          <div class="yui-u first">
            <img src="/Common/images/CompanyPrint.gif" name="Print" width="437" height="61" id="Print" />
            <a href="../home.php" title="<?= $default['title1']; ?>|Home Page"><img src="/Common/images/Company.gif" width="300" height="50" border="0"></a> 
          </div>
          <div class="yui-u"><!-- Center Title Area -->&nbsp;</div>
          <div class="yui-u">
              <div style="font-weight:bold;font-size:115%;text-align:right">&nbsp;</div>
              <div id="noPrint" class="FieldNumberDisabled" style="text-align:right">&nbsp;</div>
            <div id="styleSwitcher" style="text-align:right">&nbsp;</div>
          </div>
      </div>		      
    </div>
   <div id="bd">
	<div class="yui-g" id="mainMenu">
    	<div class="yuimenubar" style="height:26px"></div>
        <?php if (isset($message)) { ?>
        <div id="messageCenter" <?= ($hotMessage) ? 'class="hotMessage"' : ''; ?> style="display:none"><div><?= $message; ?></div></div>
        <?php } ?>    
	</div>
	<div class="yui-g">
      <div style="padding-top:50px; padding-bottom:50px; text-align:center"> <span class="DarkHeaderSubSub">
        <?= $language['label']['title0']; ?>
        </span> <br>
        <span class="DarkHeader">
          <?= $language['label']['title1']; ?>
        </span> <br>
        <span class="DarkHeaderSubSub">
          <?= $language['label']['title2']; ?>
        </span> </div>
      <?php if ((is_null($_SESSION['username']) and $default['maintenance'] == 'off') or $_GET['maint'] == 'off') {?>
      <table id="loginBorder" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td><table border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td width="300" height="250" align="center" valign="middle"><form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" name="login" id="login" style="margin: 0">
                    <table  border="0" align="center" cellpadding="0" cellspacing="5">
                      <tr>
                        <td height="30"><label for="username">
                          <?= $language['label']['username']; ?>:</label></td>
                        <td><input name="username" type="text" id="username" onKeyPress="checkCapsLock( event )" size="25" maxlength="10" autocomplete="off"></td>
                      </tr>
                      <tr>
                        <td height="30"><label for="password">
                          <?= $language['label']['password']; ?>:</label></td>
                        <td><input name="password" type="password" id="password" onKeyPress="checkCapsLock( event )" size="25"></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td><input name="remember" type="checkbox" id="remember" value="yes">
                                  <label for="remember"><a href="#" class="black" title="User Task|<?= $language['help']['rememberlogin']; ?>">
                                    <?= $language['label']['rememberlogin']; ?>
                                  </a></label></td>
                              <td align="right"><input name="redirect" type="hidden" id="redirect" value="<?= $_SESSION['redirect']; ?>">
                              <input name="login" type="image" id="login" src="images/button.php?i=w70.png&l=Login" border="0"></td>
                            </tr>
                          </table></td>
                      </tr>
                      </table>
                </form></td>
                <td width="50" align="center" valign="middle" class="vLine"><img src="/Common/images/or.png" width="37" height="37"></td>
                <td width="300" height="150" align="center" valign="middle"><form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" name="admin" id="admin">
                    <table border="0" align="center" cellpadding="0" cellspacing="5">
                      <tr>
                        <td height="25" colspan="2">Forgot your password?</td>
                      </tr>
                      <tr>
                        <td><select name="eid" id="eid">
                            <option value="0">Select One</option>
                            <?php
							while($employees_sth->fetchInto($EMPOLYEES)) {
								print "<option value=\"".$EMPOLYEES['eid']."\" ".$selected.">".caps($EMPOLYEES['lst'].", ".$EMPOLYEES['fst'])."</option>";
							}
							?>
                          </select></td>
                        <td align="right"><input name="action" type="hidden" id="action" value="forgot">
                        <input name="login" type="image" id="login" src="images/button.php?i=w70.png&l=<?= $language['label']['send']; ?>" border="0"></td>
                      </tr>
                      <tr>
                        <td height="35" colspan="2" valign="middle" class="hLine">&nbsp;</td>
                      </tr>
                      <tr>
                        <td colspan="2" height="25">Don't have access?</td>
                      </tr>
                      <tr>
                        <td height="25" colspan="2" align="right"><a href="Administration/accessRequest.php" title="Contact Administration|Request Access" class="dark" rel="gb_page_center[500, 300]"><img src="images/button.php?i=w130.png&l=Request Access" border="0" align="absmiddle"></a></td>
                      </tr>
                      </table>
                </form></td>
              </tr>
            </table></td>
        </tr>
      </table>
      <?php } ?>
      </td>
      </tr>
      </table>
      <!-- YOUR DATA GOES HERE -->
    </div>
	</div>
   <div id="ft" style="padding-top:50px">
	 <div class="yui-gb">
        <div class="yui-u first"><?php include('include/copyright.php'); ?></div>
        <div class="yui-u"><!-- FOOTER CENTER AREA -->&nbsp;</div>
        <div class="yui-u" style="text-align:right"><!-- FOOTER RIGHT AREA -->&nbsp;</div>
	    </div>
     </div>  
   </div>
</div>
<script>
	var message='<?= $message; ?>';
	var msgClass='<?= $msgClass; ?>';
</script>

<script type="text/javascript" src="/Common/js/capslock.js"></script>

<script type="text/javascript" src="/Common/js/greybox5/options1.js"></script>
<script type="text/javascript" src="/Common/js/greybox5/AJS.js"></script>
<script type="text/javascript" src="/Common/js/greybox5/AJS_fx.js"></script>
<script type="text/javascript" src="/Common/js/greybox5/gb_scripts.js"></script>

<script type="text/javascript" src="/Common/js/jquery/cluetip/jquery.dimensions.js"></script>
<script type="text/javascript" src="/Common/js/jquery/cluetip/jquery.cluetip.js"></script>

<?php if (!$debug_page) { ?>
<!--<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "<?= $default['google_analytics']; ?>";
urchinTracker();
</script>-->
<?php } ?>
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