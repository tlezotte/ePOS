<?php
/**
 * Request System
 *
 * check_access1.php check for user access level 1.
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
 * - Security related functions
 */
require_once('functions.php');

 
/* ----- CHECK USER LOGIN and ACCESS ----- */
if (is_null($_SESSION['username']) or $_SESSION['request_access'] <= 0) {
	$_SESSION['error'] = "Unauthorized Area";
	
	header("Location: ../error.php");
} else {
	/* ---- Record time visited for Online status ---- */
	MarkOnline();	
}
?>