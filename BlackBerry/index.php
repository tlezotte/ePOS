<?php 
/**
 * Request System
 *
 * index.php is Login page for BlackBerry.
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

/* Forward user if they are allready logged in */
if (isset($_SESSION['username']) and ! isset($_SESSION['error'])) {
	header("Location: home.php");
}

/* Set Cookie Expiration */
$cookie_days = 30;
$cookie_expire = time()+60*60*24*$cookie_days;

setcookie(cookietest, 'on', time()+60);		// Set cookie test for 1 minute

/* Check user login stats,  store that information into session and cookies */
if (isset($_POST['username']) OR isset($_COOKIE['username'])) {
	if (isset($_COOKIE['username'])) {
		/* Set COOKIE variables as SESSION variables */
		$_SESSION['username'] = $_COOKIE['username'];
		$_SESSION['request_access']  = $_COOKIE['request_access'];
		$_SESSION['eid']  = $_COOKIE['eid'];
		
		$GoTo = (isset($_SESSION['redirect'])) ? $_SESSION['redirect'] : "home.php";	
	} else {
		$login_query = $dbh->prepare("SELECT e.username, e.password, u.access, u.eid
									  FROM Users u
									    INNER JOIN Standards.Employees e ON u.eid = e.eid
									  WHERE e.username like '".$_POST['username']."' 
									    AND u.status = '0' 
										AND e.status = '0';");
		$login_exe = $dbh->execute($login_query);
		$num_rows = $login_exe->numRows();
		$login_db = $login_exe->fetchInto($login);
		
		if ( $num_rows == '1' and $_POST['password'] == $login['password']) {
		  /* Set form variables as session variables */
		  $_SESSION['username'] = $_POST['username'];
		  $_SESSION['request_access']  = $login['access'];
		  $_SESSION['eid']  = $login['eid'];
		  
		  /* Using SESSION variable set COOKIE variables for 30days */
		  if ($_POST['remember'] == "yes") {
			  setcookie(username, $_SESSION['username'], $cookie_expire);
			  setcookie(request_access, $_SESSION['request_access'], $cookie_expire);
			  setcookie(eid, $_SESSION['eid'], $cookie_expire);
		  }
		  
		  /* Show that user is logged in */
		  $res = $dbh->query("UPDATE Users SET online = '1' WHERE eid = '".$_SESSION['eid']."'");
		  
		  /* Check Vacation status */
	//	  if ($login['request_vacation'] != '0') {
	//		$GoTo = 'Administration/vacation.php';
	//	  } else {
			$GoTo = (isset($_SESSION['redirect'])) ? $_SESSION['redirect'] : "home.php";
	//	  }
		} else {
			/* Incorrect Username and Password */
			$_SESSION['error'] = "Username or Password is incorrect";
			$GoTo = "index.php"; 
		}
	}
	
	unset($_SESSION['error']);			//Cleanup errors after proper login
	unset($_SESSION['redirect']);		//Cleanup errors after proper login		
	
	header("Location: ".$GoTo); 
}
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title><?= $default['title1']; ?></title>
<meta name="author" content="Thomas LeZotte" />
<meta name="copyright" content="2005 Your Company" />
<link href="handheld.css" rel="stylesheet" type="text/css" media="handheld">
</head>

<body>
<div class="transform_rule" rule="retaintable" devices="palm,rim" >
<table width="240"  border="0" cellpadding="0" cellspacing="0" class="transform_rule">
        <tr>
          <td nowrap><div align="center"><img src="/Common/images/Company200.gif" alt="Your Company" name="Company" width="200" height="50"></div></td>
        </tr>
<tr>
          <td nowrap><div align="center">
            <?= $default['title0']; ?>
          </div></td>
        </tr>
        <tr>
          <td nowrap><div align="center">
            <?= $default['title1']; ?>
          </div></td>
        </tr>
        <tr>
          <td nowrap><div align="center">
            <?= $default['title2']; ?>
          </div></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <?php if ($default['bb_maintenance'] == 'on') {?>
        <tr>
          <td nowrap class="ErrorNameText"><div align="center">The system is down for maintenance</div></td>
        </tr>
        <tr>
          <td nowrap class="ErrorNameText"><div align="center">Estimated return time is
            <?= $default['maintenance_time']; ?>
          </div></td>
        </tr>
        <tr>
          <td nowrap class="ErrorNameText"><div align="center">Sorry for the inconvenience</div></td>
        </tr>
        <?php } else { ?>
        <tr>
          <td nowrap class="ErrorNameText"><div align="center">
            <?= $_SESSION['error']; ?>
          </div></td>
        </tr>
        <?php } ?>
        <tr>
          <td>
            <?php if ((is_null($_SESSION['username']) and $default['bb_maintenance'] == 'off') or $_GET['maint'] == 'off') {?>
            <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" name="login" id="login" style="margin: 0">
			  <div align="center">
              <table width="220" border="0" cellpadding="0" cellspacing="0" class="transform_rule">
                <tr>
                  <td class="BGAccentVeryDark"><div align="left">
                      <table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                          <td height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Login...</td>
                          <td><div align="right"> </div></td>
                        </tr>
                      </table>
                  </div></td>
                </tr>
                <tr>
                  <td nowrap class="BGAccentVeryDarkBorder"><table width="100%" border="0" align="center" class="transform_rule">
                      <tr>
                        <td nowrap><label for="username">Username:
                          <input name="username" type="text" id="username" onKeyPress="checkCapsLock( event )" size="7" maxlength="8" autocomplete="off">
                        </label></td>
                      </tr>
                      <tr>
                        <td nowrap><label for="password">Password:
                          <input name="password" type="password" id="password" onKeyPress="checkCapsLock( event )" size="7" maxlength="10">
                        </label></td>
                      </tr>
                  </table></td>
                </tr>
				<?php
				if ($_COOKIE['cookietest'] == 'on') {
				?>
                <tr>
                  <td height="5"><input name="remember" type="checkbox" id="remember" value="yes"><span class="NavBarActiveLink">Remember Login</span></td>
                </tr>
				<?php } else { ?>
                <tr>
                  <td height="5">Turn cookies ON to store your login information</td>
                </tr>				
				<?php } ?>
                <tr>
                  <td><div align="right">
                      <input name="redirect" type="hidden" id="redirect" value="<?= $_SESSION['redirect']; ?>">
                      <input name="login" type="submit" value="Login" class="button">
                    &nbsp;</div></td>
                </tr>
              </table>
			  </div>
            </form>
            <?php } ?>
           </td>
        </tr>
</table>
</div>
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