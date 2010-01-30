<?php
/**
 * Request System
 *
 * check_user.php check to see if user has logged in.
 *
 * @version 1.5
 * @link http://www.yourdomain.com/go/Request/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package Security
  * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 */

/**
 * - Security functions
 */ 
include('functions.php');


/* ----- CHECK USER LOGIN ----- */
if ($default['maintenance'] == 'on' and $_SESSION['request_access'] == '0') {
	unset($_SESSION['username']);
	unset($_SESSION['eid']);
	unset($_SESSION['request_access']);
	header("Location: ../index.php");
}

if (is_null($_SESSION['username'])) {
	$_SESSION['error'] = "Unauthorized Area - Please Login";
	$_SESSION['redirect'] = "http://".$_SERVER['SERVER_NAME']."".$_SERVER['REQUEST_URI'];
	
	header("Location: ../index.php");
} else {
	/* ---- Record time visited for Online status ---- */
	MarkOnline();	
}
?>