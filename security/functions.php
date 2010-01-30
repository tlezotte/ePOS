<?php
/**
 * Request System
 *
 * functions.php for the security section.
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

function MarkOnline() {
	global $dbh;
	
	/* ---- Record time visited for Online status ---- */
	$sql="UPDATE Users SET online=NOW(), address='".$_SERVER['REMOTE_ADDR']."' WHERE eid='".$_SESSION['eid']."'";
	$dbh->query($sql);	
}
?>